import "server-only";

import { createHash, createHmac, randomBytes, timingSafeEqual } from "crypto";
import { createRemoteJWKSet, jwtVerify } from "jose";

export const GOOGLE_OAUTH_COOKIE = "reverence_google_oauth";
export const GOOGLE_OAUTH_MAX_AGE_SECONDS = 10 * 60;

export type GoogleOAuthIntent = "login" | "register";

type GoogleOAuthFlow = {
  state: string;
  verifier: string;
  nonce: string;
  intent: GoogleOAuthIntent;
};

type GoogleTokenResponse = {
  access_token?: string;
  id_token?: string;
  error?: string;
  error_description?: string;
};

const googleJwks = createRemoteJWKSet(new URL("https://www.googleapis.com/oauth2/v3/certs"));

function requiredEnvironment(name: "GOOGLE_CLIENT_ID" | "GOOGLE_CLIENT_SECRET" | "AUTH_SECRET") {
  const value = process.env[name]?.trim();
  if (!value) throw new Error(`${name} is required.`);
  return value;
}

function baseUrl(requestOrigin: string) {
  return (process.env.APP_URL?.trim() || requestOrigin).replace(/\/$/, "");
}

export function googleRedirectUri(requestOrigin: string) {
  return `${baseUrl(requestOrigin)}/api/auth/google/callback`;
}

function signature(payload: string) {
  return createHmac("sha256", requiredEnvironment("AUTH_SECRET")).update(payload).digest("base64url");
}

export function createGoogleOAuthFlow(intent: GoogleOAuthIntent) {
  const flow: GoogleOAuthFlow = {
    state: randomBytes(32).toString("base64url"),
    verifier: randomBytes(48).toString("base64url"),
    nonce: randomBytes(32).toString("base64url"),
    intent,
  };
  const payload = Buffer.from(JSON.stringify(flow)).toString("base64url");
  return { flow, cookieValue: `${payload}.${signature(payload)}` };
}

export function parseGoogleOAuthFlow(value: string | undefined) {
  if (!value) return null;
  const [payload, suppliedSignature] = value.split(".");
  if (!payload || !suppliedSignature) return null;
  const expectedSignature = signature(payload);
  const supplied = Buffer.from(suppliedSignature);
  const expected = Buffer.from(expectedSignature);
  if (supplied.length !== expected.length || !timingSafeEqual(supplied, expected)) return null;

  try {
    const flow = JSON.parse(Buffer.from(payload, "base64url").toString("utf8")) as GoogleOAuthFlow;
    if (!flow.state || !flow.verifier || !flow.nonce || !["login", "register"].includes(flow.intent)) return null;
    return flow;
  } catch {
    return null;
  }
}

export function googleAuthorizationUrl(requestOrigin: string, flow: GoogleOAuthFlow) {
  const challenge = createHash("sha256").update(flow.verifier).digest("base64url");
  const url = new URL("https://accounts.google.com/o/oauth2/v2/auth");
  url.searchParams.set("client_id", requiredEnvironment("GOOGLE_CLIENT_ID"));
  url.searchParams.set("redirect_uri", googleRedirectUri(requestOrigin));
  url.searchParams.set("response_type", "code");
  url.searchParams.set("scope", "openid email profile");
  url.searchParams.set("state", flow.state);
  url.searchParams.set("nonce", flow.nonce);
  url.searchParams.set("code_challenge", challenge);
  url.searchParams.set("code_challenge_method", "S256");
  url.searchParams.set("prompt", "select_account");
  return url;
}

export async function exchangeGoogleCode(requestOrigin: string, code: string, flow: GoogleOAuthFlow) {
  const clientId = requiredEnvironment("GOOGLE_CLIENT_ID");
  const response = await fetch("https://oauth2.googleapis.com/token", {
    method: "POST",
    headers: { "content-type": "application/x-www-form-urlencoded" },
    body: new URLSearchParams({
      code,
      client_id: clientId,
      client_secret: requiredEnvironment("GOOGLE_CLIENT_SECRET"),
      redirect_uri: googleRedirectUri(requestOrigin),
      grant_type: "authorization_code",
      code_verifier: flow.verifier,
    }),
    cache: "no-store",
  });
  const tokens = (await response.json()) as GoogleTokenResponse;
  if (!response.ok || !tokens.id_token) {
    throw new Error(tokens.error_description || tokens.error || "Google token exchange failed.");
  }

  const { payload } = await jwtVerify(tokens.id_token, googleJwks, {
    issuer: ["https://accounts.google.com", "accounts.google.com"],
    audience: clientId,
  });
  if (payload.nonce !== flow.nonce) throw new Error("Google nonce validation failed.");
  if (typeof payload.sub !== "string" || typeof payload.email !== "string" || payload.email_verified !== true) {
    throw new Error("Google did not return a verified email address.");
  }

  return {
    googleId: payload.sub,
    email: payload.email.trim().toLowerCase(),
    name: typeof payload.name === "string" && payload.name.trim() ? payload.name.trim() : payload.email.split("@")[0],
    avatarUrl: typeof payload.picture === "string" ? payload.picture : null,
  };
}

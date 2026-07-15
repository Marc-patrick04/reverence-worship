const TRANSIENT_DATABASE_ERRORS = [
  "connection terminated unexpectedly",
  "connection terminated",
  "connection timeout",
  "connection closed",
  "server closed the connection",
  "can't reach database server",
  "econnreset",
  "etimedout",
  "p1001",
  "p1017",
];

function errorMessages(error: unknown) {
  const messages: string[] = [];
  let current: unknown = error;

  for (let depth = 0; current && depth < 4; depth += 1) {
    if (current instanceof Error) {
      messages.push(current.message);
      current = current.cause;
    } else {
      messages.push(String(current));
      break;
    }
  }

  return messages.join(" ").toLowerCase();
}

export function isTransientDatabaseError(error: unknown) {
  const message = errorMessages(error);
  return TRANSIENT_DATABASE_ERRORS.some((pattern) => message.includes(pattern));
}

export async function withDatabaseRetry<T>(operation: () => Promise<T>, attempts = 2): Promise<T> {
  let lastError: unknown;

  for (let attempt = 1; attempt <= attempts; attempt += 1) {
    try {
      return await operation();
    } catch (error) {
      lastError = error;
      if (!isTransientDatabaseError(error) || attempt === attempts) throw error;
      console.warn(`Database connection dropped; retrying (${attempt}/${attempts - 1}).`);
      await new Promise((resolve) => setTimeout(resolve, attempt * 150));
    }
  }

  throw lastError;
}

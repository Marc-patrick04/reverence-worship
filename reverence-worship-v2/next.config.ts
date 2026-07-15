import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  images: {
    remotePatterns: [
      {
        protocol: "https",
        hostname: "i.ytimg.com",
      },
      {
        protocol: "https",
        hostname: "*.public.blob.vercel-storage.com",
      },
    ],
  },
  ...(process.env.VERCEL
    ? {}
    : {
        turbopack: {
          root: process.cwd(),
        },
      }),
};

export default nextConfig;

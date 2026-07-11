import Image from "next/image";
import Link from "next/link";
import { ArrowLeft } from "lucide-react";

export default function AuthLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <main className="auth-page">
      <div className="auth-shell">
        <section className="auth-card auth-card-register">
          <aside className="brand-panel p-8">
            <Link href="/" className="flex items-center gap-3" aria-label="Back to home">
              <div className="brand-mark size-12 rounded-[0.9rem]">
                <Image
                  src="/logo.png"
                  alt="Reverence Worship"
                  width={48}
                  height={48}
                  className="h-full w-full object-contain p-1"
                  priority
                />
              </div>
              <div>
                <p className="text-lg font-extrabold tracking-wide">REVERENCE</p>
                <p className="brand-subtitle text-xs">Worship Team</p>
              </div>
            </Link>

            <div className="mobile-hide max-w-sm">
              <h1 className="auth-display mt-3 text-3xl font-extrabold leading-tight sm:text-4xl">
                Serve with a faithful heart
              </h1>
              <p className="mt-3 text-sm leading-6 text-white/85">
                &quot;Whatever you do, work at it with all your heart, as working for the
                Lord.&quot;
              </p>
              <p className="brand-subtitle mt-2 text-xs">Colossians 3:23</p>
            </div>

            <div className="mobile-hide border-t border-white/20 pt-4">
              <p className="text-sm text-white/85">Psalm 95:6</p>
              <p className="mt-1 text-xs leading-5 text-white/60">
                Come, let us bow down in worship, let us kneel before the Lord our Maker.
              </p>
              <Link
                href="/"
                className="mt-4 inline-flex items-center gap-2 text-xs text-white/75 transition hover:text-white"
              >
                <ArrowLeft className="size-3" aria-hidden="true" />
                Back to Home
              </Link>
            </div>
          </aside>

          <section className="form-panel flex items-center justify-center p-5 sm:p-7 lg:p-8">
            <div className="w-full max-w-xl">{children}</div>
          </section>
        </section>
      </div>
    </main>
  );
}

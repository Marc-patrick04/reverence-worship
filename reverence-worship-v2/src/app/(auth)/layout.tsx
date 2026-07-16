export default function AuthLayout({
  children,
}: Readonly<{
  children: React.ReactNode;
}>) {
  return (
    <main className="auth-page">
      <div className="auth-shell">
        <section className="auth-card auth-card-single">
          <section className="form-panel flex items-center justify-center p-5 sm:p-7 lg:p-8">
            <div className="w-full max-w-xl">{children}</div>
          </section>
        </section>
      </div>
    </main>
  );
}

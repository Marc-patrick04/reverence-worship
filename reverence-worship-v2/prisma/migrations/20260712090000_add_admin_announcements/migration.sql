CREATE TABLE IF NOT EXISTS "announcements" (
  "id" SERIAL PRIMARY KEY,
  "title" TEXT NOT NULL,
  "content" TEXT NOT NULL,
  "type" VARCHAR(50) NOT NULL DEFAULT 'general',
  "priority" VARCHAR(50) NOT NULL DEFAULT 'normal',
  "status" VARCHAR(50) NOT NULL DEFAULT 'active',
  "scheduled_date" DATE,
  "expiry_date" DATE,
  "target_audience" TEXT,
  "target_type" VARCHAR(50) NOT NULL DEFAULT 'all',
  "target_roles" TEXT,
  "target_users" TEXT,
  "image_path" TEXT,
  "email_sent" BOOLEAN NOT NULL DEFAULT false,
  "created_by" INTEGER REFERENCES "users"("id") ON DELETE SET NULL,
  "published_by" INTEGER REFERENCES "users"("id") ON DELETE SET NULL,
  "published_at" TIMESTAMP,
  "created_at" TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  "updated_at" TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS "announcement_user_reads" (
  "id" SERIAL PRIMARY KEY,
  "announcement_id" INTEGER NOT NULL REFERENCES "announcements"("id") ON DELETE CASCADE,
  "user_id" INTEGER NOT NULL REFERENCES "users"("id") ON DELETE CASCADE,
  "read_at" TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT "announcement_user_reads_announcement_id_user_id_key" UNIQUE ("announcement_id", "user_id")
);

CREATE INDEX IF NOT EXISTS "announcements_status_idx" ON "announcements"("status");
CREATE INDEX IF NOT EXISTS "announcements_target_type_idx" ON "announcements"("target_type");
CREATE INDEX IF NOT EXISTS "announcements_created_by_idx" ON "announcements"("created_by");
CREATE INDEX IF NOT EXISTS "announcements_published_by_idx" ON "announcements"("published_by");
CREATE INDEX IF NOT EXISTS "announcement_user_reads_user_id_idx" ON "announcement_user_reads"("user_id");

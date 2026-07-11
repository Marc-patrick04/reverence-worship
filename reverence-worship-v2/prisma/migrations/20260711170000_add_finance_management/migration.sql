CREATE TABLE IF NOT EXISTS "finance_term_settings" (
  "id" SERIAL PRIMARY KEY,
  "current_year" INTEGER DEFAULT 2026,
  "created_at" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  "updated_at" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  "number_of_terms" INTEGER DEFAULT 3,
  "term_percentages" TEXT DEFAULT '[40,30,30]',
  "term_numbers" TEXT DEFAULT '[1,2,3]'
);

CREATE TABLE IF NOT EXISTS "contributions" (
  "id" SERIAL PRIMARY KEY,
  "user_id" INTEGER NOT NULL REFERENCES "users"("id") ON DELETE CASCADE,
  "annual_amount" DECIMAL(15,2) DEFAULT 0,
  "year" INTEGER NOT NULL,
  "notes" TEXT,
  "status" VARCHAR(50) DEFAULT 'active',
  "created_by" INTEGER REFERENCES "users"("id") ON DELETE SET NULL,
  "created_at" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  "updated_at" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT "contributions_user_id_year_key" UNIQUE ("user_id", "year")
);

CREATE TABLE IF NOT EXISTS "payments" (
  "id" SERIAL PRIMARY KEY,
  "user_id" INTEGER REFERENCES "users"("id") ON DELETE SET NULL,
  "amount" DECIMAL(15,2) NOT NULL,
  "payment_date" DATE DEFAULT CURRENT_DATE,
  "payment_method" VARCHAR(50),
  "term" INTEGER,
  "reference_number" VARCHAR(100),
  "status" VARCHAR(50) DEFAULT 'completed',
  "notes" TEXT,
  "created_by" INTEGER REFERENCES "users"("id") ON DELETE SET NULL,
  "created_at" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  "updated_at" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  "year" INTEGER NOT NULL
);

CREATE TABLE IF NOT EXISTS "gifts" (
  "id" SERIAL PRIMARY KEY,
  "donor_name" VARCHAR(255) NOT NULL,
  "commitment_amount" DECIMAL(15,2) DEFAULT 0,
  "received_amount" DECIMAL(15,2) DEFAULT 0,
  "gift_type" VARCHAR(50),
  "date" DATE,
  "status" VARCHAR(50) DEFAULT 'pending',
  "notes" TEXT,
  "created_by" INTEGER REFERENCES "users"("id") ON DELETE SET NULL,
  "created_at" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  "updated_at" TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS "expenses" (
  "id" SERIAL PRIMARY KEY,
  "category" VARCHAR(100),
  "description" TEXT,
  "amount" DECIMAL(15,2) NOT NULL,
  "date" DATE DEFAULT CURRENT_DATE,
  "status" VARCHAR(50) DEFAULT 'pending',
  "approved_by" INTEGER REFERENCES "users"("id") ON DELETE SET NULL,
  "receipt_path" VARCHAR(500),
  "notes" TEXT,
  "created_by" INTEGER REFERENCES "users"("id") ON DELETE SET NULL,
  "created_at" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  "updated_at" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  "approver_id_1" INTEGER REFERENCES "users"("id") ON DELETE SET NULL,
  "approver_id_2" INTEGER REFERENCES "users"("id") ON DELETE SET NULL,
  "year" INTEGER
);

CREATE TABLE IF NOT EXISTS "sponsors" (
  "id" SERIAL PRIMARY KEY,
  "name" VARCHAR(255) NOT NULL,
  "email" VARCHAR(255),
  "phone" VARCHAR(50),
  "commitment_amount" DECIMAL(15,2) DEFAULT 0,
  "received_amount" DECIMAL(15,2) DEFAULT 0,
  "fund_type" VARCHAR(50) DEFAULT 'one_time',
  "status" VARCHAR(50) DEFAULT 'active',
  "notes" TEXT,
  "created_by" INTEGER REFERENCES "users"("id") ON DELETE SET NULL,
  "created_at" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  "updated_at" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  "year" INTEGER NOT NULL DEFAULT EXTRACT(YEAR FROM CURRENT_DATE)::INTEGER
);

CREATE TABLE IF NOT EXISTS "sponsor_payments" (
  "id" SERIAL PRIMARY KEY,
  "sponsor_id" INTEGER NOT NULL REFERENCES "sponsors"("id") ON DELETE CASCADE,
  "amount" DECIMAL(15,2) NOT NULL,
  "payment_date" DATE DEFAULT CURRENT_DATE,
  "payment_method" VARCHAR(50) DEFAULT 'cash',
  "notes" TEXT,
  "created_by" INTEGER REFERENCES "users"("id") ON DELETE SET NULL,
  "created_at" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  "updated_at" TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  "year" INTEGER,
  "month" INTEGER
);

CREATE INDEX IF NOT EXISTS "contributions_year_idx" ON "contributions"("year");
CREATE INDEX IF NOT EXISTS "payments_user_id_idx" ON "payments"("user_id");
CREATE INDEX IF NOT EXISTS "payments_year_idx" ON "payments"("year");
CREATE INDEX IF NOT EXISTS "expenses_year_idx" ON "expenses"("year");
CREATE INDEX IF NOT EXISTS "expenses_status_idx" ON "expenses"("status");
CREATE INDEX IF NOT EXISTS "sponsors_year_idx" ON "sponsors"("year");
CREATE INDEX IF NOT EXISTS "sponsor_payments_sponsor_id_idx" ON "sponsor_payments"("sponsor_id");
CREATE INDEX IF NOT EXISTS "sponsor_payments_year_idx" ON "sponsor_payments"("year");
CREATE INDEX IF NOT EXISTS "sponsor_payments_month_idx" ON "sponsor_payments"("month");

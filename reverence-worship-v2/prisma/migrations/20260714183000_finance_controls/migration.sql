ALTER TABLE "expenses"
  ADD COLUMN "reference_number" TEXT,
  ADD COLUMN "rejection_reason" TEXT,
  ADD COLUMN "void_reason" TEXT,
  ADD COLUMN "voided_at" TIMESTAMP(3),
  ADD COLUMN "voided_by" INTEGER;

CREATE TABLE "finance_reconciliations" (
  "id" SERIAL NOT NULL,
  "source_type" TEXT NOT NULL,
  "source_id" INTEGER NOT NULL,
  "reference" TEXT,
  "notes" TEXT,
  "reconciled_by" INTEGER NOT NULL,
  "reconciled_at" TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT "finance_reconciliations_pkey" PRIMARY KEY ("id")
);

CREATE UNIQUE INDEX "finance_reconciliations_source_type_source_id_key"
  ON "finance_reconciliations"("source_type", "source_id");
CREATE INDEX "finance_reconciliations_reconciled_at_idx"
  ON "finance_reconciliations"("reconciled_at");

ALTER TABLE "expenses" ADD CONSTRAINT "expenses_voided_by_fkey"
  FOREIGN KEY ("voided_by") REFERENCES "users"("id") ON DELETE SET NULL ON UPDATE CASCADE;
ALTER TABLE "finance_reconciliations" ADD CONSTRAINT "finance_reconciliations_reconciled_by_fkey"
  FOREIGN KEY ("reconciled_by") REFERENCES "users"("id") ON DELETE RESTRICT ON UPDATE CASCADE;

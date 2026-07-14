ALTER TABLE "expenses"
  ADD COLUMN "void_requested_at" TIMESTAMP(3),
  ADD COLUMN "void_requested_by" INTEGER;

ALTER TABLE "expenses" ADD CONSTRAINT "expenses_void_requested_by_fkey"
  FOREIGN KEY ("void_requested_by") REFERENCES "users"("id") ON DELETE SET NULL ON UPDATE CASCADE;

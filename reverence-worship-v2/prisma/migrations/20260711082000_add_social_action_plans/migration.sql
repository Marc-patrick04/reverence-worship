-- CreateTable
CREATE TABLE "action_plans" (
    "id" SERIAL NOT NULL,
    "family_id" INTEGER,
    "title" TEXT NOT NULL,
    "description" TEXT,
    "start_date" DATE NOT NULL,
    "due_date" DATE NOT NULL,
    "status" TEXT NOT NULL DEFAULT 'pending',
    "priority" TEXT NOT NULL DEFAULT 'medium',
    "department" TEXT NOT NULL DEFAULT 'social-fellowship',
    "year" INTEGER NOT NULL DEFAULT 2026,
    "progress" INTEGER NOT NULL DEFAULT 0,
    "created_by" INTEGER,
    "created_at" TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    "updated_at" TIMESTAMP(3) NOT NULL,

    CONSTRAINT "action_plans_pkey" PRIMARY KEY ("id")
);

-- CreateTable
CREATE TABLE "action_plan_tasks" (
    "id" SERIAL NOT NULL,
    "action_plan_id" INTEGER NOT NULL,
    "task_name" TEXT NOT NULL,
    "activity" TEXT,
    "target_milestone" TEXT,
    "estimated_budget" DECIMAL(15,2) NOT NULL DEFAULT 0,
    "start_date" DATE,
    "deadline" DATE,
    "priority" TEXT NOT NULL DEFAULT 'medium',
    "progress" INTEGER NOT NULL DEFAULT 0,
    "assigned_to" INTEGER,
    "status" TEXT NOT NULL DEFAULT 'pending',
    "started_at" TIMESTAMP(3),
    "completed_at" TIMESTAMP(3),
    "created_at" TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    "updated_at" TIMESTAMP(3) NOT NULL,

    CONSTRAINT "action_plan_tasks_pkey" PRIMARY KEY ("id")
);

-- CreateIndex
CREATE INDEX "action_plans_family_id_idx" ON "action_plans"("family_id");

-- CreateIndex
CREATE INDEX "action_plans_department_idx" ON "action_plans"("department");

-- CreateIndex
CREATE INDEX "action_plans_year_idx" ON "action_plans"("year");

-- CreateIndex
CREATE INDEX "action_plans_status_idx" ON "action_plans"("status");

-- CreateIndex
CREATE INDEX "action_plan_tasks_action_plan_id_idx" ON "action_plan_tasks"("action_plan_id");

-- CreateIndex
CREATE INDEX "action_plan_tasks_assigned_to_idx" ON "action_plan_tasks"("assigned_to");

-- CreateIndex
CREATE INDEX "action_plan_tasks_status_idx" ON "action_plan_tasks"("status");

-- AddForeignKey
ALTER TABLE "action_plans" ADD CONSTRAINT "action_plans_family_id_fkey" FOREIGN KEY ("family_id") REFERENCES "families"("id") ON DELETE SET NULL ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE "action_plans" ADD CONSTRAINT "action_plans_created_by_fkey" FOREIGN KEY ("created_by") REFERENCES "users"("id") ON DELETE SET NULL ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE "action_plan_tasks" ADD CONSTRAINT "action_plan_tasks_action_plan_id_fkey" FOREIGN KEY ("action_plan_id") REFERENCES "action_plans"("id") ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE "action_plan_tasks" ADD CONSTRAINT "action_plan_tasks_assigned_to_fkey" FOREIGN KEY ("assigned_to") REFERENCES "users"("id") ON DELETE SET NULL ON UPDATE CASCADE;

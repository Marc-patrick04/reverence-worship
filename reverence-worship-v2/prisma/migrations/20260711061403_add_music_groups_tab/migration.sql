-- CreateTable
CREATE TABLE "service_teams" (
    "id" SERIAL NOT NULL,
    "service_name" TEXT NOT NULL,
    "number_of_teams" INTEGER NOT NULL DEFAULT 1,
    "generated_at" TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    "created_by" INTEGER,
    "created_at" TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    "service_date" DATE,

    CONSTRAINT "service_teams_pkey" PRIMARY KEY ("id")
);

-- CreateTable
CREATE TABLE "team_members" (
    "id" SERIAL NOT NULL,
    "service_team_id" INTEGER,
    "team_number" INTEGER NOT NULL,
    "user_id" INTEGER,
    "voice_part" TEXT,
    "performance_level" TEXT,
    "created_at" TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT "team_members_pkey" PRIMARY KEY ("id")
);

-- CreateIndex
CREATE INDEX "service_teams_created_by_idx" ON "service_teams"("created_by");

-- CreateIndex
CREATE INDEX "service_teams_created_at_idx" ON "service_teams"("created_at");

-- CreateIndex
CREATE INDEX "team_members_service_team_id_idx" ON "team_members"("service_team_id");

-- CreateIndex
CREATE INDEX "team_members_user_id_idx" ON "team_members"("user_id");

-- AddForeignKey
ALTER TABLE "service_teams" ADD CONSTRAINT "service_teams_created_by_fkey" FOREIGN KEY ("created_by") REFERENCES "users"("id") ON DELETE SET NULL ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE "team_members" ADD CONSTRAINT "team_members_service_team_id_fkey" FOREIGN KEY ("service_team_id") REFERENCES "service_teams"("id") ON DELETE CASCADE ON UPDATE CASCADE;

-- AddForeignKey
ALTER TABLE "team_members" ADD CONSTRAINT "team_members_user_id_fkey" FOREIGN KEY ("user_id") REFERENCES "users"("id") ON DELETE SET NULL ON UPDATE CASCADE;

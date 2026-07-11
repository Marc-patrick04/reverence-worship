-- CreateTable
CREATE TABLE "photo_gallery" (
    "id" SERIAL NOT NULL,
    "title" TEXT NOT NULL,
    "image_path" TEXT NOT NULL,
    "description" TEXT,
    "event_date" DATE,
    "created_by" INTEGER,
    "category" TEXT,
    "tags" TEXT,
    "alt_text" TEXT,
    "created_at" TIMESTAMP(3) NOT NULL DEFAULT CURRENT_TIMESTAMP,
    "updated_at" TIMESTAMP(3) NOT NULL,

    CONSTRAINT "photo_gallery_pkey" PRIMARY KEY ("id")
);

-- CreateIndex
CREATE INDEX "photo_gallery_created_by_idx" ON "photo_gallery"("created_by");

-- CreateIndex
CREATE INDEX "photo_gallery_created_at_idx" ON "photo_gallery"("created_at");

-- AddForeignKey
ALTER TABLE "photo_gallery" ADD CONSTRAINT "photo_gallery_created_by_fkey" FOREIGN KEY ("created_by") REFERENCES "users"("id") ON DELETE SET NULL ON UPDATE CASCADE;

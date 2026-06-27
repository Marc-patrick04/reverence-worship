<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->integer('approver_id_1')->nullable()->index();
            $table->integer('approver_id_2')->nullable()->index();

            $table->foreign('approver_id_1')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
            $table->foreign('approver_id_2')
                ->references('id')
                ->on('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('expenses', function (Blueprint $table) {
            $table->dropForeign(['approver_id_1']);
            $table->dropForeign(['approver_id_2']);
            $table->dropColumn(['approver_id_1', 'approver_id_2']);
        });
    }
};

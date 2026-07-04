<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alter type columns from ENUM to VARCHAR(50)
        DB::statement("ALTER TABLE `grades` MODIFY `type` VARCHAR(50)");
        DB::statement("ALTER TABLE `exam_plans` MODIFY `type` VARCHAR(50)");
        DB::statement("ALTER TABLE `teaching_documents` MODIFY `type` VARCHAR(50)");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // It's dangerous to revert back to ENUM if we have string values that don't match the old ENUM,
        // but for the sake of completeness, we can define the down method.
        DB::statement("ALTER TABLE `grades` MODIFY `type` ENUM('tugas', 'ulangan_harian', 'uts', 'uas', 'praktik')");
        DB::statement("ALTER TABLE `exam_plans` MODIFY `type` ENUM('ulangan_harian', 'remidi', 'tugas', 'uts', 'uas')");
        DB::statement("ALTER TABLE `teaching_documents` MODIFY `type` ENUM('rpp', 'silabus', 'prota', 'prosem', 'kkm', 'lainnya')");
    }
};

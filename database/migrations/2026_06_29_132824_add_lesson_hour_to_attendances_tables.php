<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update attendances table
        Schema::table('attendances', function (Blueprint $table) {
            $table->integer('lesson_hour')->nullable()->after('date');
            
            // Drop old unique constraint from previous migration
            // Previous name was attendance_schedule_unique
            $table->dropUnique('attendance_schedule_unique');
            
            // Add new unique constraint
            $table->unique(['student_id', 'lesson_hour', 'date'], 'attendance_lesson_unique');
        });

        // 2. Update teacher_attendances table
        Schema::table('teacher_attendances', function (Blueprint $table) {
            $table->integer('lesson_hour')->nullable()->after('date');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropUnique('attendance_lesson_unique');
            $table->dropColumn('lesson_hour');
            $table->unique(['student_id', 'schedule_id', 'date'], 'attendance_schedule_unique');
        });

        Schema::table('teacher_attendances', function (Blueprint $table) {
            $table->dropColumn('lesson_hour');
        });
    }
};

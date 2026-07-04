<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Add explicit index for student_id so foreign key has an index
            $table->index('student_id', 'attendances_student_id_index');
            
            // Drop old unique constraint
            $table->dropUnique('attendance_unique');
            
            // Create new unique constraint with schedule_id
            $table->unique(['student_id', 'schedule_id', 'date'], 'attendance_schedule_unique');
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropUnique('attendance_schedule_unique');
            $table->unique(['student_id', 'subject_id', 'date'], 'attendance_unique');
            $table->dropIndex('attendances_student_id_index');
        });
    }
};

<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Delete all existing schedules because we are changing fundamental data structure
        DB::table('schedules')->delete();

        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('start_time');
            $table->dropColumn('end_time');
            $table->integer('lesson_hour')->after('day')->default(1);
        });
    }

    public function down(): void
    {
        DB::table('schedules')->delete();
        
        Schema::table('schedules', function (Blueprint $table) {
            $table->dropColumn('lesson_hour');
            $table->time('start_time')->default('07:00:00');
            $table->time('end_time')->default('08:00:00');
        });
    }
};

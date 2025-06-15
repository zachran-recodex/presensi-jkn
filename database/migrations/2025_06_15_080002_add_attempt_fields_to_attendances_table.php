<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->integer('attempt_number')->default(1)->after('status');
            $table->integer('total_attempts_today')->default(1)->after('attempt_number');
            $table->json('attempt_history')->nullable()->after('total_attempts_today');
            
            // Index untuk query attempt
            $table->index(['user_id', 'type', 'attendance_date', 'attempt_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropIndex(['user_id', 'type', 'attendance_date', 'attempt_number']);
            $table->dropColumn(['attempt_number', 'total_attempts_today', 'attempt_history']);
        });
    }
};

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
        Schema::table('users', function (Blueprint $table) {
            // Add location assignment for employees
            $table->foreignId('location_id')->nullable()->constrained()->onDelete('set null');

            // Add additional fields for employee management
            $table->string('employee_id')->nullable()->unique();
            $table->string('phone')->nullable();
            $table->string('position')->nullable();

            // Add indexes for better performance
            $table->index('role');
            $table->index('status');
            $table->index('employee_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['location_id']);
            $table->dropColumn([
                'location_id',
                'employee_id',
                'phone',
                'position'
            ]);

            $table->dropIndex(['role']);
            $table->dropIndex(['status']);
            $table->dropIndex(['employee_id']);
        });
    }
};

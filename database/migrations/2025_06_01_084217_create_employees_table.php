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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->constrained('locations')->onDelete('restrict');
            $table->string('employee_id')->unique(); // NIK/ID Karyawan
            $table->string('phone')->nullable(); // Nomor telepon
            $table->string('position'); // Jabatan
            $table->string('department')->nullable(); // Departemen
            $table->date('join_date'); // Tanggal bergabung
            $table->time('work_start_time')->default('08:00:00'); // Jam masuk kerja
            $table->time('work_end_time')->default('17:00:00'); // Jam pulang kerja
            $table->boolean('is_flexible_time')->default(false); // Jam kerja fleksibel
            $table->enum('status', ['active', 'inactive', 'terminated'])->default('active');
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->timestamps();

            // Indexes untuk performa
            $table->index('employee_id');
            $table->index('status');
            $table->index(['location_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};

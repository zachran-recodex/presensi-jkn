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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('location_id')->nullable()->constrained('locations')->onDelete('set null');
            $table->enum('type', ['clock_in', 'clock_out']); // Jenis presensi
            $table->date('attendance_date'); // Tanggal presensi
            $table->time('attendance_time'); // Waktu presensi
            $table->string('photo_path')->nullable(); // Path foto selfie
            $table->decimal('latitude', 10, 8)->nullable(); // Koordinat saat presensi
            $table->decimal('longitude', 11, 8)->nullable(); // Koordinat saat presensi
            $table->boolean('is_valid_location')->default(false); // Apakah lokasi valid
            $table->decimal('distance_from_office', 8, 2)->nullable(); // Jarak dari kantor (meter)
            $table->enum('status', ['success', 'failed', 'pending'])->default('pending');
            $table->string('device_info')->nullable(); // Info device (browser, OS)
            $table->string('ip_address')->nullable(); // IP address saat presensi
            $table->json('face_recognition_result')->nullable(); // Response dari Biznet Face API
            $table->decimal('face_similarity_score', 3, 2)->nullable(); // Score similarity wajah
            $table->boolean('is_late')->default(false); // Apakah terlambat
            $table->integer('late_minutes')->default(0); // Berapa menit terlambat
            $table->text('notes')->nullable(); // Catatan tambahan
            $table->text('failure_reason')->nullable(); // Alasan gagal (jika status failed)
            $table->timestamps();

            // Indexes untuk performa query
            $table->index(['user_id', 'attendance_date']);
            $table->index(['attendance_date', 'type']);
            $table->index('status');
            $table->index(['user_id', 'type', 'attendance_date']);

            // Unique constraint untuk mencegah duplicate clock in/out di hari yang sama
            $table->unique(['user_id', 'type', 'attendance_date'], 'unique_daily_attendance');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};

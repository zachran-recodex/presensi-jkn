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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nama lokasi (Kantor Pusat, Cabang Jakarta, dll)
            $table->string('address')->nullable(); // Alamat lengkap
            $table->decimal('latitude', 10, 8); // Koordinat latitude
            $table->decimal('longitude', 11, 8); // Koordinat longitude
            $table->integer('radius')->default(100); // Radius dalam meter untuk validasi lokasi
            $table->string('timezone')->default('Asia/Jakarta'); // Timezone untuk lokasi
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Index untuk performa query koordinat
            $table->index(['latitude', 'longitude']);
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};

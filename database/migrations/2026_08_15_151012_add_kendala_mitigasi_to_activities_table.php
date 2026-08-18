<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->text('kendala')->nullable();
            $table->text('mitigasi')->nullable();
            // Juga tambahkan flag keterlambatan jika dibutuhkan, atau kita bisa hitung on the fly.
            // Kita hitung on the fly saja, tidak perlu field baru.
            // Tambahkan persentase progress dan durasi untuk di-sync dari ProjectTask
            $table->decimal('percent_complete', 5, 2)->default(0);
            $table->decimal('duration_days', 8, 2)->default(1);
        });
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropColumn(['kendala', 'mitigasi', 'percent_complete', 'duration_days']);
        });
    }
};
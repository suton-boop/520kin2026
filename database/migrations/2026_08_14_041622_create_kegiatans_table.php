<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kegiatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('program_id')->constrained('programs')->onDelete('cascade');
            $table->string('nama');
            $table->integer('volume_target')->default(0);
            $table->integer('volume_realisasi')->default(0);
            $table->decimal('anggaran_alokasi', 20, 2)->default(0);
            $table->decimal('anggaran_realisasi', 20, 2)->default(0);
            $table->string('pelaksanaan')->nullable();
            $table->json('kelengkapan_bulanan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kegiatans');
    }
};
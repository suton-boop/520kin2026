<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_dependencies', function (Blueprint $table) {
            $table->id();
            $table->foreignId('successor_task_id')->constrained('project_tasks')->cascadeOnDelete();
            $table->foreignId('predecessor_task_id')->constrained('project_tasks')->cascadeOnDelete();
            $table->string('dependency_type')->default('FS'); // FS, SS, FF, SF
            $table->decimal('lag_days', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_dependencies');
    }
};

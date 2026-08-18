<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('project_tasks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->cascadeOnDelete();
            $table->foreignId('parent_id')->nullable()->constrained('project_tasks')->nullOnDelete();
            $table->integer('task_id_number')->nullable();
            $table->string('wbs_code')->nullable();
            $table->string('name');
            $table->boolean('is_auto_scheduled')->default(true);
            $table->boolean('effort_driven')->default(false);
            
            $table->dateTime('start_date')->nullable();
            $table->dateTime('finish_date')->nullable();
            
            $table->dateTime('actual_start_date')->nullable();
            $table->dateTime('actual_finish_date')->nullable();
            
            $table->dateTime('baseline_start_date')->nullable();
            $table->dateTime('baseline_finish_date')->nullable();
            
            $table->decimal('percent_complete', 5, 2)->default(0);
            
            $table->decimal('duration_days', 8, 2)->default(1);
            $table->decimal('actual_duration_days', 8, 2)->nullable();
            $table->decimal('remaining_duration_days', 8, 2)->nullable();
            
            $table->boolean('is_estimated')->default(false);
            
            $table->decimal('work_hours', 10, 2)->default(0);
            $table->decimal('actual_work_hours', 10, 2)->default(0);
            $table->decimal('actual_cost', 15, 2)->default(0);
            
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('project_tasks');
    }
};

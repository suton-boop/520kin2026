<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->string('approval_status')->default('Draft')->after('status_akhir');
            $table->foreignId('manager_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('admin_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('manager_approved_at')->nullable();
            $table->timestamp('admin_approved_at')->nullable();
            $table->text('reviewer_notes')->nullable();
        });

        // Migrate existing data
        $submissions = DB::table('report_submissions')->get();
        foreach ($submissions as $sub) {
            DB::table('activities')->where('report_submission_id', $sub->id)->update([
                'approval_status' => $sub->approval_status,
                'manager_id' => $sub->manager_id,
                'admin_id' => $sub->admin_id,
                'manager_approved_at' => $sub->manager_approved_at,
                'admin_approved_at' => $sub->admin_approved_at,
                'reviewer_notes' => $sub->reviewer_notes ?? null,
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('activities', function (Blueprint $table) {
            $table->dropForeign(['manager_id']);
            $table->dropForeign(['admin_id']);
            $table->dropColumn([
                'approval_status',
                'manager_id',
                'admin_id',
                'manager_approved_at',
                'admin_approved_at',
                'reviewer_notes',
            ]);
        });
    }
};

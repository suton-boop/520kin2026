<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use App\Http\Controllers\ExcelImportController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GugusMutuController;

Route::get('/', [DashboardController::class, 'publicDashboard'])->name('welcome');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Reports Routes (Perencanaan & Pelaporan)
    Route::get('reports/export', [\App\Http\Controllers\ReportController::class, 'export'])->name('reports.export');
    Route::resource('Project', \App\Http\Controllers\ReportController::class)->names('reports')->parameter('Project', 'report');
    
    // Gugus Mutu Report
    Route::get('/gugus-mutu-report', [\App\Http\Controllers\GugusMutuReportController::class, 'index'])->name('gugus-mutu-report.index');

    // Rute Submit Plan: terikat aturan tgl 20
    Route::post('Project/{id}/submit-plan', [\App\Http\Controllers\ReportController::class, 'submitPlan'])
        ->middleware(\App\Http\Middleware\Check520Rule::class.':plan')
        ->name('reports.submit_plan');

    // Rute Submit Report: terikat aturan tgl 5
    Route::post('Project/{id}/submit-report', [\App\Http\Controllers\ReportController::class, 'submitReport'])
        ->middleware(\App\Http\Middleware\Check520Rule::class.':report')
        ->name('reports.submit_report');

    // Approvals Routes
    Route::get('/approvals', [\App\Http\Controllers\ApprovalController::class, 'index'])->name('approvals.index');
    Route::post('/approvals/bulk-approve', [\App\Http\Controllers\ApprovalController::class, 'bulkApprove'])->name('approvals.bulk_approve');
    Route::post('/approvals/{id}/approve', [\App\Http\Controllers\ApprovalController::class, 'approve'])->name('approvals.approve');
    Route::post('/approvals/{id}/reject', [\App\Http\Controllers\ApprovalController::class, 'reject'])->name('approvals.reject');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Global components shared across roles
    Route::get('/users/export-template', [ExcelImportController::class, 'downloadTemplate'])->name('import.template');
    Route::post('/import-program', [ExcelImportController::class, 'importProgram'])->name('import.program');
    Route::post('/import-debug', [\App\Http\Controllers\DebugImportController::class, 'debug'])->name('import.debug');

    Route::middleware(['role:superadmin|admin'])->group(function () {
        Route::resource('users', \App\Http\Controllers\UserController::class);
        Route::get('/settings', [\App\Http\Controllers\SettingController::class, 'index'])->name('settings.index');
        Route::post('/settings/roles', [\App\Http\Controllers\SettingController::class, 'updateRoles'])->name('settings.roles');
        Route::post('/settings/features', [\App\Http\Controllers\SettingController::class, 'updateFeatures'])->name('settings.features');
        Route::post('/settings/permissions', [\App\Http\Controllers\SettingController::class, 'updateRolePermissions'])->name('settings.permissions');
        Route::resource('gugus-mutus', GugusMutuController::class)->except(['create', 'show', 'edit']);
        Route::post('/gugus-mutu/{gugusMutu}/toggle-import', [GugusMutuController::class, 'toggleImport'])->name('gugus-mutu.toggle-import');
    });

});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/transformasi-organisasi', function () {
        return Inertia::render('Placeholder', ['title' => 'Transformasi Organisasi']);
    })->name('transformasi');

        Route::get('projects/export', [\App\Http\Controllers\ProjectController::class, 'export'])->name('projects.export');
    Route::resource('projects', \App\Http\Controllers\ProjectController::class);
    Route::get('/projects/{project}/gantt-data', [\App\Http\Controllers\ProjectTaskController::class, 'getGanttData'])->name('projects.gantt_data');
    Route::post('/projects/{project}/task', [\App\Http\Controllers\ProjectTaskController::class, 'storeTask'])->name('projects.task.store');
    Route::put('/projects/{project}/task/{task}', [\App\Http\Controllers\ProjectTaskController::class, 'updateTask'])->name('projects.task.update');
    Route::delete('/projects/{project}/task/{task}', [\App\Http\Controllers\ProjectTaskController::class, 'destroyTask'])->name('projects.task.destroy');
    Route::post('/projects/{project}/link', [\App\Http\Controllers\ProjectTaskController::class, 'storeLink'])->name('projects.link.store');
    Route::put('/projects/{project}/link/{link}', [\App\Http\Controllers\ProjectTaskController::class, 'updateLink'])->name('projects.link.update');
    Route::delete('/projects/{project}/link/{link}', [\App\Http\Controllers\ProjectTaskController::class, 'destroyLink'])->name('projects.link.destroy');
    Route::get('/anggaran', [\App\Http\Controllers\AnggaranController::class, 'index'])->name('anggaran');
    Route::get('/anggaran/export', [\App\Http\Controllers\AnggaranController::class, 'export'])->name('anggaran.export');
    Route::post('/anggaran', [\App\Http\Controllers\AnggaranController::class, 'store'])->name('anggaran.store');
    Route::put('/anggaran/{anggaran}', [\App\Http\Controllers\AnggaranController::class, 'update'])->name('anggaran.update');
    Route::post('/anggaran/{anggaran}/toggle-active', [\App\Http\Controllers\AnggaranController::class, 'toggleActive'])->name('anggaran.toggle_active');
    Route::delete('/anggaran/{anggaran}', [\App\Http\Controllers\AnggaranController::class, 'destroy'])->name('anggaran.destroy');
    
    Route::post('/Project/{id}/activities', [\App\Http\Controllers\ActivityController::class, 'store'])->name('activities.store');
    Route::put('/activities/{activity}', [\App\Http\Controllers\ActivityController::class, 'update'])->name('activities.update');
    Route::delete('/activities/{activity}', [\App\Http\Controllers\ActivityController::class, 'destroy'])->name('activities.destroy');
});

require __DIR__.'/auth.php';










<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProjectTask;


$tasks = [
    [
        'project_id' => 1,
        'name' => 'Fase Perencanaan',
        'start_date' => '2026-08-17',
        'finish_date' => '2026-08-20',
        'percent_complete' => 100,
        'duration_days' => 4,
        'wbs_code' => '1'
    ],
    [
        'project_id' => 1,
        'name' => 'Pembuatan Modul Ajar',
        'start_date' => '2026-08-21',
        'finish_date' => '2026-08-25',
        'percent_complete' => 40,
        'duration_days' => 5,
        'wbs_code' => '2'
    ],
    [
        'project_id' => 2,
        'name' => 'Identifikasi Kendala Daerah 3T',
        'start_date' => '2026-08-17',
        'finish_date' => '2026-08-19',
        'percent_complete' => 50,
        'duration_days' => 3,
        'wbs_code' => '1'
    ]
];

foreach ($tasks as $idx => $task) {
    $task['task_id_number'] = $idx + 1;
    $task['sort_order'] = $idx + 1;
    ProjectTask::create($task);
}

echo "Seeded dummy project tasks.\n";

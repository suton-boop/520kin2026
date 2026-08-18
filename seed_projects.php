<?php
require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Project;
use App\Models\GugusMutu;

$gmp = GugusMutu::where('name', 'like', '%PAUD%')->first();
$gms = GugusMutu::where('name', 'like', '%SD%')->first();

Project::create([
    'name' => 'Proyek Pengembangan Kurikulum',
    'description' => 'Pengembangan modul ajar dan kurikulum.',
    'start_date' => '2026-01-01',
    'end_date' => '2026-12-31',
    'status' => 'Ongoing',
    'gugus_mutu_id' => $gmp ? $gmp->id : 1,
]);

Project::create([
    'name' => 'Fasilitasi Penjaminan Mutu',
    'description' => 'Fasilitasi untuk penjaminan mutu pendidikan.',
    'start_date' => '2026-03-01',
    'end_date' => '2026-09-30',
    'status' => 'Planned',
    'gugus_mutu_id' => $gms ? $gms->id : 2,
]);

echo "Seeded dummy projects.\n";

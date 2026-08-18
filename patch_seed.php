<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(\Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Anggaran;

Anggaran::truncate();

$parent = Anggaran::create([
    'kode' => 'RO 7605.QDB.750',
    'nomenklatur' => 'Satuan PAUD, Dikdas, Dikmen dan Dikmas yang difasilitasi penjaminan mutunya',
    'volume' => '5,725.0 LEMBAGA',
    'pelaksanaan' => 100.0,
    'anggaran_alokasi' => 4931404000,
    'anggaran_realisasi' => 3235455127,
    'kelengkapan' => array_fill(0, 12, true),
]);

$children = [
    [
        'urut' => 1,
        'kode' => 'KOMP 091',
        'tipe' => 'utama',
        'nomenklatur' => 'Pelaksanaan Pembinaan Kurikulum Merdeka',
        'volume' => '7.0 Sekolah',
        'pelaksanaan' => 100.0,
        'anggaran_alokasi' => 47180000,
        'anggaran_realisasi' => 0,
        'kelengkapan' => array_fill(0, 12, true),
    ],
    [
        'urut' => 2,
        'kode' => 'KOMP 092',
        'tipe' => 'utama',
        'nomenklatur' => 'Pelaksanaan Pembinaan Asesmen Nasional',
        'volume' => '16.0 Sekolah',
        'pelaksanaan' => 100.0,
        'anggaran_alokasi' => 473760000,
        'anggaran_realisasi' => 0,
        'kelengkapan' => array_fill(0, 12, true),
    ],
    [
        'urut' => 3,
        'kode' => 'KOMP 093',
        'tipe' => 'utama',
        'nomenklatur' => 'Pelaksanaan Pembinaan Transfer Daerah',
        'volume' => '1,725.0 Sekolah',
        'pelaksanaan' => 100.0,
        'anggaran_alokasi' => 551484000,
        'anggaran_realisasi' => 551481603,
        'kelengkapan' => array_fill(0, 12, true),
    ],
    [
        'urut' => 4,
        'kode' => 'KOMP 094',
        'tipe' => 'utama',
        'nomenklatur' => 'Pelaksanaan Pembinaan Sekolah Penggerak',
        'volume' => '7.0 Sekolah',
        'pelaksanaan' => 100.0,
        'anggaran_alokasi' => 465616000,
        'anggaran_realisasi' => 0,
        'kelengkapan' => array_fill(0, 12, true),
    ],
    [
        'urut' => 5,
        'kode' => 'KOMP 095',
        'tipe' => 'utama',
        'nomenklatur' => 'Pelaksanaan Pembinaan Perencanaan Berbasis Data',
        'volume' => '3,000.0 Sekolah',
        'pelaksanaan' => 100.0,
        'anggaran_alokasi' => 1243148000,
        'anggaran_realisasi' => 1243144648,
        'kelengkapan' => array_fill(0, 12, true),
    ],
    [
        'urut' => 6,
        'kode' => 'KOMP 096',
        'tipe' => 'utama',
        'nomenklatur' => 'Pelaksanaan Program',
        'volume' => '1,000.0 Sekolah',
        'pelaksanaan' => 100.0,
        'anggaran_alokasi' => 1529077000,
        'anggaran_realisasi' => 1440828876,
        'kelengkapan' => array_fill(0, 12, true),
    ]
];

foreach ($children as $c) {
    $parent->children()->create($c);
}

echo "Seeded database with dummy Anggaran Data.";

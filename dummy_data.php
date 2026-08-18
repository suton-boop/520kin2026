<?php
use App\Models\Program;
use App\Models\Kegiatan;

$program = Program::create([
    'kode' => '7605.QDB.750',
    'nama' => 'Satuan PAUD, Dikdas, Dikmen dan Dikmas yang difasilitasi Penjaminan mutunya',
    'satuan' => 'Lembaga',
    'target' => 5689,
    'alokasi' => 1000000000 // Just a dummy allocation
]);

Kegiatan::create([
    'program_id' => $program->id,
    'nama' => 'Kegiatan Dummy 1',
    'volume_target' => 1000,
    'volume_realisasi' => 500,
    'anggaran_alokasi' => 500000000,
    'anggaran_realisasi' => 250000000,
    'pelaksanaan' => 'Sedang berjalan',
    'kelengkapan_bulanan' => ['Januari' => true, 'Februari' => false]
]);

Kegiatan::create([
    'program_id' => $program->id,
    'nama' => 'Kegiatan Dummy 2',
    'volume_target' => 1500,
    'volume_realisasi' => 1500,
    'anggaran_alokasi' => 500000000,
    'anggaran_realisasi' => 500000000,
    'pelaksanaan' => 'Selesai',
    'kelengkapan_bulanan' => ['Januari' => true, 'Februari' => true]
]);

echo "Dummy data created.\n";

<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\GugusMutu;
use App\Models\Period;
use App\Models\ReportSubmission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Data Gugus Mutu
        $gugusIT = GugusMutu::firstOrCreate(['name' => 'GM1- PAUD']);
        $gugusHR = GugusMutu::firstOrCreate(['name' => 'GM2-SD']);
        GugusMutu::firstOrCreate(['name' => 'GM3-SMP']);
        GugusMutu::firstOrCreate(['name' => 'GM4-SMA']);
        GugusMutu::firstOrCreate(['name' => 'GM5- UMUM']);

        // 2. Data Periode (hanya ada kolom month_year dan timestamps)
        $periodJan = Period::create(['month_year' => '01-2026']);
        $periodFeb = Period::create(['month_year' => '02-2026']);
        $periodMar = Period::create(['month_year' => '03-2026']);

        // 3. User tambahan (Anggota Gugus Mutu)
        $userIT = User::firstOrCreate(
            ['email' => 'user_it@user.com'],
            ['name' => 'Pegawai IT', 'password' => Hash::make('password'), 'gugus_mutu_id' => $gugusIT->id]
        );
        $userIT->assignRole('staff');

        $userHR = User::firstOrCreate(
            ['email' => 'user_hr@user.com'],
            ['name' => 'Pegawai HR', 'password' => Hash::make('password'), 'gugus_mutu_id' => $gugusHR->id]
        );
        $userHR->assignRole('staff');

        // Update Manager exist untuk memegang salah satu gugus (Opsional)
        $manager = User::where('email', 'manager@manager.com')->first();
        if ($manager) {
            $manager->update(['gugus_mutu_id' => $gugusIT->id]);
        }

        // 4. Data Dummy Report Submission
        // Kolom report_submissions = user_id, period_id, form_type, approval_status

        // Laporan Disetujui (Januari)
        ReportSubmission::create([
            'user_id' => $userIT->id,
            'period_id' => $periodJan->id,
            'form_type' => 'Rencana',
            'approval_status' => 'Approved',
            'created_at' => '2026-01-05 10:00:00',
        ]);

        // Laporan Disetujui (Februari)
        ReportSubmission::create([
            'user_id' => $userIT->id,
            'period_id' => $periodFeb->id,
            'form_type' => 'Laporan',
            'approval_status' => 'Approved',
            'created_at' => '2026-02-04 10:00:00',
        ]);

        // Laporan Menunggu Persetujuan (Maret) - IT
        ReportSubmission::create([
            'user_id' => $userIT->id,
            'period_id' => $periodMar->id,
            'form_type' => 'Rencana',
            'approval_status' => 'Pending',
            'created_at' => now(),
        ]);

        // Laporan Ditolak (Februari) - HR
        ReportSubmission::create([
            'user_id' => $userHR->id,
            'period_id' => $periodFeb->id,
            'form_type' => 'Laporan',
            'approval_status' => 'Rejected',
            'created_at' => '2026-02-06 10:00:00',
        ]);

        // Laporan Menunggu Persetujuan (Maret) - HR
        ReportSubmission::create([
            'user_id' => $userHR->id,
            'period_id' => $periodMar->id,
            'form_type' => 'Rencana',
            'approval_status' => 'Pending',
            'created_at' => now(),
        ]);
        // Dummy data Activities
        \App\Models\Activity::create([
            'report_submission_id' => 1,
            'kode_pmo' => 'PDM-01',
            'nama_kegiatan_turunan' => 'Sosialisasi Program Revitalisasi',
            'deskripsi_kegiatan' => 'Melakukan sosialisasi ke daerah terkait program revitalisasi sekolah',
            'hasil_kegiatan' => 'Dinas Pendidikan paham program revitalisasi',
            'nama_kegiatan_di_dipa' => 'Koordinasi dengan Pemda Dalam Peningkatan Akses Layanan Pendidikan Melalui Program Revitalisasi',
            'rencana_start_date' => '2026-04-01',
            'rencana_end_date' => '2026-06-30',
            'mekanisme_kegiatan' => 'Fullboard',
            'peserta_sasaran' => '1. Satuan Pendidikan\n2. Pemerintah Daerah',
            'tempat_kegiatan' => 'BPMP Provinsi Kalimantan Timur',
            'rincian_ketersediaan_anggaran' => '- 300 Peserta (Kab/Kota)\n- 2 Narasumber Pusat',
            'persiapan' => 'Belum',
            'status_akhir' => 'Belum',
        ]);

        \App\Models\Activity::create([
            'report_submission_id' => 1,
            'kode_pmo' => 'PDM-02',
            'nama_kegiatan_turunan' => 'Pendampingan Program Digitalisasi',
            'deskripsi_kegiatan' => 'Mendampingi daerah sasaran program digitalisasi',
            'hasil_kegiatan' => 'Daerah sasaran dapat menggunakan DAK Fisik IT',
            'nama_kegiatan_di_dipa' => 'Pendampingan Program Digitalisasi',
            'rencana_start_date' => '2026-08-01',
            'rencana_end_date' => '2026-11-30',
            'mekanisme_kegiatan' => 'Fullboard',
            'peserta_sasaran' => 'Pemerintah Daerah',
            'tempat_kegiatan' => 'BPMP Provinsi Kalimantan Timur',
            'rincian_ketersediaan_anggaran' => '- 22 Peserta (Kab/Kota)\n- 2 Narasumber Pusat',
            'persiapan' => 'Sudah',
            'realisasi_start_date' => '2026-08-15',
            'realisasi_end_date' => '2026-10-30',
            'laporan' => 'Selesai',
            'status_akhir' => 'Sudah',
        ]);
    }
}


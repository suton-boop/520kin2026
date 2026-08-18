<?php
$seederPath = 'database/seeders/RolePermissionSeeder.php';
$content = file_get_contents($seederPath);

$target = <<<'EOF'
        // Data Permissions berdasarkan screenshot
        $permissions = [
            ['name' => 'dashboard-read', 'label' => 'Lihat Dashboard', 'group_name' => 'Dashboard'],
            ['name' => 'event-manage', 'label' => 'Kelola Event', 'group_name' => 'Event & Peserta'],
            ['name' => 'participant-manage', 'label' => 'Kelola Peserta', 'group_name' => 'Event & Peserta'],
            ['name' => 'template-manage', 'label' => 'Kelola Template Sertifikat', 'group_name' => 'Sertifikat'],
            ['name' => 'certificate-generate', 'label' => 'Generate Sertifikat', 'group_name' => 'Sertifikat'],
            ['name' => 'certificate-send', 'label' => 'Kirim Sertifikat via Email', 'group_name' => 'Sertifikat'],
            ['name' => 'certificate-approve', 'label' => 'Persetujuan Sertifikat', 'group_name' => 'Sertifikat'],
            ['name' => 'tte-manage', 'label' => 'Kelola TTE (Tanda Tangan Elektronik)', 'group_name' => 'TTE & Monitoring'],
            ['name' => 'monitoring-read', 'label' => 'Lihat Monitoring', 'group_name' => 'TTE & Monitoring'],
            ['name' => 'audit-read', 'label' => 'Lihat Audit Trail', 'group_name' => 'TTE & Monitoring'],
            ['name' => 'user-manage', 'label' => 'Kelola User', 'group_name' => 'Manajemen Sistem'],
            ['name' => 'role-manage', 'label' => 'Kelola Role', 'group_name' => 'Manajemen Sistem'],
            ['name' => 'permission-manage', 'label' => 'Kelola Permission', 'group_name' => 'Manajemen Sistem'],
        ];
EOF;

$replacement = <<<'EOF'
        // Data Permissions berdasarkan sistem yang ada
        $permissions = [
            ['name' => 'dashboard-read', 'label' => 'Lihat Dashboard', 'group_name' => 'Dashboard'],
            ['name' => 'anggaran-manage', 'label' => 'Kelola Anggaran', 'group_name' => 'Anggaran'],
            ['name' => 'laporan-manage', 'label' => 'Kelola Laporan', 'group_name' => 'Laporan'],
            ['name' => 'aktivitas-manage', 'label' => 'Kelola Aktivitas', 'group_name' => 'Aktivitas'],
            ['name' => 'gugus-mutu-manage', 'label' => 'Kelola Gugus Mutu', 'group_name' => 'Gugus Mutu'],
            ['name' => 'user-manage', 'label' => 'Kelola User', 'group_name' => 'Manajemen Sistem'],
            ['name' => 'role-manage', 'label' => 'Kelola Role', 'group_name' => 'Manajemen Sistem'],
            ['name' => 'permission-manage', 'label' => 'Kelola Permission', 'group_name' => 'Manajemen Sistem'],
            ['name' => 'setting-manage', 'label' => 'Kelola Pengaturan', 'group_name' => 'Manajemen Sistem'],
        ];
EOF;

$content = str_replace(str_replace("\r\n", "\n", $target), str_replace("\r\n", "\n", $replacement), str_replace("\r\n", "\n", $content));
file_put_contents($seederPath, $content);
echo "Seeder updated successfully.\n";
?>

use Spatie\Permission\Models\Permission;
\ = Permission::whereIn('name', ['event-manage', 'participant-manage', 'template-manage', 'certificate-generate', 'certificate-send', 'certificate-approve', 'tte-manage', 'monitoring-read', 'audit-read'])->delete();
echo "Deleted " . \ . " permissions.\n";

// Add actual permissions
\ = [
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

foreach (\ as \) {
    Permission::firstOrCreate(
        ['name' => \['name']],
        ['label' => \['label'], 'group_name' => \['group_name']]
    );
}
echo "Ensured actual permissions exist.\n";

<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use App\Models\GugusMutu;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

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

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['name' => $perm['name']],
                ['label' => $perm['label'], 'group_name' => $perm['group_name']]
            );
        }

        // 1. Buat Role Baku
        $roleStaff = Role::firstOrCreate(['name' => 'staff']);
        $roleManager = Role::firstOrCreate(['name' => 'manager']);
        $roleAdmin = Role::firstOrCreate(['name' => 'admin']);

        // Beri semua permission ke admin
        $roleAdmin->syncPermissions(Permission::all());

        // 2. Gugus Mutu Ensure
        $gugusPaud = GugusMutu::firstOrCreate(['name' => 'GM1- PAUD']);
        $gugusSD = GugusMutu::firstOrCreate(['name' => 'GM2-SD']);
        
        // 3. User Dummies
        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            ['name' => 'Admin Pusat', 'password' => Hash::make('password')]
        );
        $admin->assignRole($roleAdmin);

        $managerPaud = User::firstOrCreate(
            ['email' => 'manager_paud@admin.com'],
            ['name' => 'Manajer PAUD', 'password' => Hash::make('password'), 'gugus_mutu_id' => $gugusPaud->id]
        );
        $managerPaud->assignRole($roleManager);

        $managerSD = User::firstOrCreate(
            ['email' => 'manager@manager.com'],
            ['name' => 'Manajer SD Lama', 'password' => Hash::make('password'), 'gugus_mutu_id' => $gugusSD->id]
        );
        $managerSD->assignRole($roleManager);

        $staffPaud = User::firstOrCreate(
            ['email' => 'staff_paud@admin.com'],
            ['name' => 'Staf Pelapor PAUD', 'password' => Hash::make('password'), 'gugus_mutu_id' => $gugusPaud->id]
        );
        $staffPaud->assignRole($roleStaff);

        $staffSD = User::firstOrCreate(
            ['email' => 'user@user.com'],
            ['name' => 'Staf Pelapor SD Lama', 'password' => Hash::make('password'), 'gugus_mutu_id' => $gugusSD->id]
        );
        $staffSD->assignRole($roleStaff);

        echo "Seeder Roles & Permissions berhasil dijalankan dilengkapi user Dummy terikat.\n";
    }
}

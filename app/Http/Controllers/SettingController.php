<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\AppSetting;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        $users = User::with('roles')->get();
        $roles = Role::with('permissions')->get();
        $settings = AppSetting::all();
        
        // Group permissions by group_name
        $permissions = Permission::all()->groupBy('group_name');
        
        // Buat default setting jika belum ada
        if ($settings->isEmpty()) {
            AppSetting::create(['key' => 'enable_feature_x', 'label' => 'Enable Feature X', 'value' => '1', 'type' => 'toggle']);
            $settings = AppSetting::all();
        }

        return Inertia::render('Settings/Index', [
            'users' => $users,
            'roles' => $roles,
            'app_settings' => $settings,
            'grouped_permissions' => $permissions,
        ]);
    }

    public function updateRoles(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'role' => 'required|string|exists:roles,name',
        ]);

        $user = User::findOrFail($request->user_id);
        $user->syncRoles([$request->role]);

        return redirect()->back()->with('success', 'Role berhasil diperbarui');
    }

    public function updateFeatures(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
        ]);

        foreach ($request->settings as $key => $value) {
            AppSetting::where('key', $key)->update(['value' => $value]);
        }

        return redirect()->back()->with('success', 'Fitur berhasil diperbarui');
    }

    public function updateRolePermissions(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'permissions' => 'array',
        ]);

        $role = Role::findOrFail($request->role_id);
        
        // Prevent modifying super-admin/admin role if strict rules apply
        // if ($role->name === 'admin') {
        //     return redirect()->back()->with('error', 'Role admin tidak dapat diubah.');
        // }

        $role->syncPermissions($request->permissions ?? []);

        return redirect()->back()->with('success', 'Permission Role berhasil diperbarui');
    }
}

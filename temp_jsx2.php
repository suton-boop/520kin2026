<?php
$content = <<<'JSX'
import React, { useState, useEffect } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { Cog6ToothIcon, ShieldCheckIcon, UserGroupIcon, KeyIcon, CheckCircleIcon } from '@heroicons/react/24/outline';

export default function Index({ users, roles, app_settings, grouped_permissions }) {
    const [activeTab, setActiveTab] = useState('kewenangan');
    
    // Form for Role Assignment
    const { data: roleData, setData: setRoleData, post: postRole, processing: roleProcessing } = useForm({
        user_id: '',
        role: ''
    });

    // Form for App Features
    const { data: featureData, setData: setFeatureData, post: postFeature, processing: featureProcessing } = useForm({
        settings: (app_settings || []).reduce((acc, setting) => ({ ...acc, [setting.key]: setting.value }), {})
    });

    // State for Permissions Management
    const [selectedRoleId, setSelectedRoleId] = useState('');
    const { data: permData, setData: setPermData, post: postPerm, processing: permProcessing } = useForm({
        role_id: '',
        permissions: []
    });

    useEffect(() => {
        if (selectedRoleId) {
            const role = roles.find(r => r.id === parseInt(selectedRoleId));
            if (role) {
                setPermData({
                    role_id: role.id,
                    permissions: role.permissions ? role.permissions.map(p => p.name) : []
                });
            }
        } else {
            setPermData({ role_id: '', permissions: [] });
        }
    }, [selectedRoleId]);

    const handleRoleChange = (userId, roleName) => {
        setRoleData({ user_id: userId, role: roleName });
    };

    const submitRoleUpdate = (e) => {
        e.preventDefault();
        postRole(route('settings.roles'));
    };

    const handleFeatureToggle = (key, value) => {
        setFeatureData('settings', { ...featureData.settings, [key]: value ? '1' : '0' });
    };

    const submitFeatureUpdate = (e) => {
        e.preventDefault();
        postFeature(route('settings.features'));
    };

    const togglePermission = (permName) => {
        if (!selectedRoleId) return;
        
        let newPerms = [...permData.permissions];
        if (newPerms.includes(permName)) {
            newPerms = newPerms.filter(p => p !== permName);
        } else {
            newPerms.push(permName);
        }
        setPermData('permissions', newPerms);
    };

    const submitPermissionsUpdate = (e) => {
        e.preventDefault();
        postPerm(route('settings.permissions'));
    };

    return (
        <AuthenticatedLayout>
            <Head title="Settings" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg flex">
                        
                        {/* Sidebar Tabs */}
                        <div className="w-64 bg-gray-50 border-r border-gray-200 p-4">
                            <h2 className="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                                <Cog6ToothIcon className="w-5 h-5 text-gray-500" /> Pengaturan
                            </h2>
                            <ul className="space-y-2">
                                <li>
                                    <button
                                        onClick={() => setActiveTab('kewenangan')}
                                        className={`w-full text-left px-4 py-3 rounded-xl flex items-center gap-3 font-semibold text-sm transition-all ${activeTab === 'kewenangan' ? 'bg-amber-400 text-blue-900 shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'}`}
                                    >
                                        <UserGroupIcon className="w-5 h-5" />
                                        Kewenangan
                                    </button>
                                </li>
                                <li>
                                    <button
                                        onClick={() => setActiveTab('permissions')}
                                        className={`w-full text-left px-4 py-3 rounded-xl flex items-center gap-3 font-semibold text-sm transition-all ${activeTab === 'permissions' ? 'bg-amber-400 text-blue-900 shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'}`}
                                    >
                                        <KeyIcon className="w-5 h-5" />
                                        Kelola Permission
                                    </button>
                                </li>
                                <li>
                                    <button
                                        onClick={() => setActiveTab('fitur')}
                                        className={`w-full text-left px-4 py-3 rounded-xl flex items-center gap-3 font-semibold text-sm transition-all ${activeTab === 'fitur' ? 'bg-amber-400 text-blue-900 shadow-md' : 'text-gray-600 hover:bg-gray-100 hover:text-gray-900'}`}
                                    >
                                        <ShieldCheckIcon className="w-5 h-5" />
                                        Fitur Aplikasi
                                    </button>
                                </li>
                            </ul>
                        </div>

                        {/* Content Area */}
                        <div className="flex-1 p-8 min-h-[500px]">
                            {activeTab === 'kewenangan' && (
                                <div className="animate-fade-in-up">
                                    <h3 className="text-xl font-bold text-gray-900 mb-6">Manajemen Role Pengguna</h3>
                                    
                                    <div className="bg-white border border-gray-200 rounded-2xl overflow-hidden shadow-sm">
                                        <table className="w-full text-sm text-left text-gray-600">
                                            <thead className="text-xs text-blue-900 uppercase bg-amber-400 font-black">
                                                <tr>
                                                    <th className="px-6 py-4">Pengguna</th>
                                                    <th className="px-6 py-4">Role Saat Ini</th>
                                                    <th className="px-6 py-4 text-center">Aksi</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                {(users || []).map((user) => (
                                                    <tr key={user.id} className="border-b border-gray-100 hover:bg-gray-50/50">
                                                        <td className="px-6 py-4">
                                                            <div className="font-bold text-gray-900">{user.name}</div>
                                                            <div className="text-xs text-gray-500">{user.email}</div>
                                                        </td>
                                                        <td className="px-6 py-4">
                                                            {user.roles.length > 0 ? (
                                                                <span className="bg-blue-100 text-blue-800 text-xs font-semibold px-2.5 py-0.5 rounded-full uppercase tracking-wider">
                                                                    {user.roles[0].name}
                                                                </span>
                                                            ) : (
                                                                <span className="text-gray-400 italic text-xs">Belum ada role</span>
                                                            )}
                                                        </td>
                                                        <td className="px-6 py-4 text-center">
                                                            <form onSubmit={submitRoleUpdate} className="flex items-center justify-center gap-2">
                                                                <select
                                                                    onChange={(e) => handleRoleChange(user.id, e.target.value)}
                                                                    className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block p-2"
                                                                    defaultValue={user.roles.length > 0 ? user.roles[0].name : ''}
                                                                >
                                                                    <option value="" disabled>Pilih Role...</option>
                                                                    {(roles || []).map(r => (
                                                                        <option key={r.id} value={r.name}>{r.name}</option>
                                                                    ))}
                                                                </select>
                                                                <button
                                                                    type="submit"
                                                                    disabled={roleProcessing || roleData.user_id !== user.id}
                                                                    className="px-4 py-2 bg-blue-900 text-amber-400 hover:bg-blue-800 text-xs font-black uppercase tracking-widest rounded-lg shadow-md disabled:opacity-50 transition-colors"
                                                                >
                                                                    Simpan
                                                                </button>
                                                            </form>
                                                        </td>
                                                    </tr>
                                                ))}
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            )}

                            {activeTab === 'permissions' && (
                                <div className="animate-fade-in-up">
                                    <div className="flex items-center justify-between mb-2">
                                        <h3 className="text-2xl font-bold text-blue-900">Kelola Permission</h3>
                                        <div className="bg-blue-600 text-white text-xs font-black px-4 py-1.5 rounded-full shadow-sm">
                                            Total: {Object.values(grouped_permissions || {}).flat().length} Permission
                                        </div>
                                    </div>
                                    <p className="text-sm text-gray-500 mb-6">Daftar hak akses sistem (dikelompokkan per modul fungsionalitas).</p>
                                    
                                    <div className="bg-blue-50/50 p-4 rounded-2xl border border-blue-100 mb-6 flex items-center justify-between">
                                        <div className="flex items-center gap-4">
                                            <label className="text-sm font-bold text-blue-900 uppercase tracking-wide">Pilih Role untuk dikelola:</label>
                                            <select
                                                value={selectedRoleId}
                                                onChange={(e) => setSelectedRoleId(e.target.value)}
                                                className="bg-white border-none shadow-sm text-blue-900 text-sm font-bold rounded-xl focus:ring-amber-500 block p-2.5 min-w-[200px]"
                                            >
                                                <option value="">-- Pilih Role --</option>
                                                {(roles || []).map(r => (
                                                    <option key={r.id} value={r.id}>{r.name.toUpperCase()}</option>
                                                ))}
                                            </select>
                                        </div>
                                        {selectedRoleId && (
                                            <button
                                                onClick={submitPermissionsUpdate}
                                                disabled={permProcessing}
                                                className="px-6 py-2.5 bg-amber-400 text-blue-900 hover:bg-amber-500 text-sm font-black uppercase tracking-widest rounded-xl shadow-lg disabled:opacity-50 transition-all"
                                            >
                                                Simpan Perubahan
                                            </button>
                                        )}
                                    </div>

                                    {!selectedRoleId ? (
                                        <div className="text-center py-12 bg-gray-50 border-2 border-dashed border-gray-200 rounded-2xl">
                                            <KeyIcon className="w-12 h-12 text-gray-300 mx-auto mb-3" />
                                            <p className="text-gray-500 font-semibold">Silakan pilih Role terlebih dahulu di atas untuk mengatur permission.</p>
                                        </div>
                                    ) : (
                                        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                            {Object.entries(grouped_permissions || {}).map(([groupName, perms]) => (
                                                <div key={groupName} className="bg-gray-50 border border-gray-200 rounded-2xl p-5 shadow-sm">
                                                    
                                                    <div className="flex items-center justify-between mb-4 border-b border-gray-200 pb-3">
                                                        <div className="flex items-center gap-3">
                                                            <div className="w-1.5 h-6 bg-blue-600 rounded-full"></div>
                                                            <h4 className="font-bold text-blue-900 text-base">{groupName}</h4>
                                                        </div>
                                                        <div className="bg-white border border-gray-200 text-gray-600 text-xs font-bold px-3 py-1 rounded-full shadow-sm">
                                                            {perms.length}
                                                        </div>
                                                    </div>

                                                    <div className="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                        {perms.map(perm => {
                                                            const isChecked = permData.permissions.includes(perm.name);
                                                            return (
                                                                <div 
                                                                    key={perm.id} 
                                                                    onClick={() => togglePermission(perm.name)}
                                                                    className={`flex items-start gap-3 p-3 rounded-xl border cursor-pointer transition-all ${isChecked ? 'bg-white border-green-400 shadow-sm' : 'bg-white border-gray-100 hover:border-blue-200'}`}
                                                                >
                                                                    <div className={`mt-0.5 flex-shrink-0 w-6 h-6 rounded-full flex items-center justify-center border transition-colors ${isChecked ? 'bg-green-50 border-green-500' : 'bg-gray-50 border-gray-200'}`}>
                                                                        {isChecked && <CheckCircleIcon className="w-4 h-4 text-green-600" />}
                                                                    </div>
                                                                    <div>
                                                                        <div className="text-sm font-bold text-gray-900 leading-tight">{perm.label || perm.name}</div>
                                                                        <div className="text-[10px] text-gray-500 font-mono mt-1">{perm.name}</div>
                                                                    </div>
                                                                </div>
                                                            );
                                                        })}
                                                    </div>

                                                </div>
                                            ))}
                                        </div>
                                    )}
                                </div>
                            )}

                            {activeTab === 'fitur' && (
                                <div className="animate-fade-in-up">
                                    <h3 className="text-xl font-bold text-gray-900 mb-6">Pengaturan Fitur Aplikasi</h3>
                                    
                                    <form onSubmit={submitFeatureUpdate}>
                                        <div className="space-y-4">
                                            {(app_settings || []).map((setting) => (
                                                <div key={setting.id} className="flex items-center justify-between p-5 bg-white border border-gray-200 rounded-2xl shadow-sm hover:border-amber-400 transition-colors">
                                                    <div>
                                                        <div className="font-bold text-gray-900">{setting.label}</div>
                                                        <div className="text-xs text-gray-500 font-mono mt-1">Key: {setting.key}</div>
                                                    </div>
                                                    
                                                    {setting.type === 'toggle' ? (
                                                        <label className="relative inline-flex items-center cursor-pointer">
                                                            <input 
                                                                type="checkbox" 
                                                                className="sr-only peer" 
                                                                checked={featureData.settings[setting.key] === '1'}
                                                                onChange={(e) => handleFeatureToggle(setting.key, e.target.checked)}
                                                            />
                                                            <div className="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-amber-400"></div>
                                                        </label>
                                                    ) : (
                                                        <input 
                                                            type="text" 
                                                            className="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-amber-500 focus:border-amber-500 block p-2.5" 
                                                            value={featureData.settings[setting.key] || ''}
                                                            onChange={(e) => setFeatureData('settings', { ...featureData.settings, [setting.key]: e.target.value })}
                                                        />
                                                    )}
                                                </div>
                                            ))}
                                        </div>
                                        
                                        <div className="mt-6 flex justify-end">
                                            <button
                                                type="submit"
                                                disabled={featureProcessing}
                                                className="px-6 py-3 bg-blue-900 text-amber-400 hover:bg-blue-800 text-sm font-black uppercase tracking-widest rounded-xl shadow-lg disabled:opacity-50 transition-all"
                                            >
                                                Simpan Pengaturan
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            )}
                        </div>

                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
JSX;
file_put_contents('resources/js/Pages/Settings/Index.jsx', $content);
echo "Settings updated successfully.";
?>

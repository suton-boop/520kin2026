import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm, router } from '@inertiajs/react';
import Modal from '@/Components/Modal';

export default function Index({ projects, gugusMutus }) {
    const [isCreateModalOpen, setIsCreateModalOpen] = useState(false);
    const [isEditModalOpen, setIsEditModalOpen] = useState(false);
    const [editingProject, setEditingProject] = useState(null);
    
    const { data, setData, post, put, processing, errors, reset } = useForm({
        name: '',
        start_date: '',
        description: '',
        gugus_mutu_id: ''
    });

    const submitCreate = (e) => {
        e.preventDefault();
        post(route('projects.store'), {
            onSuccess: () => {
                setIsCreateModalOpen(false);
                reset();
            }
        });
    };

    const openEditModal = (project) => {
        setEditingProject(project);
        setData({
            name: project.name,
            start_date: project.start_date || '',
            description: project.description || '',
                gugus_mutu_id: project.gugus_mutu_id || ''
        });
        setIsEditModalOpen(true);
    };

    const submitEdit = (e) => {
        e.preventDefault();
        put(route('projects.update', editingProject.id), {
            onSuccess: () => {
                setIsEditModalOpen(false);
                reset();
            }
        });
    };

    const deleteProject = (id) => {
        if(confirm('Apakah Anda yakin ingin menghapus proyek ini? Semua task dan jadwal akan ikut terhapus.')) {
            router.delete(route('projects.destroy', id));
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title="Manajemen Penjadwalan Proyek" />
            
            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="flex justify-between items-center mb-6">
                        <h2 className="text-2xl font-black text-blue-900 uppercase">Daftar Proyek / Kegiatan</h2>
                        <div className="flex gap-3">
                            <a 
                                href={route('projects.export')}
                                className="bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 px-4 rounded shadow inline-flex items-center gap-2"
                            >
                                <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                </svg>
                                Download Excel
                            </a>
                            <button 
                                onClick={() => {
                                    reset();
                                    setIsCreateModalOpen(true);
                                }}
                                className="bg-amber-400 hover:bg-amber-500 text-blue-900 font-bold py-2 px-4 rounded shadow"
                            >
                                + Buat Project Baru
                            </button>
                        </div>
                    </div>

                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <table className="w-full text-sm text-left">
                            <thead>
                                <tr>
                                    <th className="px-6 py-5 bg-blue-900 text-[10px] font-black text-blue-50 uppercase tracking-widest border-b border-blue-800">Devisi / GM</th>
                                    <th className="px-6 py-5 bg-blue-900 text-[10px] font-black text-blue-50 uppercase tracking-widest border-b border-blue-800">Nama Proyek</th>
                                    <th className="px-6 py-5 bg-blue-900 text-[10px] font-black text-blue-50 uppercase tracking-widest border-b border-blue-800">Tanggal Mulai</th>
                                    <th className="px-6 py-5 bg-blue-900 text-[10px] font-black text-blue-50 uppercase tracking-widest border-b border-blue-800 max-w-xs">Deskripsi</th>
                                    <th className="px-6 py-5 bg-blue-900 text-[10px] font-black text-blue-50 uppercase tracking-widest border-b border-blue-800 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {projects.map(project => (
                                    <tr key={project.id} className="border-b hover:bg-gray-50">
                                        <td className="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-900">{project.gugus_mutu ? project.gugus_mutu.name : '-'}</td>
                                            <td className="px-6 py-4 font-bold">{project.name}</td>
                                        <td className="px-6 py-4">{project.start_date || '-'}</td>
                                        <td className="px-6 py-4">{project.description || '-'}</td>
                                        <td className="px-6 py-4 text-right space-x-3 flex justify-end">
                                            <Link 
                                                href={route('projects.show', project.id)} 
                                                className="text-blue-600 hover:text-blue-800 font-bold bg-blue-50 px-3 py-1 rounded"
                                            >
                                                Buka Perencanaan
                                            </Link>
                                            <button 
                                                onClick={() => openEditModal(project)}
                                                className="text-amber-600 hover:text-amber-800 font-bold bg-amber-50 px-3 py-1 rounded"
                                            >
                                                Edit
                                            </button>
                                            <button 
                                                onClick={() => deleteProject(project.id)}
                                                className="text-red-600 hover:text-red-800 font-bold bg-red-50 px-3 py-1 rounded"
                                            >
                                                Hapus
                                            </button>
                                        </td>
                                    </tr>
                                ))}
                                {projects.length === 0 && (
                                    <tr>
                                        <td colSpan="4" className="px-6 py-8 text-center text-gray-500">
                                            Belum ada data proyek.
                                        </td>
                                    </tr>
                                )}
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {/* Create Modal */}
            <Modal show={isCreateModalOpen} onClose={() => setIsCreateModalOpen(false)}>
                <div className="p-6">
                    <h2 className="text-lg font-bold text-gray-900 mb-4">Buat Proyek Baru</h2>
                    <form onSubmit={submitCreate}>
                        <div className="mb-4">
                            <label className="block text-sm font-medium text-gray-700">Nama Proyek / Scope</label>
                            <input 
                                type="text" 
                                value={data.name} 
                                onChange={e => setData('name', e.target.value)} 
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                required
                            />
                            {errors.name && <div className="text-red-500 text-xs mt-1">{errors.name}</div>}
                        </div>
                        <div className="mb-4">
                            <label className="block text-sm font-medium text-gray-700">Tanggal Mulai (Project Start Date)</label>
                            <input 
                                type="date" 
                                value={data.start_date} 
                                onChange={e => setData('start_date', e.target.value)} 
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            />
                        </div>
                        <div className="mb-4">
                            <label className="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea 
                                value={data.description} 
                                onChange={e => setData('description', e.target.value)} 
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                rows="3"
                            ></textarea>
                        </div>
                        <div className="mb-4">
                            <label className="block text-sm font-medium text-gray-700">Devisi / Gugus Mutu (Opsional)</label>
                            <select
                                value={data.gugus_mutu_id}
                                onChange={e => setData('gugus_mutu_id', e.target.value)}
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            >
                                <option value="">-- Pilih Devisi (Boleh Kosong) --</option>
                                {gugusMutus && gugusMutus.map(gm => (
                                    <option key={gm.id} value={gm.id}>{gm.name}</option>
                                ))}
                            </select>
                            {errors.gugus_mutu_id && <div className="text-red-500 text-xs mt-1">{errors.gugus_mutu_id}</div>}
                        </div>
                        <div className="flex justify-end space-x-2">
                            <button type="button" onClick={() => setIsCreateModalOpen(false)} className="px-4 py-2 text-gray-600 bg-gray-100 rounded">Batal</button>
                            <button type="submit" disabled={processing} className="px-4 py-2 bg-blue-600 text-white rounded font-bold hover:bg-blue-700">Simpan</button>
                        </div>
                    </form>
                </div>
            </Modal>

            {/* Edit Modal */}
            <Modal show={isEditModalOpen} onClose={() => setIsEditModalOpen(false)}>
                <div className="p-6">
                    <h2 className="text-lg font-bold text-gray-900 mb-4">Edit Project</h2>
                    <form onSubmit={submitEdit}>
                        <div className="mb-4">
                            <label className="block text-sm font-medium text-gray-700">Nama Proyek / Scope</label>
                            <input 
                                type="text" 
                                value={data.name} 
                                onChange={e => setData('name', e.target.value)} 
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                required
                            />
                            {errors.name && <div className="text-red-500 text-xs mt-1">{errors.name}</div>}
                        </div>
                        <div className="mb-4">
                            <label className="block text-sm font-medium text-gray-700">Tanggal Mulai (Project Start Date)</label>
                            <input 
                                type="date" 
                                value={data.start_date} 
                                onChange={e => setData('start_date', e.target.value)} 
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            />
                        </div>
                        <div className="mb-4">
                            <label className="block text-sm font-medium text-gray-700">Deskripsi</label>
                            <textarea 
                                value={data.description} 
                                onChange={e => setData('description', e.target.value)} 
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                                rows="3"
                            ></textarea>
                        </div>
                        <div className="mb-4">
                            <label className="block text-sm font-medium text-gray-700">Devisi / Gugus Mutu (Opsional)</label>
                            <select
                                value={data.gugus_mutu_id}
                                onChange={e => setData('gugus_mutu_id', e.target.value)}
                                className="mt-1 block w-full rounded-md border-gray-300 shadow-sm"
                            >
                                <option value="">-- Pilih Devisi (Boleh Kosong) --</option>
                                {gugusMutus && gugusMutus.map(gm => (
                                    <option key={gm.id} value={gm.id}>{gm.name}</option>
                                ))}
                            </select>
                            {errors.gugus_mutu_id && <div className="text-red-500 text-xs mt-1">{errors.gugus_mutu_id}</div>}
                        </div>
                        <div className="flex justify-end space-x-2">
                            <button type="button" onClick={() => setIsEditModalOpen(false)} className="px-4 py-2 text-gray-600 bg-gray-100 rounded">Batal</button>
                            <button type="submit" disabled={processing} className="px-4 py-2 bg-blue-600 text-white rounded font-bold hover:bg-blue-700">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </Modal>
        </AuthenticatedLayout>
    );
}




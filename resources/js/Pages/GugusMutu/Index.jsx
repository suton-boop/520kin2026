import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Pagination from '@/Components/Pagination';
import { Head, useForm, usePage } from '@inertiajs/react';
import { useState } from 'react';
import { PlusIcon, PencilSquareIcon, TrashIcon, ExclamationTriangleIcon } from '@heroicons/react/24/solid';

export default function Index({ gugusMutus }) {
    const { auth } = usePage().props;
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingId, setEditingId] = useState(null);

    const { data, setData, post, put, delete: destroy, processing, reset, errors } = useForm({
        name: '',
        allow_import: false,
    });

    const openModal = (gm = null) => {
        if (gm) {
            setEditingId(gm.id);
            setData({
                name: gm.name,
                allow_import: Boolean(gm.allow_import),
            });
        } else {
            setEditingId(null);
            reset();
        }
        setIsModalOpen(true);
    };

    const closeModal = () => {
        setIsModalOpen(false);
        reset();
        setEditingId(null);
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (editingId) {
            put(route('gugus-mutus.update', editingId), {
                onSuccess: () => closeModal(),
            });
        } else {
            post(route('gugus-mutus.store'), {
                onSuccess: () => closeModal(),
            });
        }
    };

    const handleDelete = (id) => {
        if (confirm('Apakah Anda yakin ingin menghapus divisi ini? Semua data terkait yang menautkan divisi ini mungkin terpengaruh.')) {
            destroy(route('gugus-mutus.destroy', id));
        }
    };

    return (
        <AuthenticatedLayout>
            <Head title="Master Devisi (GM)" />

            <div className="max-w-7xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
                <div className="flex justify-between items-center mb-8">
                    <div>
                        <h1 className="text-3xl font-black text-blue-950 uppercase tracking-tighter">Master Devisi (GM)</h1>
                        <p className="text-blue-900/60 font-medium mt-1">Kelola daftar Devisi atau Gugus Mutu di tahun berjalan.</p>
                    </div>
                    <button
                        onClick={() => openModal()}
                        className="bg-amber-500 hover:bg-amber-600 text-blue-950 px-5 py-2.5 rounded-xl font-black flex items-center shadow-lg transition-transform active:scale-95"
                    >
                        <PlusIcon className="h-5 w-5 mr-2" />
                        TAMBAH DEVISI
                    </button>
                </div>

                <div className="bg-white rounded-3xl shadow-xl border border-amber-500/20 overflow-hidden">
                    <div className="overflow-x-auto">
                        <table className="w-full text-left border-collapse">
                            <thead>
                                <tr className="bg-blue-950 text-white">
                                    <th className="p-4 font-black uppercase text-xs tracking-widest rounded-tl-2xl">Nama Devisi</th>
                                    <th className="p-4 font-black uppercase text-xs tracking-widest text-center">Jml Pengguna</th>
                                    <th className="p-4 font-black uppercase text-xs tracking-widest text-center">Izin Import</th>
                                    <th className="p-4 font-black uppercase text-xs tracking-widest text-right rounded-tr-2xl">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                {(gugusMutus.data || gugusMutus).length === 0 ? (
                                    <tr>
                                        <td colSpan="4" className="p-8 text-center text-blue-900/50 font-medium">
                                            Belum ada data divisi.
                                        </td>
                                    </tr>
                                ) : (
                                    (gugusMutus.data || gugusMutus).map((gm) => (
                                        <tr key={gm.id} className="border-b border-gray-100 hover:bg-gray-50/50 transition-colors">
                                            <td className="p-4 font-bold text-blue-900">{gm.name}</td>
                                            <td className="p-4 text-center text-gray-500 font-medium">
                                                <span className="bg-blue-100 text-blue-800 py-1 px-3 rounded-full text-xs font-bold">
                                                    {gm.users_count || 0} Pengguna
                                                </span>
                                            </td>
                                            <td className="p-4 text-center">
                                                <span className={`py-1 px-3 rounded-full text-xs font-bold ${gm.allow_import ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`}>
                                                    {gm.allow_import ? 'Diizinkan' : 'Dilarang'}
                                                </span>
                                            </td>
                                            <td className="p-4 text-right">
                                                <div className="flex justify-end gap-2">
                                                    <button
                                                        onClick={() => openModal(gm)}
                                                        className="p-2 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors"
                                                        title="Edit Divisi"
                                                    >
                                                        <PencilSquareIcon className="h-5 w-5" />
                                                    </button>
                                                    <button
                                                        onClick={() => handleDelete(gm.id)}
                                                        className="p-2 text-red-600 hover:bg-red-50 rounded-lg transition-colors"
                                                        title="Hapus Divisi"
                                                    >
                                                        <TrashIcon className="h-5 w-5" />
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    ))
                                )}
                            </tbody>
                        </table>
                        </div>
                        {gugusMutus && gugusMutus.links && <Pagination links={gugusMutus.links} />}
                </div>
            </div>

            {/* Modal Form */}
            {isModalOpen && (
                <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-blue-950/40 backdrop-blur-sm">
                    <div className="bg-white rounded-3xl shadow-2xl w-full max-w-md overflow-hidden animate-in fade-in zoom-in duration-200">
                        <div className="p-6 border-b border-gray-100">
                            <h3 className="text-xl font-black text-blue-950 uppercase tracking-tight">
                                {editingId ? 'Edit Devisi' : 'Tambah Devisi Baru'}
                            </h3>
                        </div>
                        
                        <form onSubmit={handleSubmit} className="p-6 space-y-6">
                            <div>
                                <label className="block text-sm font-bold text-blue-900 mb-2 uppercase tracking-wide">
                                    Nama Devisi / Gugus Mutu
                                </label>
                                <input
                                    type="text"
                                    value={data.name}
                                    onChange={e => setData('name', e.target.value)}
                                    className="w-full border-gray-200 rounded-xl focus:ring-amber-500 focus:border-amber-500 transition-colors"
                                    placeholder="Contoh: Divisi IT"
                                    required
                                />
                                {errors.name && <p className="text-red-500 text-xs mt-2 font-medium">{errors.name}</p>}
                            </div>

                            <div className="flex items-center p-4 bg-gray-50 rounded-xl border border-gray-100">
                                <input
                                    id="allow_import"
                                    type="checkbox"
                                    checked={data.allow_import}
                                    onChange={e => setData('allow_import', e.target.checked)}
                                    className="w-5 h-5 text-amber-500 bg-white border-gray-300 rounded focus:ring-amber-500 focus:ring-2"
                                />
                                <label htmlFor="allow_import" className="ml-3 text-sm font-bold text-blue-900 cursor-pointer">
                                    Izinkan Import Laporan?
                                </label>
                            </div>
                            <p className="text-xs text-gray-500 px-1">
                                Jika diizinkan, anggota divisi ini bisa melakukan import massal kegiatan Excel.
                            </p>

                            <div className="flex justify-end gap-3 pt-4 border-t border-gray-100">
                                <button
                                    type="button"
                                    onClick={closeModal}
                                    className="px-5 py-2.5 text-sm font-bold text-gray-600 hover:bg-gray-100 rounded-xl transition-colors"
                                >
                                    BATAL
                                </button>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="bg-blue-950 hover:bg-blue-900 text-white px-6 py-2.5 rounded-xl text-sm font-black tracking-widest shadow-lg transition-transform active:scale-95 disabled:opacity-50"
                                >
                                    {processing ? 'MENYIMPAN...' : 'SIMPAN'}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm, usePage, router, Link } from '@inertiajs/react';
import { ChevronRightIcon, ChevronDownIcon, PlusCircleIcon, MinusCircleIcon, PencilSquareIcon, TrashIcon, PlusIcon, WalletIcon, DocumentTextIcon, XCircleIcon } from '@heroicons/react/24/solid';
import React, { useState } from 'react';
import { PieChart, Pie, Cell, Tooltip, ResponsiveContainer } from 'recharts';
import Modal from '@/Components/Modal';
import InputLabel from '@/Components/InputLabel';
import TextInput from '@/Components/TextInput';

const formatRp = (number) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(number);
};

const formatShortRp = (number) => {
    if (number >= 1000000000) {
        return (number / 1000000000).toFixed(2).replace('.', ',') + ' M';
    }
    if (number >= 1000000) {
        return (number / 1000000).toFixed(2).replace('.', ',') + ' jt';
    }
    return formatRp(number);
};

export default function Index({ auth, anggaranData, isAdmin }) {
    const [expandedRows, setExpandedRows] = useState({});
    
    // CRUD State
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [isDeleteModalOpen, setIsDeleteModalOpen] = useState(false);
    const [modalMode, setModalMode] = useState('add'); 
    const [selectedId, setSelectedId] = useState(null);

    const { data, setData, post, put, delete: destroy, processing, reset } = useForm({
        parent_id: '',
        urut: '',
        kode: '',
        tipe: '',
        nomenklatur: '',
        satuan: '',
        volume: '',
        volume_realisasi: '',
        pelaksanaan: '',
        anggaran_alokasi: '',
        anggaran_realisasi: '',
        kelengkapan: Array(12).fill(false),
    });

    const toggleRow = (id) => {
        setExpandedRows(prev => ({
            ...prev,
            [id]: !prev[id]
        }));
    };

    const openAddModal = (parentId = null) => {
        reset();
        setModalMode('add');
        setData({
            parent_id: parentId || '',
            urut: '',
            kode: '',
            tipe: '',
            nomenklatur: '',
            satuan: '',
            volume: '',
            volume_realisasi: '',
            pelaksanaan: '',
            anggaran_alokasi: '',
            anggaran_realisasi: '',
            kelengkapan: Array(12).fill(false),
        });
        setIsModalOpen(true);
    };

    const openEditModal = (item) => {
        reset();
        setModalMode('edit');
        setSelectedId(item.id);
        setData({
            parent_id: item.parent_id || '',
            urut: item.urut || '',
            kode: item.kode || '',
            tipe: item.tipe || '',
            nomenklatur: item.nomenklatur || '',
            satuan: item.satuan || '',
            volume: item.volume || '',
            volume_realisasi: item.volume_realisasi || '',
            pelaksanaan: item.pelaksanaan || '',
            anggaran_alokasi: item.anggaran_alokasi || '',
            anggaran_realisasi: item.anggaran_realisasi || '',
            kelengkapan: item.kelengkapan || Array(12).fill(false),
        });
        setIsModalOpen(true);
    };

    const openDeleteModal = (id) => {
        setSelectedId(id);
        setIsDeleteModalOpen(true);
    };

    const closeModal = () => {
        setIsModalOpen(false);
        setIsDeleteModalOpen(false);
        reset();
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        if (modalMode === 'add') {
            post(route('anggaran.store'), {
                onSuccess: () => {
                    if (data.parent_id) {
                        setExpandedRows(prev => ({ ...prev, [data.parent_id]: true }));
                    }
                    closeModal();
                },
            });
        } else {
            put(route('anggaran.update', selectedId), {
                onSuccess: () => closeModal(),
            });
        }
    };

    const handleDelete = (e) => {
        e.preventDefault();
        destroy(route('anggaran.destroy', selectedId), {
            onSuccess: () => closeModal(),
        });
    };

    const handleKelengkapanChange = (index) => {
        const newKelengkapan = [...data.kelengkapan];
        newKelengkapan[index] = !newKelengkapan[index];
        setData('kelengkapan', newKelengkapan);
    };

    const totalAlokasi = anggaranData ? anggaranData.reduce((acc, curr) => acc + Number(curr.anggaran_alokasi), 0) : 0;
    const totalRealisasi = anggaranData ? anggaranData.reduce((acc, curr) => acc + Number(curr.anggaran_realisasi), 0) : 0;
    const sisaAnggaran = totalAlokasi - totalRealisasi;
    
    const pieData = [
        { name: 'REALISASI', value: totalRealisasi, color: '#3B82F6' },
        { name: 'SISA ALOKASI', value: sisaAnggaran > 0 ? sisaAnggaran : 0, color: '#DBEAFE' },
    ];
    
    const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agt', 'Sep', 'Okt', 'Nov', 'Des'];

    return (
        <AuthenticatedLayout>
            <Head title="Modul Anggaran - Dashboardkin 520" />

            <main className="flex-1 max-w-screen-2xl mx-auto p-4 md:p-10 space-y-12 w-full">
                
                <div className="flex justify-between items-end mb-4">
                    <div>
                        <h2 className="text-4xl md:text-5xl font-black text-blue-900 uppercase tracking-tighter italic decoration-amber-400 decoration-8 underline-offset-8 underline">Manajemen Anggaran</h2>
                        <p className="mt-8 text-gray-400 font-bold uppercase tracking-[0.3em] text-xs">Integrasi RKKL 2026 & Bull; Monitoring Realitas Fisik dan Keuangan</p>
                    </div>
                    <div className="flex items-center gap-4">
                        <a 
                            href={route('anggaran.export')}
                            className="inline-flex items-center gap-2 px-8 py-4 bg-emerald-600 text-white rounded-[24px] font-black text-[12px] uppercase tracking-widest hover:bg-emerald-700 transition-transform hover:-translate-y-1 shadow-[0_20px_40px_-5px_rgba(5,150,105,0.4)]"
                        >
                            <svg className="w-5 h-5 text-emerald-100" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                            </svg>
                            Download Excel
                        </a>
                        {isAdmin && (
                            <button 
                                onClick={() => openAddModal()}
                                className="bg-blue-900 text-white px-10 py-5 rounded-[24px] font-black text-[12px] uppercase tracking-widest shadow-[0_20px_40px_-5px_rgba(30,58,138,0.4)] flex items-center hover:scale-105 transition-transform"
                            >
                                <PlusIcon className="w-5 h-5 mr-3 text-amber-400" /> Tambah RO Utama
                            </button>
                        )}
                    </div>
                </div>

                <div className="grid grid-cols-1 lg:grid-cols-4 gap-12">
                    
                    {/* Sidebar Stats */}
                    <div className="space-y-8">
                        <div className="bg-blue-900 p-10 rounded-[40px] text-white shadow-2xl relative overflow-hidden group">
                            <WalletIcon className="absolute -right-8 -bottom-8 h-40 w-40 text-white/5 transform -rotate-12" />
                            <p className="text-[11px] font-black tracking-widest uppercase opacity-60 mb-4">TOTAL ALOKASI RKKL</p>
                            <p className="text-2xl lg:text-3xl font-black italic mb-10 tracking-tighter">{formatRp(totalAlokasi)}</p>
                            
                            <div className="bg-white/10 p-6 rounded-[24px] backdrop-blur-md border border-white/10">
                                <p className="text-[10px] font-black tracking-widest uppercase opacity-60 mb-2">Penyerapan Aktif</p>
                                <div className="flex items-center justify-between mb-4">
                                    <p className="text-lg font-black">{formatRp(totalRealisasi)}</p>
                                    <span className="text-amber-400 font-black text-sm">{totalAlokasi > 0 ? ((totalRealisasi / totalAlokasi) * 100).toFixed(1) : 0}%</span>
                                </div>
                                <div className="h-2 w-full bg-white/20 rounded-full overflow-hidden">
                                     <div className="h-full bg-amber-400 shadow-[0_0_10px_rgba(251,191,36,0.8)]" style={{ width: (totalAlokasi > 0 ? ((totalRealisasi / totalAlokasi) * 100) : 0) + '%' }}></div>
                                </div>
                            </div>
                        </div>

                        <div className="bg-white p-10 rounded-[40px] shadow-sm border border-gray-100 flex flex-col items-center">
                            <p className="text-[11px] font-black text-gray-400 uppercase tracking-widest mb-8 text-center italic">Proporsi Dana Terserap</p>
                            <div className="h-48 w-full">
                                <ResponsiveContainer width="100%" height="100%">
                                    <PieChart>
                                        <Pie
                                            data={pieData}
                                            innerRadius={50}
                                            outerRadius={75}
                                            paddingAngle={8}
                                            dataKey="value"
                                            stroke="none"
                                        >
                                            {pieData.map((entry, index) => (
                                                <Cell key={`cell-${index}`} fill={entry.color} />
                                            ))}
                                        </Pie>
                                        <Tooltip formatter={(v) => formatRp(v)} contentStyle={{ borderRadius: '24px', border: 'none', background: '#FFF font-weight: bold', boxShadow: '0 20px 40px -10px rgba(0,0,0,0.1)' }} />
                                    </PieChart>
                                </ResponsiveContainer>
                            </div>
                            <div className="grid grid-cols-2 gap-4 w-full mt-6">
                                <div className="text-center">
                                    <div className="w-3 h-3 bg-blue-600 rounded-full mx-auto mb-2 shadow-lg"></div>
                                    <p className="text-[9px] font-black text-gray-400">REALISASI</p>
                                </div>
                                <div className="text-center">
                                    <div className="w-3 h-3 bg-blue-100 rounded-full mx-auto mb-2 shadow-lg"></div>
                                    <p className="text-[9px] font-black text-gray-400">SISA</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {/* Main Content Table Area */}
                    <div className="lg:col-span-3">
                        <div className="bg-white rounded-xl shadow-xl border border-blue-50 overflow-hidden">
                            <div className="overflow-x-auto">
                                <table className="w-full text-left border-collapse min-w-max text-sm">
                                    <thead>
                                        <tr className="bg-blue-50/50 text-blue-900 font-bold border-b border-gray-100">
                                            <th className="px-2 py-3 border-r border-gray-100 text-center w-8" rowSpan={2}></th>
                                            <th className="px-4 py-3 border-r border-gray-100 min-w-[300px]" rowSpan={2}>NOMENKLATUR</th>
                                            <th className="px-4 py-3 border-r border-gray-100 text-center" rowSpan={2}>Satuan</th>
                                            <th className="px-4 py-3 border-r border-gray-100 text-center" colSpan={2}>Volume</th>
                                            <th className="px-4 py-3 border-r border-gray-100 text-center" colSpan={3}>Anggaran</th>
                                            <th className="px-4 py-3 border-r border-gray-100 text-center" rowSpan={2}>Pelaksanaan</th>
                                            <th className="px-4 py-3 text-center" colSpan={12}>Kelengkapan</th>
                                        </tr>
                                        <tr className="bg-blue-50/50 text-blue-900 font-bold border-b border-gray-100 text-xs text-center">
                                            <th className="px-2 py-2 border-r border-gray-100">Target</th>
                                            <th className="px-2 py-2 border-r border-gray-100">Realisasi</th>
                                            <th className="px-2 py-2 border-r border-gray-100">Alokasi</th>
                                            <th className="px-2 py-2 border-r border-gray-100">Realisasi</th>
                                            <th className="px-2 py-2 border-r border-gray-100">%</th>
                                            {months.map(m => (
                                                <th key={m} className="px-1 py-2 font-normal text-gray-500 w-8">{m}</th>
                                            ))}
                                        </tr>
                                    </thead>
                                    <tbody className="text-gray-700">
                                        {anggaranData?.map((parent, parentIdx) => (
                                            <React.Fragment key={parent.id}>
                                                {/* Parent Row */}
                                                <tr className={`border-b border-amber-100 group ${!parent.is_active ? 'opacity-50 grayscale' : 'bg-[#fffcf5]'}`}>
                                                    <td className="px-2 py-3 border-r border-gray-100 text-center relative">
                                                        <div className={`absolute left-0 top-0 bottom-0 w-1 ${parent.is_active ? 'bg-amber-400' : 'bg-gray-300'}`}></div>
                                                        <button 
                                                            onClick={() => toggleRow(parent.id)}
                                                            className="text-blue-600 hover:text-blue-800 focus:outline-none"
                                                        >
                                                            {expandedRows[parent.id] ? (
                                                                <MinusCircleIcon className="w-5 h-5" />
                                                            ) : (
                                                                <PlusCircleIcon className="w-5 h-5 text-blue-500 bg-white rounded-full border-none" />
                                                            )}
                                                        </button>
                                                    </td>
                                                    <td className="px-4 py-3 border-r border-gray-100">
                                                        <div className="flex justify-between items-start group/actions relative">
                                                            <div>
                                                                <p className="font-bold text-gray-900">{parent.kode}</p>
                                                                <p className="text-sm">{parent.nomenklatur}</p>
                                                            </div>
                                                            {isAdmin && (
                                                                <div className="absolute right-0 top-0 flex space-x-1 opacity-0 group-hover/actions:opacity-100 transition-opacity bg-[#fffcf5] pl-2">
                                                                    <button onClick={() => openAddModal(parent.id)} className="text-green-600 hover:text-green-800" title="Tambah Sub"><PlusIcon className="w-4 h-4"/></button>
                                                                    <button onClick={() => openEditModal(parent)} className="text-amber-600 hover:text-amber-800"><PencilSquareIcon className="w-4 h-4"/></button>
                                                                    <button onClick={() => openDeleteModal(parent.id)} className="text-red-600 hover:text-red-800"><TrashIcon className="w-4 h-4"/></button>
                                                                </div>
                                                            )}
                                                        </div>
                                                    </td>
                                                    <td className="px-4 py-3 border-r border-gray-100 text-center font-semibold">{parent.satuan}</td>
                                                    <td className="px-4 py-3 border-r border-gray-100 text-right font-bold">{parent.volume}</td>
                                                    <td className="px-4 py-3 border-r border-gray-100 text-right font-bold">{parent.volume_realisasi}</td>
                                                    <td className="px-4 py-3 border-r border-gray-100 text-right font-bold text-xs">{formatShortRp(parent.anggaran_alokasi || 0)}</td>
                                                      <td className="px-4 py-3 border-r border-gray-100 text-right font-bold text-xs">{formatShortRp(parent.anggaran_realisasi || 0)}</td>
                                                      <td className="px-4 py-3 border-r border-gray-100 text-center font-bold text-xs">{Number(parent.anggaran_persen || 0).toFixed(1)}%</td>
                                                    <td className="px-4 py-3 border-r border-gray-100 text-center"></td>
                                                    <td className="px-4 py-3 text-center" colSpan={12}></td>
                                                </tr>

                                                {/* Child Rows */}
                                                {expandedRows[parent.id] && parent.children?.map((child, childIdx) => (
                                                    <tr key={child.id} className={`border-b border-gray-50 transition-all group/child hover:bg-blue-50/10 ${!child.is_active ? 'opacity-50 grayscale' : ''}`}>
                                                        <td className="px-2 py-3 border-r border-gray-100 text-center"></td>
                                                        <td className="px-4 py-3 border-r border-gray-100 pl-8">
                                                            <div className="flex justify-between items-start relative group/actions">
                                                                <div className="flex">
                                                                    <span className="text-gray-400 mr-2 w-4 text-right">{childIdx + 1}</span>
                                                                    <div>
                                                                        <p className="font-semibold text-gray-800">{child.kode} {child.nomenklatur}</p>
                                                                    </div>
                                                                </div>
                                                                {isAdmin && (
                                                                    <div className="absolute right-0 top-0 flex space-x-1 opacity-0 group-hover/actions:opacity-100 transition-opacity bg-white pl-2">
                                                                        <button onClick={() => openEditModal(child)} className="text-amber-500 hover:text-amber-700"><PencilSquareIcon className="w-4 h-4"/></button>
                                                                        <button onClick={() => openDeleteModal(child.id)} className="text-red-500 hover:text-red-700"><TrashIcon className="w-4 h-4"/></button>
                                                                    </div>
                                                                )}
                                                            </div>
                                                        </td>
                                                        <td className="px-4 py-3 border-r border-gray-100 text-center text-gray-600">{child.satuan}</td>
                                                        <td className="px-2 py-3 border-r border-gray-100 text-right text-gray-600">{child.volume}</td>
                                                        <td className="px-2 py-3 border-r border-gray-100 text-right text-gray-600">{child.volume_realisasi}</td>
                                                        <td className="px-2 py-3 border-r border-gray-100 text-right text-gray-600 whitespace-nowrap">{formatShortRp(child.anggaran_alokasi)}</td>
                                                        <td className="px-2 py-3 border-r border-gray-100 text-right text-gray-600 whitespace-nowrap">{formatShortRp(child.anggaran_realisasi)}</td>
                                                        <td className="px-2 py-3 border-r border-gray-100 text-center text-gray-600">{Number(child.anggaran_persen).toFixed(1)}%</td>
                                                        <td className="px-4 py-3 border-r border-gray-100 min-w-[120px]">
                                                            <div className="flex items-center space-x-2">
                                                                <div className="w-full bg-gray-200 rounded-full h-1.5 overflow-hidden">
                                                                    <div className={`h-1.5 rounded-full ${child.pelaksanaan >= 100 ? 'bg-emerald-500' : child.pelaksanaan > 50 ? 'bg-amber-500' : 'bg-red-500'}`} style={{ width: `${Math.min(100, Math.max(0, child.pelaksanaan))}%` }}></div>
                                                                </div>
                                                                <span className="text-[10px] text-gray-500 whitespace-nowrap w-8 text-right">{child.pelaksanaan}%</span>
                                                            </div>
                                                        </td>
                                                        {months.map((m, idx) => (
                                                            <td key={idx} className="px-1 py-3 text-center border-r border-gray-50">
                                                                {child.kelengkapan && child.kelengkapan[idx] ? (
                                                                    <PlusCircleIcon className="w-4 h-4 text-emerald-400 mx-auto" />
                                                                ) : (
                                                                    <XCircleIcon className="w-4 h-4 text-red-400 mx-auto opacity-50" />
                                                                )}
                                                            </td>
                                                        ))}
                                                    </tr>
                                                ))}
                                            </React.Fragment>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </main>

            <Modal show={isModalOpen} onClose={closeModal} maxWidth="4xl">
                <form onSubmit={handleSubmit} className="p-8 relative overflow-hidden bg-white rounded-3xl border border-blue-50">
                    <div className="absolute top-0 right-0 w-32 h-32 bg-amber-400/10 rounded-full blur-3xl -mr-16 -mt-16"></div>
                    
                    <h2 className="text-2xl font-black text-blue-900 uppercase tracking-tight mb-8 border-l-4 border-amber-400 pl-4">
                        {modalMode === 'add' ? 'Tambah Data Anggaran' : 'Perbarui Data Anggaran'}
                    </h2>
                    
                    <div className="grid grid-cols-1 md:grid-cols-2 gap-8">
                        <div className="space-y-5">
                            <div>
                                <InputLabel htmlFor="kode" className="font-bold text-xs uppercase text-gray-500 mb-2" value="Kode RKKL" />
                                <TextInput id="kode" className="w-full bg-gray-50 border-gray-200 rounded-xl" value={data.kode} onChange={(e) => setData('kode', e.target.value)} required />
                            </div>
                            <div>
                                <InputLabel htmlFor="nomenklatur" className="font-bold text-xs uppercase text-gray-500 mb-2" value="Nomenklatur Program" />
                                <textarea id="nomenklatur" className="w-full bg-gray-50 border-gray-200 rounded-xl p-3 h-24" value={data.nomenklatur} onChange={(e) => setData('nomenklatur', e.target.value)} required />
                            </div>
                            <div>
                                <InputLabel htmlFor="satuan" className="font-bold text-xs uppercase text-gray-500 mb-2" value="Satuan (Lembaga/Sekolah/Kegiatan dll)" />
                                <TextInput id="satuan" className="w-full bg-gray-50 border-gray-200 rounded-xl" value={data.satuan} onChange={(e) => setData('satuan', e.target.value)} />
                            </div>
                        </div>
                        
                        <div className="space-y-5">
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <InputLabel htmlFor="volume" className="font-bold text-xs uppercase text-gray-500 mb-2" value="Volume Target" />
                                    <TextInput id="volume" className="w-full bg-gray-50 border-gray-200 rounded-xl" value={data.volume} onChange={(e) => setData('volume', e.target.value)} required />
                                </div>
                                <div>
                                    <InputLabel htmlFor="volume_realisasi" className="font-bold text-xs uppercase text-gray-500 mb-2" value="Volume Realisasi" />
                                    <TextInput id="volume_realisasi" className="w-full bg-gray-50 border-gray-200 rounded-xl" value={data.volume_realisasi} onChange={(e) => setData('volume_realisasi', e.target.value)} />
                                </div>
                            </div>
                            
                            <div className="grid grid-cols-2 gap-4">
                                <div>
                                    <InputLabel htmlFor="anggaran_alokasi" className="font-bold text-xs uppercase text-gray-500 mb-2" value="Nominal Alokasi (Rp)" />
                                    <TextInput id="anggaran_alokasi" type="number" className="w-full bg-gray-50 border-gray-200 rounded-xl" value={data.anggaran_alokasi} onChange={(e) => setData('anggaran_alokasi', e.target.value)} required />
                                </div>
                                <div>
                                    <InputLabel htmlFor="anggaran_realisasi" className="font-bold text-xs uppercase text-gray-500 mb-2" value="Nominal Realisasi (Rp)" />
                                    <TextInput id="anggaran_realisasi" type="number" className="w-full bg-gray-50 border-gray-200 rounded-xl" value={data.anggaran_realisasi} onChange={(e) => setData('anggaran_realisasi', e.target.value)} required />
                                </div>
                            </div>
                            
                            <div>
                                <InputLabel htmlFor="pelaksanaan" className="font-bold text-xs uppercase text-gray-500 mb-2" value="Pelaksanaan Fisik (%)" />
                                <TextInput id="pelaksanaan" type="number" step="0.1" className="w-full bg-gray-50 border-gray-200 rounded-xl" value={data.pelaksanaan} onChange={(e) => setData('pelaksanaan', e.target.value)} required />
                            </div>
                        </div>
                    </div>
                    
                    <div className="mt-8 border-t border-gray-100 pt-6">
                        <InputLabel className="font-bold text-xs uppercase text-gray-500 mb-4" value="Kelengkapan Bulanan (Centang yang selesai)" />
                        <div className="grid grid-cols-4 md:grid-cols-6 gap-3">
                            {months.map((m, idx) => (
                                <label key={m} className={`flex items-center justify-center space-x-2 p-2 rounded-lg border cursor-pointer transition-colors ${data.kelengkapan[idx] ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-gray-50 border-gray-200 text-gray-500 hover:bg-gray-100'}`}>
                                    <input 
                                        type="checkbox" 
                                        className="rounded text-emerald-500 focus:ring-emerald-500 border-gray-300" 
                                        checked={data.kelengkapan[idx]}
                                        onChange={() => handleKelengkapanChange(idx)}
                                    />
                                    <span className="text-xs font-semibold">{m}</span>
                                </label>
                            ))}
                        </div>
                    </div>

                    <div className="mt-10 flex justify-end space-x-4">
                        <button type="button" onClick={closeModal} className="px-6 py-3 text-sm font-bold text-gray-500 hover:text-gray-700 transition-colors">Batal</button>
                        <button 
                            type="submit" 
                            disabled={processing}
                            className="bg-blue-900 text-white px-8 py-3 rounded-xl font-bold text-sm shadow-lg hover:bg-blue-800 disabled:opacity-50 transition-colors"
                        >
                            Simpan Data
                        </button>
                    </div>
                </form>
            </Modal>

            <Modal show={isDeleteModalOpen} onClose={closeModal}>
                <div className="p-10 text-center bg-white rounded-3xl">
                    <div className="bg-red-50 p-6 rounded-full w-20 h-20 flex items-center justify-center mx-auto mb-6">
                        <TrashIcon className="h-10 w-10 text-red-500" />
                    </div>
                    <h2 className="text-xl font-bold text-gray-900 mb-2">Hapus Data?</h2>
                    <p className="text-gray-500 text-sm mb-8">
                        Data ini akan dihapus secara permanen. Tindakan ini tidak dapat dibatalkan.
                    </p>
                    <div className="flex items-center justify-center space-x-4">
                         <button onClick={closeModal} className="px-6 py-2 text-sm font-bold text-gray-500 hover:bg-gray-100 rounded-lg transition-colors">Batal</button>
                         <button 
                            onClick={handleDelete}
                            disabled={processing}
                            className="bg-red-500 text-white px-6 py-2 rounded-lg font-bold text-sm shadow-md hover:bg-red-600 transition-colors"
                         >
                            Hapus
                         </button>
                    </div>
                </div>
            </Modal>
        </AuthenticatedLayout>
    );
}








import React, { useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, Link, useForm, router } from '@inertiajs/react';
import { DocumentArrowUpIcon, DocumentMagnifyingGlassIcon, ArrowPathIcon } from '@heroicons/react/24/outline';
import Modal from '@/Components/Modal';
import Pagination from '@/Components/Pagination';

export default function Index({ activities, userRole, allowImport, gugusMutus, periods, filters }) {
    const [search, setSearch] = useState(filters?.search || '');
    const [gugusMutuId, setGugusMutuId] = useState(filters?.gugus_mutu_id || '');
    const [statusAkhir, setStatusAkhir] = useState(filters?.status_akhir || '');
    const [periodId, setPeriodId] = useState(filters?.period_id || '');

    const handleFilter = (e) => {
        e.preventDefault();
        router.get(route('reports.index'), { search, gugus_mutu_id: gugusMutuId, status_akhir: statusAkhir, period_id: periodId }, { preserveState: true, replace: true });
    };
    
    const handleReset = () => {
        setSearch('');
        setGugusMutuId('');
        setStatusAkhir('');
        setPeriodId('');
        router.get(route('reports.index'), {}, { preserveState: true, replace: true });
    };
    const { post } = useForm();
    const [showImportModal, setShowImportModal] = useState(false);

    const getStatusBadge = (status) => {
        switch(status?.toLowerCase()) {
            case 'approved': return <span className="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-lg text-[10px] font-black uppercase tracking-widest border border-emerald-200">Approved</span>;
            case 'pending_manager': return <span className="px-3 py-1 bg-amber-100 text-amber-800 rounded-lg text-[10px] font-black uppercase tracking-widest border border-amber-200">Pending Mgr</span>;
            case 'pending_gm': return <span className="px-3 py-1 bg-blue-100 text-blue-800 rounded-lg text-[10px] font-black uppercase tracking-widest border border-blue-200">Pending GM</span>;
            case 'rejected': return <span className="px-3 py-1 bg-red-100 text-red-800 rounded-lg text-[10px] font-black uppercase tracking-widest border border-red-200">Rejected</span>;
            case 'pending': return <span className="px-3 py-1 bg-amber-100 text-amber-800 rounded-lg text-[10px] font-black uppercase tracking-widest border border-amber-200">Menunggu</span>;
            default: return <span className="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-[10px] font-black uppercase tracking-widest border border-gray-200">Draft</span>;
        }
    };

    const getKegiatanBadge = (status) => {
        switch(status?.toLowerCase()) {
            case 'selesai': return <span className="text-emerald-600 font-bold text-xs uppercase">Selesai</span>;
            case 'proses': return <span className="text-blue-600 font-bold text-xs uppercase">Proses</span>;
            default: return <span className="text-gray-400 font-bold text-xs uppercase">Belum Mulai</span>;
        }
    }

    return (
        <AuthenticatedLayout>
            <Head title="Daftar Kegiatan" />
            
            <div className="min-h-screen bg-gray-50 pb-20">
                <div className="max-w-[95%] mx-auto px-4 sm:px-6 lg:px-8 pt-8">
                    
                    {/* Header Section */}
                    <div className="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
                        <div>
                            <h1 className="text-2xl font-black text-blue-900 tracking-tight uppercase">Daftar Kegiatan & Realisasi</h1>
                            <p className="text-sm font-semibold text-gray-500 mt-1">Daftar ini otomatis ditarik dari Master Perencanaan. Silakan isi realisasi dengan menekan Lihat Detail.</p>
                        </div>
                        <div className="flex gap-3 items-center">
                            <Link 
                                href={route('reports.store')}
                                method="post"
                                as="button"
                                className="inline-flex items-center gap-2 px-5 py-3 bg-blue-600 text-white rounded-xl text-sm font-black uppercase tracking-widest hover:bg-blue-700 transition shadow-xl border border-blue-500 hover:-translate-y-0.5"
                            >
                                <DocumentArrowUpIcon className="w-5 h-5" />
                                Buat Laporan / Tarik Data
                            </Link>
                            <a 
                                href={route('reports.export')}
                                className="inline-flex items-center gap-2 px-5 py-3 bg-emerald-600 text-white rounded-xl text-sm font-black uppercase tracking-widest hover:bg-emerald-700 transition shadow-xl border border-emerald-500 hover:-translate-y-0.5"
                            >
                                <svg className="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z" />
                                </svg>
                                Download Excel
                            </a>
                        </div>
                    </div>

                    {/* Executive Dashboard Section */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div className="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
                            <div className="w-14 h-14 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                                <svg className="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" /></svg>
                            </div>
                            <div>
                                <p className="text-sm font-bold text-gray-400 uppercase tracking-widest">Total Progress</p>
                                <h2 className="text-3xl font-black text-gray-900">
                                    {(Object.values(activities.data || activities || {}).length > 0 
                                        ? Object.values(activities.data || activities || {}).reduce((acc, curr) => acc + parseFloat(curr.percent_complete || 0), 0) / Object.values(activities.data || activities || {}).length
                                        : 0).toFixed(1)}%
                                </h2>
                            </div>
                        </div>
                        <div className="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
                            <div className="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center text-red-600">
                                <svg className="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                            </div>
                            <div>
                                <p className="text-sm font-bold text-gray-400 uppercase tracking-widest">Task Terlambat</p>
                                <h2 className="text-3xl font-black text-gray-900">
                                    {Object.values(activities.data || activities || {}).filter(act => {
                                        if (act.status_akhir === 'Selesai' || act.status_akhir === 'Sudah') return false;
                                        if (parseFloat(act.percent_complete) >= 100) return false;
                                        if (!act.rencana_end_date) return false;
                                        return new Date() > new Date(act.rencana_end_date);
                                    }).length}
                                </h2>
                            </div>
                        </div>
                        <div className="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
                            <div className="w-14 h-14 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600">
                                <svg className="w-7 h-7" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <div>
                                <p className="text-sm font-bold text-gray-400 uppercase tracking-widest">Task On-Track</p>
                                <h2 className="text-3xl font-black text-gray-900">
                                    {Object.values(activities.data || activities || {}).filter(act => {
                                        if (act.status_akhir === 'Selesai' || act.status_akhir === 'Sudah') return true;
                                        if (parseFloat(act.percent_complete) >= 100) return true;
                                        if (!act.rencana_end_date) return true;
                                        return new Date() <= new Date(act.rencana_end_date);
                                    }).length}
                                </h2>
                            </div>
                        </div>
                    </div>

                    {/* Table Section */}
                    <div className="p-4 bg-white border border-gray-100 rounded-2xl shadow-sm mb-6 flex flex-wrap gap-4 items-center">
                        <form onSubmit={handleFilter} className="flex flex-wrap gap-2 w-full">
                            <input 
                                type="text" 
                                placeholder="Cari kegiatan..." 
                                className="border-gray-300 rounded text-sm px-3 py-2 w-full md:w-64"
                                value={search}
                                onChange={e => setSearch(e.target.value)}
                            />
                            {userRole && (userRole === 'admin' || userRole === 'super-admin' || userRole === 'superadmin') && (
                                <select 
                                    className="border-gray-300 rounded text-sm px-3 py-2"
                                    value={gugusMutuId}
                                    onChange={e => setGugusMutuId(e.target.value)}
                                >
                                    <option value="">Semua Devisi</option>
                                    {gugusMutus && gugusMutus.map(gm => (
                                        <option key={gm.id} value={gm.id}>{gm.name}</option>
                                    ))}
                                </select>
                            )}
                                                        <select 
                                className="border-gray-300 rounded text-sm px-3 py-2"
                                value={periodId}
                                onChange={e => setPeriodId(e.target.value)}
                            >
                                <option value="">Semua Periode</option>
                                {periods && periods.map(p => (
                                    <option key={p.id} value={p.id}>{p.month_year}</option>
                                ))}
                            </select>
                            <select 
                                className="border-gray-300 rounded text-sm px-3 py-2"
                                value={statusAkhir}
                                onChange={e => setStatusAkhir(e.target.value)}
                            >
                                <option value="">Semua Status</option>
                                <option value="Belum Mulai">Belum Mulai</option>
                                <option value="Proses">Proses</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                            <button type="submit" className="bg-blue-600 text-white px-4 py-2 rounded text-sm font-semibold hover:bg-blue-700">Filter</button>
                            <button type="button" onClick={handleReset} className="bg-gray-200 text-gray-700 px-4 py-2 rounded text-sm font-semibold hover:bg-gray-300">Reset</button>
                        </form>
                    </div>
                    <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div className="overflow-x-auto">
                            <table className="w-full text-left whitespace-nowrap">
                                <thead>
                                    <tr>
                                        <th className="px-6 py-5 bg-blue-900 text-[10px] font-black text-blue-50 uppercase tracking-widest border-b border-blue-800">Devisi / GM</th>
                                        <th className="px-6 py-5 bg-blue-900 text-[10px] font-black text-blue-50 uppercase tracking-widest border-b border-blue-800">Nama Kegiatan</th>
                                        
                                        <th className="px-6 py-5 bg-blue-900 text-[10px] font-black text-blue-50 uppercase tracking-widest border-b border-blue-800">Periode</th>
                                        <th className="px-6 py-5 bg-blue-900 text-[10px] font-black text-blue-50 uppercase tracking-widest border-b border-blue-800">Tgl Realisasi Mulai</th>
                                        <th className="px-6 py-5 bg-blue-900 text-[10px] font-black text-blue-50 uppercase tracking-widest border-b border-blue-800">Tgl Realisasi Selesai</th>
                                        <th className="px-6 py-5 bg-blue-900 text-[10px] font-black text-blue-50 uppercase tracking-widest border-b border-blue-800">Ceklis Laporan (Status)</th>
                                        <th className="px-6 py-5 bg-blue-900 text-[10px] font-black text-blue-50 uppercase tracking-widest border-b border-blue-800">Status Kegiatan</th>
                                        <th className="px-6 py-5 bg-blue-900 text-center text-[10px] font-black text-blue-50 uppercase tracking-widest border-b border-blue-800">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {activities && Object.values(activities.data || activities || {}).length > 0 ? (
                                        Object.values(activities.data || activities || {}).map((activity) => (
                                            <tr key={activity.id} className="hover:bg-gray-50/50 transition border-b border-gray-100 group">
                                                <td className="px-6 py-5">
                                                    <span className="text-xs font-bold text-blue-900 bg-blue-50 px-3 py-1 rounded-lg border border-blue-100 uppercase tracking-widest">
                                                        {activity.report_submission?.project?.gugus_mutu?.name || activity.report_submission?.user?.gugus_mutu?.name || "Umum"}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-5">
                                                    <div className="text-sm font-black text-gray-900 uppercase">{activity.report_submission?.project?.name || "-"}</div>
                                                    <div className="text-[10px] text-gray-500 font-semibold mt-1">{activity.nama_kegiatan_turunan}</div>
                                                </td>
                                                
                                                <td className="px-6 py-5">
                                                    <div className="text-sm font-bold text-gray-800">{activity.report_submission?.period?.month_year || "-"}</div>
                                                </td>
                                                <td className="px-6 py-5">
                                                    <div className="text-sm font-bold text-gray-800">{activity.realisasi_start_date ? activity.realisasi_start_date.split("-").reverse().join("/") : "-"}</div>
                                                </td>
                                                <td className="px-6 py-5">
                                                    <div className="text-sm font-bold text-gray-800">{activity.realisasi_end_date ? activity.realisasi_end_date.split("-").reverse().join("/") : "-"}</div>
                                                </td>
                                                <td className="px-6 py-5">
                                                    <div className="flex flex-col items-start gap-2">
                                                        {getStatusBadge(activity.approval_status)}
                                                        {(!activity.approval_status || activity.approval_status === "Draft" || activity.approval_status?.includes("Rejected")) && (
                                                            <Link
                                                                href={route('activities.submit_report', activity.id)}
                                                                method="post"
                                                                as="button"
                                                                className="inline-flex items-center justify-center px-3 py-1.5 bg-emerald-600 text-white rounded-md text-[10px] font-black uppercase tracking-widest hover:bg-emerald-700 transition shadow-sm whitespace-nowrap"
                                                            >
                                                                Kirim Laporan
                                                            </Link>
                                                        )}
                                                        {activity.approval_status === "Pending" && (userRole === "admin" || userRole === "super-admin" || userRole === "manager") && (
                                                            <div className="flex gap-2 mt-1">
                                                                <Link
                                                                    href={route('approvals.approve', activity.id)}
                                                                    method="post"
                                                                    as="button"
                                                                    className="inline-flex items-center px-3 py-1 bg-blue-600 text-white rounded text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 transition shadow"
                                                                >
                                                                    Approve
                                                                </Link>
                                                            </div>
                                                        )}
                                                    </div>
                                                </td>
                                                <td className="px-6 py-5">
                                                    {getKegiatanBadge(activity.status_akhir)}
                                                </td>
                                                <td className="px-6 py-5">
                                                    <div className="flex justify-center items-center gap-2">
                                                        <Link
                                                            href={route('reports.show', activity.report_submission_id)}
                                                            className="inline-flex items-center justify-center px-4 py-2 bg-blue-600 text-white rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-blue-700 transition shadow-sm whitespace-nowrap"
                                                        >
                                                            Isi Realisasi & Detail
                                                        </Link>

                                                          {(activity.approval_status === 'Approved' || activity.approval_status === 'approved') && (
                                                              <button
                                                                  onClick={() => {
                                                                      if(confirm('Apakah Anda yakin ingin mengembalikan laporan ini menjadi Draft?')) {
                                                                          router.post(route('activities.revert', activity.id));
                                                                      }
                                                                  }}
                                                                  className="inline-flex items-center justify-center px-4 py-2 bg-orange-500 text-white rounded-lg text-[10px] font-black uppercase tracking-widest hover:bg-orange-600 transition shadow-sm whitespace-nowrap ml-2"
                                                              >
                                                                  Kembalikan ke Draft
                                                              </button>
                                                          )}
                                                    </div>
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan="8" className="px-6 py-20 text-center">
                                                <div className="flex flex-col items-center justify-center">
                                                    <div className="w-20 h-20 rounded-full bg-gray-50 flex items-center justify-center mb-4 border border-gray-100 shadow-inner">
                                                        <ArrowPathIcon className="w-10 h-10 text-gray-300" />
                                                    </div>
                                                    <h3 className="text-sm font-black text-gray-400 uppercase tracking-widest mb-1">Belum Ada Kegiatan</h3>
                                                    <p className="text-xs text-gray-400 font-semibold">Daftar kegiatan akan terisi otomatis saat Proyek direncanakan di Gantt Chart.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                        {activities && activities.links && <Pagination links={activities.links} />}
                    </div>

                </div>
            </div>
        </AuthenticatedLayout>
    );
}








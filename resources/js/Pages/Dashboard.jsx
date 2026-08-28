import React, { useState } from 'react';
import { Head, Link, router } from '@inertiajs/react';
import { LineChart, Line, AreaChart, Area, XAxis, YAxis, CartesianGrid, Tooltip, Legend, ResponsiveContainer } from 'recharts';
import { InformationCircleIcon, CheckCircleIcon, ExclamationTriangleIcon, LightBulbIcon, BanknotesIcon } from '@heroicons/react/24/solid';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';

export default function Dashboard({ auth, activities = [], lateTasks = [], invalidBudgets = [], monthlyStats = [], budgetStats = [], selectedYear = 2026, divisions = [], periods = [], selectedMonth = '', selectedDivision = '', metrics = { total_terkirim: 0, total_disetujui: 0, total_ditolak: 0, anggaran: { total_alokasi: 0, total_realisasi: 0, persentase: 0 } } }) {
    
    const [activeMainTab, setActiveMainTab] = useState('Kinerja');
    const [activeSubTab, setActiveSubTab] = useState('Rincian Task');
    const [currentPage, setCurrentPage] = useState(1);
    const itemsPerPage = 10;

    const handleSubTabChange = (t) => {
        setActiveSubTab(t);
        setCurrentPage(1);
    };

    let currentData = [];
    if (activeSubTab === 'Rincian Task' || activeSubTab === 'Milestone') currentData = activities || [];
    else if (activeSubTab === 'Task Terlambat') currentData = lateTasks || [];
    else if (activeSubTab === 'Risiko & Isu') currentData = (activities || []).filter(a => a.kendala);
    else if (activeSubTab === 'Ang Invalid') currentData = invalidBudgets || [];

    const totalPages = Math.ceil(currentData.length / itemsPerPage);
    const paginatedData = currentData.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage);

    const renderPagination = () => {
        if (totalPages <= 1) return null;
        return (
            <div className="flex justify-center items-center gap-4 mt-6 p-4">
                <button 
                    onClick={() => setCurrentPage(p => Math.max(1, p - 1))}
                    disabled={currentPage === 1}
                    className="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg disabled:opacity-50 font-bold text-xs uppercase"
                >
                    Prev
                </button>
                <span className="text-xs font-black text-gray-400">
                    Halaman {currentPage} dari {totalPages}
                </span>
                <button 
                    onClick={() => setCurrentPage(p => Math.min(totalPages, p + 1))}
                    disabled={currentPage === totalPages}
                    className="px-4 py-2 bg-gray-100 text-gray-600 rounded-lg disabled:opacity-50 font-bold text-xs uppercase"
                >
                    Next
                </button>
            </div>
        );
    };

    const brandColor = '#FBBF24';
    const realisasiColor = '#9CA3AF';
    const budgetColor = '#3B82F6';
    const realBudgetColor = "#10b981";

    return (
        <AuthenticatedLayout>
            <Head title="Dashboardkin 520" />

            <main className="max-w-screen-2xl mx-auto p-4 md:p-10 space-y-12 w-full">

                <div className="bg-white p-6 md:p-12 rounded-[40px] shadow-sm border border-gray-100 flex flex-col items-center">
                    <div className="flex flex-col md:flex-row justify-between items-center w-full mb-16 gap-6">
                        <div className="w-1/3"></div>
                        <div className="flex space-x-6 bg-gray-50/80 p-2.5 rounded-full shadow-inner border border-gray-200 mx-auto">
                            <button onClick={() => setActiveMainTab('Kinerja')} className={`px-16 py-4 rounded-full text-xs font-black transition-all ${activeMainTab === 'Kinerja' ? 'bg-amber-400 text-gray-900 shadow-2xl scale-105' : 'text-gray-400 hover:text-gray-600 hover:bg-white'}`}>KINERJA</button>
                            <button onClick={() => setActiveMainTab('Anggaran')} className={`px-16 py-4 rounded-full text-xs font-black transition-all ${activeMainTab === 'Anggaran' ? 'bg-amber-400 text-gray-900 shadow-2xl scale-105' : 'text-gray-400 hover:text-gray-600 hover:bg-white'}`}>ANGGARAN</button>
                        </div>
                        <div className="flex space-x-4 w-1/3 justify-end">
                            <select 
                                className="bg-white border border-gray-200 text-gray-700 text-xs rounded-2xl focus:ring-amber-500 focus:border-amber-500 block p-3 font-bold shadow-sm"
                                value={selectedDivision || ''}
                                onChange={(e) => {
                                    router.get(window.location.pathname, { 
                                        year: selectedYear, 
                                        month: selectedMonth, 
                                        gugus_mutu_id: e.target.value 
                                    }, { preserveState: true });
                                }}
                            >
                                <option value="">SEMUA DIVISI</option>
                                {divisions?.map(d => (
                                    <option key={d.id} value={d.id}>{d.name.toUpperCase()}</option>
                                ))}
                            </select>
                            <select 
                                className="bg-white border border-gray-200 text-gray-700 text-xs rounded-2xl focus:ring-amber-500 focus:border-amber-500 block p-3 font-bold shadow-sm"
                                value={selectedMonth || ''}
                                onChange={(e) => {
                                    router.get(window.location.pathname, { 
                                        year: selectedYear, 
                                        month: e.target.value, 
                                        gugus_mutu_id: selectedDivision 
                                    }, { preserveState: true });
                                }}
                            >
                                <option value="">SEMUA BULAN</option>
                                {[1,2,3,4,5,6,7,8,9,10,11,12].map(m => (
                                    <option key={m} value={m}>BULAN {m}</option>
                                ))}
                            </select>
                        </div>
                    </div>

                    <div className="grid grid-cols-1 lg:grid-cols-4 gap-12 w-full">
                        <div className="lg:col-span-3">
                             {activeMainTab === 'Kinerja' ? (
                                <>
                                    <h3 className="text-3xl font-black text-blue-900 mb-12 text-center uppercase tracking-widest italic decoration-amber-400 decoration-8 underline-offset-8">Target dan Capaian Kinerja</h3>
                                    <div className="flex justify-center space-x-12 text-[11px] mb-10 font-black text-gray-400 italic bg-white py-4 rounded-full w-max mx-auto px-12 border border-blue-50 shadow-sm uppercase tracking-widest">
                                        <div className="flex items-center"><div className="w-5 h-5 bg-gray-300 rounded-full mr-4 shadow-md border-2 border-white"></div> REALISASI</div>
                                        <div className="flex items-center"><div className="w-5 h-5 bg-amber-400 rounded-full mr-4 shadow-md border-2 border-white"></div> TARGET</div>
                                    </div>
                                    <div className="h-[450px] w-full">
                                        <ResponsiveContainer width="100%" height="100%">
                                            <LineChart data={monthlyStats} margin={{ top: 20, right: 30, left: 10, bottom: 5 }}>
                                                <CartesianGrid strokeDasharray="6 6" vertical={false} stroke="#E5E7EB" />
                                                <XAxis dataKey="name" axisLine={false} tickLine={false} tick={{ fontSize: 11, fontWeight: 900, fill: '#6B7280' }} />
                                                <YAxis hide domain={[0, 110]} />
                                                <Tooltip cursor={{ stroke: brandColor, strokeWidth: 3 }} contentStyle={{ borderRadius: '24px', border: 'none', background: '#FFF font-weight: bold', boxShadow: '0 30px 60px -15px rgba(0,0,0,0.3)' }} />
                                                <Line type="monotone" dataKey="target" stroke={brandColor} strokeWidth={7} dot={{ r: 10, fill: brandColor, stroke: '#FFF', strokeWidth: 4 }} label={{ position: 'top', fontSize: 13, fill: brandColor, fontWeight: 900, formatter: (v) => v > 0 ? v + '%' : '' }} />
                                                <Line type="monotone" dataKey="realisasi" stroke={realisasiColor} strokeWidth={7} dot={{ r: 10, fill: realisasiColor, stroke: '#FFF', strokeWidth: 4 }} label={{ position: 'bottom', fontSize: 13, fill: realisasiColor, fontWeight: 900, formatter: (v) => v > 0 ? v + '%' : '' }} />
                                            </LineChart>
                                        </ResponsiveContainer>
                                    </div>
                                </>
                             ) : (
                                <>
                                    <h3 className="text-3xl font-black text-blue-900 mb-12 text-center uppercase tracking-widest italic decoration-blue-500 decoration-8 underline-offset-8">Statistik Serapan Anggaran</h3>
                                    <div className="flex justify-center space-x-12 text-[11px] mb-10 font-black text-gray-400 italic bg-white py-4 rounded-full w-max mx-auto px-12 border border-blue-50 shadow-sm uppercase tracking-widest">
                                        <div className="flex items-center"><div className="w-5 h-5 bg-blue-500 rounded-full mr-4 shadow-md border-2 border-white"></div> ALOKASI</div>
                                        <div className="flex items-center"><div className="w-5 h-5 bg-green-500 rounded-full mr-4 shadow-md border-2 border-white"></div> REALISASI</div>
                                    </div>
                                    <div className="h-[450px] w-full">
                                        <ResponsiveContainer width="100%" height="100%">
                                            <AreaChart data={budgetStats} margin={{ top: 20, right: 30, left: 10, bottom: 5 }}>
                                                <defs>
                                                    <linearGradient id="colorTarget" x1="0" y1="0" x2="0" y2="1">
                                                        <stop offset="5%" stopColor={budgetColor} stopOpacity={0.1}/>
                                                        <stop offset="95%" stopColor={budgetColor} stopOpacity={0}/>
                                                    </linearGradient>
                                                    <linearGradient id="colorReal" x1="0" y1="0" x2="0" y2="1">
                                                        <stop offset="5%" stopColor={realBudgetColor} stopOpacity={0.4}/>
                                                        <stop offset="95%" stopColor={realBudgetColor} stopOpacity={0}/>
                                                    </linearGradient>
                                                </defs>
                                                <CartesianGrid strokeDasharray="6 6" vertical={false} stroke="#E5E7EB" />
                                                <XAxis dataKey="name" axisLine={false} tickLine={false} tick={{ fontSize: 11, fontWeight: 900, fill: '#6B7280' }} />
                                                <YAxis hide />
                                                <Tooltip contentStyle={{ borderRadius: '24px', border: 'none', boxShadow: '0 30px 60px -15px rgba(0,0,0,0.3)', fontWeight: 'bold' }} />
                                                <Area type="monotone" dataKey="target" stroke={budgetColor} strokeWidth={7} fillOpacity={1} fill="url(#colorTarget)" dot={{ r: 10, fill: budgetColor, stroke: '#FFF', strokeWidth: 4 }} activeDot={{ r: 12 }} />
                                                <Area type="monotone" dataKey="realisasi" stroke={realBudgetColor} strokeWidth={7} fillOpacity={1} fill="url(#colorReal)" dot={{ r: 10, fill: realBudgetColor, stroke: '#FFF', strokeWidth: 4 }} activeDot={{ r: 12 }} />
                                            </AreaChart>
                                        </ResponsiveContainer>
                                    </div>
                                </>
                             )}
                        </div>

                        <div className="bg-white rounded-[40px] border border-blue-100 flex flex-col h-[550px] shadow-sm overflow-hidden border-t-8 border-t-amber-400 bg-gradient-to-b from-blue-50/30 to-white">
                            <div className="bg-blue-900/5 p-6 flex text-[10px] font-black text-blue-900/60 uppercase italic border-b border-blue-100 tracking-widest">
                                <div className="w-24 px-2">KODETP</div>
                                <div className="flex-1 px-4 text-center">OBJEKTIF STRATEGIS</div>
                            </div>
                            <div className="flex-1 overflow-y-auto p-6 space-y-5 custom-scrollbar">
                                {activities?.map(act => (
                                    <div key={act.id} className="bg-white p-6 rounded-3xl border border-gray-100 shadow-sm flex items-start group hover:border-amber-400 hover:shadow-2xl transition-all cursor-pointer hover:-translate-y-1">
                                        <span className="w-16 flex-shrink-0 text-[11px] font-mono text-amber-500 pt-1 leading-none font-black italic tracking-tighter">[{act.kode_pmo}]</span>
                                        <p className="flex-1 text-[13px] font-black text-gray-800 leading-snug group-hover:text-blue-900 uppercase tracking-tighter">{act.nama_kegiatan_turunan}</p>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>

                <div className="bg-white p-6 md:p-12 rounded-[40px] shadow-sm border border-gray-100 flex flex-col mt-12 bg-gradient-to-b from-white to-gray-50/30 overflow-x-auto">
                    <div className="flex space-x-3 md:space-x-6 mb-16 bg-gray-100 p-2.5 rounded-full w-max mx-auto shadow-inner border border-gray-200 min-w-max">
                        {['Rincian Task', 'Milestone', 'Task Terlambat', 'Risiko & Isu', 'Ang Invalid'].map(t => (
                            <button key={t} onClick={() => handleSubTabChange(t)} className={`px-8 md:px-12 py-5 rounded-full text-[11px] font-black transition-all ${activeSubTab === t ? 'bg-blue-900 text-white shadow-2xl scale-110 border border-blue-950 px-10' : 'text-gray-400 hover:text-blue-900 hover:bg-white'}`}>
                                {t.toUpperCase()}
                            </button>
                        ))}
                    </div>

                    <div className="min-h-[600px] w-full">
                        {activeSubTab === 'Rincian Task' && (
                             <div className="overflow-x-auto rounded-[32px] border border-blue-50 shadow-sm">
                                <table className="w-full text-left border-collapse bg-white">
                                    <thead>
                                        <tr className="bg-blue-900 text-white border-b border-blue-950 text-[10px] font-black uppercase tracking-widest italic">
                                            <th className="px-8 py-5 border-r border-blue-800">DIVISI / GM</th>
                                            <th className="px-8 py-5 border-r border-blue-800">DESKRIPSI & INDIKATOR KINERJA</th>
                                            <th className="px-8 py-5 border-r border-blue-800 text-center">RENCANA</th>
                                            <th className="px-8 py-5 border-r border-blue-800 text-center">REALISASI</th>
                                            <th className="px-8 py-5 text-center">STATUS CAPAIAN</th>
                                        </tr>
                                    </thead>
                                    <tbody className="text-[11px] font-bold">
                                        {paginatedData.map(act => (
                                            <tr key={act.id} className="border-b border-gray-100 hover:bg-blue-50/30 transition-all group">
                                                <td className="px-8 py-8 font-black text-blue-800 align-top text-xs border-r border-gray-50 uppercase">{act.project_task?.project?.gugus_mutu?.name || act.report_submission?.user?.gugus_mutu?.name || "UMUM"}</td>
                                                <td className="px-8 py-8 align-top border-r border-gray-50">
                                                    <div className="space-y-2">
                                                        <p className="font-black text-gray-900 text-[13px] leading-snug uppercase tracking-tight group-hover:text-blue-700">{act.nama_kegiatan_turunan}</p>
                                                        <div className="flex items-center text-[10px] text-gray-400 uppercase tracking-widest italic pt-1">
                                                            <InformationCircleIcon className="h-3 w-3 mr-2" /> IND: {act.indikator_kinerja_kegiatan || '-'}
                                                        </div>
                                                        <div className="pt-3 flex items-center text-[10px] text-blue-900/40 uppercase bg-blue-50/50 p-2 rounded-lg border border-blue-100/50">
                                                            <CheckCircleIcon className="h-4 w-4 mr-3 text-green-500" /> HASIL: {act.hasil_kegiatan || 'DOKUMENTASI PROGRAM'}
                                                        </div>
                                                    </div>
                                                </td>
                                                <td className="px-8 py-8 border-r border-gray-50 align-top text-center">
                                                    <div className="inline-flex flex-col bg-gray-50 p-3 rounded-2xl border border-gray-100 shadow-inner">
                                                        <span className="text-[9px] text-gray-400 mb-1 font-black">MULA-TEN</span>
                                                        <span className="text-gray-900 font-extrabold">{act.rencana_start_date}</span>
                                                        <span className="text-gray-900 font-extrabold">{act.rencana_end_date}</span>
                                                    </div>
                                                </td>
                                                <td className="px-8 py-8 border-r border-gray-50 align-top text-center">
                                                    <div className="inline-flex flex-col bg-blue-50/50 p-3 rounded-2xl border border-blue-100 shadow-inner">
                                                        <span className="text-[9px] text-blue-400 mb-1 font-black">REALITA</span>
                                                        <span className="text-blue-900 font-extrabold">{act.realisasi_start_date || '-'}</span>
                                                        <span className="text-blue-900 font-extrabold">{act.realisasi_end_date || '-'}</span>
                                                    </div>
                                                </td>
                                                <td className="px-8 py-8 align-top text-center">
                                                    <span className={`inline-block px-4 py-2 rounded-xl font-black text-[10px] uppercase shadow-md transition-all ${act.status_akhir === 'Selesai' || act.status_akhir === 'Sudah' ? 'bg-green-600 text-white' : 'bg-amber-400 text-gray-900'}`}>
                                                        {act.status_akhir || 'DALAM PROSES'}
                                                    </span>
                                                </td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                                {renderPagination()}
                             </div>
                        )}

                        {activeSubTab === 'Milestone' && (
                             <div className="overflow-x-auto rounded-[32px] border border-blue-50 shadow-sm bg-white p-4">
                                 <div className="flex min-w-max bg-blue-900 text-white rounded-t-[20px] shadow-lg">
                                     <div className="w-48 flex-shrink-0 p-5 border-r border-blue-800 font-black text-[10px] uppercase tracking-widest italic">DIVISI / GM</div>
                                     {['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'].map(m => (
                                         <div key={m} className="w-28 border-r border-blue-800 text-center p-5 text-[10px] font-black uppercase tracking-widest italic">{m}</div>
                                     ))}
                                 </div>
                                 <div className="space-y-4 p-4 min-w-max">
                                     {paginatedData.map((act) => {
                                         let startMonth = 1; let endMonth = 12;
                                         if (act.rencana_start_date) startMonth = new Date(act.rencana_start_date).getMonth() + 1;
                                         if (act.rencana_end_date) endMonth = new Date(act.rencana_end_date).getMonth() + 1;
                                         const cellWidth = 112; const marginLeft = (startMonth - 1) * cellWidth; const width = ((endMonth - startMonth) + 1) * cellWidth;
                                         
                                         return (
                                             <div key={act.id} className="flex h-12 items-center bg-gray-50/50 rounded-2xl border border-gray-100 group hover:bg-amber-50/50 transition-all">
                                                 <div className="w-44 flex-shrink-0 text-[10px] font-black px-6 text-blue-900 uppercase">{act.project_task?.project?.gugus_mutu?.name || act.report_submission?.user?.gugus_mutu?.name || "UMUM"}</div>
                                                 <div className="flex-1 relative h-6 mr-4">
                                                     <div className="absolute h-6 rounded-full bg-blue-600 shadow-xl top-0 border-2 border-white group-hover:bg-blue-700 transition-all" style={{ left: marginLeft + 'px', width: width + 'px' }}>
                                                         <div className="flex items-center justify-center h-full text-[8px] font-black text-white uppercase italic tracking-widest opacity-30 group-hover:opacity-100">PROG: {act.kode_pmo}</div>
                                                     </div>
                                                 </div>
                                             </div>
                                         );
                                     })}
                                 </div>
                                 {renderPagination()}
                             </div>
                        )}

                        {activeSubTab === 'Task Terlambat' && (
                             <div className="rounded-[40px] border border-red-100 bg-red-50/20 overflow-hidden shadow-sm">
                                 <table className="w-full text-left border-collapse bg-white">
                                     <thead>
                                         <tr className="bg-red-600 text-white uppercase font-black text-[11px] border-b border-red-700 tracking-widest italic">
                                             <th className="px-10 py-6 w-48">DIVISI / GM</th>
                                             <th className="px-10 py-6">RINCIAN KEGIATAN TERHUBUNG KERLALUI (LATE)</th>
                                             <th className="px-10 py-6 text-center">TENGGAT AKHIR</th>
                                         </tr>
                                     </thead>
                                     <tbody>
                                         {paginatedData.length > 0 ? paginatedData.map(task => (
                                             <tr key={task.id} className="border-b border-red-50 hover:bg-red-50/50 transition-all">
                                                 <td className="px-10 py-10 font-black text-red-800 align-top text-xs uppercase">{task.project_task?.project?.gugus_mutu?.name || task.report_submission?.user?.gugus_mutu?.name || "UMUM"}</td>
                                                 <td className="px-10 py-10">
                                                     <p className="font-extrabold text-gray-900 text-[14px] mb-3 uppercase tracking-tighter">{task.nama_kegiatan_turunan}</p>
                                                     <div className="flex items-center space-x-3">
                                                         <span className="bg-red-100 text-red-600 px-3 py-1 rounded-lg text-[9px] font-black uppercase tracking-widest border border-red-200">STATUS: OVERDUE</span>
                                                         <span className="text-[9px] text-gray-400 font-bold uppercase italic">PIC: ADMIN KIN520</span>
                                                     </div>
                                                 </td>
                                                 <td className="px-10 py-10 text-center">
                                                     <div className="inline-block bg-red-700 text-white px-6 py-3 rounded-[20px] font-black text-xs shadow-xl min-w-[150px] uppercase">
                                                         DEADLINE: {task.rencana_end_date}
                                                     </div>
                                                 </td>
                                             </tr>
                                         )) : (
                                             <tr><td colSpan="3" className="text-center py-40 text-gray-400 font-black uppercase tracking-[0.4em] italic opacity-40 animate-pulse">SELURUH TARGET TERKEJAR TEPAT WAKTU</td></tr>
                                         )}
                                     </tbody>
                                 </table>
                             </div>
                        )}

                        {activeSubTab === 'Risiko & Isu' && (
                             <div className="rounded-[40px] border border-amber-100 bg-amber-50/20 overflow-hidden shadow-xl border-t-8 border-t-amber-400">
                                  <table className="w-full text-left border-collapse">
                                      <thead>
                                          <tr className="bg-amber-500 text-white uppercase font-black text-[12px] border-b border-amber-600 tracking-widest italic">
                                              <th className="px-10 py-6 w-48">DIVISI / GM</th>
                                              <th className="px-10 py-6">URAIAN KEGIATAN & ISU / RISIKO</th>
                                              <th className="px-10 py-6">MITIGASI PROAKTIF</th>
                                          </tr>
                                      </thead>
                                      <tbody className="bg-white">
                                          {paginatedData.length > 0 ? paginatedData.map(act => (
                                              <tr key={act.id} className="border-b border-amber-50 hover:bg-amber-100/10 transition-all">
                                                  <td className="px-10 py-10 font-black text-amber-800 align-top text-xs border-r border-amber-50 uppercase">{act.project_task?.project?.gugus_mutu?.name || act.report_submission?.user?.gugus_mutu?.name || "UMUM"}</td>
                                                  <td className="px-10 py-10 align-top border-r border-gray-50">
                                                      <div className="flex items-start">
                                                          <div className="bg-green-500/10 p-3 rounded-2xl mr-5 shadow-inner"><ExclamationTriangleIcon className="h-5 w-5 text-red-600" /></div>
                                                          <div>
                                                              <p className="font-black text-blue-900 text-[13px] mb-3 uppercase leading-none tracking-tight italic">{act.nama_kegiatan_turunan}</p>
                                                              <div className="bg-red-50 p-4 rounded-2xl border border-red-100/50">
                                                                  <p className="text-red-900 text-[11px] leading-relaxed font-black uppercase tracking-tighter shadow-sm">{act.resiko_isu}</p>
                                                              </div>
                                                          </div>
                                                      </div>
                                                  </td>
                                                  <td className="px-10 py-10 align-top">
                                                       <div className="flex items-start">
                                                          <div className="bg-green-500/10 p-3 rounded-2xl mr-5 shadow-inner"><LightBulbIcon className="h-5 w-5 text-green-600" /></div>
                                                          <div className="flex-1">
                                                              <div className="bg-green-50 p-4 rounded-2xl border border-green-100/50">
                                                                  <p className="text-green-900 text-[11px] leading-relaxed font-black uppercase tracking-tighter italic">{act.solusi}</p>
                                                              </div>
                                                              <p className="text-[9px] text-gray-400 mt-4 italic font-black uppercase underline decoration-green-400 decoration-2 underline-offset-4 tracking-[0.2em]">Mitigasi Proaktif</p>
                                                          </div>
                                                      </div>
                                                  </td>
                                              </tr>
                                          )) : (
                                              <tr><td colSpan="3" className="text-center py-40 text-gray-400 font-black uppercase tracking-widest italic animate-pulse">NIHIL RISIKO TERCATAT</td></tr>
                                          )}
                                      </tbody>
                                  </table>
                                  {renderPagination()}
                             </div>
                        )}

                        {activeSubTab === 'Ang Invalid' && (
                             <div className="rounded-[40px] border border-red-100 bg-white overflow-hidden shadow-2xl border-t-8 border-t-red-600">
                                  <table className="w-full text-left border-collapse">
                                      <thead>
                                          <tr className="bg-red-600 text-white uppercase font-black text-[12px] border-b border-red-700 tracking-widest italic">
                                              <th className="px-10 py-8 w-48">DIVISI / GM</th>
                                              <th className="px-10 py-8">RINCIAN DATA ANGGARAN TIDAK VALID</th>
                                              <th className="px-10 py-8 text-center">ALOKASI VS REALITA</th>
                                              <th className="px-10 py-8 text-center">STATUS KESALAHAN</th>
                                          </tr>
                                      </thead>
                                      <tbody>
                                          {paginatedData.length > 0 ? paginatedData.map(act => {
                                              const alokasi = act.budget ? floatval(act.budget.anggaran_alokasi) : 0;
                                              const realisasi = act.budget ? floatval(act.budget.anggaran_realisasi) : 0;
                                              const isOver = realisasi > alokasi && alokasi > 0;
                                              const isMissing = alokasi <= 0;

                                              return (
                                                  <tr key={act.id} className="border-b border-red-50 hover:bg-red-100/10 transition-all">
                                                      <td className="px-10 py-12 font-black text-red-800 align-top text-sm uppercase">{act.project_task?.project?.gugus_mutu?.name || act.report_submission?.user?.gugus_mutu?.name || "UMUM"}</td>
                                                      <td className="px-10 py-12 align-top">
                                                          <p className="font-black text-blue-900 text-[16px] mb-4 uppercase leading-none tracking-tight italic drop-shadow-sm">{act.nama_kegiatan_turunan}</p>
                                                          <div className="flex items-center text-[10px] font-black text-gray-400 uppercase tracking-widest bg-gray-50/80 p-3 rounded-2xl w-max border border-gray-100">
                                                              <BanknotesIcon className="h-4 w-4 mr-3 text-red-500 shadow-sm" /> KODE RRKL: <span className="ml-2 text-red-600 bg-red-50 px-3 py-1 rounded shadow-inner">{act.kode_rrkl || 'BELUM TERDEFINISI'}</span>
                                                          </div>
                                                      </td>
                                                      <td className="px-10 py-12 align-top text-center">
                                                           <div className="inline-flex flex-col space-y-3">
                                                               <span className="text-[11px] font-black text-gray-400 bg-gray-50 px-4 py-1.5 rounded-full uppercase italic">ALOKASI: Rp {new Intl.NumberFormat('id-ID').format(alokasi)}</span>
                                                               <span className="text-[13px] font-black text-white bg-red-700 px-6 py-3 rounded-[24px] shadow-2xl border-2 border-white/20 scale-105 font-black tracking-widest leading-none">REALITA: Rp {new Intl.NumberFormat('id-ID').format(realisasi)}</span>
                                                           </div>
                                                      </td>
                                                      <td className="px-10 py-12 align-top text-center">
                                                           <div className="flex flex-col items-center space-y-4">
                                                               <span className="bg-red-100 text-red-800 px-6 py-3 rounded-2xl text-[12px] font-black uppercase shadow-xl border-2 border-red-200 animate-pulse tracking-widest">
                                                                    {isOver ? 'OVER BUDGET' : (isMissing ? 'ALOKASI 0' : 'DATA ANGG. INVALID')}
                                                               </span>
                                                           </div>
                                                      </td>
                                                  </tr>
                                              );
                                          }) : (
                                              <tr><td colSpan="4" className="text-center py-48 text-gray-300 font-black uppercase tracking-[0.8em] italic opacity-40 animate-pulse">INTEGRITAS DATA ANGGARAN AMAN</td></tr>
                                          )}
                                      </tbody>
                                  </table>
                                  {renderPagination()}
                             </div>
                        )}
                    </div>
                </div>

            </main>
        </AuthenticatedLayout>
    );
}

function floatval(val) {
    if (typeof val === 'string') return parseFloat(val.replace(/[^0-9.-]+/g, "")) || 0;
    return parseFloat(val) || 0;
}


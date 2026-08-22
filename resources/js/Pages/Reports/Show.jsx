import React, { useState } from "react";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout";
import { Head, Link, useForm } from "@inertiajs/react";
import { ArrowLeftIcon, ExclamationTriangleIcon, CheckCircleIcon, ChartBarIcon, PencilSquareIcon } from "@heroicons/react/24/outline";
import Modal from '@/Components/Modal';

export default function Show({ report, userRole, allowImport, canEdit, projects }) {
  
  const [showImportModal, setShowImportModal] = useState(false);
  const [showSyncModal, setShowSyncModal] = useState(false);
  const [showUpdateModal, setShowUpdateModal] = useState(false);
  const [selectedActivity, setSelectedActivity] = useState(null);

  const { data: syncData, setData: setSyncData, post: postSync, processing: syncProcessing, errors: syncErrors } = useForm({ project_id: report.project_id || '' });

  const handleSync = (e) => { e.preventDefault(); postSync(route('reports.pull', report.id), { onSuccess: () => setShowSyncModal(false) }); };

  const { data: importData, setData: setImportData, post: postImport, processing: importProcessing, errors: importErrors } = useForm({
      file: null
  });

  const { data: updateData, setData: setUpdateData, put: putUpdate, processing: updateProcessing, reset: resetUpdate } = useForm({
      realisasi_start_date: '',
        realisasi_end_date: '',
        status_akhir: 'Belum Mulai',
      kendala: '',
      mitigasi: ''
  });

  const handleImport = (e) => {
      e.preventDefault();
      postImport(route('reports.import_excel', report.id), {
          onSuccess: () => setShowImportModal(false)
      });
  };

  const openUpdateModal = (activity) => {
      setSelectedActivity(activity);
      setUpdateData({
          realisasi_start_date: activity.realisasi_start_date || '',
            realisasi_end_date: activity.realisasi_end_date || '',
            status_akhir: activity.status_akhir || 'Belum Mulai',
          kendala: activity.kendala || '',
          mitigasi: activity.mitigasi || ''
      });
      setShowUpdateModal(true);
  };

  const handleUpdateSubmit = (e) => {
      e.preventDefault();
      putUpdate(route('reports.activities.update', { id: report.id, activity: selectedActivity.id }), {
          onSuccess: () => {
              setShowUpdateModal(false);
              resetUpdate();
          }
      });
  };

  const today = new Date();
  const isDelayed = (activity) => {
      if (activity.status_akhir === 'Selesai' || activity.status_akhir === 'Sudah') return false;
      if (parseFloat(activity.percent_complete) >= 100) return false;
      if (!activity.rencana_end_date) return false;
      const end = new Date(activity.rencana_end_date);
      return today > end;
  };

  const delayedActivities = report.activities.filter(isDelayed);
  const onTrackActivities = report.activities.filter(a => !isDelayed(a));
  
  const totalProgress = report.activities.length > 0 
      ? report.activities.reduce((acc, curr) => acc + parseFloat(curr.percent_complete || 0), 0) / report.activities.length
      : 0;

  const getStatusBadge = (status) => {
      switch(status) {
          case 'Selesai':
              return <span className="bg-emerald-100 text-emerald-800 px-2 py-1 rounded text-xs font-bold">Selesai</span>;
          case 'Proses':
              return <span className="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-bold">Proses</span>;
          default:
              return <span className="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs font-bold">Belum Mulai</span>;
      }
  };

  return (
    <AuthenticatedLayout>
      <Head title="Detail Realisasi" />
      
      <div className="min-h-screen bg-gray-50 pb-20">
        <div className="bg-white border-b sticky top-0 z-30">
          <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div className="flex items-center justify-between h-20">
              <div className="flex items-center gap-4">
                <Link
                  href={route("reports.index")}
                  className="w-10 h-10 rounded-full bg-gray-50 flex items-center justify-center text-gray-400 hover:bg-gray-100 hover:text-gray-600 transition"
                >
                  <ArrowLeftIcon className="w-5 h-5" />
                </Link>
                <div>
                  <h1 className="text-xl font-black text-gray-900 tracking-tight">
                    Laporan Kinerja Bulanan
                  </h1>
                  <p className="text-xs font-bold text-gray-400 uppercase tracking-widest mt-1">
                    Proyek: {report.project?.name} | Periode: {report.period?.month_year || '-'}
                  </p>
                </div>
              </div>
              <div className="flex gap-2">
                                {canEdit && (
                    <button 
                        onClick={() => setShowSyncModal(true)}
                        className="px-4 py-2 bg-blue-100 text-blue-800 rounded-md font-bold text-sm hover:bg-blue-200 transition"
                    >
                        Tarik Data Perencanaan
                    </button>
                )}
                {allowImport && canEdit && (
                    <button 
                        onClick={() => setShowImportModal(true)}
                        className="px-4 py-2 bg-emerald-100 text-emerald-800 rounded-md font-bold text-sm hover:bg-emerald-200 transition"
                    >
                        Import Excel
                    </button>
                )}
              </div>
            </div>
          </div>
        </div>

        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-8">
          
          {/* Executive Dashboard */}
          <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
              <div className="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
                  <div className="w-14 h-14 rounded-full bg-blue-50 flex items-center justify-center text-blue-600">
                      <ChartBarIcon className="w-7 h-7" />
                  </div>
                  <div>
                      <p className="text-sm font-bold text-gray-400 uppercase tracking-widest">Total Progress</p>
                      <h2 className="text-3xl font-black text-gray-900">{totalProgress.toFixed(1)}%</h2>
                  </div>
              </div>
              <div className="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
                  <div className="w-14 h-14 rounded-full bg-red-50 flex items-center justify-center text-red-600">
                      <ExclamationTriangleIcon className="w-7 h-7" />
                  </div>
                  <div>
                      <p className="text-sm font-bold text-gray-400 uppercase tracking-widest">Task Terlambat</p>
                      <h2 className="text-3xl font-black text-gray-900">{delayedActivities.length}</h2>
                  </div>
              </div>
              <div className="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
                  <div className="w-14 h-14 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-600">
                      <CheckCircleIcon className="w-7 h-7" />
                  </div>
                  <div>
                      <p className="text-sm font-bold text-gray-400 uppercase tracking-widest">Task On-Track</p>
                      <h2 className="text-3xl font-black text-gray-900">{onTrackActivities.length}</h2>
                  </div>
              </div>
          </div>

          {/* Daftar Semua Kegiatan */}
          <div className="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden mb-8">
            <div className="p-6 border-b border-gray-100 flex justify-between items-center">
                <h2 className="text-lg font-black text-gray-900">Daftar Kegiatan untuk Diisi Realisasinya</h2>
            </div>
            <div className="overflow-x-auto">
                <table className="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th className="px-6 py-4 text-[10px] font-black text-blue-50 uppercase tracking-widest border-b border-blue-800 bg-blue-900">Kegiatan</th>
                            <th className="px-6 py-4 text-[10px] font-black text-blue-50 uppercase tracking-widest border-b border-blue-800 bg-blue-900">Jadwal Rencana</th>
                            <th className="px-6 py-4 text-[10px] font-black text-blue-50 uppercase tracking-widest border-b border-blue-800 bg-blue-900">Tgl Realisasi Selesai</th>
                            <th className="px-6 py-4 text-[10px] font-black text-blue-50 uppercase tracking-widest border-b border-blue-800 bg-blue-900">Status Kegiatan</th>
                            <th className="px-6 py-4 text-[10px] font-black text-blue-50 uppercase tracking-widest border-b border-blue-800 bg-blue-900">Kendala & Mitigasi</th>
                            <th className="px-6 py-4 text-center text-[10px] font-black text-blue-50 uppercase tracking-widest border-b border-blue-800 bg-blue-900">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {report.activities.map((act) => (
                            <tr key={act.id} className="hover:bg-gray-50 transition border-b border-gray-100 group">
                                <td className="px-6 py-4">
                                    <div className="text-sm font-bold text-gray-900">{act.nama_kegiatan_turunan}</div>
                                    <div className="text-xs text-gray-500 mt-1 max-w-xs truncate">{act.deskripsi_kegiatan}</div>
                                </td>
                                <td className="px-6 py-4 text-sm text-gray-500 font-medium">
                                    {act.rencana_start_date} <br/> s/d {act.rencana_end_date}
                                </td>
                                <td className="px-6 py-4">
                                    {act.realisasi_end_date ? (
                                        <div className="text-sm font-bold text-gray-800">{act.realisasi_end_date}</div>
                                    ) : (
                                        <div className="text-xs text-red-500 italic">Belum diisi</div>
                                    )}
                                </td>
                                <td className="px-6 py-4">
                                    {getStatusBadge(act.status_akhir)}
                                </td>
                                <td className="px-6 py-4 text-xs text-gray-500 max-w-xs truncate">
                                    {act.kendala || act.mitigasi ? (
                                        <div>
                                            <span className="font-bold text-gray-700">K:</span> {act.kendala || '-'}<br/>
                                            <span className="font-bold text-gray-700">M:</span> {act.mitigasi || '-'}
                                        </div>
                                    ) : (
                                        '-'
                                    )}
                                </td>
                                <td className="px-6 py-4 text-center">
                                    <button 
                                        onClick={() => openUpdateModal(act)}
                                        className="inline-flex items-center gap-1 px-4 py-2 bg-blue-50 text-blue-700 text-xs font-bold rounded-xl hover:bg-blue-600 hover:text-white transition shadow-sm border border-blue-100"
                                    >
                                        <PencilSquareIcon className="w-4 h-4" />
                                        Isi Data
                                    </button>
                                </td>
                            </tr>
                        ))}
                    </tbody>
                </table>
            </div>
          </div>

        </div>
      </div>

            {/* Modal Sync */}
      <Modal show={showSyncModal} onClose={() => setShowSyncModal(false)}>
          <div className="p-6">
              <h2 className="text-lg font-bold text-gray-900 mb-4">Tarik Data Perencanaan (Sinkronisasi)</h2>
              <p className="text-sm text-gray-600 mb-6">Sistem akan menarik daftar task dari Gantt Chart ke Laporan ini sebagai Snapshot.</p>
              <form onSubmit={handleSync}>
                  <div className="mb-6">
                      <label className="block text-sm font-medium text-gray-700 mb-2">Pilih Proyek Sumber</label>
                      <select 
                          className="block w-full text-sm border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                          value={syncData.project_id}
                          onChange={e => setSyncData('project_id', e.target.value)}
                          required
                      >
                          <option value="">-- Pilih Proyek --</option>
                          {projects && projects.map(p => (
                              <option key={p.id} value={p.id}>{p.name}</option>
                          ))}
                      </select>
                      {syncErrors.project_id && <div className="text-red-500 text-xs mt-1">{syncErrors.project_id}</div>}
                  </div>
                  <div className="flex justify-end space-x-2">
                      <button type="button" onClick={() => setShowSyncModal(false)} className="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md text-sm font-bold">Batal</button>
                      <button type="submit" disabled={syncProcessing} className="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-md text-sm font-bold">Tarik Data</button>
                  </div>
              </form>
          </div>
      </Modal>

      {/* Modal Import Excel */}
      <Modal show={showImportModal} onClose={() => setShowImportModal(false)}>
          <div className="p-6">
              <h2 className="text-lg font-bold text-gray-900 mb-4">Import Data Laporan (Excel)</h2>
              <form onSubmit={handleImport}>
                  <div className="mb-4">
                      <label className="block text-sm font-medium text-gray-700 mb-2">Upload File Excel</label>
                      <input 
                          type="file" 
                          accept=".xlsx,.xls,.csv"
                          onChange={e => setImportData('file', e.target.files[0])}
                          className="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700"
                          required
                      />
                      {importErrors.file && <div className="text-red-500 text-xs mt-1">{importErrors.file}</div>}
                  </div>
                  <div className="flex justify-end space-x-2">
                      <button type="button" onClick={() => setShowImportModal(false)} className="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded-md text-sm font-bold">Batal</button>
                      <button type="submit" disabled={importProcessing} className="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-md text-sm font-bold">Import Data</button>
                  </div>
              </form>
          </div>
      </Modal>

      {/* Modal Update Realisasi */}
      <Modal show={showUpdateModal} onClose={() => setShowUpdateModal(false)}>
          <div className="p-6">
              <h2 className="text-lg font-black text-gray-900 mb-4 tracking-tight">Form Pengisian Realisasi</h2>
              
              {selectedActivity && (
                  <div className="mb-6 bg-blue-50 p-4 rounded-xl border border-blue-100">
                      <p className="text-xs font-black text-blue-400 uppercase tracking-widest mb-1">Kegiatan Terpilih</p>
                      <p className="text-sm font-bold text-blue-900">{selectedActivity.nama_kegiatan_turunan}</p>
                      <p className="text-xs text-blue-700 mt-1">Jadwal Rencana: {selectedActivity.rencana_start_date} s/d {selectedActivity.rencana_end_date}</p>
                  </div>
              )}

              <form onSubmit={handleUpdateSubmit}>
                  <div className="grid grid-cols-2 gap-4 mb-5">
                      <div>
                          <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tanggal Realisasi Mulai</label>
                          <input 
                              type="date"
                              className="w-full border-gray-200 rounded-xl text-sm font-semibold text-gray-800 focus:ring-blue-500 focus:border-blue-500 shadow-inner p-3"
                              value={updateData.realisasi_start_date}
                              onChange={e => setUpdateData('realisasi_start_date', e.target.value)}
                          />
                      </div>
                      <div>
                          <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Tanggal Realisasi Selesai</label>
                          <input 
                              type="date"
                              className="w-full border-gray-200 rounded-xl text-sm font-semibold text-gray-800 focus:ring-blue-500 focus:border-blue-500 shadow-inner p-3"
                              value={updateData.realisasi_end_date}
                              onChange={e => setUpdateData('realisasi_end_date', e.target.value)}
                          />
                      </div>
                      <div>
                          <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Status Kegiatan</label>
                          <select 
                              className="w-full border-gray-200 rounded-xl text-sm font-semibold text-gray-800 focus:ring-blue-500 focus:border-blue-500 shadow-inner p-3"
                              value={updateData.status_akhir}
                              onChange={e => setUpdateData('status_akhir', e.target.value)}
                          >
                              <option value="Belum Mulai">Belum Mulai</option>
                              <option value="Proses">Proses</option>
                              <option value="Selesai">Selesai</option>
                          </select>
                      </div>
                  </div>

                  <div className="mb-5">
                      <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Kendala / Akar Masalah (Jika Ada)</label>
                      <textarea 
                          className="w-full border-gray-200 rounded-xl text-sm font-semibold text-gray-800 focus:ring-blue-500 focus:border-blue-500 shadow-inner p-4"
                          rows="2"
                          placeholder="Jelaskan kendala yang dialami..."
                          value={updateData.kendala}
                          onChange={e => setUpdateData('kendala', e.target.value)}
                      ></textarea>
                  </div>

                  <div className="mb-6">
                      <label className="block text-[10px] font-black text-gray-400 uppercase tracking-widest mb-2">Rencana Mitigasi / Solusi (Jika Ada Kendala)</label>
                      <textarea 
                          className="w-full border-gray-200 rounded-xl text-sm font-semibold text-gray-800 focus:ring-emerald-500 focus:border-emerald-500 shadow-inner p-4"
                          rows="2"
                          placeholder="Langkah perbaikan atau mitigasi..."
                          value={updateData.mitigasi}
                          onChange={e => setUpdateData('mitigasi', e.target.value)}
                      ></textarea>
                  </div>

                  <div className="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                      <button 
                          type="button" 
                          onClick={() => setShowUpdateModal(false)} 
                          className="px-6 py-3 bg-gray-100 text-gray-700 rounded-xl text-sm font-black uppercase tracking-widest hover:bg-gray-200 transition"
                      >
                          Batal
                      </button>
                      <button 
                          type="submit" 
                          disabled={updateProcessing} 
                          className="px-6 py-3 bg-blue-900 text-white rounded-xl text-sm font-black uppercase tracking-widest hover:bg-blue-950 transition shadow-xl disabled:opacity-50"
                      >
                          {updateProcessing ? 'Menyimpan...' : 'Simpan Realisasi'}
                      </button>
                  </div>
              </form>
          </div>
      </Modal>

    </AuthenticatedLayout>
  );
}



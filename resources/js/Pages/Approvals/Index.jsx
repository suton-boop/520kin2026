import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import Pagination from '@/Components/Pagination';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';

export default function Index({ auth, pending_approvals, userRole }) {
    const [selectedIds, setSelectedIds] = useState([]);

    const handleApprove = (id) => {
        if(confirm('Apakah Anda yakin ingin menyetujui pengajuan ini?')) {
            router.post('/approvals/' + id + '/approve');
        }
    };

    const handleReject = (id) => {
        const reason = prompt('Masukkan alasan penolakan untuk dikembalikan ke staf:');
        if (reason) {
            router.post('/approvals/' + id + '/reject', { reason });
        }
    };

    const handleSelectAll = (e) => {
        if (e.target.checked) {
            setSelectedIds((pending_approvals.data || pending_approvals).map(item => item.id));
        } else {
            setSelectedIds([]);
        }
    };

    const handleSelect = (id) => {
        if (selectedIds.includes(id)) {
            setSelectedIds(selectedIds.filter(selectedId => selectedId !== id));
        } else {
            setSelectedIds([...selectedIds, id]);
        }
    };

    const handleBulkApprove = () => {
        if(confirm('Apakah Anda yakin ingin menyetujui ' + selectedIds.length + ' laporan sekaligus?')) {
            router.post('/approvals/bulk-approve', { submission_ids: selectedIds }, {
                onSuccess: () => setSelectedIds([])
            });
        }
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="text-xl font-semibold leading-tight text-gray-800">Menunggu Persetujuan ({(userRole === 'admin' || userRole === 'super-admin') ? 'Admin Pusat' : 'Manajer Gugus'})</h2>}
        >
            <Head title="Approval" />

            <div className="py-12 relative">
                <div className="mx-auto max-w-7xl sm:px-6 lg:px-8">

                    {selectedIds.length > 0 && (
                        <div className="mb-4 p-4 bg-blue-50 rounded-lg border border-blue-200 flex justify-between items-center shadow-sm">
                            <span className="text-blue-800 font-medium">{selectedIds.length} Laporan dipilih</span>
                            <button 
                                onClick={handleBulkApprove} 
                                className="px-4 py-2 bg-blue-600 text-white font-bold rounded shadow hover:bg-blue-700 transition-colors"
                            >
                                Setujui Semua yang Dipilih
                            </button>
                        </div>
                    )}

                    <div className="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900 border-b border-gray-200">
                            <h3 className="text-lg font-medium mb-4">Daftar Laporan yang Butuh Validasi Anda</h3>
                            
                            <table className="min-w-full divide-y divide-gray-200">
                                <thead className="bg-gray-50">
                                    <tr>
                                        <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            <input 
                                                type="checkbox" 
                                                className="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                                onChange={handleSelectAll}
                                                checked={pending_approvals?.length > 0 && selectedIds.length === pending_approvals.length}
                                            />
                                        </th>
                                        <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Staf</th>
                                        <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Gugus Mutu</th>
                                        <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Periode</th>
                                        <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Pembacaan</th>
                                        <th scope="col" className="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody className="bg-white divide-y divide-gray-200">
                                    {pending_approvals?.length > 0 ? (
                                        (pending_approvals.data || pending_approvals).map((item) => (
                                            <tr key={item.id} className={selectedIds.includes(item.id) ? "bg-blue-50/30" : ""}>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <input 
                                                        type="checkbox" 
                                                        className="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500"
                                                        onChange={() => handleSelect(item.id)}
                                                        checked={selectedIds.includes(item.id)}
                                                    />
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap">{item.user?.name}</td>
                                                <td className="px-6 py-4 whitespace-nowrap">{item.user?.gugus_mutu?.name}</td>
                                                <td className="px-6 py-4 whitespace-nowrap font-bold text-gray-700">{item.period?.month_year}</td>
                                                <td className="px-6 py-4 whitespace-nowrap">
                                                    <span className="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                        {item.approval_status}
                                                    </span>
                                                </td>
                                                <td className="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                                                    {item.approval_status === 'Pending' ? (
                                                        <>
                                                            <button onClick={() => handleApprove(item.id)} className="px-3 py-1 bg-green-500 text-white rounded shadow hover:bg-green-600 transition-colors">Setuju</button>
                                                            <button onClick={() => handleReject(item.id)} className="px-3 py-1 bg-red-500 text-white rounded shadow hover:bg-red-600 transition-colors">Tolak</button>
                                                        </>
                                                    ) : (
                                                        <span className="text-gray-400 italic">Sudah direview</span>
                                                    )}
                                                </td>
                                            </tr>
                                        ))
                                    ) : (
                                        <tr>
                                            <td colSpan="6" className="px-6 py-4 whitespace-nowrap text-center text-sm text-gray-500">
                                                Tidak ada pengajuan yang menunggu persetujuan Anda saat ini.
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                        {pending_approvals && pending_approvals.links && <Pagination links={pending_approvals.links} />}
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}


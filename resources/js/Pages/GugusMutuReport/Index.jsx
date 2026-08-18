import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, router } from '@inertiajs/react';
import { ChartBarIcon, FunnelIcon, ArrowUpIcon, AcademicCapIcon } from '@heroicons/react/24/outline';

export default function Index({ reportData, periods, selectedPeriodId }) {
    
    const handlePeriodChange = (e) => {
        router.get(route('gugus-mutu-report.index'), { period_id: e.target.value }, {
            preserveState: true,
            replace: true
        });
    };

    const getAchievementColor = (achievement) => {
        if (achievement >= 80) return 'bg-emerald-500';
        if (achievement >= 50) return 'bg-amber-500';
        return 'bg-rose-500';
    };

    const getAchievementBg = (achievement) => {
        if (achievement >= 80) return 'bg-emerald-50';
        if (achievement >= 50) return 'bg-amber-50';
        return 'bg-rose-50';
    };

    const getAchievementText = (achievement) => {
        if (achievement >= 80) return 'text-emerald-700';
        if (achievement >= 50) return 'text-amber-700';
        return 'text-rose-700';
    };

    return (
        <AuthenticatedLayout>
            <Head title="Capaian Gugus Mutu" />

            <div className="py-12 bg-gray-50/50 min-h-screen">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    {/* Header Section */}
                    <div className="flex flex-col md:flex-row md:items-center justify-between mb-8 space-y-4 md:space-y-0">
                        <div>
                            <h1 className="text-3xl font-black text-blue-900 flex items-center gap-3 tracking-tighter uppercase">
                                <AcademicCapIcon className="h-8 w-8 text-amber-500" />
                                Capaian Gugus Mutu
                            </h1>
                            <p className="mt-1 text-sm text-gray-500 font-medium tracking-tight">
                                Monitoring performa dan ketercapaian target per unit kerja secara real-time.
                            </p>
                        </div>
                        
                        <div className="flex items-center space-x-3 bg-white p-2 rounded-2xl shadow-sm border border-gray-100">
                            <div className="p-2 bg-blue-50 rounded-lg">
                                <FunnelIcon className="h-5 w-5 text-blue-600" />
                            </div>
                            <select 
                                value={selectedPeriodId || ''} 
                                onChange={handlePeriodChange}
                                className="border-none focus:ring-0 text-sm font-bold bg-transparent pr-8 text-blue-900 cursor-pointer"
                            >
                                {periods.map(period => (
                                    <option key={period.id} value={period.id}>
                                        Periode: {period.month_year}
                                    </option>
                                ))}
                            </select>
                        </div>
                    </div>

                    {/* Stats Overview */}
                    <div className="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div className="bg-white p-6 rounded-3xl shadow-sm border border-gray-100 hover:shadow-md transition-all group overflow-hidden relative">
                            <div className="absolute top-0 right-0 w-32 h-32 bg-blue-50 rounded-full -mr-16 -mt-16 transition-all group-hover:scale-110 opacity-50" />
                            <div className="relative">
                                <div className="p-3 bg-blue-100 rounded-2xl w-fit mb-4">
                                    <ChartBarIcon className="h-6 w-6 text-blue-600" />
                                </div>
                                <h3 className="text-gray-500 text-xs font-bold uppercase tracking-widest">Rata-rata Capaian</h3>
                                <div className="flex items-baseline mt-1 space-x-2">
                                    <span className="text-3xl font-black text-blue-900 tracking-tighter">
                                        {reportData.length > 0 ? (reportData.reduce((acc, curr) => acc + curr.achievement, 0) / reportData.length).toFixed(2) : 0}%
                                    </span>
                                </div>
                            </div>
                        </div>
                        {/* Other stats could go here */}
                    </div>

                    {/* Main Table */}
                    <div className="bg-white rounded-[2rem] shadow-xl shadow-blue-900/5 border border-gray-100 overflow-hidden">
                        <div className="overflow-x-auto">
                            <table className="w-full text-left border-collapse">
                                <thead>
                                    <tr className="bg-gray-50/50 border-b border-gray-100">
                                        <th className="px-8 py-5 text-xs font-black text-gray-500 uppercase tracking-widest">Gugus Mutu</th>
                                        <th className="px-8 py-5 text-xs font-black text-gray-500 uppercase tracking-widest">Target</th>
                                        <th className="px-8 py-5 text-xs font-black text-gray-500 uppercase tracking-widest">Capaian</th>
                                        <th className="px-8 py-5 text-xs font-black text-gray-500 uppercase tracking-widest">Ketercapaian</th>
                                        <th className="px-8 py-5 text-xs font-black text-gray-500 uppercase tracking-widest text-right">Status</th>
                                    </tr>
                                </thead>
                                <tbody className="divide-y divide-gray-50">
                                    {reportData.map((data) => (
                                        <tr key={data.id} className="hover:bg-blue-50/30 transition-colors group">
                                            <td className="px-8 py-6">
                                                <div className="flex items-center space-x-3">
                                                    <div className="w-10 h-10 rounded-xl bg-blue-900 flex items-center justify-center text-amber-400 font-black text-sm shadow-lg group-hover:scale-110 transition-transform">
                                                        {data.name.charAt(0)}
                                                    </div>
                                                    <div>
                                                        <div className="text-sm font-black text-blue-900 uppercase tracking-tight">{data.name}</div>
                                                        <div className="text-[10px] text-gray-400 font-bold uppercase tracking-widest">Unit Kerja Terdaftar</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td className="px-8 py-6">
                                                <div className="text-sm font-bold text-gray-600 tracking-tight">{data.total_target.toLocaleString()}</div>
                                            </td>
                                            <td className="px-8 py-6">
                                                <div className="text-sm font-bold text-blue-700 tracking-tight">{data.total_capaian.toLocaleString()}</div>
                                            </td>
                                            <td className="px-8 py-6 w-1/4">
                                                <div className="flex items-center space-x-3">
                                                    <div className="flex-1 h-2 bg-gray-100 rounded-full overflow-hidden">
                                                        <div 
                                                            className={`h-full rounded-full transition-all duration-1000 ${getAchievementColor(data.achievement)}`}
                                                            style={{ width: `${Math.min(data.achievement, 100)}%` }}
                                                        />
                                                    </div>
                                                    <span className={`text-sm font-black italic tracking-tighter ${getAchievementText(data.achievement)}`}>
                                                        {data.achievement}%
                                                    </span>
                                                </div>
                                            </td>
                                            <td className="px-8 py-6 text-right">
                                                <span className={`px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest border ${getAchievementBg(data.achievement)} ${getAchievementText(data.achievement)} border-current opacity-80`}>
                                                    {data.achievement >= 80 ? 'Excellence' : data.achievement >= 50 ? 'On Track' : 'Needs Attention'}
                                                </span>
                                            </td>
                                        </tr>
                                    ))}
                                    {reportData.length === 0 && (
                                        <tr>
                                            <td colSpan="5" className="px-8 py-20 text-center">
                                                <div className="flex flex-col items-center justify-center space-y-3">
                                                    <AcademicCapIcon className="h-12 w-12 text-gray-200" />
                                                    <p className="text-gray-400 font-bold uppercase text-[10px] tracking-widest uppercase">Belum ada data capaian untuk periode ini</p>
                                                </div>
                                            </td>
                                        </tr>
                                    )}
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}


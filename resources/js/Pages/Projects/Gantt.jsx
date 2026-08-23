import React, { useEffect, useRef, useState } from 'react';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';

import gantt from 'dhtmlx-gantt';

export default function Gantt({ project }) {
    const ganttContainer = useRef(null);
    const [showImportModal, setShowImportModal] = useState(false);
    const { data, setData, post, processing, errors, reset } = useForm({ file: null });
    const [scale, setScale] = useState('day');

        const handleScaleChange = (e) => {
        const val = e.target.value;
        setScale(val);
        if (val === 'month') {
            gantt.config.scales = [
                {unit: "year", step: 1, format: "%Y"},
                {unit: "month", step: 1, format: "%M"}
            ];
            gantt.config.min_column_width = 80;
        } else {
            gantt.config.scales = [
                {unit: "month", step: 1, format: "%F %Y"},
                {unit: "day", step: 1, format: "%d"}
            ];
            gantt.config.min_column_width = 40;
        }
        gantt.render();
    };

    const handleImportSubmit = (e) => {
        e.preventDefault();
        post(route('projects.tasks.import', project.id), {
            onSuccess: () => {
                setShowImportModal(false);
                reset('file');
                gantt.clearAll();
                    window.axios.get(route('projects.gantt_data', project.id) + '?t=' + new Date().getTime()).then(resData => {
                            gantt.parse(resData.data);
                            if (res.data && res.data.tid) {
                                setTimeout(() => { try { gantt.showTask(res.data.tid); gantt.selectTask(res.data.tid); gantt.showLightbox(res.data.tid); } catch(e){ console.error(e); } }, 200);
                            }
                        }).catch(parseErr => alert("Parse error: " + parseErr.message));
            }
        });
    };

    useEffect(() => {
                gantt.plugins({
            pagination: true
        });
        gantt.config.page_size = 15;
        gantt.config.pager = {
            container: "gantt_pager"
        };
        // initial scale
        gantt.config.scales = [
            {unit: "month", step: 1, format: "%F %Y"},
            {unit: "day", step: 1, format: "%d"}
        ];
        gantt.config.min_column_width = 40;
        gantt.config.date_format = "%Y-%m-%d %H:%i:%s";
            gantt.config.show_errors = false;
        gantt.config.auto_scheduling = false;
        gantt.config.auto_scheduling_strict = false;
        gantt.config.work_time = true;

        gantt.config.columns = [
            {name: "wbs_code", label: "WBS", width: 60, template: function(task){ return task.wbs_code || ""; }},
            {name: "text", label: "Task Name", tree: true, width: 200, resize: true},
            {name: "start_date", label: "Start", align: "center", width: 90, resize: true},
            {name: "end_date", label: "Finish", align: "center", width: 90, resize: true},
            {name: "duration", label: "Durasi", align: "center", width: 60},
            {name: "add", label: "+", width: 44},
            {name: "buttons", label: "Aksi", width: 90, template: function(task) {
                return '<button class="edit-btn" style="color:blue; font-size:12px; font-weight:bold; margin-right:8px;" data-action="edit">Edit</button>' +
                       '<button class="del-btn" style="color:red; font-size:12px; font-weight:bold;" data-action="delete">Del</button>';
            }}
        ];

        
        gantt.attachEvent("onGridHeaderClick", function(name, e){
            if (name === "add") {
                window.axios.post(route('projects.task.store', project.id), {
                    text: "Task Baru",
                    start_date: project.start_date ? project.start_date : new Date().toISOString().slice(0,10),
                    duration: 1,
                    parent: 0,
                    progress: 0
                }).then(res => {
                    gantt.clearAll();
                    window.axios.get(route('projects.gantt_data', project.id) + '?t=' + new Date().getTime()).then(resData => {
                            gantt.parse(resData.data);
                            if (res.data && res.data.tid) {
                                setTimeout(() => { try { gantt.showTask(res.data.tid); gantt.selectTask(res.data.tid); gantt.showLightbox(res.data.tid); } catch(e){ console.error(e); } }, 200);
                            }
                        }).catch(parseErr => alert("Parse error: " + parseErr.message));
                }).catch(err => {
                    alert("Error: " + (err.response?.status || "") + " " + (err.response?.data?.message || err.message));
                });
                return false;
            }
            return true;
        });

        gantt.attachEvent("onTaskCreated", function(task){
            window.axios.post(route('projects.task.store', project.id), {
                text: "Task Baru",
                start_date: project.start_date ? project.start_date : new Date().toISOString().slice(0,10),
                duration: 1,
                parent: task.parent || 0,
                progress: 0
            }).then(res => {
                gantt.clearAll();
                window.axios.get(route('projects.gantt_data', project.id) + '?t=' + new Date().getTime()).then(resData => {
                            gantt.parse(resData.data);
                            if (res.data && res.data.tid) {
                                setTimeout(() => { try { gantt.showTask(res.data.tid); gantt.selectTask(res.data.tid); gantt.showLightbox(res.data.tid); } catch(e){ console.error(e); } }, 200);
                            }
                        }).catch(parseErr => alert("Parse error: " + parseErr.message));
            }).catch(err => {
                alert("Error: " + (err.response?.status || "") + " " + (err.response?.data?.message || err.message));
            });
            return false;
        });

        gantt.attachEvent("onTaskClick", function(id, e){
            var button = e.target.closest("[data-action]");
            if(button){
                var action = button.getAttribute("data-action");
                if(action === "edit"){
                    gantt.showLightbox(id);
                    return false;
                }
                if(action === "delete"){
                    gantt.confirm({
                        text: "Hapus task ini?",
                        ok: "Ya",
                        cancel: "Batal",
                        callback: function(result){
                            if(result){
                                gantt.deleteTask(id);
                            }
                        }
                    });
                    return false;
                }
            }
            return true;
        });

        gantt.init(ganttContainer.current);
        
        // Load data from backend
        gantt.load(route('projects.gantt_data', project.id) + '?t=' + new Date().getTime());

        // Setup DataProcessor to sync with Laravel backend
        const dp = gantt.createDataProcessor({
            
            url: route('projects.gantt_data', project.id).replace('/gantt-data', ''),
            mode: "REST",
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            },
            headers: {
                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
            }
        });

        return () => {
            dp.destructor();
            gantt.clearAll();
        };
    }, [project.id]);

    return (
        <AuthenticatedLayout>
            <Head title={`Gantt - {project.name}`} />
            <div className="h-screen w-full flex flex-col bg-white">
                <div className="p-4 bg-gray-50 border-b flex justify-between items-center">
                    <h2 className="text-xl font-bold text-gray-800">Perencanaan Project: {project.name}</h2>
                    <div className="space-x-2">
                        <a href={route('projects.index')} className="px-3 py-1 bg-gray-200 text-gray-700 rounded text-sm hover:bg-gray-300 font-semibold inline-block">&larr; Kembali</a>
                          <button className="px-3 py-1 bg-blue-100 text-blue-800 rounded text-sm hover:bg-blue-200 font-semibold" onClick={() => {
                            window.axios.post(route('projects.task.store', project.id), {
                                text: "Task Baru",
                                start_date: project.start_date ? project.start_date : new Date().toISOString().slice(0,10),
                                duration: 1,
                                parent: 0,
                                progress: 0
                            }).then(res => {
                                gantt.clearAll();
                    window.axios.get(route('projects.gantt_data', project.id) + '?t=' + new Date().getTime()).then(resData => {
                            gantt.parse(resData.data);
                            if (res.data && res.data.tid) {
                                setTimeout(() => { try { gantt.showTask(res.data.tid); gantt.selectTask(res.data.tid); gantt.showLightbox(res.data.tid); } catch(e){ console.error(e); } }, 200);
                            }
                        }).catch(parseErr => alert("Parse error: " + parseErr.message));
                            }).catch(err => {
                                alert("Error: " + (err.response?.status || "") + " " + (err.response?.data?.message || err.message));
                            });
                        }}>+ Tambah WBS/Task</button>
                        <button className="px-3 py-1 bg-green-100 text-green-800 rounded text-sm hover:bg-green-200 font-semibold" onClick={() => setShowImportModal(true)}>Import Excel</button>
                        <select value={scale} onChange={handleScaleChange} className="px-3 py-1 bg-white border border-gray-300 text-gray-800 rounded text-sm font-semibold">
                            <option value="day">Harian (Tgl)</option>
                            <option value="month">Bulanan</option>
                        </select>
                        <button className="px-3 py-1 bg-gray-200 text-gray-800 rounded text-sm hover:bg-gray-300" onClick={() => gantt.autoSchedule()}>Auto Schedule</button>
                    </div>
                </div>
                <div 
                    ref={ganttContainer} 
                    className="flex-1 w-full"
                    style={{ minHeight: '600px' }}
                ></div>
                <div id="gantt_pager" className="w-full flex justify-center p-2 bg-white border-t border-gray-200 gantt-pagination-container"></div>
            </div>
        
            {showImportModal && (
                <div className="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50">
                    <div className="bg-white rounded-lg shadow-xl w-full max-w-md p-6">
                        <h3 className="text-lg font-medium text-gray-900 mb-4">Import Data Perencanaan (Excel)</h3>
                        <form onSubmit={handleImportSubmit}>
                            <div className="mb-4">
                                <label className="block text-sm font-medium text-gray-700 mb-2">Pilih file Excel MS Project (.xlsx)</label>
                                <input
                                    type="file"
                                    accept=".xlsx,.xls,.csv"
                                    onChange={(e) => setData('file', e.target.files[0])}
                                    className="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                />
                                {errors.file && <div className="text-red-500 text-sm mt-1">{errors.file}</div>}
                            </div>
                            <div className="flex justify-between items-center mt-2">
                                <a href="/users/export-template" className="text-sm text-indigo-600 hover:text-indigo-800 underline font-medium" target="_blank" download>
                                    Download Template
                                </a>
                                <div className="flex gap-2">
                                <button
                                    type="button"
                                    onClick={() => { setShowImportModal(false); reset(); }}
                                    className="px-4 py-2 bg-gray-300 text-gray-700 rounded-md hover:bg-gray-400 transition"
                                >
                                    Batal
                                </button>
                                <button
                                    type="submit"
                                    disabled={processing}
                                    className="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 transition disabled:opacity-50"
                                >
                                    {processing ? 'Mengimpor...' : 'Import'}
                                </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            )}
        </AuthenticatedLayout>
    );
}







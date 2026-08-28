<?php

namespace App\Http\Controllers;

use App\Models\Anggaran;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Auth;

class AnggaranController extends Controller
{
    
    
    public function downloadTemplate()
    {
        return \Excel::download(new \App\Exports\AnggaranTemplateExport, 'Template_Import_WBS_Anggaran.xlsx');
    }

    public function import(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole(['admin', 'super-admin', 'superadmin'])) {
            return redirect()->back()->with('error', 'Hanya Admin yang dapat mengimpor anggaran.');
        }

        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv|max:10240',
        ]);

        try {
            Excel::import(new AppImportsAnggaranImport, $request->file('file'));
            return redirect()->back()->with('success', 'Data Anggaran berhasil diimpor.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', 'Terjadi kesalahan saat impor: ' . $e->getMessage());
        }
    }

    public function export(Request $request)
    {
        $query = Anggaran::query();

        if ($request->has('search') && $request->search !== '') {
            $searchTerm = '%' . $request->search . '%';
            $query->where('kode_pmo', 'like', $searchTerm)
                  ->orWhere('kode_rrkl', 'like', $searchTerm);
        }

        $anggarans = $query->get();
        return \Excel::download(new \App\Exports\AnggaransExport($anggarans), 'Daftar_Anggaran.xlsx');
    }

    public function index()
    {
        $user = Auth::user();
        $isAdmin = $user->hasRole(['admin', 'super-admin', 'superadmin']);

        // Only admins can see inactive ones? Or everyone sees inactive ones but dimmed?
        // Let's pass all to frontend, frontend handles display of active/inactive
        $data = Anggaran::whereNull('parent_id')->with('children.children')->orderBy('id')->get();
        
        // Calculate percent and auto-sum from children
        $data->transform(function ($parent) {
            
            $parent->children->transform(function ($child) {
                if ($child->children && $child->children->count() > 0) {
                    $child->volume_realisasi = $child->children->sum('volume_realisasi');
                    $child->anggaran_realisasi = $child->children->sum('anggaran_realisasi');
                    $child->anggaran_alokasi = $child->children->sum('anggaran_alokasi');
                }

                $child->anggaran_persen = $child->anggaran_alokasi > 0 
                    ? round(($child->anggaran_realisasi / $child->anggaran_alokasi) * 100, 1) 
                    : 0;
                if (!$child->kelengkapan) {
                    $child->kelengkapan = array_fill(0, 12, true);
                }

                if ($child->children) {
                    $child->children->transform(function ($grandchild) {
                        $grandchild->anggaran_persen = $grandchild->anggaran_alokasi > 0 
                            ? round(($grandchild->anggaran_realisasi / $grandchild->anggaran_alokasi) * 100, 1) 
                            : 0;
                        if (!$grandchild->kelengkapan) {
                            $grandchild->kelengkapan = array_fill(0, 12, true);
                        }
                        return $grandchild;
                    });
                }
                return $child;
            });

            // Auto sum from children
            if ($parent->children && $parent->children->count() > 0) {
                $parent->volume_realisasi = $parent->children->sum('volume_realisasi');
                $parent->anggaran_realisasi = $parent->children->sum('anggaran_realisasi');
                $parent->anggaran_alokasi = $parent->children->sum('anggaran_alokasi');
            }

            $parent->anggaran_persen = $parent->anggaran_alokasi > 0 
                ? round(($parent->anggaran_realisasi / $parent->anggaran_alokasi) * 100, 1) 
                : 0;
            
            if (!$parent->kelengkapan) {
                 $parent->kelengkapan = array_fill(0, 12, true);
            }
                
            return $parent;
        });

        return Inertia::render('Anggaran/Index', [
            'anggaranData' => $data,
            'isAdmin' => $isAdmin
        ]);
    }

    public function store(Request $request)
    {
        $user = Auth::user();
        if (!$user->hasRole(['admin', 'super-admin', 'superadmin'])) {
            return redirect()->back()->with('error', 'Hanya Admin yang dapat menambahkan anggaran.');
        }

        $validated = $request->validate([
            'parent_id' => 'nullable|exists:anggarans,id',
            'urut' => 'nullable|integer',
            'kode' => 'required|string|max:255',
            'tipe' => 'nullable|string|max:255',
            'nomenklatur' => 'required|string',
            'satuan' => 'nullable|string',
            'volume' => 'required|string',
            'volume_realisasi' => 'nullable|string',
            'pelaksanaan' => 'required|numeric',
            'anggaran_alokasi' => 'required|numeric',
            'anggaran_realisasi' => 'required|numeric',
            'kelengkapan' => 'nullable|array',
        ]);

        if (empty($validated['kelengkapan'])) {
            $validated['kelengkapan'] = array_fill(0, 12, false);
        }

        Anggaran::create($validated);

        return redirect()->back()->with('success', 'Data Anggaran berhasil ditambahkan.');
    }

    public function update(Request $request, Anggaran $anggaran)
    {
        $user = Auth::user();
        if (!$user->hasRole(['admin', 'super-admin', 'superadmin'])) {
            return redirect()->back()->with('error', 'Hanya Admin yang dapat mengubah anggaran.');
        }

        $validated = $request->validate([
            'parent_id' => 'nullable|exists:anggarans,id',
            'urut' => 'nullable|integer',
            'kode' => 'required|string|max:255',
            'tipe' => 'nullable|string|max:255',
            'nomenklatur' => 'required|string',
            'satuan' => 'nullable|string',
            'volume' => 'required|string',
            'volume_realisasi' => 'nullable|string',
            'pelaksanaan' => 'required|numeric',
            'anggaran_alokasi' => 'required|numeric',
            'anggaran_realisasi' => 'required|numeric',
            'kelengkapan' => 'nullable|array',
        ]);
        
        if (empty($validated['kelengkapan'])) {
            $validated['kelengkapan'] = $anggaran->kelengkapan ?? array_fill(0, 12, false);
        }

        $anggaran->update($validated);

        return redirect()->back()->with('success', 'Data Anggaran berhasil diperbarui.');
    }

    public function toggleActive(Request $request, Anggaran $anggaran)
    {
        $user = Auth::user();
        if (!$user->hasRole(['admin', 'super-admin', 'superadmin'])) {
            return redirect()->back()->with('error', 'Hanya Admin yang dapat mengubah status anggaran.');
        }

        $anggaran->update([
            'is_active' => !$anggaran->is_active
        ]);

        $status = $anggaran->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()->with('success', "Anggaran berhasil $status.");
    }

    public function destroy(Anggaran $anggaran)
    {
        $user = Auth::user();
        if (!$user->hasRole(['admin', 'super-admin', 'superadmin'])) {
            return redirect()->back()->with('error', 'Hanya Admin yang dapat menghapus anggaran.');
        }

        $anggaran->delete();
        return redirect()->back()->with('success', 'Data Anggaran berhasil dihapus.');
    }
}



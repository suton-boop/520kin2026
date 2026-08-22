<?php

namespace App\Http\Controllers;

use App\Models\GugusMutu;
use Illuminate\Http\Request;
use Inertia\Inertia;

class GugusMutuController extends Controller
{
    public function index()
    {
        $gugusMutus = GugusMutu::withCount('users')->orderBy('name')->paginate(15);
        return Inertia::render('GugusMutu/Index', [
            'gugusMutus' => $gugusMutus
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:gugus_mutus',
            'allow_import' => 'boolean',
        ]);

        GugusMutu::create([
            'name' => $validated['name'],
            'allow_import' => $validated['allow_import'] ?? false,
        ]);

        return redirect()->back()->with('success', 'Divisi/GM berhasil ditambahkan.');
    }

    public function update(Request $request, GugusMutu $gugusMutu)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:gugus_mutus,name,' . $gugusMutu->id,
            'allow_import' => 'boolean',
        ]);

        $gugusMutu->update([
            'name' => $validated['name'],
            'allow_import' => $validated['allow_import'] ?? false,
        ]);

        return redirect()->back()->with('success', 'Data Divisi/GM berhasil diperbarui.');
    }

    public function destroy(GugusMutu $gugusMutu)
    {
        // Check if there are users attached to this GM
        if ($gugusMutu->users()->count() > 0) {
            return redirect()->back()->with('error', 'Tidak dapat menghapus Divisi ini karena masih ada pengguna yang tertaut.');
        }

        $gugusMutu->delete();

        return redirect()->back()->with('success', 'Divisi/GM berhasil dihapus.');
    }

    public function toggleImport(GugusMutu $gugusMutu)
    {
        $gugusMutu->update([
            'allow_import' => !$gugusMutu->allow_import
        ]);

        return redirect()->back()->with('success', 'Status import berhasil diperbarui.');
    }
}
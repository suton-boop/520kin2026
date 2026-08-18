<?php
use App\Models\Anggaran;

$data = Anggaran::whereNull('parent_id')->with('children')->orderBy('id')->get();

$data->transform(function ($parent) {
    // Auto sum from children
    $parent->volume_realisasi = $parent->children->sum('volume_realisasi');
    $parent->anggaran_realisasi = $parent->children->sum('anggaran_realisasi');
    $parent->anggaran_alokasi = $parent->children->sum('anggaran_alokasi');

    return $parent;
});

foreach ($data as $parent) {
    echo "Parent: {$parent->nomenklatur} (ID: {$parent->id})\n";
    echo " - Total Volume Realisasi: {$parent->volume_realisasi}\n";
    echo " - Total Anggaran Alokasi: {$parent->anggaran_alokasi}\n";
    echo " - Total Anggaran Realisasi: {$parent->anggaran_realisasi}\n\n";

    if ($parent->children->count() > 0) {
        echo "   Children:\n";
        foreach ($parent->children as $child) {
            echo "   -> Child ID {$child->id} | Vol Real: {$child->volume_realisasi} | Ang Alokasi: {$child->anggaran_alokasi} | Ang Real: {$child->anggaran_realisasi}\n";
        }
    } else {
        echo "   (No children)\n";
    }
    echo "--------------------------\n";
}

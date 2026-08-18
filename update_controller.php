<?php
$file = 'app/Http/Controllers/AnggaranController.php';
$content = file_get_contents($file);

$oldCode = <<<EOT
        // Calculate percent
        \$data->transform(function (\$parent) {
            \$parent->anggaran_persen = \$parent->anggaran_alokasi > 0 
                ? round((\$parent->anggaran_realisasi / \$parent->anggaran_alokasi) * 100, 1) 
                : 0;
            
            if (!\$parent->kelengkapan) {
                 \$parent->kelengkapan = array_fill(0, 12, true);
            }
                
            \$parent->children->transform(function (\$child) {
EOT;

$newCode = <<<EOT
        // Calculate percent and auto-sum from children
        \$data->transform(function (\$parent) {
            // Auto sum from children
            \$parent->volume_realisasi = \$parent->children->sum('volume_realisasi');
            \$parent->anggaran_realisasi = \$parent->children->sum('anggaran_realisasi');
            \$parent->anggaran_alokasi = \$parent->children->sum('anggaran_alokasi');

            \$parent->anggaran_persen = \$parent->anggaran_alokasi > 0 
                ? round((\$parent->anggaran_realisasi / \$parent->anggaran_alokasi) * 100, 1) 
                : 0;
            
            if (!\$parent->kelengkapan) {
                 \$parent->kelengkapan = array_fill(0, 12, true);
            }
                
            \$parent->children->transform(function (\$child) {
EOT;

$content = str_replace($oldCode, $newCode, $content);
file_put_contents($file, $content);
echo "AnggaranController updated with auto-sum.\n";
?>

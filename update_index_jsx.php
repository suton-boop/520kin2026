<?php
$file = 'resources/js/Pages/Anggaran/Index.jsx';
$content = file_get_contents($file);

$oldRow = <<<EOT
                                                      <td className="px-4 py-3 border-r border-gray-100 text-right text-xs"></td>
                                                      <td className="px-4 py-3 border-r border-gray-100 text-right text-xs"></td>
                                                      <td className="px-4 py-3 border-r border-gray-100 text-center"></td>
                                                      <td className="px-4 py-3 border-r border-gray-100 text-center"></td>
EOT;

$newRow = <<<EOT
                                                      <td className="px-4 py-3 border-r border-gray-100 text-right font-bold text-xs">{formatShortRp(parent.anggaran_alokasi)}</td>
                                                      <td className="px-4 py-3 border-r border-gray-100 text-right font-bold text-xs">{formatShortRp(parent.anggaran_realisasi)}</td>
                                                      <td className="px-4 py-3 border-r border-gray-100 text-center font-bold text-xs">{Number(parent.anggaran_persen).toFixed(1)}%</td>
                                                      <td className="px-4 py-3 border-r border-gray-100 text-center"></td>
EOT;

$content = str_replace($oldRow, $newRow, $content);
file_put_contents($file, $content);
echo "Index.jsx updated with parent anggaran display.\n";
?>

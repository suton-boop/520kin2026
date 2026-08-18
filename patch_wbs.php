<?php
$f = 'app/Imports/ProgramImport.php';
$c = file_get_contents($f);
$search = "if (preg_match('/^[A-Z](\\.)?$/i', \$kode) || preg_match('/^[A-Z]\\./i', \$kode)) {";
$replace = "if (preg_match('/^[A-Z](\\.)?$/i', \$kode) || preg_match('/^[A-Z]\\./i', \$kode) || preg_match('/^\d+$/', \$kode)) {";
$c = str_replace($search, $replace, $c);
file_put_contents($f, $c);
echo "Updated!";

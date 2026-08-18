<?php
$file = 'resources/js/Pages/Anggaran/Index.jsx';
$content = file_get_contents($file);

// Remove Chevron from inertiajs
$content = str_replace(
    "import { ChevronRightIcon, ChevronDownIcon, Head", 
    "import { Head", 
    $content
);

// Add Chevron to heroicons
$content = str_replace(
    "import { PlusCircleIcon, MinusCircleIcon", 
    "import { ChevronRightIcon, ChevronDownIcon, PlusCircleIcon, MinusCircleIcon", 
    $content
);

file_put_contents($file, $content);
echo "Import fixed.\n";
?>

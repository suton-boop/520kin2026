<?php
$file = 'resources/js/Pages/Settings/Index.jsx';
$content = file_get_contents($file);
$content = str_replace('className={w-full text-left px-4', 'className={w-full text-left px-4', $content);
$content = str_replace('className={w-full text-left', 'className={w-full text-left', $content);
file_put_contents($file, $content);
echo "JSX syntax fixed.";
?>

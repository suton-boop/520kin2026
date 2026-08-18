<?php
$file = 'routes/web.php';
$content = file_get_contents($file);
if (strpos($content, 'settings.permissions') === false) {
    $content = str_replace("name('settings.features');", "name('settings.features');\n        Route::post('/settings/permissions', [\App\Http\Controllers\SettingController::class, 'updateRolePermissions'])->name('settings.permissions');", $content);
    file_put_contents($file, $content);
    echo "Route added.";
} else {
    echo "Route already exists.";
}

<?php
$file = 'app/Providers/AppServiceProvider.php';
$content = file_get_contents($file);

// Import Gate
if (strpos($content, 'use Illuminate\Support\Facades\Gate;') === false) {
    $content = str_replace(
        "use Illuminate\Support\Facades\Vite;", 
        "use Illuminate\Support\Facades\Vite;\nuse Illuminate\Support\Facades\Gate;", 
        $content
    );
}

// Add Gate::before
$oldBoot = <<<EOT
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
EOT;

$newBoot = <<<EOT
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);

        // Implicitly grant "Super Admin" role all permissions
        // This ensures admins can process everything in emergency situations
        Gate::before(function (\$user, \$ability) {
            return \$user->hasRole(['admin', 'super-admin', 'superadmin']) ? true : null;
        });
    }
EOT;

$content = str_replace($oldBoot, $newBoot, $content);
file_put_contents($file, $content);
echo "AppServiceProvider updated.\n";
?>

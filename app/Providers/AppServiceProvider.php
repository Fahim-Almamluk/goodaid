<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Clean up hot file if Vite dev server is not running
        $this->cleanupHotFile();

        // Register Blade directive for permission check
        Blade::if('hasPermission', function ($permission) {
            return auth()->check() && auth()->user()->hasPermission($permission);
        });
    }

    /**
     * Remove hot file if Vite dev server is not running
     */
    protected function cleanupHotFile(): void
    {
        $hotFile = public_path('hot');
        
        if (File::exists($hotFile)) {
            try {
                $hotUrl = trim(File::get($hotFile));
                $parsedUrl = parse_url($hotUrl);
                
                if (!isset($parsedUrl['host']) || !isset($parsedUrl['port'])) {
                    // Invalid hot file format, remove it
                    File::delete($hotFile);
                    return;
                }
                
                $host = $parsedUrl['host'] === '[::1]' ? 'localhost' : $parsedUrl['host'];
                $port = $parsedUrl['port'] ?? 5173;
                
                // Try to connect to the Vite dev server (timeout after 1 second)
                $connection = @fsockopen($host, $port, $errno, $errstr, 1);
                
                if (!$connection) {
                    // Vite dev server is not running, remove the hot file
                    File::delete($hotFile);
                } else {
                    fclose($connection);
                }
            } catch (\Exception $e) {
                // Error reading/parsing hot file, remove it
                File::delete($hotFile);
            }
        }
    }
}

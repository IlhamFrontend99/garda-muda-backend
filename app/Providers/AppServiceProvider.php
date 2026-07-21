<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // DAFTARKAN FILES & VIEW SERVICE PROVIDER KE SERVICE CONTAINER LARAVEL
        if (!$this->app->bound('files')) {
            $this->app->register(\Illuminate\Filesystem\FilesystemServiceProvider::class);
        }
        if (!$this->app->bound('view')) {
            $this->app->register(\Illuminate\View\ViewServiceProvider::class);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Pengalihan storage ke folder sementara (/tmp) khusus di lingkungan Vercel Serverless
        if (isset($_ENV['VERCEL']) || isset($_SERVER['VERCEL'])) {
            $storagePath = '/tmp/storage';
            if (!file_exists($storagePath . '/framework/views')) {
                @mkdir($storagePath . '/framework/views', 0755, true);
                @mkdir($storagePath . '/framework/cache', 0755, true);
                @mkdir($storagePath . '/framework/sessions', 0755, true);
            }
            $this->app->useStoragePath($storagePath);
            config(['view.compiled' => $storagePath . '/framework/views']);
        }
    }
}
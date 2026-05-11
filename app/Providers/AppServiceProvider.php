<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        // Force HTTPS di production (InfinityFree pakai HTTPS)
        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }

        // Fix untuk shared hosting: pastikan storage path bisa ditulis
        $storagePath = storage_path('app/private/uploads');
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }

        $frameworkPaths = [
            storage_path('framework/sessions'),
            storage_path('framework/views'),
            storage_path('framework/cache/data'),
            storage_path('logs'),
        ];

        foreach ($frameworkPaths as $path) {
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
        }
    }
}

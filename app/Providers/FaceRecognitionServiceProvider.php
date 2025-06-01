<?php

namespace App\Providers;

use App\Services\FaceRecognitionService;
use Illuminate\Support\ServiceProvider;

class FaceRecognitionServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(FaceRecognitionService::class, function ($app) {
            return new FaceRecognitionService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}

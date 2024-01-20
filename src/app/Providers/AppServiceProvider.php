<?php

namespace App\Providers;

use App\Services\CSVProcessingService;
use App\Services\ZipProcessingService;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(ZipProcessingService::class, function () {
            return new ZipProcessingService();
        });

        $this->app->bind(CsvProcessingService::class, function () {
            return new CsvProcessingService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}

<?php

namespace App\Providers;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    final public function boot(): void
    {
        Schema::defaultStringLength(191);

        if (config('app.env') === 'local') {
            URL::forceScheme('https');
        }

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}

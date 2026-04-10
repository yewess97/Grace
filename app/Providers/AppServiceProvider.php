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

        if (in_array(config('app.env'), ['local', 'production'], true)) {
            URL::forceScheme('https');
        }
    }
}

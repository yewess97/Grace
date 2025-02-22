<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    final public function register(): void
    {
        $helper_files = glob(app_path('Helpers').'/*.php');

        foreach ($helper_files as $helper_file) {
            require_once $helper_file;
        }
    }
}

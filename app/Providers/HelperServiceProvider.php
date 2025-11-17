<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class HelperServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    final public function boot(): void
    {
        $helper_files = [
            'AppPrinciples',
            'ResponseHelpers',
            'RequestHelpers',
            'GraceStandards',
            'Helpers',
        ];

        foreach ($helper_files as $helper_file) {
            require_once app_path("Helpers/{$helper_file}.php");
        }
    }
}

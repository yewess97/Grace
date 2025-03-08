<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AdminBladeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    final public function boot(): void
    {
        /**
         * Clear Search or Filter.
         *
         * @param string $route
         * @return string
         */
        Blade::directive('clearSearchFilter', static fn(string $route) =>
            "<?php echo \"<a href=\".array_from($route)[0].\" role='link' id='clear_filter' class='text-decoration-underline lh-base'>Clear Search/\".ucfirst(FILTER).\"</a>\" ?>"
        );
    }
}

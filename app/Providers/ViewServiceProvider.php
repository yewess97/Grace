<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\ServiceProvider;
use Random\RandomException;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     * @throws RandomException|BindingResolutionException
     */
    final public function boot(): void
    {
        // Share common data with all user views
        view()->composer(
            [USER_MODEL.".*"],
            static function ($view) {
                $view->with([
                    COMMON_COLLECTIONS => commonCollections(),
                    'aside_menus'      => commonAsideMenus(),
                    USER_CART_ITEMS    => cartConfig()[USER_CART_ITEMS],
                    TOTAL_COST         => cartConfig()[TOTAL_COST],
                    TOTAL_ITEMS        => cartConfig()[TOTAL_ITEMS],
                ]);
            }
        );

        // Generate a unique nonce for inline scripts and styles to enhance security
        $this->app->singleton('csp_nonce', fn() => base64_encode(random_bytes(16)));
        view()->share('nonce', $this->app->make('csp_nonce'));
    }
}

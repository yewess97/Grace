<?php

namespace App\Providers;

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
                    COMMON_COLLECTIONS   => commonCollections(),
                    'aside_menus'        => commonAsideMenus(),
                    USER_CART_ITEMS      => userCollectionsData()[CART_MODEL][ITEMS],
                    CART_TOTAL_ITEMS     => userCollectionsData()[CART_MODEL][TOTAL_ITEMS],
                    TOTAL_COST           => userCollectionsData()[CART_MODEL][TOTAL_COST],
                    WISHLIST_TOTAL_ITEMS => userCollectionsData()[WISHLIST_MODEL][TOTAL_ITEMS],
                ]);
            }
        );

        // Generate a unique nonce for inline scripts and styles to enhance security
        $this->app->singleton('csp_nonce', fn() => base64_encode(random_bytes(16)));
        view()->share('nonce', $this->app->make('csp_nonce'));
    }
}

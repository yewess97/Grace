<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    final public function boot(): void
    {
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
    }
}

<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "products list" route for the application.
     * Typically, users are redirected here after authentication.
     *
     * @var string
     */
    public const PRODUCTS_LIST = '/products';

    /**
     * Define your route model bindings, pattern filters, and other route configuration.
     *
     * @return void
     */
    final public function boot(): void
    {
//        $this->configureRateLimiting();

        $this->routes(function () {
//            Route::middleware('api')
//                ->prefix('api')
//                ->group(base_path('routes/api.php'));

            Route::middleware(['web', AUTH, ADMIN])
                ->prefix('/'.ADMIN)
                ->group(base_path('routes/admin/admin-routes.php'));

            Route::middleware(['web', 'guest'])
                ->group(base_path('routes/guest/guest-routes.php'));

            Route::middleware(['web', AUTH])
                ->group(base_path('routes/auth/auth-routes.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

//    /**
//     * Configure the rate limiters for the application.
//     *
//     * @return RateLimiting
//     */
//    protected function configureRateLimiting(): RateLimiting
//    {
//        return RateLimiter::for('web', static fn() => Limit::perMinute(60)->by(request()?->user()?->getKey() ?: request()?->ip()));
//    }
}

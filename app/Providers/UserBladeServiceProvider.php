<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class UserBladeServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    final public function boot(): void
    {
        /**
         * Check if the user is admin.
         *
         *  @return bool
         */
        Blade::if(ADMIN, static fn() => auth()->user()?->isAdmin);

        /**
         * Check if the old price is neither equal to zero nor to the new price of the product.
         *
         * @param string $oldPrice
         * @param string $newPrice
         * @return bool
         */
        Blade::if('oldprice', static fn(string|int $oldPrice, string|int $newPrice) => (int) $oldPrice !== 0 && (int) $oldPrice !== (int) $newPrice);

        /**
         * Calculate the product discount.
         *
         * @param string $prices
         * @return string
         */
        Blade::directive('discount', static function (string $prices) {
            [$selling_price, $original_price] = array_from($prices);

            return "<?php echo round((($selling_price * 100) / $original_price) - 100).'%' ?>";
        });

        /**
         * Show the session error message.
         *
         * @param string $error
         * @return string
         */
        Blade::directive('sessionerror', static fn(string $error) =>
            "<?php echo \"<div role='alert' class='alert alert-dismissible fade show alert-danger d-flex justify-content-between align-items-center pe-4' data-mdb-color='danger'><div class='error-message'><i class='fas fa-times-circle me-3'></i>\".session($error).\"</div><button type='button' role='button' title='Close Alert' class='btn-close position-relative p-0' data-mdb-dismiss='alert' aria-label='Close Alert'></button></div>\"?>"
        );
    }
}

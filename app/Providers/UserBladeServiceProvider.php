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
        Blade::if('admin', static fn() => 
            auth()->user()?->isAdmin
        );

        /**
         * Check if the old price is neither equal to zero nor to the new price of the product.
         *
         * @param mixed $oldPrice
         * @param mixed $newPrice
         * @return bool
         */
        Blade::if('oldprice', static fn(mixed $oldPrice, mixed $newPrice) => 
            (int) $oldPrice !== 0 && (int) $oldPrice !== (int) $newPrice
        );

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
         * Show the session message.
         * 
         * @param string $sessionArgs
         * @return string
         */
        Blade::directive('customSession', static function (string $sessionArgs) {
            [$message, $type, $icon_type] = array_from($sessionArgs);

            $message_container_class = $type !== 'danger'
                ? $type 
                : 'error';

            return "<?php echo \"<div role='alert' class='alert alert-dismissible fade show alert-$type d-flex justify-content-between align-items-center pe-4' data-mdb-color='$type'><div class='$message_container_class-message'><i class='fas fa-$icon_type-circle me-3'></i><span>\".session('$message').\"</span></div><button type='button' role='button' title='Close Alert' class='btn-close position-relative p-0' data-mdb-dismiss='alert' aria-label='Close Alert'></button></div>\" ?>";
        });
    }
}

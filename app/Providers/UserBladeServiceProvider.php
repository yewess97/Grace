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
        Blade::directive('discount', static fn(string $prices) =>
            "<?php
                [\$__selling_price, \$__original_price] = [$prices];

                echo round(((\$__selling_price * 100) / \$__original_price) - 100).'%'
            ?>"
        );

        /**
         * Show the session message.
         *
         * @param string $sessionArgs
         * @return string
         */
        Blade::directive('customSession', static fn(string $sessionArgs) =>
            "<?php
                [\$__message, \$__type, \$__icon_type] = [$sessionArgs];

                \$__message_container_class = \$__type !== 'danger'
                    ? \$__type
                    : 'error';

                echo \"
                    <div role='alert' class='alert alert-dismissible fade show alert-\$__type d-flex justify-content-between align-items-center pe-4' data-mdb-color='\$__type'>
                        <div class='\$__message_container_class-message'>
                            <i class='fas fa-\$__icon_type-circle me-3'></i>
                            <span>\".session(\$__message).\"</span>
                        </div>
                        <button type='button' role='button' title='Close Alert' class='btn-close position-relative p-0' data-mdb-dismiss='alert' aria-label='Close Alert'></button>
                    </div>
                \"
            ?>"
        );
    }
}

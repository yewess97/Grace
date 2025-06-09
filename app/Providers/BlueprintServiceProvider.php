<?php

namespace App\Providers;

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\ServiceProvider;

class BlueprintServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    final public function boot(): void
    {
        /**
         * Name & Slug Macro.
         *
         * @param string|null $model
         * @return void
         */
        Blueprint::macro('nameSlug', function (?string $model = null) {
            $this->string(NAME, isset($model) && $model === PRODUCT_MODEL ? 300 : null)->index();

            $this->string(SLUG)->unique();
        });

        /**
         * Product Size & Quantity Macro.
         *
         * @return void
         */
        Blueprint::macro('productSizeQuantity', function () {
            $this->unsignedTinyInteger(PRODUCT_SIZE)->default(3); // 1 --> S, 2 --> M, 3 --> L, 4 --> XL, 5 --> XXL
            $this->unsignedInteger(PRODUCT_QUANTITY)->default(1);
        });

        /**
         * Foreign ID Macro.
         *
         * @param string $model
         * @return void
         */
        Blueprint::macro('foreignIdOf', fn($model) =>
            $this->foreignIdFor($model)->constrained((new $model())->getTable())->cascadeOnDelete()->cascadeOnUpdate()
        );

        /**
         * Nullable Foreign ID Macro.
         *
         * @param string $model
         * @return void
         */
        Blueprint::macro('nullableForeignIdOf', fn($model) =>
            $this->foreignIdFor($model)->nullable()->constrained((new $model())->getTable())->nullOnDelete()->onUpdate('set null')
        );
    }
}

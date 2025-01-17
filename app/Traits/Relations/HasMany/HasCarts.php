<?php

namespace App\Traits\Relations\HasMany;

use App\Models\Cart;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasCarts {
    /**
     * Relation with the cart model.
     */
    final public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }
}

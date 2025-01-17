<?php

namespace App\Traits\Relations\HasMany;

use App\Models\Order;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait HasOrders
{
    /**
     * Relation with the order model.
     */
    final public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}

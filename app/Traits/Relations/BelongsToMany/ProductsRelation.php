<?php

namespace App\Traits\Relations\BelongsToMany;

use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait ProductsRelation {
    /**
     * Relation with the product model.
     */
    final public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class);
    }
}

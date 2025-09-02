<?php

namespace App\Traits\Relations\BelongsTo;

use App\Models\Product;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait ProductRelation
{
    /**
     * Relation with the product model.
     */
    final public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class)->select(PRODUCT_ITEM_ATTRIBUTES);
    }
}

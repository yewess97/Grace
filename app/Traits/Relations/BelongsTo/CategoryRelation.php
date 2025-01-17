<?php

namespace App\Traits\Relations\BelongsTo;

use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait CategoryRelation {
    /**
     * Relation with the category model.
     */
    final public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
}

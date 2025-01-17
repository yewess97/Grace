<?php

namespace App\Traits\Relations\BelongsTo;

use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait SubcategoryRelation
{
    /**
     * Relation with the subcategory model.
     */
    final public function subcategory(): BelongsTo
    {
        return $this->belongsTo(Subcategory::class);
    }
}

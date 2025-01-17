<?php

namespace App\Traits\Relations\BelongsToMany;

use App\Models\Subcategory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait SubcategoriesRelation
{
    /**
     * Relation with the subcategory model.
     */
    final public function subcategories(): BelongsToMany
    {
        return $this->belongsToMany(Subcategory::class);
    }
}

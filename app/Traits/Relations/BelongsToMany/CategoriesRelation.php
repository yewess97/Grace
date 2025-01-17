<?php

namespace App\Traits\Relations\BelongsToMany;

use App\Models\Category;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait CategoriesRelation
{
    /**
     * Relation with the category model.
     */
    final public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }
}

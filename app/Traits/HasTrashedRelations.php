<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Casts\Attribute;

trait HasTrashedRelations
{
    /**
     * Get the trashed relations of the specified model.
     *
     * @return Attribute
     */
    final public function trashedRelations(): Attribute
    {
        return Attribute::get(fn() => softDeletedRelations($this, $this->trashedRelationsList));
    }
}

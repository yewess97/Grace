<?php

namespace App\Traits\Relations\BelongsTo;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait UserRelation
{
    /**
     * Relation with the user model.
     */
    final public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

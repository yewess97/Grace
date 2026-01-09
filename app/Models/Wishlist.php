<?php

namespace App\Models;

use App\Traits\Relations\BelongsTo\ProductRelation;
use App\Traits\Relations\BelongsTo\UserRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Wishlist extends Model
{
    use HasFactory, UserRelation, ProductRelation;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = WISHLISTS_TABLE;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = WISHLIST_FILLABLE_ATTRIBUTES;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = DATES;
}

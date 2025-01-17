<?php

namespace App\Models;

use App\Traits\Relations\BelongsTo\ProductRelation;
use App\Traits\Relations\BelongsTo\UserRelation;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
    use HasFactory, UserRelation, ProductRelation;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = CARTS_TABLE;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = CART_FILLABLE_ATTRIBUTES;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = DATES;

    /**
     * Get the cart's product size.
     *
     * @return Attribute
     */
    final protected function selectedProductSize(): Attribute
    {
        return Attribute::get(fn() => array_search($this->{PRODUCT_SIZE}, PRODUCT_SIZE_ENUM, true) ?? '');
    }
}

<?php

namespace App\Models;

use App\Traits\Relations\BelongsTo\CategoryRelation;
use App\Traits\Relations\BelongsTo\ProductRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CategoryProduct extends Pivot
{
    use HasFactory, CategoryRelation, ProductRelation;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = CATEGORY_PRODUCT_TABLE;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [CATEGORY_ID, PRODUCT_ID];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = DATES;
}

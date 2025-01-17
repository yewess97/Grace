<?php

namespace App\Models;

use App\Traits\Relations\BelongsTo\ProductRelation;
use App\Traits\Relations\BelongsTo\SubcategoryRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class ProductSubcategory extends Pivot
{
    use HasFactory, SubcategoryRelation, ProductRelation;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = PRODUCT_SUBCATEGORY_TABLE;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [SUBCATEGORY_ID, PRODUCT_ID];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = DATES;
}

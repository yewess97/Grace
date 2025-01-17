<?php

namespace App\Models;

use App\Traits\Relations\BelongsTo\CategoryRelation;
use App\Traits\Relations\BelongsTo\SubcategoryRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CategorySubcategory extends Pivot
{
    use HasFactory, CategoryRelation, SubcategoryRelation;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = CATEGORY_SUBCATEGORY_TABLE;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [CATEGORY_ID, SUBCATEGORY_ID];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = DATES;
}

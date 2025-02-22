<?php

namespace App\Models;

use App\Traits\Relations\BelongsToMany\ProductsRelation;
use App\Traits\Relations\BelongsToMany\SubcategoriesRelation;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Category extends Model
{
    use HasFactory, SoftDeletes, SubcategoriesRelation, ProductsRelation;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = CATEGORIES_TABLE;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = CATEGORY_FILLABLE_ATTRIBUTES;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = DATES;

    /**
     * Get the data of the specified category.
     *
     * @return Attribute
     */
    final protected function data(): Attribute
    {
        return Attribute::get(fn() => getData($this, [NAME, MAIN_IMAGE, BANNER_IMAGE]));
    }
}

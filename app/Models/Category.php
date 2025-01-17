<?php

namespace App\Models;

use App\Traits\Relations\BelongsToMany\ProductsRelation;
use App\Traits\Relations\BelongsToMany\SubcategoriesRelation;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    use HasFactory, SubcategoriesRelation, ProductsRelation;

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

//    /**
//     * Get the data of the related subcategories of the category.
//     *
//     * @return array
//     */
//    protected function getRelatedSubcategoriesAttribute(): array
//    {
//        return $this->{SUBCATEGORIES_TABLE}()->pluck(NAME, ID)->toArray();
//    }
//
//    /**
//     * Get the data of the related products of the category.
//     *
//     * @return array
//     */
//    protected function getRelatedProductsAttribute(): array
//    {
//        return $this->{PRODUCTS_TABLE}()->toArray();
//    }

    /**
     * Delete all or Detach the related subcategories of the category.
     *
     * @return void
     */
    final public static function deleteRelatedSubcategories(): void
    {
        parent::boot();

        static::deleting(static function (self $category) {
            $category->{SUBCATEGORIES_TABLE}()->each(function (Subcategory $subcategory) use ($category) {
                if ($subcategory->{CATEGORIES_TABLE}()->count() === 1) {
                    Storage::delete(imageSource($subcategory, MAIN_IMAGE, true));

                    $subcategory->delete();
                }

                $category->{SUBCATEGORIES_TABLE}()->detach($subcategory->{ID});
            });
        });
    }
}

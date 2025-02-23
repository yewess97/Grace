<?php

namespace App\Models;

use App\Traits\Relations\BelongsToMany\CategoriesRelation;
use App\Traits\Relations\BelongsToMany\ProductsRelation;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subcategory extends Model
{
    use HasFactory, SoftDeletes, CategoriesRelation, ProductsRelation;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = SUBCATEGORIES_TABLE;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = SUBCATEGORY_FILLABLE_ATTRIBUTES;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = DATES;

    /**
     * Get the data of the specified subcategory.
     */
    final protected function data(): Attribute
    {
        return Attribute::get(fn() => getData($this, [NAME, MAIN_IMAGE])?->load(CATEGORIES_TABLE));
    }

//    /**
//     * Get the data of the related categories of the subcategory.
//     *
//     * @return array
//     */
//    protected function getRelatedCategoriesAttribute(): array
//    {
//        return $this->{CATEGORIES_TABLE}()->pluck(NAME, ID)->toArray();
//    }
//
//    /**
//     * Get the data of the related products of the subcategory.
//     *
//     * @return array
//     */
//    protected function getRelatedProductsAttribute(): array
//    {
//        return $this->{PRODUCTS_TABLE}()->toArray();
//    }

//    /**
//     * Delete or Detach all the related products of the subcategory.
//     *
//     * @return void
//     */
//    final public static function deleteRelatedProducts(): void
//    {
//        parent::boot();
//
//        static::deleting(static function (self $subcategory) {
//            $subcategory->{PRODUCTS_TABLE}()->each(function (Product $product) use ($subcategory) {
//               if ($product->{SUBCATEGORIES_TABLE}()->count() === 1) {
//                   Storage::delete(imageSource($product, MAIN_IMAGE, true));
//                   $product->{THUMB_IMAGES}->each(static fn(Collection $collection) => Storage::delete(imageSource($collection, THUMB_IMAGE, true)));
//
//                   $product->delete();
//               }
//
//               $subcategory->{PRODUCTS_TABLE}()->detach($product->{ID});
//            });
//        });
//    }
}

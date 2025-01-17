<?php

namespace App\Models;

use App\Traits\Relations\BelongsToMany\CategoriesRelation;
use App\Traits\Relations\BelongsToMany\SubcategoriesRelation;
use App\Traits\Relations\HasMany\HasCarts;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Pagination\LengthAwarePaginator;

class Product extends Model
{
    use HasFactory, HasCarts, CategoriesRelation, SubcategoriesRelation;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = PRODUCTS_TABLE;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = PRODUCT_FILLABLE_ATTRIBUTES;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = DATES;

    /**
     * Get the data of the specified product
     * with its related categories & subcategories, sizes, and thumbnail images.
     *
     * @return Attribute
     */
    final protected function data(): Attribute
    {
        return Attribute::get(fn() => getData($this, [NAME, SHORT_DESCRIPTION, LONG_DESCRIPTION, MAIN_IMAGE, OLD_PRICE, NEW_PRICE, QUANTITY, STATUS])?->load(CATEGORIES_TABLE, SUBCATEGORIES_TABLE, SIZES, THUMB_IMAGES));
    }

    /**
     * Get the related products of a product based on the related subcategories.
     *
     * @return Attribute
     */
    final public function relatedProducts(): Attribute
    {
        return Attribute::get(fn() => $this->{SUBCATEGORIES_TABLE}()
                ->with([
                    PRODUCTS_TABLE => static fn(BelongsToMany $product) => $product->select(ID, NAME, SLUG, MAIN_IMAGE, OLD_PRICE, NEW_PRICE, STATUS)
                ])
            ->get()
            ->pluck(PRODUCTS_TABLE)
            ->flatten()
            ->reject(fn(self $product) => $product->{ID} === $this->{ID})
            ->unique(ID)
        );
    }

    /**
     * Filter the products.
     *
     * @param array $filterAttributes
     * @param array $filterRequestValues
     * @return LengthAwarePaginator
     */
    final public static function filter(array $filterAttributes, array $filterRequestValues): LengthAwarePaginator
    {
        [$categories, $subcategories, $sizes] = $filterAttributes;

        [$categories_ids_values, $subcategories_ids_values, $sizes_values, $min_price_value, $max_price_value] = $filterRequestValues;

        $products = self::query()->select(PRODUCT_ITEM_ATTRIBUTES);

        $filter_related_collection = static function (string $collection, array $collectionValues, bool $isSize = false) use ($products): void {
            if (!empty($collectionValues)) {
                $products->whereHas($collection, function ($product) use ($collectionValues, $isSize) {
                    $product->whereIn($isSize ? SIZE : ID, $collectionValues);
                });
            }
        };

        $filter_related_collection($categories, $categories_ids_values);

        $filter_related_collection($subcategories, $subcategories_ids_values);

        $filter_related_collection($sizes, $sizes_values, true);

        if (isset($min_price_value, $max_price_value)) {
            $products->whereBetween(NEW_PRICE, [$min_price_value, $max_price_value]);
        }

        return $products->fastPaginate(16);
    }


    /**
     * Relations with other models in the database (Eloquent ORM).
     */
    final public function thumbImages(): HasMany
    {
        return $this->hasMany(ThumbImage::class);
    }

    final public function sizes(): HasMany
    {
        return $this->hasMany(ProductSize::class);
    }

    final public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }
}

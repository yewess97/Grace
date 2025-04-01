<?php

namespace App\Models;

use App\Traits\Relations\BelongsToMany\CategoriesRelation;
use App\Traits\Relations\BelongsToMany\SubcategoriesRelation;
use App\Traits\Relations\HasMany\HasCarts;
use DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Pagination\LengthAwarePaginator;

class Product extends Model
{
    use HasFactory, SoftDeletes, HasCarts, CategoriesRelation, SubcategoriesRelation;

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
        return Attribute::get(fn() => getData($this, [NAME, SHORT_DESCRIPTION, LONG_DESCRIPTION, MAIN_IMAGE, OLD_PRICE, NEW_PRICE, QUANTITY, STATUS])?->load([
            CATEGORIES_TABLE    => static fn($category)    => $category->select(ID, NAME),
            SUBCATEGORIES_TABLE => static fn($subcategory) => $subcategory->select(ID, NAME),
            SIZES,
            THUMB_IMAGES,
        ]));
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
                    PRODUCTS_TABLE => static fn(BelongsToMany $product) => $product->select(PRODUCT_ITEM_ATTRIBUTES)
                ])
            ->get()
            ->pluck(PRODUCTS_TABLE)
            ->flatten()
            ->reject(fn(self $product) => $product->{ID} === $this->{ID})
            ->unique(ID)
        );
    }

    /**
     * Get the most selling products.
     *
     * @param Builder $query
     * @return LengthAwarePaginator
     */
    final public function scopeMostSelling(Builder $query): LengthAwarePaginator
    {
        $product_item_attributes = array_map(static fn($attr) => PRODUCTS_TABLE.".$attr", PRODUCT_ITEM_ATTRIBUTES);

        return $query->join(ORDER_ITEMS_TABLE, ORDER_ITEMS_TABLE.'.'.PRODUCT_NAME, '=', $product_item_attributes[1])
            ->select([
                ...$product_item_attributes,
                DB::raw('SUM('.ORDER_ITEMS_TABLE.'.'.PRODUCT_QUANTITY.') AS total_sales')
            ])
            ->groupBy([...$product_item_attributes])
            ->havingRaw('total_sales > 20')
            ->orderByDesc('total_sales')
            ->fastPaginate(16);
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

        $filter_related_collection = static function (string $relatedCollection, array $collectionValues, bool $isSize = false) use ($products) {
            $products->when(!empty($collectionValues), function (Builder $product) use ($relatedCollection, $collectionValues, $isSize) {
                $product->whereHas($relatedCollection, function (Builder $query) use ($collectionValues, $isSize) {
                    $query->whereIn($isSize ? SIZE : ID, $collectionValues);
                });
            });
        };

        $filter_related_collection($categories, $categories_ids_values);

        $filter_related_collection($subcategories, $subcategories_ids_values);

        $filter_related_collection($sizes, $sizes_values, true);

        $products->when(isset($min_price_value, $max_price_value), function (Builder $product) use ($min_price_value, $max_price_value) {
            $product->whereBetween(NEW_PRICE, [$min_price_value, $max_price_value]);
        });

        return $products->fastPaginate(16);
    }

    /**
     * Sort the products.
     *
     * @param array $filterRequestValues
     * @return LengthAwarePaginator
     */
    final public static function sort(array $filterRequestValues):LengthAwarePaginator
    {
        [$sort_value] = $filterRequestValues;

        $products = self::query();

        if ($sort_value === 'best-selling') {
            return $products->mostSelling();
        }

        if ($sort_value === 'title-ascending') {
            $products->orderBy(NAME);
        }

        if ($sort_value === 'title-descending') {
            $products->orderByDesc(NAME);
        }

        if ($sort_value === 'price-ascending') {
            $products->orderBy(NEW_PRICE);
        }

        if ($sort_value === 'price-descending') {
            $products->orderByDesc(NEW_PRICE);
        }

        return $products->select(PRODUCT_ITEM_ATTRIBUTES)->fastPaginate(16);
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

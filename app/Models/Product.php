<?php

namespace App\Models;

use App\Contracts\HasImages;
use App\Contracts\IGrace;
use App\Traits\HasTrashedRelations;
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

class Product extends Model implements IGrace, HasImages
{
    use HasFactory, HasTrashedRelations, SoftDeletes, HasCarts, CategoriesRelation, SubcategoriesRelation;

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
     * The relations that should be considered when soft-deleting.
     *
     * @var array<string>
     */
    protected array $trashedRelationsList = [
        CATEGORIES_TABLE    => NAME,
        SUBCATEGORIES_TABLE => NAME,
    ];


    /**
     * Get the data of the specified product
     * with its related categories & subcategories, sizes, and thumbnail images.
     *
     * @return Attribute
     */
    final public function data(): Attribute
    {
        return Attribute::get(fn() => getData($this, [NAME, SHORT_DESCRIPTION, LONG_DESCRIPTION, MAIN_IMAGE, OLD_PRICE, NEW_PRICE, QUANTITY, STATUS])?->load([
            CATEGORIES_TABLE    => static fn($category)    => $category->select(ID, NAME),
            SUBCATEGORIES_TABLE => static fn($subcategory) => $subcategory->select(ID, NAME),
            SIZES,
            THUMB_IMAGES,
        ]));
    }

    /**
     * Configure all image properties for the product.
     *
     * @return array
     */
    final public function imageProperties(): array
    {
        return [
            MAIN_IMAGE => [
                'type' => 'column',
            ],
            THUMB_IMAGES => [
                'type'       => 'relation',
                'image_type' => THUMB_IMAGE,
            ],
        ];
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
            ->cursor()
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
     * @param int $minSales
     * @return Builder
     */
    final public function scopeMostSelling(Builder $query, int $minSales = 30): Builder
    {
        $product_item_attributes = array_map(static fn(string $attribute) => PRODUCTS_TABLE.".$attribute", PRODUCT_ITEM_ATTRIBUTES);

        $total_product_sales = 'SUM('.ORDER_ITEMS_TABLE.'.'.PRODUCT_QUANTITY.')';

        return $query->join(
            ORDER_ITEMS_TABLE,
            ORDER_ITEMS_TABLE.'.'.PRODUCT_ID,
            '=',
            $product_item_attributes[0]
        )
            ->select([
                ...$product_item_attributes,
                DB::raw("$total_product_sales AS total_sales")
            ])
            ->groupBy($product_item_attributes)
            ->havingRaw(
                "$total_product_sales > ?",
                [$minSales]
            ) // Because fastPaginate() with groupBy + having(alias) sometimes causes problems with internal queries.
            ->orderByDesc('total_sales');
    }

    /**
     * Sort the products.
     *
     * @param Builder $query
     * @param string|null $sort_value
     * @return Builder
     */
    final public function scopeSort(Builder $query, ?string $sort_value = null): Builder
    {
        if (isset($sort_value)) {
            if ($sort_value === 'title-ascending') {
                return $query->orderBy(NAME);
            }

            if ($sort_value === 'title-descending') {
                return $query->orderByDesc(NAME);
            }

            if ($sort_value === 'price-ascending') {
                return $query->orderBy(NEW_PRICE);
            }

            if ($sort_value === 'price-descending') {
                return $query->orderByDesc(NEW_PRICE);
            }
        }

        return $query;
    }

    /**
     * Filter the products.
     *
     * @param Builder $query
     * @param array $filterAttributes
     * @param array $filterRequestValues
     * @return Builder
     */
    final public function scopeFilter(Builder $query, array $filterAttributes, array $filterRequestValues): Builder
    {
        $filter_related_collection = static function (string $relatedCollection, array $collectionValues) use ($query) {
            $query->when(!empty($collectionValues), static fn(Builder $product) =>
                $product->whereHas($relatedCollection, static fn(Builder $query) =>
                    $query->whereIn(str_contains($relatedCollection, SIZE) ? SIZE : ID, $collectionValues)
                )
            );
        };

        [$categories, $subcategories, $sizes] = $filterAttributes;

        [$categories_ids_values, $subcategories_ids_values, $sizes_values, $min_price_value, $max_price_value, $sort_value] = array_map(static fn($value) =>
            is_array($value)
                ? array_filter($value)
                : $value,
            $filterRequestValues
        );

        $filter_related_collection($categories,    $categories_ids_values);
        $filter_related_collection($subcategories, $subcategories_ids_values);
        $filter_related_collection($sizes,         $sizes_values);

        $query->when(is_numeric($min_price_value) && is_numeric($max_price_value), static fn(Builder $product) =>
            $product->whereBetween(NEW_PRICE, [(double) $min_price_value, (double) $max_price_value])
        );

        if ($sort_value === 'most-selling') {
            return $query->mostSelling();
        }

        return $query->sort($sort_value)
            ->select(PRODUCT_ITEM_ATTRIBUTES);
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

    final public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }
}

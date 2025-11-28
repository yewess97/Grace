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
     * The relations that should be considered when soft-deleting.
     *
     * @var array<string>
     */
    protected array $trashedRelationsList = [CATEGORIES_TABLE => NAME];

    /**
     * Get the data of the specified subcategory.
     */
    final protected function data(): Attribute
    {
        return Attribute::get(fn() => getData($this, [NAME, MAIN_IMAGE])?->load([
            CATEGORIES_TABLE => static fn($category) => $category->select(ID, NAME),
        ]));
    }

    /**
     * Get the trashed relations of the specified subcategory.
     *
     * @return Attribute
     */
    final protected function trashedRelations(): Attribute
    {
        return Attribute::get(fn() => softDeletedRelations($this, $this->trashedRelationsList));
    }
}

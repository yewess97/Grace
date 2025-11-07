<?php

namespace App\Models;

use App\Traits\Relations\BelongsTo\ProductRelation;
use App\Traits\Relations\BelongsTo\UserRelation;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Review extends Model
{
    use HasFactory, SoftDeletes, UserRelation, ProductRelation;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = REVIEWS_TABLE;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = REVIEW_FILLABLE_ATTRIBUTES;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $dates = DATES;

    /**
     * The relations that should be considered when soft-deleting.
     *
     * @var array<string>
     */
    protected array $trashedRelationsList = [PRODUCT_MODEL, USER_MODEL];

    /**
     * Get the data of the specified review.
     *
     * @return Attribute
     */
    final protected function data(): Attribute
    {
        return Attribute::get(fn() => getData($this, REVIEW_ATTRIBUTES));
    }

    /**
     * Get the trashed relations of the specified review.
     *
     * @return Attribute
     */
    final protected function trashedRelations(): Attribute
    {
        return Attribute::get(fn() => softDeletedRelations($this, $this->trashedRelationsList));
    }
}

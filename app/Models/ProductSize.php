<?php

namespace App\Models;

use App\Traits\Relations\BelongsTo\ProductRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductSize extends Model
{
    use HasFactory, ProductRelation;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = PRODUCT_SIZES_TABLE;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [SIZE, PRODUCT_ID];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = DATES;
}

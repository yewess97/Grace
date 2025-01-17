<?php

namespace App\Models;

use App\Traits\Relations\BelongsTo\ProductRelation;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ThumbImage extends Model
{
    use HasFactory, ProductRelation;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = THUMB_IMAGES_TABLE;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = [THUMB_IMAGE, PRODUCT_ID];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = DATES;
}

<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = ORDER_ITEMS_TABLE;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = ORDER_ITEM_FILLABLE_ATTRIBUTES;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array<int, string>
     */
    protected $dates = DATES;


    /**
     * Relations with other models in the database (Eloquent ORM).
     */
    final public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }
}

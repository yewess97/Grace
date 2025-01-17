<?php

namespace App\Models;

use App\Traits\Relations\BelongsTo\UserRelation;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    use HasFactory, UserRelation;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = ORDERS_TABLE;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<string>
     */
    protected $fillable = ORDER_FILLABLE_ATTRIBUTES;

    /**
     * The attributes that should be mutated to dates.
     *
     * @var array<int, string>
     */
    protected $dates = DATES;

    /**
     * Get the data of the specified order.
     *
     * @return Attribute
     */
    final protected function data(): Attribute
    {
        return Attribute::get(fn() => getData($this, [STATUS]));
    }


    /**
     * Relations with other models in the database (Eloquent ORM).
     */
    final public function address(): BelongsTo
    {
        return $this->belongsTo(Address::class);
    }

    final public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}

<?php

namespace App\Models;

use App\Contracts\IGrace;
use App\Traits\Relations\BelongsTo\UserRelation;
use App\Traits\Relations\HasMany\HasOrders;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model implements IGrace
{
    use HasFactory, SoftDeletes, UserRelation, HasOrders;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = ADDRESSES_TABLE;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ADDRESS_FILLABLE_ATTRIBUTES;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = DATES;

    /**
     * Get the data of the specified address.
     *
     * @return Attribute
     */
    final public function data(): Attribute
    {
        return Attribute::get(fn() => getData($this, [ADDRESS1, ADDRESS2, COUNTRY, CITY, STATE, PHONE, POSTAL_CODE]));
    }
}

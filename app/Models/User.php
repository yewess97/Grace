<?php

namespace App\Models;

use App\Contracts\IGrace;
use App\Traits\Relations\HasMany\HasCarts;
use App\Traits\Relations\HasMany\HasOrders;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements IGrace
{
    use HasFactory, SoftDeletes, Notifiable, HasOrders, HasCarts;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = USERS_TABLE;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = USER_FILLABLE_ATTRIBUTES;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [PASSWORD, 'remember_token', ...DATES];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [EMAIL.'_verified_at' => 'datetime'];

    /**
     * Accessor to get the full name.
     *
     * @return Attribute
     */
    final public function fullName(): Attribute
    {
        return Attribute::get(fn() => ucwords("$this->first_name $this->last_name"));
    }

    /**
     * Accessor to get the admin role.
     *
     * @return Attribute
     */
    final public function isAdmin(): Attribute
    {
        return Attribute::get(fn() => $this->{ROLE} === 1);
    }

    /**
     * Get the data of the specified user.
     *
     * @return Attribute
     */
    final public function data(): Attribute
    {
        return Attribute::get(fn() => getData($this, [FIRST_NAME, LAST_NAME, EMAIL, ROLE, LAST_SEEN]));
    }

    /**
     * Get the profile's data of the specified user.
     *
     * @param int|null $id
     * @return static
     */
    final public static function profileData(?int $id = null): static
    {
        return static::query()->select(USER_SELECTED_ATTRIBUTES)
            ->with([
                ORDERS_TABLE,
                ADDRESSES_TABLE => static fn(HasMany $address) => $address->userCountries(),
            ])
            ->findOrFail($id ?? auth()->id());
    }


    /**
     * Relations with other models in the database (Eloquent ORM).
     */
    final public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
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

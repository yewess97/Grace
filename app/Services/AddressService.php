<?php

namespace App\Services;

use App\Contracts\ServiceData;
use App\Http\Requests\AddressRequest;
use App\Models\Address;
use App\Models\User;
use App\Notifications\NewAdminActionTaken;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Psr\SimpleCache\InvalidArgumentException as CacheInvalidArgumentException;
use Throwable;

class AddressService implements ServiceData
{
    /**
     * Get the detailed data of a specified user's addresses.
     *
     * @return Application|Factory|View|RedirectResponse|JsonResponse
     * @throws Throwable
     */
    final public function getUserAddressesData(): Application|Factory|View|RedirectResponse|JsonResponse
    {
        try {
            $user_id = auth()->check() && !auth()->user()?->isAdmin
                ? auth()->id()
                : decrypt(request()?->input(ID));
        }
        catch (DecryptException $ex) {
            abort(Response::HTTP_NOT_FOUND, ucfirst(USER_MODEL).' not found.');
        }

        $user_addresses_ids = cache()->remember(ADDRESSES_PAGINATION_CACHE_KEY, 1800, fn() =>
            Address::query()->where(USER_ID, $user_id)
                ->withTrashed()
                ->pluck(ID)
                ->toArray()
        );

        $user_addresses = paginateWithFallback(Address::class, $user_addresses_ids);

        $user_addresses_title = '*'.User::profileData((int) $user_id)->{FULL_NAME}.'* '.ucfirst(ADDRESSES_TABLE);
        $role                 = isAdminRoute(true);

        $add_address_error    = static fn(string $attributeName) => formError(ADD, ADDRESS_MODEL, $attributeName);
        $update_address_error = static fn(string $attributeName) => formError(UPDATE, ADDRESS_MODEL, $attributeName);

        return request()?->ajax()
            ? ajaxPaginationResponse($user_addresses, USER_ADDRESSES_PAGINATION_PARTIAL, USER_ADDRESSES)
            : showView(USER_ADDRESSES_COMPONENT, compact(USER_ID, USER_ADDRESSES, USER_ADDRESSES_TITLE, ROLE, ADD_ADDRESS_ERROR, UPDATE_ADDRESS_ERROR));
    }

    /**
     * Store or Update an address.
     *
     * @param string $operation
     * @return Address
     * @throws ValidationException|CacheInvalidArgumentException
     */
    final public function createOrUpdateAddress(string $operation): Address
    {
        $address_id = request()?->input(UPDATE_ADDRESS_ID);

        $validated_address_request = $this->validateRequest($operation);

        $address = $this->createOrUpdateCollection($validated_address_request, compact(ADDRESS_ID));

        $this->forgetCollectionCache();

        sendNotificationToAdmins(new NewAdminActionTaken([$address, $address->{ADDRESS1}], $operation), true);

        return $address;
    }

    /**
     * Delete a specified address.
     *
     * @param Address $address
     * @return bool
     * @throws CacheInvalidArgumentException
     */
    final public function deleteAddress(Address $address): bool
    {
        $deleted_address = removeDeleteOrRestore($address, $address->{ADDRESS1});

        $this->forgetCollectionCache();

        return $deleted_address;
    }

    /**
     * Delete the selected addresses.
     *
     * @param Address $addresses
     * @return bool
     * @throws CacheInvalidArgumentException
     */
    final public function deleteMultipleAddresses(Address $addresses): bool
    {
        $deleted_addresses = removeDeleteOrRestore($addresses);

        $this->forgetCollectionCache();

        return $deleted_addresses;
    }

    /**
     * Restore a specified address.
     *
     * @param Address $address
     * @return bool
     * @throws CacheInvalidArgumentException
     */
    final public function restoreAddress(Address $address): bool
    {
        $restored_address = removeDeleteOrRestore($address, $address->{ADDRESS1});

        $this->forgetCollectionCache();

        return $restored_address;
    }

    /**
     * Restore the selected addresses.
     *
     * @param Address $addresses
     * @return bool
     * @throws CacheInvalidArgumentException
     */
    final public function restoreMultipleAddresses(Address $addresses): bool
    {
        $restored_addresses = removeDeleteOrRestore($addresses);

        $this->forgetCollectionCache();

        return $restored_addresses;
    }

    /**
     * Validate and return the address request.
     *
     * @param string $operation
     * @param array $extra
     * @return AddressRequest
     * @throws ValidationException
     */
    final public function validateRequest(string $operation, array $extra = []): AddressRequest
    {
        $address_request   = new AddressRequest($operation, ADDRESS_MODEL, ADDRESS_FILLABLE_ATTRIBUTES);
        $decrypted_user_id = decrypt(request()?->input($address_request->dataKeyOf(USER_ID)));

        request()?->merge([$address_request->dataKeyOf(USER_ID) => $decrypted_user_id]);

        validateAttributes($address_request);

        return $address_request;
    }

    /**
     * Create or Update the address.
     *
     * @param FormRequest|AddressRequest $collectionRequest
     * @param array $extra
     * @return Address
     */
    final public function createOrUpdateCollection(FormRequest|AddressRequest $collectionRequest, array $extra): Address
    {
        [$address1, $address2, $city, $state, $country, $postal_code, $user_id] = ADDRESS_FILLABLE_ATTRIBUTES;

        [$address1_value, $address2_value, $city_value, $state_value, $country_value, $postal_code_value, $user_id_value] = $collectionRequest->dataValues();

        return Address::query()->updateOrCreate(
            [ID => $extra[ADDRESS_ID]],
            [
                $address1    => $address1_value,
                $address2    => $address2_value,
                $city        => $city_value,
                $state       => $state_value,
                $country     => $country_value,
                $postal_code => $postal_code_value,
                $user_id     => $user_id_value,
            ]
        );
    }

    /**
     * Forget the address cache.
     *
     * @param Model|null $model
     * @return void
     * @throws CacheInvalidArgumentException
     */
    final public function forgetCollectionCache(Model $model = null): void
    {
        forgetCache([ADDRESSES_PAGINATION_CACHE_KEY, USER_ADDRESSES_PAGINATION_CACHE_KEY]);
    }
}

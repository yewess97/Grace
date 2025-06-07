<?php

namespace App\Services;

use App\Http\Requests\AddressRequest;
use App\Models\Address;
use App\Models\User;
use App\Notifications\NewAdminActionTaken;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class AddressService {
    /**
     * Store or Update an address.
     *
     * @param string $operation
     * @return array
     * @throws ValidationException
     */
    final public function createOrUpdateAddress(string $operation): array
    {
        $address_request = new AddressRequest($operation, ADDRESS_MODEL, ADDRESS_FILLABLE_ATTRIBUTES);

        $address_id        = request()?->input(UPDATE_ADDRESS_ID);
        $decrypted_user_id = decrypt(request()?->input($address_request->dataKeyOf(USER_ID)));

        request()?->merge([$address_request->dataKeyOf(USER_ID) => $decrypted_user_id]);

        validateAttributes($address_request);

        [$address1, $address2, $city, $state, $country, $postal_code, $user_id] = ADDRESS_FILLABLE_ATTRIBUTES;

        [$address1_value, $address2_value, $city_value, $state_value, $country_value, $postal_code_value, $user_id_value] = $address_request->dataValues();

        $address = Address::query()->updateOrCreate(
            [ID => $address_id],
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

        sendNotificationToAdmins(new NewAdminActionTaken([$address, $address->{ADDRESS1}], $operation), true);

        return [$address, getLastPage(new Address())];
    }

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

        $user_addresses = Address::query()->where(USER_ID, $user_id)
            ->when(request()?->input(CONDITION), static fn($query) => $query->onlyTrashed())
            ->fastPaginate(16);

        $user_addresses_title = '*'.User::profileData((int) $user_id)->{FULL_NAME}.'* '.ucfirst(ADDRESSES_TABLE);
        $role                 = isAdminRoute(true);

        $add_address_error    = static fn(string $attributeName) => formError(ADD, ADDRESS_MODEL, $attributeName);
        $update_address_error = static fn(string $attributeName) => formError(UPDATE, ADDRESS_MODEL, $attributeName);

        return request()?->ajax()
            ? ajaxPaginationResponse($user_addresses, USER_ADDRESSES_PAGINATION, USER_ADDRESSES)
            : showView(USER_ADDRESSES_COMPONENT, compact(USER_ID, USER_ADDRESSES, USER_ADDRESSES_TITLE, ROLE, ADD_ADDRESS_ERROR, UPDATE_ADDRESS_ERROR));
    }

    /**
     * Delete a specified address.
     *
     * @param Address $address
     * @return bool
     */
    final public function deleteAddress(Address $address): bool
    {
        return customDelete($address, ADDRESS1);
    }

    /**
     * Delete the selected addresses.
     *
     * @param Address $addresses
     * @return bool
     */
    final public function deleteMultipleAddresses(Address $addresses): bool
    {
        return customDelete($addresses);
    }

    /**
     * Restore a specified address.
     *
     * @param Address $address
     * @return bool
     */
    final public function restoreAddress(Address $address): bool
    {
        return restore($address, ADDRESS1);
    }

    /**
     * Restore the selected addresses.
     *
     * @param Address $addresses
     * @return bool
     */
    final public function restoreMultipleAddresses(Address $addresses): bool
    {
        return restore($addresses);
    }
}

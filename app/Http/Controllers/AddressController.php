<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Services\AddressService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Throwable;

class AddressController extends Controller
{
    /**
     * Address Controller Constructor.
     *
     * @param AddressService $addressService
     */
    final public function __construct(private readonly AddressService $addressService){}

    /**
     * Get the detailed data of a specified user's addresses.
     *
     * @return Application|Factory|View|RedirectResponse
     * @throws Throwable
     */
    final public function userAddresses(): Application|Factory|View|RedirectResponse
    {
        return $this->addressService->getUserAddressesData();
    }

    /**
     * Store or Update an address.
     *
     * @param string $operation
     * @return Response
     * @throws ValidationException
     */
    final public function storeOrUpdate(string $operation): Response
    {
        $this->addressService->createOrUpdateAddress($operation);

        return responseSuccess();
    }

    /**
     * Get the data of a specified address.
     *
     * @param Address $address
     * @return JsonResponse
     */
    final public function edit(Address $address): JsonResponse
    {
        return responseWithData([ADDRESS_MODEL => $address->data]);
    }

    /**
     * Delete a specified address.
     *
     * @param Address $address
     * @return Response
     */
    final public function destroy(Address $address): Response
    {
        $this->addressService->deleteAddress($address);

        return responseSuccess();
    }

    /**
     * Delete the selected addresses.
     *
     * @param Address $addresses
     * @return Response
     */
    final public function destroyMultiple(Address $addresses): Response
    {
        $this->addressService->deleteMultipleAddresses($addresses);

        return responseSuccess();
    }

    /**
     * Restore a specified address.
     *
     * @param Address $address
     * @return Response
     */
    final public function restore(Address $address): Response
    {
        $this->addressService->restoreAddress($address);

        return responseSuccess();
    }

    /**
     * Restore the selected addresses.
     *
     * @param Address $addresses
     * @return Response
     */
    final public function restoreMultiple(Address $addresses): Response
    {
        $this->addressService->restoreMultipleAddresses($addresses);

        return responseSuccess();
    }
}

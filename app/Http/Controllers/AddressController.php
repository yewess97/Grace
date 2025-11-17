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
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Psr\SimpleCache\InvalidArgumentException as CacheInvalidArgumentException;
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
     * @return Application|Factory|View|RedirectResponse|JsonResponse
     * @throws Throwable
     */
    final public function userAddresses(): Application|Factory|View|RedirectResponse|JsonResponse
    {
        return $this->addressService->getUserAddressesData();
    }

    /**
     * Store or Update an address.
     *
     * @param string $operation
     * @return JsonResponse
     * @throws ValidationException|Throwable
     */
    final public function storeOrUpdate(string $operation): JsonResponse
    {
        $address = $this->addressService->createOrUpdateAddress($operation);

        $row       = view(ADDRESS_ROW_PARTIAL, compact(ADDRESS_MODEL))->render();
        $last_page = getLastPage($address);

        return responseWithData(compact(ROW, LAST_PAGE));
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
     * @throws CacheInvalidArgumentException|ModelNotFoundException
     */
    final public function destroy(Address $address): Response
    {
        $address_deleted = $this->addressService->deleteAddress($address);

        return $address_deleted
            ? responseSuccess()
            : throw new ModelNotFoundException('The '.ADDRESS_MODEL.' you are trying to '.REMOVE.'/'.DELETE.' is not found!');
    }

    /**
     * Delete the selected addresses.
     *
     * @param Address $addresses
     * @return Response
     * @throws CacheInvalidArgumentException|ModelNotFoundException
     */
    final public function destroyMultiple(Address $addresses): Response
    {
        $addresses_deleted = $this->addressService->deleteMultipleAddresses($addresses);

        return $addresses_deleted
            ? responseSuccess()
            : throw new ModelNotFoundException('The '.ADDRESSES_TABLE.' (or some of them) you are trying to '.REMOVE.'/'.DELETE.' are not found!');
    }

    /**
     * Restore a specified address.
     *
     * @param Address $address
     * @return Response
     * @throws CacheInvalidArgumentException|ModelNotFoundException
     */
    final public function restore(Address $address): Response
    {
        $address_restored = $this->addressService->restoreAddress($address);

        return $address_restored
            ? responseSuccess()
            : throw new ModelNotFoundException('The '.ADDRESS_MODEL.' you are trying to '.RESTORE.' is not found!');
    }

    /**
     * Restore the selected addresses.
     *
     * @param Address $addresses
     * @return Response
     * @throws CacheInvalidArgumentException|ModelNotFoundException
     */
    final public function restoreMultiple(Address $addresses): Response
    {
        $addresses_restored = $this->addressService->restoreMultipleAddresses($addresses);

        return $addresses_restored
            ? responseSuccess()
            : throw new ModelNotFoundException('The '.ADDRESSES_TABLE.' (or some of them) you are trying to '.RESTORE.'are not found!');
    }
}

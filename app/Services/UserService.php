<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Validation\ValidationException;
use Throwable;

class UserService
{
    /**
     * Get all the data of a specified user with relations.
     *
     * @return Application|Factory|View|JsonResponse
     * @throws Throwable
     */
    final public function getUserProfile(): Application|Factory|View|JsonResponse
    {
        $user_profile_title = auth()->user()?->{FULL_NAME}.' - '.ucfirst(PROFILE);
        $user               = cache()->remember(USER_MODEL, 1800, static fn() => User::profileData());
        $user_orders        = cache()->remember(paginationCacheKey(USER_ORDERS), 1800, static fn():
            LengthAwarePaginator => $user->{ORDERS_TABLE}()->fastPaginate(5)
        );

        return request()?->ajax()
            ? ajaxPaginationResponse($user_orders, PROFILE_ORDERS_PAGINATION, USER_ORDERS, compact(USER_MODEL))
            : showView(USER_PROFILE_VIEW, compact(USER_MODEL, USER_ORDERS, USER_PROFILE_TITLE));
    }

    /**
     * Store or Update a user.
     *
     * @param string $operation
     * @return array
     * @throws ValidationException
     */
    final public function createOrUpdateUser(string $operation): array
    {
        return [storeOrUpdateUser($operation), getLastPage(new User())];
    }

    /**
     * Delete a specified user.
     *
     * @param User $user
     * @return bool
     */
    final public function deleteUser(User $user): bool
    {
        forgetCacheFor(USERS_TABLE);
        forgetCacheFor(USER_ADDRESSES.auth()->id());
        forgetCacheFor(USER_ORDERS);
        cache()->forget(USER_MODEL);

        return customDelete($user, FULL_NAME);
    }

    /**
     * Delete the selected users.
     *
     * @param User $users
     * @return bool
     */
    final public function deleteMultipleUsers(User $users): bool
    {
        forgetCacheFor(USERS_TABLE);
        forgetCacheFor(USER_ADDRESSES.auth()->id());
        forgetCacheFor(USER_ORDERS);
        cache()->forget(USER_MODEL);

        return customDelete($users);
    }

    /**
     * Restore a specified user.
     *
     * @param User $user
     * @return bool
     */
    final public function restoreUser(User $user): bool
    {
        forgetCacheFor(USERS_TABLE);
        forgetCacheFor(USER_ADDRESSES.auth()->id());
        forgetCacheFor(USER_ORDERS);
        cache()->forget(USER_MODEL);

        return restore($user, FULL_NAME);
    }

    /**
     * Restore the selected users.
     *
     * @param User $users
     * @return bool
     */
    final public function restoreMultipleUsers(User $users): bool
    {
        forgetCacheFor(USERS_TABLE);
        forgetCacheFor(USER_ADDRESSES.auth()->id());
        forgetCacheFor(USER_ORDERS);
        cache()->forget(USER_MODEL);

        return restore($users);
    }
}


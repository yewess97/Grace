<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;
use Psr\SimpleCache\InvalidArgumentException as CacheInvalidArgumentException;
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

        $user_orders_ids = cache()->remember(USER_ORDERS_PAGINATION_CACHE_KEY, 1800, function () use ($user) {
            return $user->{ORDERS_TABLE}()
                ->pluck(ID)
                ->toArray();
        });

        $user_orders = paginateWithFallback(new Order(), $user_orders_ids);

        return request()?->ajax()
            ? ajaxPaginationResponse($user_orders, PROFILE_ORDERS_PAGINATION, USER_ORDERS, compact(USER_MODEL))
            : showView(USER_PROFILE_VIEW, compact(USER_MODEL, USER_ORDERS, USER_PROFILE_TITLE));
    }

    /**
     * Store or Update a user.
     *
     * @param string $operation
     * @return array
     * @throws ValidationException|CacheInvalidArgumentException
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
     * @throws CacheInvalidArgumentException
     */
    final public function deleteUser(User $user): bool
    {
        $deleted_user = removeDeleteOrRestore($user, $user->{FULL_NAME});

        $this->forgetUserCache();

        return $deleted_user;
    }

    /**
     * Delete the selected users.
     *
     * @param User $users
     * @return bool
     * @throws CacheInvalidArgumentException
     */
    final public function deleteMultipleUsers(User $users): bool
    {
        $deleted_users = removeDeleteOrRestore($users);

        $this->forgetUserCache();

        return $deleted_users;
    }

    /**
     * Restore a specified user.
     *
     * @param User $user
     * @return bool
     * @throws CacheInvalidArgumentException
     */
    final public function restoreUser(User $user): bool
    {
        $restored_user = removeDeleteOrRestore($user, $user->{FULL_NAME});

        $this->forgetUserCache();

        return $restored_user;
    }

    /**
     * Restore the selected users.
     *
     * @param User $users
     * @return bool
     * @throws CacheInvalidArgumentException
     */
    final public function restoreMultipleUsers(User $users): bool
    {
        $restored_users = removeDeleteOrRestore($users);

        $this->forgetUserCache();

        return $restored_users;
    }

    /**
     * Forget the user cache.
     *
     * @return void
     * @throws CacheInvalidArgumentException
     */
    private function forgetUserCache(): void
    {
        forgetCache([USERS_PAGINATION_CACHE_KEY, USER_ADDRESSES_PAGINATION_CACHE_KEY, USER_ORDERS_PAGINATION_CACHE_KEY, USER_MODEL]);
    }
}


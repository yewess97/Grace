<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Validation\ValidationException;
use Throwable;

class UserService
{
    /**
     * Get all the data of a specified user with relations.
     *
     * @return Application|Factory|View|string
     * @throws Throwable
     */
    final public function getUserProfile(): Application|Factory|View|string
    {
        $user               = User::profileData();
        $user_orders        = $user->{ORDERS_TABLE}()->fastPaginate(5);
        $user_profile_title = auth()->user()->{FULL_NAME}.' - '.ucfirst(PROFILE);

        if (request()?->ajax()) {
            return view(USER_PROFILE_PAGINATION, compact(USER_MODEL, USER_ORDERS))->render();
        }

        return showView(USER_PROFILE_VIEW, compact(USER_MODEL, USER_ORDERS, USER_PROFILE_TITLE));
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
        return delete($user);
    }

    /**
     * Delete the selected users.
     *
     * @param User $users
     * @return bool
     */
    final public function deleteMultipleUsers(User $users): bool
    {
        return delete($users, true);
    }

    /**
     * Restore a specified user.
     *
     * @param User $user
     * @return bool
     */
    final public function restoreUser(User $user): bool
    {
        return restore($user);
    }

    /**
     * Restore the selected users.
     *
     * @param User $users
     * @return bool
     */
    final public function restoreMultipleUsers(User $users): bool
    {
        return restore($users, true);
    }
}


<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\UserService;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Psr\SimpleCache\InvalidArgumentException as CacheInvalidArgumentException;
use Throwable;

class UserController extends Controller
{
    /**
     * User Controller Constructor.
     *
     * @param UserService $userService
     * @return void
     */
    final public function __construct(private readonly UserService $userService){}

    /**
     * Display the user's profile
     * By getting all the data of a specified user with relations.
     *
     * @return Application|Factory|View|JsonResponse
     * @throws Throwable
     */
    final public function profile(): Application|Factory|View|JsonResponse
    {
        return $this->userService->getUserProfile();
    }

    /**
     * Store or Update a user.
     *
     * @param string $operation
     * @return JsonResponse
     * @throws ValidationException|CacheInvalidArgumentException|Throwable
     */
    final public function storeOrUpdate(string $operation): JsonResponse
    {
        $user = $this->userService->createOrUpdateUser($operation);

        $row       = view(USER_ROW_PARTIAL, compact(USER_MODEL))->render();
        $last_page = getLastPage($user);

        return responseWithData(compact(ROW, LAST_PAGE));
    }

    /**
     * Get the data of a specified user.
     *
     * @param User $user
     * @return JsonResponse
     */
    final public function edit(User $user): JsonResponse
    {
        return responseWithData([USER_MODEL => $user->data]);
    }

    /**
     * Delete a specified user.
     *
     * @param User $user
     * @return Response
     * @throws CacheInvalidArgumentException|ModelNotFoundException
     */
    final public function destroy(User $user): Response
    {
        $user_deleted = $this->userService->deleteUser($user);

        return $user_deleted
            ? responseSuccess()
            : throw new ModelNotFoundException('The '.USER_MODEL.' you are trying to '.REMOVE.'/'.DELETE.' is not found!');
    }

    /**
     * Delete the selected users.
     *
     * @param User $users
     * @return Response
     * @throws CacheInvalidArgumentException|ModelNotFoundException
     */
    final public function destroyMultiple(User $users): Response
    {
        $users_deleted = $this->userService->deleteMultipleUsers($users);

        return $users_deleted
            ? responseSuccess()
            : throw new ModelNotFoundException('The '.USERS_TABLE.' (or some of them) you are trying to '.REMOVE.'/'.DELETE.' are not found!');
    }

    /**
     * Restore a specified user.
     *
     * @param User $user
     * @return Response
     * @throws CacheInvalidArgumentException|ModelNotFoundException
     */
    final public function restore(User $user): Response
    {
        $user_restored =  $this->userService->restoreUser($user);

        return $user_restored
            ? responseSuccess()
            : throw new ModelNotFoundException('The '.USER_MODEL.' you are trying to '.RESTORE.' is not found!');
    }

    /**
     * Restore the selected users.
     *
     * @param User $users
     * @return Response
     * @throws CacheInvalidArgumentException|ModelNotFoundException
     */
    final public function restoreMultiple(User $users): Response
    {
        $users_restored = $this->userService->restoreMultipleUsers($users);

        return $users_restored
            ? responseSuccess()
            : throw new ModelNotFoundException('The '.USERS_TABLE.' (or some of them) you are trying to '.RESTORE.' are not found!');
    }
}

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
     * @return Application|Factory|View|string
     * @throws Throwable
     */
    final public function profile(): Application|Factory|View|string
    {
        return $this->userService->getUserProfile();
    }

    /**
     * Store or Update a user.
     *
     * @param string $operation
     * @return Response
     * @throws ValidationException
     */
    final public function storeOrUpdate(string $operation): Response
    {
        $this->userService->createOrUpdateUser($operation);

        return responseSuccess();
    }

    /**
     * Get the data of a specified user.
     *
     * @param User $user
     * @return JsonResponse
     */
    final public function edit(User $user): JsonResponse
    {
        return $this->userService->getUserData($user);
    }

    /**
     * Delete a specified user.
     *
     * @param User $user
     * @return Response
     */
    final public function destroy(User $user): Response
    {
        $this->userService->deleteUser($user);

        return responseSuccess();
    }

    /**
     * Delete the selected users.
     *
     * @param User $users
     * @return Response
     */
    final public function destroyMultiple(User $users): Response
    {
        $this->userService->deleteMultipleUsers($users);

        return responseSuccess();
    }

    /**
     * Restore a specified user.
     *
     * @param User $user
     * @return Response
     */
    final public function restore(User $user): Response
    {
        $this->userService->restoreUser($user);

        return responseSuccess();
    }

    /**
     * Restore the selected users.
     *
     * @param User $users
     * @return Response
     */
    final public function restoreMultiple(User $users): Response
    {
        $this->userService->restoreMultipleUsers($users);

        return responseSuccess();
    }
}

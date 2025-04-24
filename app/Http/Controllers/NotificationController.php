<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;

class NotificationController extends Controller
{
    /**
     * Mark a notification as read.
     *
     * @return bool
     */
    final public function markAsRead(): JsonResponse
    {
        auth()->user()
            ->unreadNotifications()
            ->whereId(decrypt(request()?->input(ID)))
            ->first([ID, 'read_at'])
            ?->markAsRead();

        return responseSuccess(null, [ID => decrypt(request()?->input(ID))]);
    }

    /**
     * Mark all notifications as read.
     *
     * @return bool
     */
    final public function markAllAsRead(): Response
    {
        auth()->user()
            ->unreadNotifications
            ->markAsRead();

        return responseSuccess();
    }
}

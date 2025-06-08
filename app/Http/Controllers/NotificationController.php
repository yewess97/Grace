<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class NotificationController extends Controller
{
    /**
     * Display the admin's notifications.
     *
     * @return void
     */
    final public function index(): void
    {
        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no'); // For nginx, to disable buffering

        $unread_notification = auth()->user()
            ->unreadNotifications()
            ->first([ID, 'data', DATES[0]]);

        if ($unread_notification) {
            echo 'data: '.json_encode([
                'notification' => [
                    ID        => $unread_notification->{ID},
                    'message' => $unread_notification->data['message'],
                    DATES[0]  => $unread_notification->{DATES[0]}->diffForHumans(),
                ]
            ]).PHP_EOL.PHP_EOL;
        } 
        else {
            echo PHP_EOL.PHP_EOL;
        }

        ob_flush();
        flush();

        sleep(1);
    }

    /**
     * Mark a notification as read.
     *
     * @return JsonResponse
     */
    final public function markAsRead(): JsonResponse
    {
        auth()->user()
            ->unreadNotifications()
            ->whereId(request()?->input(ID))
            ->first([ID, 'read_at'])
            ?->markAsRead();

        return responseSuccess(null, [ID => request()?->input(ID)]);
    }

    /**
     * Mark all notifications as read.
     *
     * @return JsonResponse
     */
    final public function markAllAsRead(): JsonResponse
    {
        $unread_notifications = auth()->user()->unreadNotifications;
        
        $unread_notifications->markAsRead();

        return responseSuccess(null, [pluralize(ID) => $unread_notifications->pluck(ID)->toArray()]);
    }

    /**
     * Delete a notification.
     *
     * @return JsonResponse
     */
    final public function destroy(): JsonResponse
    {
        auth()->user()
            ->notifications()
            ->whereId(request()?->input(ID))
            ->delete();
            
        return responseSuccess(null, [ID => request()?->input(ID)]);
    }
}

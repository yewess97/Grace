<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Response;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

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

        sleep(2);






        // header('Content-Type: text/event-stream');
        // header('Cache-Control: no-cache');
        // header('Connection: keep-alive');
        // header('X-Accel-Buffering: no'); // For nginx, to disable buffering

        // $is_client_connected = static fn() => connection_aborted() === 0;

        // $last_id = 0; // Track last notification ID to avoid duplicates

        // while ($is_client_connected) {
        //     $notifications = auth()->user()
        //         ->unreadNotifications()
        //         ->where(ID, '>', $last_id)
        //         ->get([ID, 'data', 'created_at'])
        //         ->map(fn($notification) => [
        //             ID           => encrypt($notification->id),
        //             'message'    => $notification->data['message'],
        //             'created_at' => $notification->created_at->diffForHumans(),
        //         ]);

        //     if ($notifications->isNotEmpty()) {
        //         $last_id = $notifications->last()[ID]; // Update last ID
        //         $event_data = [
        //             'notifications_count' => $notifications->count(),
        //             'notifications'       => $notifications,
        //         ];

        //         echo "event: new_notification\n";
        //         echo "data: ".json_encode($event_data)."\n\n";
        //     } 
        //     else {
        //         echo ": keep-alive\n\n";
        //     }

        //     ob_flush();
        //     flush();
            
        //     sleep(2);
        // }

        // exit;


        // $headers = [
        //     'Content-Type'  => 'text/event-stream',
        //     'Cache-Control' => 'no-cache',
        //     'Connection'    => 'keep-alive',
        // ];

        // header('Content-Type: text/event-stream');
        // header('Cache-Control: no-cache');
        // header('Connection: keep-alive');

        // $notifications = auth()->user()
        // ->unreadNotifications()
        // ->get([ID, 'data', 'created_at'])
        // ->map(fn($notification) => [
        //     ID           => encrypt($notification->{ID}),
        //     'message'    => $notification->data['message'],
        //     'created_at' => $notification->created_at->diffForHumans(),
        // ]);

        // if ($notifications->isNotEmpty()) {
        //     $event_data = [
        //         'notifications_count' => $notifications->count(),
        //         'notifications'       => $notifications
        //     ];

        //     echo 'data: '.json_encode($event_data)."\n\n";
        // }
        // else {
        //     echo "\n\n";
        // }

        // ob_flush();
        // flush();

        // sleep(2);

        // while (true) {
        //     $notifications = auth()->user()
        //         ->unreadNotifications()
        //         ->get([ID, 'data', 'created_at'])
        //         ->map(fn($notification) => [
        //             ID           => encrypt($notification->{ID}),
        //             'message'    => $notification->data['message'],
        //             'created_at' => $notification->created_at->diffForHumans(),
        //         ]);

        //     $event_data = [
        //         'notifications_count' => $notifications->count(),
        //         'notifications'       => $notifications
        //     ];

        //     echo 'data: '.json_encode($event_data)."\n\n";

        //     ob_flush();
        //     flush();

        //     if (connection_aborted()) {
        //         break;
        //     }

        //     sleep(2);
        // }
        
        // return response()->stream(function () {
        //     while (true) {
        //         $notifications = auth()->user()
        //             ->unreadNotifications()
        //             ->get([ID, 'data', 'created_at'])
        //             ->map(fn($notification) => [
        //                 ID           => encrypt($notification->{ID}),
        //                 'message'    => $notification->data['message'],
        //                 'created_at' => $notification->created_at->diffForHumans(),
        //             ]);

        //         $event_data = [
        //             'notifications_count' => $notifications->count(),
        //             'notifications'       => $notifications
        //         ];

        //         echo 'data: '.json_encode($event_data)."\n\n";

        //         ob_flush();
        //         flush();

        //         if (connection_aborted()) {
        //             break;
        //         }

        //         sleep(2);
        //     }
        // }, HttpFoundationResponse::HTTP_OK, $headers);
    }

    /**
     * Mark a notification as read.
     *
     * @return bool
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

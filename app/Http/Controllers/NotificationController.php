<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

/**
 * NotificationController handles notification-related operations for the radiology report system.
 * Manages notification redirection and read status updates.
 */
class NotificationController extends Controller
{

    /**
     * Redirect user to the target URL specified in a notification and mark it as read.
     * Handles notification lookup, authentication, and status updates.
     */
    public function redirectNotification(Request $request)
    {
        // Get notification ID from query parameter
        $notificationId = $request->query('notification');

        // Debug line (commented out)
        // dd($notificationId);
        
        // Find the notification for the authenticated user
        $notification = auth()->user()->notifications()->where('id', $notificationId)->first();

        // Check if notification exists, return 404 if not found
        if (!$notification) {
            abort(404, 'Notification not found.');
        }

        // Mark notification as read if it's currently unread
        if ($notification->unread()) {
            $notification->markAsRead();
        }

        // Extract target URL from notification data, fallback to home route if not found
        $redirectUrl = $notification->data['url'] ?? route('home');

        // Redirect user to the target URL
        return redirect($redirectUrl);
    }

}

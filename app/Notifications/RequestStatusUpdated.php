<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\RequestListing;

class RequestStatusUpdated extends Notification
{
    use Queueable;

    protected $requestListing;

    public function __construct(RequestListing $requestListing)
    {
        $this->requestListing = $requestListing;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable)
    {
        $uuid = $this->requestListing->uuid;
        $examId = $this->requestListing->exam_id ?? $uuid;
        $status = $this->requestListing->status;

        if ($status === 'Completed') {
            return [
                'title' => "Report Ready for Download",
                'message' => "The report for the submitted case [Exam ID: {$examId}] is now ready. Please review and download it from the portal.",
                'request_uuid' => $uuid,
                'url' => route('request-listing.view', ['uuid' => $uuid]),
                'icon' => asset('images/report-icon.png'),
                'class' => 'yesterday-panel active', // optional for rendering
            ];
        }

        
        return [
            'title' => "Status Updated for Your Case - ID {$examId}",
            'message' => "The status for your case [Exam ID: {$examId}] has been updated by the admin. Please review the changes.",
            'request_uuid' => $uuid,
            'url' => route('request-listing.view', ['uuid' => $uuid]),
            'icon' => asset('images/yellow-notification-icon.png'),
            'class' => 'default-panel',
        ];
    }
}

<?php

// app/Notifications/PaymentReceived.php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use App\Models\RequestListing;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class PaymentReceived extends Notification
{
    use Queueable;

    protected $requestListing;

    public function __construct(RequestListing $requestListing)
    {
        $this->requestListing = $requestListing;
    }

    public function via($notifiable)
    {
        return ['database']; // You can also add 'mail' here if needed
    }

    
    public function toDatabase($notifiable)
    {
        $uuid = $this->requestListing->uuid;
        $examId = $this->requestListing->exam_id ?? $uuid; // fallback to uuid if no exam_id

        return [
            'title' => "Payment Confirmed for Pending Case - ID {$examId}",
            'message' => "Payment Received - Case Ready for Processing [Exam ID: {$examId}]. Payment has been successfully processed for the following case, and it is now ready for review.",
            'request_uuid' => $uuid,
            // 'url' => route('request-listing.index'),
            'url' => route('request-listing.view', ['uuid' => $uuid]),
        ];
    }

}


<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\RequestListing;
use App\Models\User;

class StatusChangeApproved extends Notification
{
    use Queueable;

    protected $requestListing;
    protected $approvedBy;

    /**
     * Create a new notification instance.
     */
    public function __construct(RequestListing $requestListing, User $approvedBy)
    {
        $this->requestListing = $requestListing;
        $this->approvedBy = $approvedBy;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->line('The introduction to the notification.')
            ->action('Notification Action', url('/'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        $uuid = $this->requestListing->uuid;
        $examId = $this->requestListing->exam_id ?? $uuid;
        $approvedByName = $this->approvedBy->name ?? 'Unknown';

        return [
            'title' => "Status Change Approved",
            'message' => "Your status change request has been approved by {$approvedByName}.",
            'request_uuid' => $uuid,
            'url' => route('request-listing.view', ['uuid' => $uuid]),
            'icon' => asset('images/green-notification-icon.png'),
            'class' => 'default-panel',
        ];
    }
}

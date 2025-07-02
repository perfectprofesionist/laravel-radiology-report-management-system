<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\RequestListing;

class NotesStatusUpdated extends Notification
{
    use Queueable;

    protected $requestListing;
    protected $status;
    protected $updatedBy;
    protected $rejectionComment;
    protected $approvalComment;

    /**
     * Create a new notification instance.
     */
    public function __construct(RequestListing $requestListing, $status, $updatedBy, $rejectionComment = null, $approvalComment = null)
    {
        $this->requestListing = $requestListing;
        $this->status = $status;
        $this->updatedBy = $updatedBy;
        $this->rejectionComment = $rejectionComment;
        $this->approvalComment = $approvalComment;
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
        $updatedByName = $this->updatedBy->username ?? 'Unknown';

        $message = '';
        
        if ($this->status === 'pending') {
            $message = "Notes have been updated and need review and approval.";
        } elseif ($this->status === 'rejected') {
            $message = "Your notes have been rejected.";
            if ($this->rejectionComment) {
                $message .= " Reason: " . $this->rejectionComment;
            }
        } elseif ($this->status === 'approved') {
            $message = "Your notes have been approved.";
            if ($this->approvalComment) {
                $message .= " Comment: " . $this->approvalComment;
            }
            $message .= " You can view the approved notes in the case details.";
        }

        return [
            'title' => "Notes Status Updated",
            'message' => $message,
            'request_uuid' => $uuid,
            'url' => route('request-listing.view', ['uuid' => $uuid]),
            'icon' => asset('images/yellow-notification-icon.png'),
            'class' => 'default-panel',
        ];
    }
}

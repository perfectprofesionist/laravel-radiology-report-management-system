<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\RequestListing;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class NotesChangeRequested extends Notification
{
    use Queueable;

    protected $requestListing;
    protected $user;
    protected $token;

    /**
     * Create a new notification instance.
     */
    public function __construct(RequestListing $requestListing, User $user)
    {
        $this->requestListing = $requestListing;
        $this->user = $user;
        $this->token = Str::random(64);
        
        // Store the token in the database
        $this->requestListing->update([
            'admin_access_token' => $this->token,
            'token_expires_at' => now()->addHours(24) // Token expires in 24 hours
        ]);

        // Log for debugging
        Log::info('NotesChangeRequested notification created', [
            'request_uuid' => $this->requestListing->uuid,
            'user_id' => $this->user->id,
            /*'token' => $this->token*/
        ]);
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via($notifiable)
    {
        // Log for debugging
        Log::info('NotesChangeRequested notification channels', [
            'channels' => ['mail', 'database'],
            'notifiable' => $notifiable->email
        ]);
        
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail($notifiable)
    {
        $url = route('request-listing.view', [
            'uuid' => $this->requestListing->uuid
        ]);

        // Log for debugging
        Log::info('NotesChangeRequested mail being sent', [
            'to' => $notifiable->email,
            'url' => $url
        ]);

        return (new MailMessage)
            ->subject('Notes Change Request - Action Required')
            ->view('emails.notes-change-requested', [
                'notifiable' => $notifiable,
                'requestListing' => $this->requestListing,
                'user' => $this->user,
                'url' => $url
            ]);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray($notifiable)
    {
        return [
            'title' => 'Notes Change Request',
            'message' => 'A sub-admin has made changes to the notes for request #' . $this->requestListing->exam_id,
            'icon' => asset('images/yellow-notification-icon.png'),
            'url' => route('request-listing.view', [
                'uuid' => $this->requestListing->uuid,
                /*'token' => $this->token*/
            ]),
            'class' => 'bg-warning'
        ];
    }
}

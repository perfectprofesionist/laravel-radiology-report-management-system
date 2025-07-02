<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserRegistered extends Notification
{
    use Queueable;

    
    protected $newUser;
    /**
     * Create a new notification instance.
     */
    public function __construct($newUser)
    {
        $this->newUser = $newUser;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
     public function via($notifiable)
    {
        return ['database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toDatabase($notifiable)
    {
        return [
            'title' => 'New User Registered',
            'message' => "A new user, <strong>{$this->newUser->username}</strong>, has completed registration and awaits account activation.",
            'user_id' => $this->newUser->id,
            // 'url' => route('users.index'),
            'url' => route('check.user-details', ['uuid' => $this->newUser->uuid]),
        ];
    }

}

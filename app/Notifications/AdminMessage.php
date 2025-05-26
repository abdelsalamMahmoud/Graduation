<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminMessage extends Notification
{
    use Queueable;

    public $message ;

    public function __construct($message)
    {
        $this->message = $message;
    }
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
                'message'=> $this->message
        ];
    }
}

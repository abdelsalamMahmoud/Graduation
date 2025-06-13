<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SchedulesNotification extends Notification
{
    use Queueable;

    public $teacher_name , $message;

    public function __construct($teacher_name , $message)
    {
        $this->teacher_name = $teacher_name;
        $this->message = $message;
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }


    public function toArray(object $notifiable): array
    {
        return [
            'message'=> '  الطلب الذى ارسلته الى الشيخ  ' . $this->teacher_name . $this->message,
        ];
    }
}

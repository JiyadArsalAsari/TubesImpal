<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewQuizNotification extends Notification
{
    use Queueable;

    public $exercise;

    /**
     * Create a new notification instance.
     */
    public function __construct($exercise)
    {
        $this->exercise = $exercise;
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
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'quiz',
            'title' => 'New Quiz',
            'message' => 'New quiz "' . $this->exercise->title . '" has been published.',
            'exercise_id' => $this->exercise->id,
            'dosen_name' => $this->exercise->dosen->user->name ?? 'Dosen',
        ];
    }
}

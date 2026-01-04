<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class GradePublishedNotification extends Notification
{
    use Queueable;

    public $submission;
    public $exercise;

    /**
     * Create a new notification instance.
     */
    public function __construct($submission)
    {
        $this->submission = $submission;
        $this->exercise = $submission->exercise;
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
            'type' => 'grade',
            'title' => 'Grade Published',
            'message' => 'Your assignment "' . $this->exercise->title . '" has been graded.',
            'exercise_id' => $this->exercise->id,
            'exercise_type' => $this->exercise->type,
            'grade' => $this->submission->grade,
        ];
    }
}

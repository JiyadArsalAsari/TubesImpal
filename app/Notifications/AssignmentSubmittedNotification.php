<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AssignmentSubmittedNotification extends Notification
{
    use Queueable;

    public $submission;
    public $exercise;
    public $mahasiswaName;

    /**
     * Create a new notification instance.
     */
    public function __construct($submission)
    {
        $this->submission = $submission;
        $this->exercise = $submission->exercise;
        $this->mahasiswaName = $submission->mahasiswa->nama ?? 'Student';
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
            'type' => 'assignment_submitted',
            'title' => 'Assignment Submitted',
            'message' => $this->mahasiswaName . ' has submitted the assignment "' . $this->exercise->title . '".',
            'exercise_id' => $this->exercise->id,
            'mahasiswa_id' => $this->submission->mahasiswa_id,
            'mahasiswa_name' => $this->mahasiswaName,
        ];
    }
}

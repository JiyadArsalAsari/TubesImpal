<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class QuizSubmittedNotification extends Notification
{
    use Queueable;

    public $attempt;
    public $exercise;
    public $mahasiswaName;

    /**
     * Create a new notification instance.
     */
    public function __construct($attempt)
    {
        $this->attempt = $attempt;
        $this->exercise = $attempt->exercise;
        $this->mahasiswaName = $attempt->mahasiswa->nama ?? 'Student';
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
            'type' => 'quiz_submitted',
            'title' => 'Quiz Submitted',
            'message' => $this->mahasiswaName . ' has completed the quiz "' . $this->exercise->title . '".',
            'exercise_id' => $this->exercise->id,
            'mahasiswa_id' => $this->attempt->mahasiswa_id,
            'mahasiswa_name' => $this->mahasiswaName,
            'score' => $this->attempt->score,
        ];
    }
}

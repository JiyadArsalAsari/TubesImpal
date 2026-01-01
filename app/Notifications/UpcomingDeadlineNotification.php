<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Deadline;

class UpcomingDeadlineNotification extends Notification
{
    use Queueable;

    protected $deadline;

    /**
     * Create a new notification instance.
     */
    public function __construct(Deadline $deadline)
    {
        $this->deadline = $deadline;
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
            'title' => 'Deadline Reminder: ' . $this->deadline->subject_name,
            'message' => 'Assignment due on ' . $this->deadline->date . ' at ' . $this->deadline->time,
            'type' => 'deadline',
            'subject' => $this->deadline->subject_name,
            'time' => $this->deadline->time,
            'date' => $this->deadline->date,
        ];
    }
}

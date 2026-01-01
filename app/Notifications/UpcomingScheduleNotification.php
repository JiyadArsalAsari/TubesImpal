<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\Schedule;

class UpcomingScheduleNotification extends Notification
{
    use Queueable;

    protected $schedule;

    /**
     * Create a new notification instance.
     */
    public function __construct(Schedule $schedule)
    {
        $this->schedule = $schedule;
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
            'title' => 'Upcoming Class: ' . $this->schedule->subject_name,
            'message' => 'You have a class at ' . $this->schedule->time . ' in room ' . $this->schedule->room,
            'type' => 'schedule',
            'subject' => $this->schedule->subject_name,
            'time' => $this->schedule->time,
            'date' => $this->schedule->day,
            'room' => $this->schedule->room,
        ];
    }
}

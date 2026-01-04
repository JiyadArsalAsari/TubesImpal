<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RequestResponseNotification extends Notification
{
    use Queueable;

    public $request;
    public $status;
    public $mahasiswaName;

    /**
     * Create a new notification instance.
     */
    public function __construct($request, $status)
    {
        $this->request = $request;
        $this->status = $status;
        $this->mahasiswaName = $request->mahasiswa_name;
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
            'type' => 'request_response',
            'title' => 'Connection Request ' . ucfirst($this->status),
            'message' => $this->mahasiswaName . ' has ' . $this->status . ' your connection request.',
            'mahasiswa_id' => $this->request->mahasiswa_id,
            'mahasiswa_name' => $this->mahasiswaName,
            'status' => $this->status,
        ];
    }
}

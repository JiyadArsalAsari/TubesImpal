<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use App\Models\DosenMahasiswaRequest;

class DosenRequestNotification extends Notification
{
    use Queueable;

    protected $request;

    /**
     * Create a new notification instance.
     */
    public function __construct(DosenMahasiswaRequest $request)
    {
        $this->request = $request;
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
            'title' => 'New Connection Request',
            'message' => $this->request->dosen->user->name . ' wants to connect with you',
            'type' => 'dosen_request',
            'subject' => 'Connection Request',
            'request_id' => $this->request->id,
            'dosen_name' => $this->request->dosen->user->name,
        ];
    }
}
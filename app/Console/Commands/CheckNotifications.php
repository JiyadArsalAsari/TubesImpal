<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Mahasiswa;
use App\Models\Schedule;
use App\Models\Deadline;
use App\Notifications\UpcomingScheduleNotification;
use App\Notifications\UpcomingDeadlineNotification;
use Carbon\Carbon;

class CheckNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for upcoming schedules and deadlines and send notifications';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for notifications...');

        // Set timezone to Asia/Jakarta
        $now = Carbon::now('Asia/Jakarta');
        $todayDate = $now->toDateString();
        $todayDay = $now->format('l'); // e.g., "Thursday"

        $this->info("Checking notifications for: $todayDay, $todayDate");

        // 1. Check Schedules (Today)
        // Map English days to Indonesian days
        $englishDay = $todayDay; // e.g., "Sunday"
        $indonesianDays = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu'
        ];
        $indoDay = $indonesianDays[$englishDay] ?? $englishDay;

        // Match day name with multiple formats (English & Indonesian)
        $schedules = Schedule::where(function ($query) use ($englishDay, $indoDay) {
            // Check English formats
            $query->where('day', $englishDay)
                ->orWhere('day', strtolower($englishDay))
                ->orWhere('day', strtoupper($englishDay))
                // Check Indonesian formats
                ->orWhere('day', $indoDay)
                ->orWhere('day', strtolower($indoDay))
                ->orWhere('day', strtoupper($indoDay));
        })
            ->with('mahasiswa.user')
            ->get();

        foreach ($schedules as $schedule) {
            $user = $schedule->mahasiswa->user;
            if (!$user)
                continue;

            // Check if notification already sent today for this schedule
            $alreadySent = $user->notifications()
                ->where('created_at', '>=', $now->copy()->startOfDay())
                ->where('data->type', 'schedule')
                ->where('data->subject', $schedule->subject_name)
                ->exists();

            if (!$alreadySent) {
                $user->notify(new UpcomingScheduleNotification($schedule));
                $this->info("Sent schedule notification to {$user->username} for {$schedule->subject_name}");
            }
        }

        // 2. Check Deadlines (Today Only)
        // User requested: "what schedule and deadline are on that day" (specifically today)
        $deadlines = Deadline::whereDate('date', $todayDate)
            ->with('mahasiswa.user')
            ->get();

        foreach ($deadlines as $deadline) {
            $user = $deadline->mahasiswa->user;
            if (!$user)
                continue;

            // Check if notification already sent today for this deadline
            $alreadySent = $user->notifications()
                ->where('created_at', '>=', $now->copy()->startOfDay())
                ->where('data->type', 'deadline')
                ->where('data->subject', $deadline->subject_name)
                ->exists();

            if (!$alreadySent) {
                $user->notify(new UpcomingDeadlineNotification($deadline));
                $this->info("Sent deadline notification to {$user->username} for {$deadline->subject_name}");
            }
        }

        $this->info('Notifications check completed.');
    }
}

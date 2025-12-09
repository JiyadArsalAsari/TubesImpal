<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use App\Models\Deadline;

class MahasiswaController extends Controller
{
    public function dashboard()
    {
        // Ensure only mahasiswa can access this dashboard
        if (Auth::user()->role !== 'mahasiswa') {
            return redirect('/'); // Redirect non-mahasiswa users
        }

        // Get the authenticated mahasiswa data
        $mahasiswa = Auth::user()->mahasiswa;
        
        // Get today's schedule with proper timezone
        $today = Carbon::now()->setTimezone(config('app.timezone', 'Asia/Jakarta'))->format('l'); // Get day name (Monday, Tuesday, etc.)
        
        // Check with multiple formats to ensure compatibility
        $todaysSchedule = $mahasiswa->schedules()
            ->where('day', $today)
            ->orWhere('day', strtolower($today))
            ->orWhere('day', ucfirst(strtolower($today)))
            ->orderBy('time')
            ->first();
        
        // Get all schedules for today
        $allTodaysSchedules = $mahasiswa->schedules()
            ->where('day', $today)
            ->orWhere('day', strtolower($today))
            ->orWhere('day', ucfirst(strtolower($today)))
            ->orderBy('time')
            ->get();
        
        // Get today's deadline
        $todayDate = Carbon::now()->setTimezone(config('app.timezone', 'Asia/Jakarta'))->toDateString();
        $todaysDeadline = $mahasiswa->deadlines()
            ->where('date', $todayDate)
            ->orderBy('time')
            ->first();
        
        // Get all deadlines sorted by date and time
        $allDeadlines = $mahasiswa->deadlines->sortBy(function ($deadline) {
            return [
                $deadline->date,
                $deadline->time
            ];
        });

        // Return the dashboard view
        return view('mahasiswa.dashboard', compact('mahasiswa', 'todaysSchedule', 'allTodaysSchedules', 'todaysDeadline', 'allDeadlines'));
    }

    public function content()
    {
        // Ensure only mahasiswa can access this content
        if (Auth::user()->role !== 'mahasiswa') {
            return redirect('/'); // Redirect non-mahasiswa users
        }

        // Redirect to learning recommendation page
        return redirect()->route('mahasiswa.learning.recommendation');
    }

}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Schedule;

class ScheduleController extends Controller
{
    public function index()
    {
        // Ensure only mahasiswa can access this
        if (Auth::user()->role !== 'mahasiswa') {
            return redirect('/');
        }
        
        // Get the authenticated mahasiswa's schedules
        $mahasiswa = Auth::user()->mahasiswa;
        
        // Define day order for sorting (Monday first)
        $dayOrder = [
            'Monday' => 1,
            'Tuesday' => 2,
            'Wednesday' => 3,
            'Thursday' => 4,
            'Friday' => 5,
            'Saturday' => 6,
            'Sunday' => 7
        ];
        
        // Get schedules sorted by day and time
        $schedules = $mahasiswa->schedules->sortBy(function ($schedule) use ($dayOrder) {
            return [
                $dayOrder[$schedule->day] ?? 8, // Put unknown days at the end
                $schedule->time
            ];
        });
        
        return view('mahasiswa.schedule', compact('schedules'));
    }
    
    public function store(Request $request)
    {
        // Ensure only mahasiswa can access this
        if (Auth::user()->role !== 'mahasiswa') {
            return redirect('/');
        }
        
        // Validate the request
        $validated = $request->validate([
            'subject_name' => 'required|string|max:255',
            'day' => 'required|string|max:20',
            'time' => 'required|string|max:50',
            'room' => 'required|string|max:50',
        ]);
        
        // Add mahasiswa_id to the validated data
        $validated['mahasiswa_id'] = Auth::user()->mahasiswa->id;
        
        // Create the schedule
        Schedule::create($validated);
        
        return redirect()->back()->with('success', 'Schedule added successfully!');
    }
}

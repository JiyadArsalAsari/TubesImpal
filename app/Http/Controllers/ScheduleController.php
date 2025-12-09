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
            'time' => [
                'required',
                'string',
                'max:50',
                'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9] - ([0-1][0-9]|2[0-3]):[0-5][0-9]$/'
            ],
            'room' => 'required|string|max:50',
        ], [
            'time.regex' => 'The time format must be HH:MM - HH:MM (24-hour format), e.g., 08:00 - 10:00'
        ]);
        
        // Add mahasiswa_id to the validated data
        $validated['mahasiswa_id'] = Auth::user()->mahasiswa->id;
        
        // Create the schedule
        Schedule::create($validated);
        
        return redirect()->back()->with('success', 'Schedule added successfully!');
    }
    
    public function destroy($id)
    {
        // Ensure only mahasiswa can access this
        if (Auth::user()->role !== 'mahasiswa') {
            return redirect('/');
        }
        
        // Find the schedule
        $schedule = Schedule::findOrFail($id);
        
        // Check if the schedule belongs to the authenticated mahasiswa
        if ($schedule->mahasiswa_id !== Auth::user()->mahasiswa->id) {
            return redirect()->back()->with('error', 'Unauthorized access!');
        }
        
        // Delete the schedule
        $schedule->delete();
        
        return redirect()->back()->with('success', 'Schedule deleted successfully!');
    }
}

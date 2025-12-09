<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Deadline;

class DeadlineController extends Controller
{
    public function index()
    {
        // Ensure only mahasiswa can access this
        if (Auth::user()->role !== 'mahasiswa') {
            return redirect('/');
        }
        
        // Get the authenticated mahasiswa's deadlines
        $mahasiswa = Auth::user()->mahasiswa;
        
        // Get deadlines sorted by date and time
        $deadlines = $mahasiswa->deadlines->sortBy(function ($deadline) {
            return [
                $deadline->date,
                $deadline->time
            ];
        });
        
        return view('mahasiswa.deadline', compact('deadlines'));
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
            'date' => 'required|date',
            'time' => [
                'required',
                'string',
                'max:50',
                'regex:/^([0-1][0-9]|2[0-3]):[0-5][0-9]$/'
            ],
        ], [
            'time.regex' => 'The time format must be HH:MM (24-hour format), e.g., 08:00'
        ]);
        
        // Add mahasiswa_id to the validated data
        $validated['mahasiswa_id'] = Auth::user()->mahasiswa->id;
        
        // Add day based on the date
        $date = new \DateTime($validated['date']);
        $days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $validated['day'] = $days[$date->format('w')];
        
        // Create the deadline
        Deadline::create($validated);
        
        return redirect()->back()->with('success', 'Deadline added successfully!');
    }
    
    public function destroy($id)
    {
        // Ensure only mahasiswa can access this
        if (Auth::user()->role !== 'mahasiswa') {
            return redirect('/');
        }
        
        // Find the deadline
        $deadline = Deadline::findOrFail($id);
        
        // Check if the deadline belongs to the authenticated mahasiswa
        if ($deadline->mahasiswa_id !== Auth::user()->mahasiswa->id) {
            return redirect()->back()->with('error', 'Unauthorized access!');
        }
        
        // Delete the deadline
        $deadline->delete();
        
        return redirect()->back()->with('success', 'Deadline deleted successfully!');
    }
}
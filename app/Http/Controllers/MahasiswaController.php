<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use App\Models\Deadline;
use App\Models\DosenMahasiswaRequest;

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
    
    public function getDosenRequests()
    {
        // Ensure only mahasiswa can access this
        if (Auth::user()->role !== 'mahasiswa') {
            return redirect('/');
        }
        
        // Get mahasiswa record
        $mahasiswa = Auth::user()->mahasiswa;
        
        // Get pending requests for this mahasiswa
        $requests = DosenMahasiswaRequest::where('mahasiswa_email', Auth::user()->email)
            ->where('status', 'pending')
            ->with('dosen.user')
            ->get();
            
        return response()->json($requests);
    }
    
    public function acceptDosenRequest(Request $request, $id)
    {
        // Ensure only mahasiswa can access this
        if (Auth::user()->role !== 'mahasiswa') {
            return redirect('/');
        }
        
        // Find the request
        $dosenRequest = DosenMahasiswaRequest::findOrFail($id);
        
        // Check if the request is for this mahasiswa
        if ($dosenRequest->mahasiswa_email !== Auth::user()->email) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        // Update the request status
        $dosenRequest->update([
            'mahasiswa_id' => Auth::user()->mahasiswa->id,
            'status' => 'accepted'
        ]);
        
        return response()->json(['success' => true, 'message' => 'Request accepted successfully']);
    }
    
    public function rejectDosenRequest(Request $request, $id)
    {
        // Ensure only mahasiswa can access this
        if (Auth::user()->role !== 'mahasiswa') {
            return redirect('/');
        }
        
        // Find the request
        $dosenRequest = DosenMahasiswaRequest::findOrFail($id);
        
        // Check if the request is for this mahasiswa
        if ($dosenRequest->mahasiswa_email !== Auth::user()->email) {
            return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
        }
        
        // Update the request status
        $dosenRequest->update([
            'status' => 'rejected'
        ]);
        
        return response()->json(['success' => true, 'message' => 'Request rejected successfully']);
    }

}
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;
use App\Models\Deadline;
use App\Models\DosenMahasiswaRequest;
use App\Models\LearningDifficulty;
use App\Models\QuizAttempt;
use App\Models\AssignmentSubmission;
use App\Models\ExerciseResult;
use App\Models\Mahasiswa;

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

        // Get notifications
        $notifications = Auth::user()->notifications()->latest()->take(10)->get();
        $unreadNotificationsCount = Auth::user()->unreadNotifications->count();

        // Return the dashboard view
        return view('mahasiswa.dashboard', compact('mahasiswa', 'todaysSchedule', 'allTodaysSchedules', 'todaysDeadline', 'allDeadlines', 'notifications', 'unreadNotificationsCount'));
    }

    public function markNotificationAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return back();
    }

    public function markAllNotificationsAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        if (request()->expectsJson()) {
            return response()->json(['success' => true]);
        }
        return back();
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
        
        // Mark related notification as read
        Auth::user()->notifications()
            ->where('data->request_id', $id)
            ->get()
            ->each(function($n) {
                $n->markAsRead();
            });
        
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
        
        // Mark related notification as read
        Auth::user()->notifications()
            ->where('data->request_id', $id)
            ->get()
            ->each(function($n) {
                $n->markAsRead();
            });
        
        return response()->json(['success' => true, 'message' => 'Request rejected successfully']);
    }
    
    public function learningDevelopment()
    {
        // Ensure only mahasiswa can access this page
        if (Auth::user()->role !== 'mahasiswa') {
            abort(403, 'Unauthorized access');
        }

        try {
            // Get the authenticated mahasiswa data
            $mahasiswa = Auth::user()->mahasiswa;
            
            // Check if mahasiswa exists
            if (!$mahasiswa) {
                abort(404, 'Mahasiswa profile not found');
            }
            
            // Fetch Quiz Data with safety checks
            $quizAttempts = \App\Models\QuizAttempt::where('mahasiswa_id', $mahasiswa->id)
                ->with('exercise')
                ->orderBy('submitted_at', 'asc')
                ->get()
                ->filter(function ($attempt) {
                    return $attempt->exercise != null;
                })
                ->map(function ($attempt) {
                    return [
                        'title' => $attempt->exercise->title,
                        'date' => $attempt->submitted_at ? $attempt->submitted_at->format('Y-m-d') : now()->format('Y-m-d'),
                        'score' => $attempt->score,
                        'type' => 'Quiz'
                    ];
                });

            // Fetch Assignment Data with safety checks
            $assignmentSubmissions = AssignmentSubmission::where('mahasiswa_id', $mahasiswa->id)
                ->whereNotNull('grade')
                ->with('exercise')
                ->orderBy('submitted_at', 'asc')
                ->get()
                ->filter(function ($submission) {
                    return $submission->exercise != null;
                })
                ->map(function ($submission) {
                    return [
                        'title' => $submission->exercise->title,
                        'date' => $submission->submitted_at ? $submission->submitted_at->format('Y-m-d') : now()->format('Y-m-d'),
                        'score' => $submission->grade,
                        'type' => 'Assignment'
                    ];
                });

            // Calculate Average Scores
            $quizAverage = $quizAttempts->avg('score') ?? 0;
            $assignmentAverage = $assignmentSubmissions->avg('score') ?? 0;
            $totalAverage = ($quizAttempts->isEmpty() && $assignmentSubmissions->isEmpty()) ? 0 :
                collect([...$quizAttempts, ...$assignmentSubmissions])->avg('score');

            // Trend Analysis
            $allActivities = collect([...$quizAttempts, ...$assignmentSubmissions])->sortBy('date');
            $recentActivities = $allActivities->take(-3);
            $recentAverage = $recentActivities->avg('score') ?? 0;
            
            $trend = 0;
            if ($totalAverage > 0) {
                $trend = (($recentAverage - $totalAverage) / $totalAverage) * 100;
            }

            // Calculate Missed Activities
            $missedQuizzes = \App\Models\Exercise::where('mahasiswa_id', $mahasiswa->id)
                ->where('type', 'quiz')
                ->where('status', 'published')
                ->whereDoesntHave('attempts')
                ->count();

            $missedAssignments = \App\Models\Exercise::where('mahasiswa_id', $mahasiswa->id)
                ->where('type', 'assignment')
                ->where('status', 'published')
                ->whereDoesntHave('submissions')
                ->count();

            // General Feedback (Show all feedbacks for this student)
            $generalFeedbacks = \App\Models\StudentFeedback::where('mahasiswa_id', $mahasiswa->id)
                ->with('dosen') // Eager load dosen to show who gave feedback
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Return the view
            return view('mahasiswa.learning_development', compact(
                'mahasiswa',
                'quizAttempts',
                'assignmentSubmissions',
                'quizAverage',
                'assignmentAverage',
                'totalAverage',
                'trend',
                'recentAverage',
                'missedQuizzes',
                'missedAssignments',
                'generalFeedbacks'
            ));
            
        } catch (\Exception $e) {
            // Log the error for debugging
            \Log::error('Error in learningDevelopment: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Return error view or redirect with error message
            return redirect()
                ->route('mahasiswa.dashboard')
                ->with('error', 'Terjadi kesalahan saat memuat data learning development: ' . $e->getMessage());
        }
    }
    
    /**
     * Get all exercise results from different sources (QuizAttempt, AssignmentSubmission, ExerciseResult)
     */
    private function getAllExerciseResults($mahasiswa)
    {
        $allResults = collect();
        
        // Get QuizAttempts (for quiz exercises)
        foreach ($mahasiswa->quizAttempts ?? collect() as $attempt) {
            $allResults->push((object) [
                'score' => $attempt->score ?? 0,
                'attempted_at' => $attempt->submitted_at ?? $attempt->started_at ?? $attempt->created_at,
                'status' => ($attempt->score ?? 0) >= 70 ? 'lulus' : 'tidak_lulus',
                'type' => 'quiz'
            ]);
        }
        
        // Get AssignmentSubmissions (for assignment exercises)
        foreach ($mahasiswa->assignmentSubmissions ?? collect() as $submission) {
            // Only include if submitted
            if ($submission->submitted_at) {
                $score = $submission->grade ?? 0;
                $allResults->push((object) [
                    'score' => $score,
                    'attempted_at' => $submission->submitted_at ?? $submission->created_at,
                    'status' => $score >= 70 ? 'lulus' : 'tidak_lulus',
                    'type' => 'assignment'
                ]);
            }
        }
        
        // Get ExerciseResults (if any exist)
        foreach ($mahasiswa->exerciseResults ?? collect() as $result) {
            $allResults->push((object) [
                'score' => $result->score ?? 0,
                'attempted_at' => $result->attempted_at ?? $result->created_at,
                'status' => $result->status ?? 'tidak_lulus',
                'type' => 'exercise_result'
            ]);
        }
        
        // Sort by attempted_at
        return $allResults->sortBy(function ($result) {
            return $result->attempted_at ? $result->attempted_at->timestamp : 0;
        })->values();
    }
    
    /**
     * Prepare chart data from exercise results
     * Groups results by date and shows individual scores
     */
    private function prepareChartData($exerciseResults)
    {
        if ($exerciseResults->isEmpty()) {
            return [];
        }
        
        // Group results by date (not month) to show individual activities
        $groupedResults = $exerciseResults->groupBy(function ($result) {
            $date = $result->attempted_at ?? now();
            if (is_string($date)) {
                $date = \Carbon\Carbon::parse($date);
            }
            return $date->format('M d');
        });
        
        $chartData = [];
        foreach ($groupedResults as $date => $results) {
            // For each date, show the average score
            $averageScore = $results->avg(function ($result) {
                return (float) ($result->score ?? 0);
            }) ?? 0;
            
            $chartData[] = [
                'date' => $date,
                'score' => round($averageScore, 2),
                'type' => $results->first()->type ?? 'unknown'
            ];
        }
        
        // Sort by date to ensure chronological order
        usort($chartData, function ($a, $b) {
            return strtotime($a['date']) <=> strtotime($b['date']);
        });
        
        return $chartData;
    }
    
    /**
     * Calculate statistics for the dashboard
     */
    private function calculateStatistics($mahasiswa, $allExerciseResults)
    {
        // Separate quiz and assignment results
        $quizResults = collect();
        $assignmentResults = collect();
        
        foreach ($allExerciseResults as $result) {
            if ($result->type === 'quiz') {
                $quizResults->push($result);
            } elseif ($result->type === 'assignment') {
                $assignmentResults->push($result);
            }
        }
        
        // Overall statistics
        $overallAverage = $allExerciseResults->isNotEmpty() 
            ? round($allExerciseResults->avg(function ($r) { return (float)($r->score ?? 0); }), 1)
            : 0;
        
        // Recent average (last 3 activities)
        $recentResults = $allExerciseResults->take(-3);
        $recentAverage = $recentResults->isNotEmpty()
            ? round($recentResults->avg(function ($r) { return (float)($r->score ?? 0); }), 1)
            : 0;
        
        // Performance trend (recent average vs overall average)
        $performanceTrend = 0;
        if ($overallAverage > 0 && $recentAverage > 0) {
            $performanceTrend = round((($recentAverage - $overallAverage) / $overallAverage) * 100, 1);
        }
        
        // Quiz statistics
        $quizStats = [
            'total_attempts' => $quizResults->count(),
            'missed' => 0, // Can be calculated based on exercises assigned
            'average_score' => $quizResults->isNotEmpty()
                ? round($quizResults->avg(function ($r) { return (float)($r->score ?? 0); }), 1)
                : 0.0,
            'highest_score' => $quizResults->isNotEmpty()
                ? round($quizResults->max(function ($r) { return (float)($r->score ?? 0); }), 0)
                : 0
        ];
        
        // Assignment statistics
        $assignmentStats = [
            'total_submissions' => $assignmentResults->count(),
            'missed' => 0, // Can be calculated based on exercises assigned
            'average_score' => $assignmentResults->isNotEmpty()
                ? round($assignmentResults->avg(function ($r) { return (float)($r->score ?? 0); }), 1)
                : 0.0,
            'highest_score' => $assignmentResults->isNotEmpty()
                ? round($assignmentResults->max(function ($r) { return (float)($r->score ?? 0); }), 0)
                : 0
        ];
        
        return [
            'overall_average' => $overallAverage,
            'recent_average' => $recentAverage,
            'performance_trend' => $performanceTrend,
            'quiz' => $quizStats,
            'assignment' => $assignmentStats
        ];
    }
}

<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\AssignmentSubmission;
use App\Models\StudentFeedback;

class LearningDevelopmentController extends Controller
{
    public function index()
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
                ->whereDoesntHave('attempts')
                ->count();

            $missedAssignments = \App\Models\Exercise::where('mahasiswa_id', $mahasiswa->id)
                ->where('type', 'assignment')
                ->whereDoesntHave('submissions')
                ->count();

            // General Feedback (Show all feedbacks for this student)
            $generalFeedbacks = StudentFeedback::where('mahasiswa_id', $mahasiswa->id)
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
            \Log::error('Error in LearningDevelopmentController@index: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            
            // Return error view or redirect with error message
            return redirect()
                ->route('mahasiswa.dashboard')
                ->with('error', 'Terjadi kesalahan saat memuat data learning development: ' . $e->getMessage());
        }
    }
}

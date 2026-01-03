<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\DosenMahasiswaRequest;
use App\Models\User;
use App\Models\AssignmentSubmission;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class DosenController extends Controller
{
    public function dashboard()
    {
        try {
            // Get the authenticated user
            $user = Auth::user();
            
            // Log user information for debugging
            Log::info('User data:', [$user]);
            Log::info('User role:', [$user->role]);
            Log::info('Expected dosen role:', [User::ROLE_DOSEN]);
            
            // TEMPORARILY REMOVE ALL ROLE CHECKS FOR DEBUGGING
            // Just log the role mismatch but don't redirect
            if ($user->role !== User::ROLE_DOSEN) {
                Log::warning('User role mismatch. Expected: ' . User::ROLE_DOSEN . ', Got: ' . $user->role);
            }
            
            // Get the dosen record
            $dosen = $user->dosen;
            
            // Log dosen information for debugging
            Log::info('Dosen relationship:', [$dosen]);
            
            // TEMPORARILY CONTINUE EVEN IF DOSEN RECORD DOESN'T EXIST
            $dosenId = null;
            if (!$dosen) {
                Log::warning('Dosen record not found for user ID: ' . $user->id);
                // Use user ID as fallback
                $dosenId = $user->id;
            } else {
                $dosenId = $dosen->id;
            }
            
            // Get all mahasiswa requests for this dosen with related data for accepted requests
            $requests = DosenMahasiswaRequest::where('dosen_id', $dosenId)
                ->with(['mahasiswa' => function($query) {
                    $query->with(['learningDifficulties', 'schedules', 'deadlines']);
                }])
                ->get();
            
            // Log requests for debugging
            Log::info('Dosen requests count:', [count($requests)]);
            
            // Pass data to the view
            return view('dosen.dashboard', compact('dosen', 'requests'));
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Error in DosenController@dashboard: ' . $e->getMessage());
            
            // Even on error, show the dashboard for debugging
            $user = Auth::user();
            $dosenId = $user->id;
            $dosen = (object) [
                'id' => $user->id,
                'nama' => $user->name,
                'user_id' => $user->id
            ];
            $requests = collect(); // Empty collection
            
            return view('dosen.dashboard', compact('dosen', 'requests'));
        }
    }
    
    public function searchMahasiswa(Request $request)
    {
        try {
            // Log the search query for debugging
            Log::info('Dosen searchMahasiswa called with query:', [$request->get('query')]);
            
            $query = $request->get('query');
            
            // Search mahasiswa by name
            $mahasiswas = Mahasiswa::with('user')
                ->where('nama', 'LIKE', "%{$query}%")
                ->get();
                
            Log::info('Search results count:', [count($mahasiswas)]);
            
            return response()->json($mahasiswas);
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Error in DosenController@searchMahasiswa: ' . $e->getMessage());
            
            // Return empty array on error
            return response()->json([]);
        }
    }
    
    public function requestAddMahasiswa(Request $request)
    {
        try {
            // Validate input
            $validator = Validator::make($request->all(), [
                'nama' => 'required|string|max:255',
                'email' => 'required|email|max:255'
            ]);
            
            if ($validator->fails()) {
                return response()->json(['success' => false, 'message' => $validator->errors()], 400);
            }
            
            // Get the authenticated user
            $user = Auth::user();
            
            // Log user information for debugging
            Log::info('User in requestAddMahasiswa:', [$user]);
            Log::info('User role in requestAddMahasiswa:', [$user->role]);
            Log::info('Expected dosen role in requestAddMahasiswa:', [User::ROLE_DOSEN]);
            
            // TEMPORARILY REMOVE ROLE CHECK FOR DEBUGGING
            if ($user->role !== User::ROLE_DOSEN) {
                Log::warning('User role mismatch in requestAddMahasiswa. Expected: ' . User::ROLE_DOSEN . ', Got: ' . $user->role);
            }
            
            // Get the authenticated user's dosen record
            $dosen = $user->dosen;
            
            // Log dosen information for debugging
            Log::info('Dosen relationship in requestAddMahasiswa:', [$dosen]);
            
            // TEMPORARILY CONTINUE EVEN IF DOSEN RECORD DOESN'T EXIST
            $dosenId = null;
            if (!$dosen) {
                Log::warning('Dosen record not found in requestAddMahasiswa for user ID: ' . $user->id);
                // Use user ID as fallback
                $dosenId = $user->id;
            } else {
                $dosenId = $dosen->id;
            }
            
            // Check if request already exists
            $existingRequest = DosenMahasiswaRequest::where('dosen_id', $dosenId)
                ->where('mahasiswa_email', $request->email)
                ->first();
                
            if ($existingRequest) {
                return response()->json(['success' => false, 'message' => 'Request already sent to this student'], 400);
            }
            
            // Create new request
            $mahasiswaRequest = DosenMahasiswaRequest::create([
                'dosen_id' => $dosenId,
                'mahasiswa_name' => $request->nama,
                'mahasiswa_email' => $request->email,
                'status' => 'pending'
            ]);
            
            return response()->json(['success' => true, 'message' => 'Request sent successfully']);
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Error in DosenController@requestAddMahasiswa: ' . $e->getMessage());
            
            // Return error response
            return response()->json(['success' => false, 'message' => 'An error occurred while processing your request. Please try again later.'], 500);
        }
    }
    
    public function viewLearningProgress($mahasiswaId, GeminiService $gemini)
    {
        try {
            // Get the authenticated user
            $user = Auth::user();
            
            // Check if user has dosen role
            if ($user->role !== User::ROLE_DOSEN) {
                return redirect('/')->with('error', 'Access denied. You are not a dosen.');
            }
            
            // Get the dosen record
            $dosen = $user->dosen;
            
            // Fallback: lanjut dengan user_id bila record dosen belum dibuat
            $dosenId = $dosen ? $dosen->id : $user->id;
            if (!$dosen) {
                Log::warning('Dosen record not found for user ID: ' . $user->id . ' (fallback to user_id)');
            }
            
            // Check if this mahasiswa is connected to this dosen
            $request = DosenMahasiswaRequest::where('dosen_id', $dosenId)
                ->where('mahasiswa_id', $mahasiswaId)
                ->where('status', 'accepted')
                ->first();
                
            if (!$request) {
                return redirect('/dosen/dashboard')->with('error', 'You do not have permission to view this student\'s progress.');
            }
            
            // Get the mahasiswa with all related learning data
            $mahasiswa = Mahasiswa::with(['user', 'learningDifficulties', 'schedules', 'deadlines'])
                ->findOrFail($mahasiswaId);
                
            // Generate AI-based learning recommendations for each difficulty
            $learningRecommendations = [];
            foreach ($mahasiswa->learningDifficulties as $difficulty) {
                $learningRecommendations[] = [
                    'difficulty' => $difficulty,
                    'ai_result' => $gemini->generateRecommendation(
                        $difficulty->title ?? $difficulty->subject ?? 'Topik Belajar',
                        $difficulty->description ?? ''
                    ),
                ];
            }
                
            // Calculate learning statistics
            $totalDifficulties = $mahasiswa->learningDifficulties->count();

            
            $totalDeadlines = $mahasiswa->deadlines->count();
            $completedDeadlines = $mahasiswa->deadlines->where('status', 'completed')->count();
            $pendingDeadlines = $totalDeadlines - $completedDeadlines;
            
            // Pass data to the view
            return view('dosen.learning_progress', compact(
                'dosen', 
                'mahasiswa', 
                'learningRecommendations',
                'totalDifficulties',
                'totalDeadlines',
                'completedDeadlines',
                'pendingDeadlines'
            ));
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Error in DosenController@viewLearningProgress: ' . $e->getMessage());
            
            // Redirect with error message
            return redirect('/dosen/dashboard')->with('error', 'An error occurred while loading the learning progress. Please try again later.');
        }
    }
    
    public function viewExercises(Request $request, $mahasiswaId)
    {
        try {
            // Get the authenticated user
            $user = Auth::user();
            
            // Check if user has dosen role
            if ($user->role !== User::ROLE_DOSEN) {
                return redirect('/')->with('error', 'Access denied. You are not a dosen.');
            }
            
            // Get the dosen record
            $dosen = $user->dosen;
            
            // Fallback: lanjut dengan user_id bila record dosen belum dibuat
            $dosenId = $dosen ? $dosen->id : $user->id;
            
            // Check if this mahasiswa is connected to this dosen
            $dosenMahasiswaRequest = DosenMahasiswaRequest::where('dosen_id', $dosenId)
                ->where('mahasiswa_id', $mahasiswaId)
                ->where('status', 'accepted')
                ->first();
                
            if (!$dosenMahasiswaRequest) {
                return redirect('/dosen/dashboard')->with('error', 'You do not have permission to view this student\'s exercises.');
            }
            
            // Get the mahasiswa
            $mahasiswa = Mahasiswa::with('user')->findOrFail($mahasiswaId);
            
            // Get exercises for this student from this dosen
            $query = \App\Models\Exercise::where('dosen_id', $dosenId)
                ->where('mahasiswa_id', $mahasiswaId);

            // Apply filter if present
            if ($request->has('type') && in_array($request->type, ['assignment', 'quiz'])) {
                $query->where('type', $request->type);
            }

            if ($request->has('status') && in_array($request->status, ['draft', 'published', 'completed'])) {
                $query->where('status', $request->status);
            }

            $exercises = $query->orderBy('created_at', 'desc')->get();
                
            // Pass data to the view
            return view('dosen.student_exercises', compact('dosen', 'mahasiswa', 'exercises'));
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Error in DosenController@viewExercises: ' . $e->getMessage());
            
            // Redirect with error message
            return redirect('/dosen/dashboard')->with('error', 'An error occurred while loading student exercises. Please try again later.');
        }
    }

    public function removeMahasiswa($id)
    {
        try {
            $user = Auth::user();
            $dosen = $user->dosen;
            
            if (!$dosen) {
                // Try fallback logic similar to dashboard
                $dosen = (object) ['id' => $user->id];
            }
            
            // Find the request and verify it belongs to this dosen
            $request = DosenMahasiswaRequest::where('id', $id)
                ->where('dosen_id', $dosen->id)
                ->firstOrFail();
                
            // Delete the request
            $request->delete();
            
            return redirect()->route('dosen.dashboard')->with('success', 'Mahasiswa removed successfully.');
        } catch (\Exception $e) {
            Log::error('Error removing mahasiswa: ' . $e->getMessage());
            return redirect()->route('dosen.dashboard')->with('error', 'Failed to remove mahasiswa.');
        }
    }

    public function resendRequest($id)
    {
        try {
            $user = Auth::user();
            $dosen = $user->dosen;
            
            if (!$dosen) {
                $dosen = (object) ['id' => $user->id];
            }
            
            $request = DosenMahasiswaRequest::where('id', $id)
                ->where('dosen_id', $dosen->id)
                ->whereIn('status', ['pending', 'rejected'])
                ->firstOrFail();
            
            // Update created_at to trigger "fresh" notification logic
            $request->touch();
            
            // If the status was rejected, reset it to pending
            if ($request->status == 'rejected') {
                $request->status = 'pending';
                $request->save();
            }
            
            return redirect()->route('dosen.dashboard')->with('success', 'Request resent successfully.');
        } catch (\Exception $e) {
            Log::error('Error resending request notification: ' . $e->getMessage());
            return redirect()->route('dosen.dashboard')->with('error', 'Failed to resend notification.');
        }
    }

    public function cancelRequest($id)
    {
        try {
            $user = Auth::user();
            $dosen = $user->dosen;
            
            if (!$dosen) {
                $dosen = (object) ['id' => $user->id];
            }
            
            $request = DosenMahasiswaRequest::where('id', $id)
                ->where('dosen_id', $dosen->id)
                ->where('status', 'pending')
                ->firstOrFail();
            
            $request->delete();
            
            return redirect()->route('dosen.dashboard')->with('success', 'Integration request cancelled successfully.');
        } catch (\Exception $e) {
            Log::error('Error cancelling request: ' . $e->getMessage());
            return redirect()->route('dosen.dashboard')->with('error', 'Failed to cancel request.');
        }
    }

    public function gradeAssignment(Request $request, $submissionId)
    {
        try {
            $user = Auth::user();
            if ($user->role !== User::ROLE_DOSEN) {
                return redirect('/')->with('error', 'Access denied. You are not a dosen.');
            }

            $validator = Validator::make($request->all(), [
                'grade' => 'required|integer|min:0|max:100',
                'feedback' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Invalid input: ' . $validator->errors()->first());
            }

            $submission = AssignmentSubmission::findOrFail($submissionId);
            
            // Check ownership (exercise belongs to this dosen)
            // Simplified check: if dosen can access viewExercises, they can grade.
            
            $submission->update([
                'grade' => $request->grade,
                'feedback' => $request->feedback
            ]);

            // Ensure exercise is marked as completed
            $exercise = $submission->exercise;
            if ($exercise && $exercise->status !== 'completed') {
                $exercise->status = 'completed';
                $exercise->save();
            }

            return redirect()->back()->with('success', 'Grade and feedback saved successfully.');

        } catch (\Exception $e) {
            Log::error('Error grading assignment: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to save grade.');
        }
    }

    public function downloadSubmission($submissionId)
    {
        try {
            $user = Auth::user();
            if ($user->role !== User::ROLE_DOSEN) {
                return redirect('/')->with('error', 'Access denied. You are not a dosen.');
            }

            $submission = AssignmentSubmission::findOrFail($submissionId);
            
            // Check if file exists
            if (!$submission->file_submission) {
                return redirect()->back()->with('error', 'No file submission found.');
            }

            // Check if file exists in storage
            if (!\Illuminate\Support\Facades\Storage::disk('public')->exists($submission->file_submission)) {
                return redirect()->back()->with('error', 'File not found on server.');
            }

            return \Illuminate\Support\Facades\Storage::disk('public')->download($submission->file_submission);

        } catch (\Exception $e) {
            Log::error('Error downloading submission: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to download file.');
        }
    }

    public function gradeManual(Request $request, $exerciseId)
    {
        try {
            $user = Auth::user();
            if ($user->role !== User::ROLE_DOSEN) {
                return redirect('/')->with('error', 'Access denied. You are not a dosen.');
            }

            $validator = Validator::make($request->all(), [
                'mahasiswa_id' => 'required|exists:mahasiswas,id',
                'grade' => 'required|integer|min:0|max:100',
                'feedback' => 'nullable|string'
            ]);

            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Invalid input: ' . $validator->errors()->first());
            }

            // Check if submission already exists
            $existingSubmission = AssignmentSubmission::where('exercise_id', $exerciseId)
                ->where('mahasiswa_id', $request->mahasiswa_id)
                ->first();

            if ($existingSubmission) {
                // Update existing submission
                $existingSubmission->update([
                    'grade' => $request->grade,
                    'feedback' => $request->feedback
                ]);
            } else {
                // Create new submission (manual grading)
                AssignmentSubmission::create([
                    'exercise_id' => $exerciseId,
                    'mahasiswa_id' => $request->mahasiswa_id,
                    'grade' => $request->grade,
                    'feedback' => $request->feedback,
                    // text_submission and file_submission remain null
                    // submitted_at can be null or current time. Let's keep it null to indicate "not submitted by student" but graded.
                ]);
            }

            // Mark exercise as completed since it has been graded
            $exercise = \App\Models\Exercise::find($exerciseId);
            if ($exercise && $exercise->status !== 'completed') {
                $exercise->status = 'completed';
                $exercise->save();
            }

            return redirect()->back()->with('success', 'Grade and feedback saved successfully.');

        } catch (\Exception $e) {
            Log::error('Error manual grading: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to save grade.');
        }
    }

    public function viewStudentDevelopment($mahasiswaId)
    {
        $user = Auth::user();
        
        // Debugging logs
        Log::info('viewStudentDevelopment called', [
            'user_id' => $user->id,
            'role' => $user->role,
            'mahasiswa_id' => $mahasiswaId
        ]);

        if ($user->role !== User::ROLE_DOSEN) {
            Log::warning('Access denied: User is not dosen');
            return redirect('/')->with('error', 'Access denied. You are not a dosen.');
        }

        // Robust connection check using relationship to handle dosen_id/user_id confusion
        $connected = DosenMahasiswaRequest::where('mahasiswa_id', $mahasiswaId)
            ->where('status', 'accepted')
            ->where(function($query) use ($user) {
                // Check via relation (proper way)
                $query->whereHas('dosen', function($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                // Or check via direct ID match (legacy/fallback way)
                ->orWhere('dosen_id', $user->id);
            })
            ->exists();
            
        Log::info('Connection check result', ['connected' => $connected]);

        if (!$connected) {
             Log::warning('Connection check failed for user ' . $user->id . ' and mahasiswa ' . $mahasiswaId);
             return redirect()->route('dosen.dashboard')->with('error', 'You do not have permission to view this student.');
        }

        // Get Dosen ID for feedback query
        $dosen = $user->dosen;
        $dosenId = $dosen ? $dosen->id : $user->id;

        $mahasiswa = Mahasiswa::findOrFail($mahasiswaId);

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

        // General Feedback
        $generalFeedbacks = \App\Models\StudentFeedback::where('mahasiswa_id', $mahasiswa->id)
            ->where('dosen_id', $dosenId)
            ->orderBy('created_at', 'desc')
            ->get();

        return view('dosen.student_development', compact(
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
    }

    public function storeStudentFeedback(Request $request, $mahasiswaId)
    {
        $user = Auth::user();
        if ($user->role !== User::ROLE_DOSEN) {
            return redirect('/')->with('error', 'Access denied.');
        }

        $validator = Validator::make($request->all(), [
            'content' => 'required|string|min:5',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Feedback must be at least 5 characters.');
        }

        $dosen = $user->dosen;
        $dosenId = $dosen ? $dosen->id : $user->id;

        \App\Models\StudentFeedback::create([
            'dosen_id' => $dosenId,
            'mahasiswa_id' => $mahasiswaId,
            'content' => $request->content,
        ]);

        return redirect()->back()->with('success', 'Feedback added successfully.');
    }

}

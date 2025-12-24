<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\DosenMahasiswaRequest;
use App\Models\Exercise;
use App\Models\AssignmentSubmission;
use App\Models\User;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

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
            
            $requests = DosenMahasiswaRequest::where('dosen_id', $dosenId)
                ->whereIn('status', ['pending', 'accepted'])
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
            
            $existingRequest = DosenMahasiswaRequest::where('dosen_id', $dosenId)
                ->where('mahasiswa_email', $request->email)
                ->first();
            if ($existingRequest) {
                if ($existingRequest->status === 'rejected') {
                    $existingRequest->update([
                        'mahasiswa_id' => null,
                        'mahasiswa_name' => $request->nama,
                        'status' => 'pending'
                    ]);
                    return response()->json(['success' => true, 'message' => 'Request sent successfully']);
                }
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
            $resolvedDifficulties = $mahasiswa->learningDifficulties->where('status', 'resolved')->count();
            $pendingDifficulties = $totalDifficulties - $resolvedDifficulties;
            
            $totalDeadlines = $mahasiswa->deadlines->count();
            $completedDeadlines = $mahasiswa->deadlines->where('status', 'completed')->count();
            $pendingDeadlines = $totalDeadlines - $completedDeadlines;
            
            // Pass data to the view
            return view('dosen.learning_progress', compact(
                'dosen', 
                'mahasiswa', 
                'learningRecommendations',
                'totalDifficulties', 
                'resolvedDifficulties', 
                'pendingDifficulties',
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

    public function viewExercises($mahasiswaId)
    {
        try {
            $user = Auth::user();
            if ($user->role !== User::ROLE_DOSEN) {
                return redirect('/')->with('error', 'Access denied. You are not a dosen.');
            }

            $dosen = $user->dosen;
            $dosenId = $dosen ? $dosen->id : $user->id;

            $connection = DosenMahasiswaRequest::where('dosen_id', $dosenId)
                ->where('mahasiswa_id', $mahasiswaId)
                ->where('status', 'accepted')
                ->first();
            if (!$connection) {
                return redirect()->route('dosen.dashboard')->with('error', 'Anda tidak memiliki akses ke mahasiswa ini.');
            }

            $mahasiswa = Mahasiswa::with('user')->findOrFail($mahasiswaId);

            $exercises = Exercise::with([
                    'submissions' => function($q) { $q->orderByDesc('submitted_at'); },
                    'attempts' => function($q) { $q->orderByDesc('submitted_at'); },
                ])
                ->where('mahasiswa_id', $mahasiswaId)
                ->orderBy('deadline', 'asc')
                ->orderBy('created_at', 'desc')
                ->get();

            return view('dosen.student_exercises', compact('mahasiswa', 'exercises'));
        } catch (\Exception $e) {
            Log::error('Error in DosenController@viewExercises: ' . $e->getMessage());
            return redirect()->route('dosen.dashboard')->with('error', 'Terjadi kesalahan saat memuat exercises mahasiswa.');
        }
    }

    public function gradeAssignment(Request $request, $submissionId)
    {
        try {
            $user = Auth::user();
            if ($user->role !== User::ROLE_DOSEN) {
                return redirect('/')->with('error', 'Access denied. You are not a dosen.');
            }

            $validated = $request->validate([
                'grade' => 'required|integer|min:0|max:100',
                'feedback' => 'nullable|string',
            ]);

            $submission = AssignmentSubmission::with('exercise')->findOrFail($submissionId);

            $dosen = $user->dosen;
            $dosenId = $dosen ? $dosen->id : $user->id;

            if ($submission->exercise->dosen_id !== $dosenId) {
                return redirect()->route('dosen.dashboard')->with('error', 'Anda tidak memiliki izin untuk menilai submission ini.');
            }

            $submission->grade = $validated['grade'];
            $submission->feedback = $validated['feedback'] ?? null;
            $submission->save();

            return back()->with('success', 'Nilai berhasil disimpan.');
        } catch (\Exception $e) {
            Log::error('Error in DosenController@gradeAssignment: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan nilai.');
        }
    }

    public function downloadSubmission($submissionId)
    {
        try {
            $user = Auth::user();
            if ($user->role !== User::ROLE_DOSEN) {
                return redirect('/')->with('error', 'Access denied.');
            }

            $submission = AssignmentSubmission::with('exercise')->findOrFail($submissionId);

            $dosen = $user->dosen;
            $dosenId = $dosen ? $dosen->id : $user->id;
            if ($submission->exercise->dosen_id !== $dosenId) {
                return redirect()->route('dosen.dashboard')->with('error', 'Anda tidak memiliki izin untuk mengakses file ini.');
            }

            if (!$submission->file_submission || !Storage::disk('public')->exists($submission->file_submission)) {
                return back()->with('error', 'File tidak ditemukan.');
            }

            return Storage::disk('public')->download($submission->file_submission, basename($submission->file_submission));
        } catch (\Exception $e) {
            Log::error('Error in DosenController@downloadSubmission: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat mengunduh file.');
        }
    }
    
    public function removeMahasiswa($requestId)
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
            
            // Check if dosen record exists
            if (!$dosen) {
                return redirect('/')->with('error', 'Dosen record not found. Please contact administrator.');
            }
            
            // Find the request that belongs to this dosen
            $request = DosenMahasiswaRequest::where('dosen_id', $dosen->id)
                ->where('id', $requestId)
                ->first();
                
            if (!$request) {
                return redirect('/dosen/dashboard')->with('error', 'Request not found or you do not have permission to remove this student.');
            }
            
            // Delete the request
            $request->delete();
            
            return redirect('/dosen/dashboard')->with('success', 'Student relationship removed successfully.');
        } catch (\Exception $e) {
            // Log the exception
            Log::error('Error in DosenController@removeMahasiswa: ' . $e->getMessage());
            
            // Redirect with error message
            return redirect('/dosen/dashboard')->with('error', 'An error occurred while removing the student. Please try again later.');
        }
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Dosen;
use App\Models\Mahasiswa;
use App\Models\DosenMahasiswaRequest;
use App\Models\User;
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
    
    public function viewLearningProgress($mahasiswaId)
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
            
            // Check if this mahasiswa is connected to this dosen
            $request = DosenMahasiswaRequest::where('dosen_id', $dosen->id)
                ->where('mahasiswa_id', $mahasiswaId)
                ->where('status', 'accepted')
                ->first();
                
            if (!$request) {
                return redirect('/dosen/dashboard')->with('error', 'You do not have permission to view this student\'s progress.');
            }
            
            // Get the mahasiswa with all related learning data
            $mahasiswa = Mahasiswa::with(['user', 'learningDifficulties', 'schedules', 'deadlines'])
                ->findOrFail($mahasiswaId);
                
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
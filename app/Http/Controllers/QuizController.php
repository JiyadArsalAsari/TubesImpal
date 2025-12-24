<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\QuizAnswer;
use App\Models\QuizAttempt;
use App\Models\QuizOption;
use App\Models\QuizQuestion;
use App\Models\User;
use App\Models\DosenMahasiswaRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class QuizController extends Controller
{
    /**
     * Form buat quiz (MCQ) untuk mahasiswa terhubung.
     */
    public function create($mahasiswaId)
    {
        $user = Auth::user();
        if ($user->role !== User::ROLE_DOSEN) {
            return redirect('/')->with('error', 'Hanya dosen yang dapat membuat quiz.');
        }

        $dosenId = $user->dosen ? $user->dosen->id : $user->id;
        $connected = DosenMahasiswaRequest::where('dosen_id', $dosenId)
            ->where('mahasiswa_id', $mahasiswaId)
            ->where('status', 'accepted')
            ->exists();

        if (!$connected) {
            return redirect()->route('dosen.dashboard')->with('error', 'Anda tidak memiliki akses ke mahasiswa ini.');
        }

        return view('dosen.create_quiz', ['mahasiswaId' => $mahasiswaId]);
    }

    /**
     * Simpan quiz dan soal-soalnya.
     */
    public function store(Request $request, $mahasiswaId)
    {
        Log::info('=== QUIZ CONTROLLER STORE METHOD CALLED ===');
        Log::info('QuizController@store method called with mahasiswaId: ' . $mahasiswaId);
        
        $user = Auth::user();
        Log::info('User authenticated: ' . ($user ? 'Yes' : 'No') . ', Role: ' . ($user ? $user->role : 'None'));
        
        if ($user->role !== User::ROLE_DOSEN) {
            Log::warning('User is not a dosen, redirecting');
            return redirect('/')->with('error', 'Hanya dosen yang dapat membuat quiz.');
        }

        $dosenId = $user->dosen ? $user->dosen->id : $user->id;
        Log::info('Dosen ID: ' . $dosenId);
        
        $connected = DosenMahasiswaRequest::where('dosen_id', $dosenId)
            ->where('mahasiswa_id', $mahasiswaId)
            ->where('status', 'accepted')
            ->exists();
        
        Log::info('Connection check result: ' . ($connected ? 'Connected' : 'Not connected'));

        if (!$connected) {
            Log::warning('Dosen does not have access to mahasiswa, redirecting');
            return redirect()->route('dosen.dashboard')->with('error', 'Anda tidak memiliki akses ke mahasiswa ini.');
        }

        $maxQuestions = env('MAX_QUIZ_QUESTIONS', 50);
        Log::info('Max questions allowed: ' . $maxQuestions);
        
        // Log request data for debugging
        Log::info('Request data: ', $request->all());
        
        // Simplified approach - try to create quiz without complex validation first
        try {
            Log::info('Attempting simplified quiz creation');
            
            // Create exercise
            $exercise = Exercise::create([
                'dosen_id' => $dosenId,
                'mahasiswa_id' => $mahasiswaId,
                'type' => 'quiz',
                'title' => $request->get('title', 'Untitled Quiz'),
                'description' => $request->get('description'),
                'deadline' => $request->get('deadline'),
                'duration_minutes' => $request->get('duration_minutes'),
                'status' => 'published',
                'max_attempts' => $request->get('max_attempts', 1),
            ]);
            
            Log::info('Created exercise with ID: ' . $exercise->id);
            
            // Try to create questions if they exist in request
            $questionsData = $request->get('questions', []);
            Log::info('Found ' . count($questionsData) . ' questions in request');
            
            foreach ($questionsData as $qIndex => $qData) {
                if (isset($qData['question']) && !empty($qData['question'])) {
                    Log::info('Processing question ' . $qIndex . ': ' . $qData['question']);
                    
                    $question = QuizQuestion::create([
                        'exercise_id' => $exercise->id,
                        'question' => $qData['question'],
                    ]);
                    
                    Log::info('Created question with ID: ' . $question->id);
                    
                    // Create options if they exist
                    if (isset($qData['options']) && is_array($qData['options'])) {
                        foreach ($qData['options'] as $optIndex => $optData) {
                            if (isset($optData['option_text']) && !empty($optData['option_text'])) {
                                Log::info('Creating option ' . $optIndex . ': ' . $optData['option_text']);
                                
                                QuizOption::create([
                                    'question_id' => $question->id,
                                    'option_text' => $optData['option_text'],
                                    'is_correct' => isset($qData['correct_option']) && (int)$qData['correct_option'] === (int)$optIndex,
                                ]);
                            }
                        }
                    }
                }
            }
            
            Log::info('Simplified quiz creation completed successfully');
            return redirect()->route('dosen.dashboard')->with('success', 'Quiz berhasil dibuat.');
        } catch (\Exception $e) {
            Log::error('Failed to create quiz: ' . $e->getMessage());
            Log::error('Exception trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Failed to create quiz: ' . $e->getMessage())->withInput();
        }
    }

    /**
     * Halaman attempt untuk mahasiswa.
     */
    public function attempt($exerciseId)
    {
        $user = Auth::user();
        if ($user->role !== User::ROLE_MAHASISWA) {
            return redirect('/')->with('error', 'Hanya mahasiswa yang dapat mengerjakan quiz.');
        }

        $exercise = Exercise::with(['mahasiswa', 'dosen'])
            ->where('type', 'quiz')
            ->findOrFail($exerciseId);

        // Debug information
        \Log::info('Quiz attempt debug info', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'user_mahasiswa_id' => $user->mahasiswa ? $user->mahasiswa->id : null,
            'exercise_mahasiswa_id' => $exercise->mahasiswa_id,
            'exercise_id' => $exercise->id,
            'mahasiswa_match' => $user->mahasiswa && $exercise->mahasiswa_id === $user->mahasiswa->id,
        ]);

        if (!$user->mahasiswa) {
            \Log::info('User has no mahasiswa relationship');
            return redirect('/')->with('error', 'Quiz ini tidak ditugaskan kepada Anda.');
        }
        
        if ($exercise->mahasiswa_id !== $user->mahasiswa->id) {
            \Log::info('Mahasiswa ID mismatch', [
                'exercise_mahasiswa_id' => $exercise->mahasiswa_id,
                'user_mahasiswa_id' => $user->mahasiswa->id,
            ]);
            return redirect('/')->with('error', 'Quiz ini tidak ditugaskan kepada Anda.');
        }

        // Check if student has exceeded max attempts
        $existingAttempts = QuizAttempt::where('exercise_id', $exercise->id)
            ->where('mahasiswa_id', $user->mahasiswa->id)
            ->count();
            
        if ($existingAttempts >= $exercise->max_attempts) {
            return redirect()->route('mahasiswa.exercise')->with('error', 'Anda telah mencapai batas maksimal percobaan untuk quiz ini.');
        }

        $exercise->load(['quizQuestions.options']);

        return view('mahasiswa.quiz_attempt', compact('exercise'));
    }

    /**
     * Submit attempt dan auto-grade MCQ.
     */
    public function submitAttempt(Request $request, $exerciseId)
    {
        $user = Auth::user();
        if ($user->role !== User::ROLE_MAHASISWA) {
            return redirect('/')->with('error', 'Hanya mahasiswa yang dapat mengerjakan quiz.');
        }

        $exercise = Exercise::with('quizQuestions.options')
            ->where('type', 'quiz')
            ->findOrFail($exerciseId);

        // Debug information
        \Log::info('Quiz submit debug info', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'user_mahasiswa_id' => $user->mahasiswa ? $user->mahasiswa->id : null,
            'exercise_mahasiswa_id' => $exercise->mahasiswa_id,
            'exercise_id' => $exercise->id,
        ]);

        if (!$user->mahasiswa || $exercise->mahasiswa_id !== $user->mahasiswa->id) {
            return redirect('/')->with('error', 'Quiz ini tidak ditugaskan kepada Anda.');
        }

        // Check if student has exceeded max attempts
        $existingAttempts = QuizAttempt::where('exercise_id', $exercise->id)
            ->where('mahasiswa_id', $user->mahasiswa->id)
            ->count();
            
        if ($existingAttempts >= $exercise->max_attempts) {
            return redirect()->route('mahasiswa.exercise')->with('error', 'Anda telah mencapai batas maksimal percobaan untuk quiz ini.');
        }

        $data = $request->validate([
            'answers' => 'required|array',
            'answers.*' => 'nullable|integer',
        ]);

        $score = 0;
        $total = $exercise->quizQuestions->count();

        $attempt = null;

        DB::transaction(function () use ($exercise, $user, $data, &$score, $total, &$attempt) {
            $attempt = QuizAttempt::create([
                'exercise_id' => $exercise->id,
                'mahasiswa_id' => $user->mahasiswa->id,
                'started_at' => now(),
                'submitted_at' => now(),
                'score' => 0,
            ]);

            foreach ($exercise->quizQuestions as $question) {
                $selectedOptionId = $data['answers'][$question->id] ?? null;
                QuizAnswer::create([
                    'attempt_id' => $attempt->id,
                    'question_id' => $question->id,
                    'option_id' => $selectedOptionId,
                ]);

                if ($selectedOptionId) {
                    $opt = $question->options->firstWhere('id', $selectedOptionId);
                    if ($opt && $opt->is_correct) {
                        $score++;
                    }
                }
            }

            $attempt->score = $total > 0 ? round(($score / $total) * 100) : 0;
            $attempt->save();
                
            // Mark exercise as completed
            \Log::info('Updating quiz exercise status', [
                'exercise_id' => $exercise->id,
                'current_status' => $exercise->status,
                'new_status' => 'completed'
            ]);
            
            // Fetch a fresh instance of the exercise to ensure we're updating the correct record
            $freshExercise = Exercise::find($exercise->id);
            if ($freshExercise) {
                $freshExercise->status = 'completed';
                $saved = $freshExercise->save();
                
                \Log::info('Quiz exercise status update result', [
                    'exercise_id' => $freshExercise->id,
                    'saved' => $saved,
                    'status_after_save' => $freshExercise->fresh()->status
                ]);
            } else {
                \Log::error('Failed to find exercise for status update', [
                    'exercise_id' => $exercise->id
                ]);
            }
        });
        
        return redirect()
            ->route('mahasiswa.quiz.review', [$exercise->id, 'attempt' => $attempt->id])
            ->with('success', 'Quiz dikumpulkan. Skor: ' . ($total > 0 ? round(($score / $total) * 100) : 0));
    }

    /**
     * Halaman review hasil quiz untuk mahasiswa.
     */
    public function review(Request $request, $exerciseId)
    {
        $user = Auth::user();
        if ($user->role !== User::ROLE_MAHASISWA) {
            return redirect('/')->with('error', 'Hanya mahasiswa yang dapat melihat review quiz.');
        }

        $exercise = Exercise::with(['quizQuestions.options'])
            ->where('type', 'quiz')
            ->findOrFail($exerciseId);

        // Debug information
        \Log::info('Quiz review debug info', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'user_mahasiswa_id' => $user->mahasiswa ? $user->mahasiswa->id : null,
            'exercise_mahasiswa_id' => $exercise->mahasiswa_id,
            'exercise_id' => $exercise->id,
        ]);

        if (!$user->mahasiswa || $exercise->mahasiswa_id !== $user->mahasiswa->id) {
            return redirect('/')->with('error', 'Quiz ini tidak ditugaskan kepada Anda.');
        }

        $attemptId = $request->get('attempt');

        $attemptQuery = QuizAttempt::with(['answers.option', 'answers.question'])
            ->where('exercise_id', $exercise->id)
            ->where('mahasiswa_id', $user->mahasiswa->id)
            ->orderByDesc('submitted_at');

        if ($attemptId) {
            $attemptQuery->where('id', $attemptId);
        }

        $attempt = $attemptQuery->firstOrFail();

        return view('mahasiswa.quiz_review', compact('exercise', 'attempt'));
    }
}


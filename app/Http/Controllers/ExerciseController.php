<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\DosenMahasiswaRequest;
use App\Models\Mahasiswa;
use App\Models\User;
use App\Models\AssignmentSubmission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Notifications\NewAssignmentNotification;
use App\Notifications\AssignmentSubmittedNotification;

class ExerciseController extends Controller
{
    /**
     * Halaman exercise untuk mahasiswa (list tugas/quiz dari dosen yang terhubung).
     */
    public function mahasiswaIndex()
    {
        $user = Auth::user();
        if ($user->role !== User::ROLE_MAHASISWA) {
            return redirect('/')->with('error', 'Hanya mahasiswa yang dapat mengakses halaman ini.');
        }

        $mahasiswa = $user->mahasiswa;
        if (!$mahasiswa) {
            return redirect('/')->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        $exercises = Exercise::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'published')
            ->orderBy('deadline', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        $hasCompleted = Exercise::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'completed')
            ->exists();

        return view('mahasiswa.exercise', compact('exercises', 'hasCompleted'));
    }

    /**
     * Get completed exercises for mahasiswa as JSON.
     */
    public function getCompletedExercises()
    {
        $user = Auth::user();
        if ($user->role !== User::ROLE_MAHASISWA) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $mahasiswa = $user->mahasiswa;
        if (!$mahasiswa) {
            return response()->json(['error' => 'Mahasiswa not found'], 404);
        }

        $exercises = Exercise::where('mahasiswa_id', $mahasiswa->id)
            ->where('status', 'completed')
            ->with([
                'submissions' => function ($query) {
                    $query->orderBy('submitted_at', 'desc');
                },
                'attempts'
            ])
            ->orderBy('updated_at', 'desc')
            ->get()
            ->map(function ($exercise) {
                if ($exercise->type === 'assignment') {
                    $submission = $exercise->submissions->first();
                    $exercise->grade = $submission ? $submission->grade : null;
                    $exercise->feedback = $submission ? $submission->feedback : null;
                    $exercise->attempts_count = $exercise->submissions->count();
                } else {
                    $exercise->attempts_count = $exercise->attempts->count();
                }
                // Ensure max_attempts is available
                $exercise->max_attempts = $exercise->max_attempts ?? 1;

                return $exercise;
            });

        return response()->json(['exercises' => $exercises]);
    }


    /**
     * Form buat exercise oleh dosen untuk mahasiswa yang sudah accepted.
     */
    public function createForMahasiswa($mahasiswaId)
    {
        $user = Auth::user();
        if ($user->role !== User::ROLE_DOSEN) {
            return redirect('/')->with('error', 'Hanya dosen yang dapat membuat exercise.');
        }

        $dosen = $user->dosen;
        $dosenId = $dosen ? $dosen->id : $user->id;

        $connected = DosenMahasiswaRequest::where('dosen_id', $dosenId)
            ->where('mahasiswa_id', $mahasiswaId)
            ->where('status', 'accepted')
            ->exists();

        if (!$connected) {
            return redirect()->route('dosen.dashboard')->with('error', 'Anda tidak memiliki akses ke mahasiswa ini.');
        }

        $mahasiswa = Mahasiswa::with('user')->findOrFail($mahasiswaId);

        return view('dosen.create_exercise', compact('mahasiswa'));
    }

    /**
     * Simpan exercise baru.
     */
    public function storeForMahasiswa(Request $request, $mahasiswaId)
    {
        $user = Auth::user();
        if ($user->role !== User::ROLE_DOSEN) {
            return redirect('/')->with('error', 'Hanya dosen yang dapat membuat exercise.');
        }

        $dosen = $user->dosen;
        $dosenId = $dosen ? $dosen->id : $user->id;

        $connected = DosenMahasiswaRequest::where('dosen_id', $dosenId)
            ->where('mahasiswa_id', $mahasiswaId)
            ->where('status', 'accepted')
            ->exists();

        if (!$connected) {
            return redirect()->route('dosen.dashboard')->with('error', 'Anda tidak memiliki akses ke mahasiswa ini.');
        }

        // Since we're focusing only on assignments, force the type to 'assignment'
        $request->merge(['type' => 'assignment']);

        $validated = $request->validate([
            'type' => 'required|in:quiz,assignment',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'nullable|date',
            'link' => 'nullable|url',
            'status' => 'nullable|in:draft,published',
            'max_attempts' => 'nullable|integer|min:1|max:10',
            'file_attachment' => 'nullable|file|mimes:pdf,doc,docx,zip,rar,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        // Handle file upload
        $filePath = null;
        if ($request->hasFile('file_attachment')) {
            $file = $request->file('file_attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('assignments', $fileName, 'public');
        }

        $exercise = Exercise::create([
            'dosen_id' => $dosenId,
            'mahasiswa_id' => $mahasiswaId,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'deadline' => $validated['deadline'] ?? null,
            'link' => $validated['link'] ?? null,
            'file_attachment' => $filePath,
            'status' => $validated['status'] ?? 'published',
            'max_attempts' => $validated['max_attempts'] ?? 1,
        ]);

        // Notify Mahasiswa
        $mahasiswa = Mahasiswa::with('user')->find($mahasiswaId);
        if ($mahasiswa && $mahasiswa->user) {
            $mahasiswa->user->notify(new NewAssignmentNotification($exercise));
        }

        return redirect()->route('dosen.dashboard')->with('success', 'Assignment berhasil dibuat.');
    }

    /**
     * Halaman edit exercise.
     */
    public function edit($id)
    {
        $user = Auth::user();
        if ($user->role !== User::ROLE_DOSEN) {
            return redirect('/')->with('error', 'Hanya dosen yang dapat mengedit exercise.');
        }

        $exercise = Exercise::findOrFail($id);

        // Check ownership/permission
        $dosen = $user->dosen;
        $dosenId = $dosen ? $dosen->id : $user->id;

        if ($exercise->dosen_id != $dosenId) {
            return redirect()->route('dosen.dashboard')->with('error', 'Anda tidak memiliki izin untuk mengedit exercise ini.');
        }

        $mahasiswa = $exercise->mahasiswa;

        return view('dosen.edit_exercise', compact('exercise', 'mahasiswa'));
    }

    /**
     * Update exercise.
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->role !== User::ROLE_DOSEN) {
            return redirect('/')->with('error', 'Hanya dosen yang dapat mengedit exercise.');
        }

        $exercise = Exercise::findOrFail($id);

        // Check ownership
        $dosen = $user->dosen;
        $dosenId = $dosen ? $dosen->id : $user->id;

        if ($exercise->dosen_id != $dosenId) {
            return redirect()->route('dosen.dashboard')->with('error', 'Anda tidak memiliki izin untuk mengedit exercise ini.');
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'deadline' => 'nullable|date',
            'link' => 'nullable|url',
            'status' => 'required|in:draft,published',
            'file_attachment' => 'nullable|file|mimes:pdf,doc,docx,zip,rar,jpg,jpeg,png|max:10240',
        ]);

        // Handle file upload
        if ($request->hasFile('file_attachment')) {
            // Delete old file if exists
            if ($exercise->file_attachment) {
                Storage::disk('public')->delete($exercise->file_attachment);
            }

            $file = $request->file('file_attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('assignments', $fileName, 'public');
            $exercise->file_attachment = $filePath;
        }

        $exercise->title = $validated['title'];
        $exercise->description = $validated['description'] ?? null;
        $exercise->deadline = $validated['deadline'] ?? null;
        $exercise->link = $validated['link'] ?? null;
        $exercise->status = $validated['status'];

        $exercise->save();

        return redirect()->route('dosen.mahasiswa.exercises', $exercise->mahasiswa_id)
            ->with('success', 'Assignment berhasil diperbarui.');
    }

    /**
     * Halaman attempt untuk assignment.
     */
    public function attemptAssignment($id)
    {
        $user = Auth::user();
        if ($user->role !== User::ROLE_MAHASISWA) {
            return redirect('/')->with('error', 'Hanya mahasiswa yang dapat mengakses halaman ini.');
        }

        $mahasiswa = $user->mahasiswa;
        if (!$mahasiswa) {
            return redirect('/')->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        // Debug information
        \Log::info('Assignment attempt debug info', [
            'user_id' => $user->id,
            'user_role' => $user->role,
            'user_mahasiswa_id' => $mahasiswa->id,
            'requested_exercise_id' => $id,
        ]);

        $exercise = Exercise::where('id', $id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('type', 'assignment')
            ->first();

        if (!$exercise) {
            \Log::info('Assignment not found or not assigned to user', [
                'requested_id' => $id,
                'mahasiswa_id' => $mahasiswa->id,
            ]);
            return redirect('/')->with('error', 'Assignment tidak ditemukan atau tidak ditugaskan kepada Anda.');
        }

        if ($exercise->status !== 'published') {
            // Allow access if status is completed AND attempts < max_attempts
            if ($exercise->status === 'completed') {
                $existingAttempts = AssignmentSubmission::where('exercise_id', $exercise->id)
                    ->where('mahasiswa_id', $mahasiswa->id)
                    ->count();

                if ($existingAttempts >= $exercise->max_attempts) {
                    return redirect()->route('mahasiswa.exercise')->with('error', 'Assignment sudah selesai dan batas percobaan habis.');
                }
            } else {
                return redirect()->route('mahasiswa.exercise')->with('error', 'Assignment belum dipublikasikan.');
            }
        }

        return view('mahasiswa.assignment_attempt', compact('exercise'));
    }

    /**
     * Download assignment file.
     */
    public function downloadAssignment($id)
    {
        $user = Auth::user();
        if ($user->role !== User::ROLE_MAHASISWA) {
            return redirect('/')->with('error', 'Hanya mahasiswa yang dapat mengakses halaman ini.');
        }

        $mahasiswa = $user->mahasiswa;
        if (!$mahasiswa) {
            return redirect('/')->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        $exercise = Exercise::where('id', $id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('type', 'assignment')
            ->firstOrFail();

        if ($exercise->status !== 'published') {
            return redirect()->route('mahasiswa.exercise')->with('error', 'Assignment belum dipublikasikan.');
        }

        if (!$exercise->file_attachment) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        // Check if file exists
        if (!Storage::disk('public')->exists($exercise->file_attachment)) {
            return redirect()->back()->with('error', 'File tidak ditemukan.');
        }

        // Return the file as a download
        return Storage::disk('public')->download($exercise->file_attachment, basename($exercise->file_attachment));
    }

    /**
     * Submit assignment answer.
     */
    public function submitAssignment(Request $request, $id)
    {
        $user = Auth::user();
        if ($user->role !== User::ROLE_MAHASISWA) {
            return redirect('/')->with('error', 'Hanya mahasiswa yang dapat mengakses halaman ini.');
        }

        $mahasiswa = $user->mahasiswa;
        if (!$mahasiswa) {
            return redirect('/')->with('error', 'Data mahasiswa tidak ditemukan.');
        }

        $exercise = Exercise::where('id', $id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->where('type', 'assignment')
            ->firstOrFail();

        if ($exercise->status !== 'published') {
            // Allow access if status is completed AND attempts < max_attempts
            if ($exercise->status === 'completed') {
                $existingAttempts = AssignmentSubmission::where('exercise_id', $exercise->id)
                    ->where('mahasiswa_id', $mahasiswa->id)
                    ->count();

                if ($existingAttempts >= $exercise->max_attempts) {
                    return redirect()->route('mahasiswa.exercise')->with('error', 'Assignment sudah selesai dan batas percobaan habis.');
                }
            } else {
                return redirect()->route('mahasiswa.exercise')->with('error', 'Assignment belum dipublikasikan.');
            }
        }

        // Validate submission
        $validated = $request->validate([
            'text_answer' => 'nullable|string',
            'file_answer' => 'nullable|file|mimes:pdf,doc,docx,zip,rar,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        // Check if student has exceeded max attempts
        $existingAttempts = AssignmentSubmission::where('exercise_id', $exercise->id)
            ->where('mahasiswa_id', $mahasiswa->id)
            ->count();

        if ($existingAttempts >= $exercise->max_attempts) {
            return redirect()->route('mahasiswa.exercise')->with('error', 'Anda telah mencapai batas maksimal percobaan untuk assignment ini.');
        }

        // Check if there's any submission
        if (empty($validated['text_answer']) && !$request->hasFile('file_answer')) {
            return redirect()->back()->with('error', 'Harap isi jawaban teks atau unggah file jawaban.');
        }

        // Handle file upload
        $filePath = null;
        if ($request->hasFile('file_answer')) {
            $file = $request->file('file_answer');
            $fileName = time() . '_' . $mahasiswa->id . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('submissions', $fileName, 'public');
        }

        // Create new submission
        $submission = AssignmentSubmission::create([
            'exercise_id' => $exercise->id,
            'mahasiswa_id' => $mahasiswa->id,
            'text_submission' => $validated['text_answer'] ?? null,
            'file_submission' => $filePath,
            'submitted_at' => now(),
        ]);

        // Mark exercise as completed
        \Log::info('Updating assignment exercise status', [
            'exercise_id' => $exercise->id,
            'current_status' => $exercise->status,
            'new_status' => 'completed'
        ]);

        // Fetch a fresh instance of the exercise to ensure we're updating the correct record
        $freshExercise = Exercise::find($exercise->id);
        if ($freshExercise) {
            $freshExercise->status = 'completed';
            $saved = $freshExercise->save();

            \Log::info('Assignment exercise status update result', [
                'exercise_id' => $freshExercise->id,
                'saved' => $saved,
                'status_after_save' => $freshExercise->fresh()->status
            ]);
        } else {
            \Log::error('Failed to find exercise for status update', [
                'exercise_id' => $exercise->id
            ]);
        }

        // Notify the Dosen about assignment submission
        if ($freshExercise && $freshExercise->dosen && $freshExercise->dosen->user) {
            $freshSubmission = $submission->fresh(['mahasiswa']);
            $freshExercise->dosen->user->notify(new AssignmentSubmittedNotification($freshSubmission));
        }

        return redirect()->back()->with('success', 'Jawaban berhasil dikirim.');
    }
}

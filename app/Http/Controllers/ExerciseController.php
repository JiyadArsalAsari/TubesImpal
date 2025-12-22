<?php

namespace App\Http\Controllers;

use App\Models\Exercise;
use App\Models\DosenMahasiswaRequest;
use App\Models\Mahasiswa;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            ->orderBy('deadline', 'asc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('mahasiswa.exercise', compact('exercises'));
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
            'file_attachment' => 'nullable|file|mimes:pdf,doc,docx,zip,rar,jpg,jpeg,png|max:10240', // 10MB max
        ]);

        // Handle file upload
        $filePath = null;
        if ($request->hasFile('file_attachment')) {
            $file = $request->file('file_attachment');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('assignments', $fileName, 'public');
        }

        Exercise::create([
            'dosen_id' => $dosenId,
            'mahasiswa_id' => $mahasiswaId,
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'] ?? null,
            'deadline' => $validated['deadline'] ?? null,
            'link' => $validated['link'] ?? null,
            'file_attachment' => $filePath,
            'status' => $validated['status'] ?? 'published',
        ]);

        return redirect()->route('dosen.dashboard')->with('success', 'Assignment berhasil dibuat.');
    }
}


<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\LearningDifficulty;
use App\Models\Mahasiswa;

class LearningDifficultyController extends Controller
{
    public function index(Request $request)
    {
        // Ensure only mahasiswa can access this
        if (Auth::user()->role !== 'mahasiswa') {
            return redirect('/');
        }

        // Get the authenticated user
        $user = Auth::user();
        
        // Check if user has a mahasiswa record
        if (!$user->mahasiswa) {
            // If mahasiswa doesn't exist, create an empty collection
            $learningDifficulties = collect();
        } else {
            // Query builder for learning difficulties
            $query = $user->mahasiswa->learningDifficulties();

            // Apply search filter if present
            if ($request->has('search')) {
                $search = $request->input('search');
                $query->where('title', 'like', "%{$search}%");
            }

            // Get the results
            $learningDifficulties = $query->get();
        }

        if ($request->ajax()) {
            return view('mahasiswa.partials.learning_difficulties_list', compact('learningDifficulties'))->render();
        }

        return view('mahasiswa.learning_difficulties', compact('learningDifficulties'));
    }

    public function create()
    {
        // Ensure only mahasiswa can access this
        if (Auth::user()->role !== 'mahasiswa') {
            return redirect('/');
        }

        return view('mahasiswa.create_learning_difficulty');
    }

    public function store(Request $request)
    {
        // Ensure only mahasiswa can access this
        if (Auth::user()->role !== 'mahasiswa') {
            return redirect('/');
        }

        // Validate the request
        $request->validate([
            'subject_name' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        // Get the authenticated user
        $user = Auth::user();
        
        // Check if user has a mahasiswa record
        if (!$user->mahasiswa) {
            return redirect()->back()->with('error', 'Mahasiswa record not found.');
        }

        // Create the learning difficulty
        LearningDifficulty::create([
            'mahasiswa_id' => $user->mahasiswa->id,
            'title' => $request->subject_name,
            'description' => $request->description,
        ]);

        return redirect()->route('mahasiswa.learning.difficulties')->with('success', 'Learning difficulty submitted successfully!');
    }
}
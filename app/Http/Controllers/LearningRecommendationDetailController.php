<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Services\GeminiService;

class LearningRecommendationDetailController extends Controller
{
    public function show($id, GeminiService $gemini)
    {
        // Ensure only mahasiswa can access this content
        if (Auth::user()->role !== 'mahasiswa') {
            return redirect('/'); // Redirect non-mahasiswa users
        }

        // Get user's learning difficulties
        $user = Auth::user();
        if (!$user->mahasiswa) {
            abort(404);
        }

        // Load learning difficulties with eager loading
        $user->load('mahasiswa.learningDifficulties');
        $difficulties = $user->mahasiswa->learningDifficulties;

        // Convert to array to access by index
        $difficultiesArray = $difficulties->values();

        // Check if difficulty exists
        if (!isset($difficultiesArray[$id])) {
            abort(404); // Return 404 if difficulty not found
        }

        $difficulty = $difficultiesArray[$id];

        // Generate detailed recommendation using AI
        $detailedRecommendation = $gemini->generateRecommendation($difficulty->title, $difficulty->description);

        // Prepare content data
        $content = [
            'title' => $difficulty->title,
            'description' => $difficulty->description,
            'type' => 'recommendation',
            'details' => $detailedRecommendation
        ];

        // Return the content detail view
        return view('mahasiswa.content_detail', compact('content', 'id'));
    }
}
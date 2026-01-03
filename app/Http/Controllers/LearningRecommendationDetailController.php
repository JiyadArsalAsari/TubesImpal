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

        // Find difficulty by ID and ensure it belongs to the user
        $difficulty = \App\Models\LearningDifficulty::where('id', $id)
            ->where('mahasiswa_id', $user->mahasiswa->id)
            ->firstOrFail();

        // Check if recommendation exists in DB, otherwise generate and save
        if (!empty($difficulty->recommendation)) {
            $detailedRecommendation = $difficulty->recommendation;
        } else {
            // Generate detailed recommendation using AI
            $detailedRecommendation = $gemini->generateRecommendation($difficulty->title, $difficulty->description);
            
            // Save if valid
            if (!str_starts_with($detailedRecommendation, 'ERROR') && !str_starts_with($detailedRecommendation, '⚠️')) {
                $difficulty->recommendation = $detailedRecommendation;
                $difficulty->save();
            }
        }

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
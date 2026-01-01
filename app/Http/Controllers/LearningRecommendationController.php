<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\LearningDifficulty;
use App\Services\GeminiService;

class LearningRecommendationController extends Controller
{
    public function index(Request $request, GeminiService $gemini)
    {
        $user = Auth::user();

        if (!$user->mahasiswa) {
            return view('mahasiswa.learning_recommendation', [
                'recommendations' => []
            ]);
        }

        // Query builder for learning difficulties
        $query = $user->mahasiswa->learningDifficulties();

        // Apply search filter if present
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%");
        }

        // Get the results
        $difficulties = $query->get();

        $recommendations = [];

        foreach ($difficulties as $difficulty) {
            $recommendations[] = [
                'id' => $difficulty->id,
                'subject' => $difficulty->title,
                'ai_result' => $gemini->generateRecommendation($difficulty->title, $difficulty->description),
            ];
        }

        if ($request->ajax()) {
            return view('mahasiswa.partials.learning_recommendations_list', compact('recommendations'))->render();
        }

        return view('mahasiswa.learning_recommendation', [
            'recommendations' => $recommendations
        ]);
    }
}

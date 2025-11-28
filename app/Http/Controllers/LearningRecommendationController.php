<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Models\LearningDifficulty;
use App\Services\GeminiService;

class LearningRecommendationController extends Controller
{
    public function index(GeminiService $gemini)
    {
        $user = Auth::user();

        if (!$user->mahasiswa) {
            return view('mahasiswa.learning_recommendation', [
                'recommendations' => []
            ]);
        }

        // Load learning difficulties with eager loading
        $user->load('mahasiswa.learningDifficulties');
        $difficulties = $user->mahasiswa->learningDifficulties;

        $recommendations = [];

        foreach ($difficulties as $difficulty) {
            $recommendations[] = [
                'subject' => $difficulty->title,
                'ai_result' => $gemini->generateRecommendation($difficulty->title, $difficulty->description),
            ];
        }

        return view('mahasiswa.learning_recommendation', [
            'recommendations' => $recommendations
        ]);
    }
}

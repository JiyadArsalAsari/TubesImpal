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
            // Cek apakah rekomendasi sudah ada di database
            if (empty($difficulty->recommendation)) {
                // Jika belum ada, generate dari AI dan simpan
                $aiResult = $gemini->generateRecommendation($difficulty->title, $difficulty->description);
                
                // Hanya simpan jika tidak error
                if (!str_starts_with($aiResult, 'ERROR') && !str_starts_with($aiResult, '⚠️')) {
                    $difficulty->recommendation = $aiResult;
                    $difficulty->save();
                }
            } else {
                $aiResult = $difficulty->recommendation;
            }

            $recommendations[] = [
                'id' => $difficulty->id,
                'subject' => $difficulty->title,
                'ai_result' => $aiResult,
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

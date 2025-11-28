<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiService
{
    protected $apiKey;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY');
        
        // Log for debugging - remove in production
        if (empty($this->apiKey)) {
            \Log::warning('GEMINI_API_KEY not found in environment variables');
        }
    }

    public function generateRecommendation($subject, $description)
    {
        $prompt = "Buat rekomendasi belajar untuk mahasiswa. "
                . "Mata kuliah: $subject. "
                . "Kendala: $description. "
                . "Buat langkah dan saran belajar yang jelas.";

        // Check if API key is set
        if (empty($this->apiKey)) {
            return "ERROR: API key not configured. Please set GEMINI_API_KEY in your .env file.";
        }
        
        $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key={$this->apiKey}";

        $response = Http::withHeaders([
            'Content-Type' => 'application/json',
        ])->post($url, [
            "contents" => [
                [
                    "role" => "user",
                    "parts" => [
                        ["text" => $prompt]
                    ]
                ]
            ]
        ]);

        if ($response->failed()) {
            $errorMessage = $response->body();
            \Log::error('Gemini API Error: ' . $errorMessage);
            return "ERROR: " . $errorMessage; // tetap tampilkan apa adanya
        }

        return $response->json()['candidates'][0]['content']['parts'][0]['text']
            ?? "No response generated.";
    }
}
    
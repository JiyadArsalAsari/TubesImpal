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
        $prompt = "Bertindaklah sebagai mentor akademik. Saya mahasiswa yang kesulitan di mata kuliah: '$subject' dengan kendala: '$description'.\n\n"
                . "Berikan panduan belajar yang TO THE POINT. Aturan main:\n"
                . "1. **Penjelasan Singkat**: Maksimal 4 paragraf pendek. Langsung ke inti permasalahan dan solusi praktis.\n"
                . "2. **Sumber Belajar Eksternal**: WAJIB sertakan minimal 3 link website/video spesifik yang membahas topik ini. Format: [Judul Referensi](URL).\n"
                . "   Contoh: [Tutorial Array di W3Schools](https://www.w3schools.com/...)\n"
                . "Gunakan format Markdown yang rapi. Jangan bertele-tele.";

        // Check if API key is set
        if (empty($this->apiKey)) {
            return "ERROR: API key not configured. Please set GEMINI_API_KEY in your .env file.";
        }
        
        $url = "https://generativelanguage.googleapis.com/v1/models/gemini-2.5-flash:generateContent?key={$this->apiKey}";

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
            $status = $response->status();
            $errorMessage = $response->body();
            \Log::error('Gemini API Error (' . $status . '): ' . $errorMessage);

            if ($status === 429) {
                return "⚠️ **Layanan AI Sedang Sibuk / Kuota Habis**\n\n" .
                       "Mohon maaf, permintaan ke Google Gemini AI gagal karena batas kuota penggunaan (Rate Limit) telah terlampaui.\n\n" .
                       "**Saran:**\n" .
                       "1. Tunggu beberapa menit sebelum mencoba lagi.\n" .
                       "2. Jika masalah berlanjut, kemungkinan kuota harian API telah habis dan akan di-reset besok.\n\n" .
                       "_(Error 429: Resource Exhausted)_";
            }

            return "⚠️ **Terjadi Kesalahan Teknis**\n\n" .
                   "Gagal menghubungi layanan AI. Silakan coba beberapa saat lagi.\n" .
                   "Detail: " . substr($errorMessage, 0, 100) . "..."; 
        }

        return $response->json()['candidates'][0]['content']['parts'][0]['text']
            ?? "No response generated.";
    }
}
    
@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#44533E] relative">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-20 pointer-events-none"
         style="background-image: url('/images/pattern.svg'); background-size: cover;">
    </div>

    <div class="relative z-10 max-w-6xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">Learning Recommendations</h1>
            <p class="text-gray-300 text-lg">Personalized recommendations based on your learning difficulties</p>
        </div>

        <!-- Recommendations List -->
        @if(isset($recommendations) && count($recommendations) > 0)
            <div class="space-y-6">
                @foreach($recommendations as $recommendation)
                    <div class="bg-[#1F2B1E] rounded-2xl p-6 shadow-lg border border-[#2D3A2D]">
                        <div class="flex items-start gap-4">
                            <div class="flex-shrink-0 mt-1">
                                <div class="bg-blue-500 rounded-lg w-12 h-12 flex items-center justify-center">
                                    <i class="fa-solid fa-robot text-white text-xl"></i>
                                </div>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-xl font-bold text-white mb-2">
                                    {{ $recommendation['subject'] ?? 'Recommended Learning Path' }}
                                </h3>
                                <div class="prose prose-invert max-w-none">
                                    <p class="text-gray-300 whitespace-pre-line">
                                        {{ $recommendation['ai_result'] ?? 'No recommendation available.' }}
                                    </p>
                                </div>
                                <div class="mt-4">
                                    <a href="{{ route('mahasiswa.learning.recommendation.detail', $loop->index) }}" 
                                       class="inline-flex items-center text-blue-400 hover:text-blue-300 font-medium">
                                        View Detailed Plan
                                        <i class="fa-solid fa-arrow-right ml-2 text-sm"></i>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-[#1F2B1E] rounded-2xl p-12 text-center border border-[#2D3A2D]">
                <div class="flex justify-center mb-6">
                    <div class="bg-gray-800 rounded-full w-16 h-16 flex items-center justify-center">
                        <i class="fa-solid fa-robot text-white text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-white font-bold text-3xl mb-4">No Recommendations Yet</h3>
                <p class="text-gray-300 text-xl mb-8">Submit your learning difficulties to get personalized recommendations.</p>
                <a href="{{ route('mahasiswa.learning.difficulties.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-full inline-flex items-center transition">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Report Learning Difficulty
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
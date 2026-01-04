@extends('layouts.app')

@section('content')
    <div class="max-w-5xl mx-auto px-6 py-10">
        <div class="mb-6">
            <button onclick="window.location.href='{{ route('mahasiswa.dashboard') }}'"
                class="flex items-center gap-2 text-white hover:text-gray-300 transition-colors">
                <i class="fa-solid fa-arrow-left"></i>
                <span>Back to Dashboard</span>
            </button>
        </div>
        <div class="mb-6">
            <p class="text-sm text-gray-200">Quiz Review</p>
            <h1 class="text-3xl font-bold">{{ $exercise->title }}</h1>
            <p class="text-sm text-gray-200 mt-1">
                Skor Anda:
                <span class="font-bold text-green-300">{{ $attempt->score }} / 100</span>
            </p>
            @if($attempt->submitted_at)
                <p class="text-sm text-gray-200">Dikumpulkan pada: {{ $attempt->submitted_at->format('d M Y H:i') }}</p>
            @endif
        </div>

        <div class="space-y-6">
            @foreach($exercise->quizQuestions as $qIndex => $question)
                @php
                    $answer = $attempt->answers->firstWhere('question_id', $question->id);
                    $selectedId = $answer?->option_id;
                @endphp
                <div class="bg-[#2f3d2c] border border-[#3c4c39] rounded-2xl p-5 shadow-lg">
                    <div class="flex items-start gap-3 text-white">
                        <div class="bg-[#4a6b46] text-white rounded-xl px-3 py-1 text-xs font-semibold uppercase">
                            Soal {{ $qIndex + 1 }}
                        </div>
                        <div class="flex-1">
                            <p class="font-semibold mb-3">{{ $question->question }}</p>
                            <div class="space-y-2">
                                @foreach($question->options as $opt)
                                                    @php
                                                        $isCorrect = $opt->is_correct;
                                                        $isSelected = $opt->id === $selectedId;
                                                    @endphp
                                     <div
                                                        class="flex items-center gap-3 text-sm
                                                                    @if($isCorrect) bg-green-700/40 border border-green-500 rounded-lg px-3 py-2 @elseif($isSelected) bg-red-700/40 border border-red-500 rounded-lg px-3 py-2 @endif">
                                                        <i class="fa-regular fa-circle-dot text-xs"></i>
                                                        <span>{{ $opt->option_text }}</span>
                                                        @if($isCorrect)
                                                            <span class="ml-auto text-xs font-semibold text-green-300">Benar</span>
                                                        @elseif($isSelected)
                                                            <span class="ml-auto text-xs font-semibold text-red-300">Jawaban Anda</span>
                                                        @endif
                                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8 flex justify-between items-center">
            <button onclick="window.location.href='{{ route('mahasiswa.exercise') }}'"
                class="bg-[#202c23] hover:bg-[#26352a] text-white font-semibold px-6 py-3 rounded-full shadow-lg transition">
                Kembali ke Exercise
            </button>
            <p class="text-sm text-gray-200">Review ini hanya menampilkan satu attempt terakhir Anda.</p>
        </div>
    </div>
@endsection
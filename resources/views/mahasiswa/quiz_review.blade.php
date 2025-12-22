<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-[#4b5b3b] text-white font-sans relative overflow-x-hidden">
    <div class="fixed inset-0 z-0 overflow-hidden">
        <img src="{{ asset('line.png') }}" alt="Decorative Line" class="w-full h-full object-cover opacity-10 scale-150">
    </div>

    <div class="relative z-10 min-h-screen">
        <!-- HEADER -->
        <header class="w-full bg-[#1f2f1f] text-white flex items-center justify-between px-8 py-4">
            <div class="text-2xl font-bold">{{ Auth::user()->name }}</div>
            <div class="flex items-center justify-center absolute left-1/2 transform -translate-x-1/2 logo-container" onclick="window.location.href='{{ route('mahasiswa.dashboard') }}'">
                <img src="{{ asset('logo.png') }}" class="w-20 h-20 filter brightness-0 invert" />
            </div>
            <div class="flex gap-6 text-3xl relative">
                <div class="relative cursor-pointer">
                    <i class="fa-regular fa-bell"></i>
                </div>
                <div class="cursor-pointer">
                    <i class="fa-solid fa-gear"></i>
                </div>
            </div>
        </header>

        <div class="max-w-5xl mx-auto px-6 py-10">
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
                                        <div class="flex items-center gap-3 text-sm
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
                <button onclick="window.location.href='{{ route('mahasiswa.exercise') }}'" class="bg-[#202c23] hover:bg-[#26352a] text-white font-semibold px-6 py-3 rounded-full shadow-lg transition">
                    Kembali ke Exercise
                </button>
                <p class="text-sm text-gray-200">Review ini hanya menampilkan satu attempt terakhir Anda.</p>
            </div>
        </div>
    </div>
</body>
</html>


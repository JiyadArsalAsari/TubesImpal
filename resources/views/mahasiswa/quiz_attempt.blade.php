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
                <p class="text-sm text-gray-200">Quiz</p>
                <h1 class="text-3xl font-bold">{{ $exercise->title }}</h1>
                @if($exercise->deadline)
                    <p class="text-sm text-gray-200 mt-1">Deadline: {{ $exercise->deadline->format('d M Y H:i') }}</p>
                @endif
                @if($exercise->duration_minutes)
                    <p class="text-sm text-gray-200 flex items-center gap-2">
                        Durasi: {{ $exercise->duration_minutes }} menit
                        <span id="countdown" class="inline-flex items-center gap-1 bg-red-600 text-white text-xs font-semibold px-3 py-1 rounded-full">
                            <i class="fa-regular fa-clock"></i>
                            <span id="countdown-text">--:--</span>
                        </span>
                    </p>
                @endif
                @if($exercise->description)
                    <p class="text-sm text-gray-100 mt-2">{{ $exercise->description }}</p>
                @endif
            </div>

            <form action="{{ route('mahasiswa.quiz.submit', $exercise->id) }}" method="POST" class="space-y-6">
                @csrf
                @foreach($exercise->quizQuestions as $qIndex => $question)
                    <div class="bg-[#2f3d2c] border border-[#3c4c39] rounded-2xl p-5 shadow-lg">
                        <div class="flex items-start gap-3 text-white">
                            <div class="bg-[#4a6b46] text-white rounded-xl px-3 py-1 text-xs font-semibold uppercase">
                                Soal {{ $qIndex + 1 }}
                            </div>
                            <div>
                                <p class="font-semibold">{{ $question->question }}</p>
                                <div class="mt-3 space-y-2">
                                    @foreach($question->options as $opt)
                                        <label class="flex items-center gap-3 text-sm">
                                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $opt->id }}" class="w-4 h-4">
                                            <span>{{ $opt->option_text }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                <div class="flex justify-end">
                    <button type="submit" class="bg-[#1d8f3b] hover:bg-[#167731] text-white font-semibold px-6 py-3 rounded-full shadow-lg transition">
                        Submit Quiz
                    </button>
                </div>
            </form>
        </div>
    </div>

    @if($exercise->duration_minutes)
    <script>
        (function() {
            const durationMinutes = {{ (int) $exercise->duration_minutes }};
            const totalSeconds = durationMinutes * 60;
            let remaining = totalSeconds;
            const countdownText = document.getElementById('countdown-text');
            const form = document.querySelector('form');

            function formatTime(sec) {
                const m = Math.floor(sec / 60).toString().padStart(2, '0');
                const s = (sec % 60).toString().padStart(2, '0');
                return `${m}:${s}`;
            }

            function tick() {
                countdownText.textContent = formatTime(Math.max(0, remaining));
                if (remaining <= 0) {
                    // auto submit when time is up
                    form.submit();
                    return;
                }
                remaining -= 1;
                setTimeout(tick, 1000);
            }

            tick();
        })();
    </script>
    @endif
</body>
</html>


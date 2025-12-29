<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body class="bg-[#4b5b3b] text-white font-sans">
    <div class="relative z-10 min-h-screen">
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
                <button onclick="window.location.href='{{ route('mahasiswa.dashboard') }}'" class="flex items-center gap-2 text-white hover:text-gray-300 transition-colors">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Back to Dashboard</span>
                </button>
            </div>
            <div class="mb-6">
                <p class="text-sm text-gray-200">Assignment Review</p>
                <h1 class="text-3xl font-bold">{{ $exercise->title }}</h1>
                @if($submission && $submission->submitted_at)
                    <p class="text-sm text-gray-200 mt-1">Dikumpulkan pada: {{ $submission->submitted_at->format('d M Y H:i') }}</p>
                @endif
            </div>

            <div class="space-y-6">
                <div class="bg-[#2f3d2c] border border-[#3c4c39] rounded-2xl p-5 shadow-lg">
                    <div class="flex items-start gap-3 text-white">
                        <div class="bg-[#4a6b46] text-white rounded-xl px-3 py-1 text-xs font-semibold uppercase">ASSIGNMENT</div>
                        <div class="flex-1">
                            @if($exercise->description)
                                <p class="text-gray-200 mb-4">{{ $exercise->description }}</p>
                            @endif

                            @if($submission && $submission->text_submission)
                                <div class="mt-4">
                                    <h3 class="font-semibold mb-2">Jawaban Teks</h3>
                                    <p class="text-gray-200 whitespace-pre-line">{{ $submission->text_submission }}</p>
                                </div>
                            @endif

                            @if($submission && $submission->file_submission)
                                <div class="mt-6">
                                    <h3 class="font-semibold mb-2">File Jawaban</h3>
                                    <a href="{{ asset('storage/' . $submission->file_submission) }}" target="_blank" class="inline-flex items-center gap-2 bg-[#202c23] text-white text-sm font-semibold px-4 py-2 rounded-full border border-[#6fbf69] hover:bg-[#26402d] transition">
                                        <i class="fa-solid fa-file"></i>
                                        Lihat/Unduh File
                                    </a>
                                </div>
                            @endif

                            @if(!$submission)
                                <p class="text-gray-300">Belum ada data submission untuk assignment ini.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 flex justify-between items-center">
                <button onclick="window.location.href='{{ route('mahasiswa.exercise') }}'" class="bg-[#202c23] hover:bg-[#26352a] text-white font-semibold px-6 py-3 rounded-full shadow-lg transition">
                    Kembali ke Exercise
                </button>
            </div>
        </div>
    </div>
</body>
</html>
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
    <!-- Decorative Line Background -->
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

        <div class="max-w-6xl mx-auto px-6 py-12">
            <div class="bg-[#2f3d2c] rounded-3xl shadow-xl p-8 border border-[#3c4c39]">
                @php
                    $grouped = $exercises->groupBy(function ($item) {
                        return optional($item->deadline)?->format('l, d F Y') ?? 'Tanpa Deadline';
                    });
                @endphp

                @if($exercises->count() === 0)
                    <p class="text-center text-gray-200">Belum ada exercise dari dosen.</p>
                @else
                    <div class="space-y-8">
                        @foreach($grouped as $date => $items)
                            <div>
                                <p class="text-sm text-gray-300 uppercase tracking-wide mb-3">{{ $date }}</p>
                                <div class="space-y-4">
                                    @foreach($items as $item)
                                        <div class="bg-[#395035] rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border border-[#436040] shadow-lg">
                                            <div class="flex items-start gap-3 text-white">
                                                <div class="bg-[#4a6b46] text-white rounded-xl px-3 py-2 text-xs font-semibold uppercase">
                                                    {{ strtoupper($item->type) }}
                                                </div>
                                                <div>
                                                    <p class="font-semibold">{{ $item->title }}</p>
                                                    <p class="text-xs text-gray-200 mt-1">
                                                        Deadline: {{ optional($item->deadline)?->format('H:i') ?? '—' }}
                                                    </p>
                                                    @if($item->description)
                                                        <p class="text-xs text-gray-300 mt-1">{{ \Illuminate\Support\Str::limit($item->description, 120) }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($item->type === 'quiz')
                                                <a href="{{ route('mahasiswa.quiz.attempt', $item->id) }}" class="bg-[#202c23] text-white text-sm font-semibold px-4 py-2 rounded-full border border-[#6fbf69] hover:bg-[#26402d] transition text-center">
                                                    Attempt
                                                </a>
                                            @elseif($item->file_attachment)
                                                <a href="{{ asset('storage/' . $item->file_attachment) }}" target="_blank" class="bg-[#202c23] text-white text-sm font-semibold px-4 py-2 rounded-full border border-[#6fbf69] hover:bg-[#26402d] transition text-center">
                                                    Download File
                                                </a>
                                            @elseif($item->link)
                                                <a href="{{ $item->link }}" target="_blank" class="bg-[#202c23] text-white text-sm font-semibold px-4 py-2 rounded-full border border-[#6fbf69] hover:bg-[#26402d] transition text-center">
                                                    Attempt
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-200">Tidak ada link</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6 flex flex-col items-center gap-3">
                        <button class="text-sm font-semibold text-white underline decoration-dotted">Show More</button>
                        <button class="bg-[#1d8f3b] hover:bg-[#167731] text-white font-semibold px-6 py-3 rounded-full shadow-lg transition">
                            Review Your Completed Quiz and Assignments
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
</body>
</html>


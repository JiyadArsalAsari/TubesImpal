@extends('layouts.app')

@section('container_class', 'max-w-6xl mx-auto px-6 py-8')

@push('styles')
    <style>
        /* Specific page styles moved from original file */
    </style>
@endpush

@section('content')
    <div class="mb-6">
        <button onclick="window.location.href='{{ route('dosen.dashboard') }}'"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-500/30 hover:bg-gray-500/50 text-white hover:text-gray-200 rounded-full transition-all duration-300 font-semibold group">
            <i class="fa-solid fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
            <span>Back to Dashboard</span>
        </button>
    </div>

    @if(session('success'))
        <div class="mb-4 bg-[#2f3d2c] text-green-400 border border-green-500 rounded-xl px-4 py-3 shadow-md">
            {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-[#3d2c2c] text-red-400 border border-red-500 rounded-xl px-4 py-3 shadow-md">{{ session('error') }}
        </div>
    @endif
    <div class="mb-6">
        <p class="text-sm text-gray-300">Mahasiswa</p>
        <h1 class="text-3xl font-bold">{{ $mahasiswa->nama }} ({{ $mahasiswa->user->email }})</h1>
    </div>

    <div class="bg-[#395035] text-white rounded-3xl shadow-xl p-6 border border-[#436040] mb-8">
        <div class="flex flex-col md:flex-row gap-4 justify-between items-center">
            <div class="relative w-full md:w-1/3">
                <i class="fa-solid fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                <input type="text" id="exerciseSearch" placeholder="Cari assignment atau quiz..."
                    class="w-full pl-12 pr-4 py-3 rounded-xl bg-[#2f3d2c] text-white border border-[#436040] placeholder-gray-400 focus:outline-none focus:border-[#48bb78] shadow-md transition-all">
            </div>
            <div class="flex flex-wrap gap-2 items-center">
                <!-- Status Filter -->
                <select onchange="window.location.href=this.value"
                    class="px-4 py-2 rounded-xl text-sm font-semibold border border-[#436040] bg-[#2f3d2c] text-white focus:outline-none focus:border-[#48bb78] cursor-pointer hover:bg-[#395035] transition-all">
                    <option
                        value="{{ route('dosen.mahasiswa.exercises', ['id' => $mahasiswa->id, 'type' => request('type')]) }}"
                        {{ !request('status') ? 'selected' : '' }}>All Status</option>
                    <option
                        value="{{ route('dosen.mahasiswa.exercises', ['id' => $mahasiswa->id, 'type' => request('type'), 'status' => 'draft']) }}"
                        {{ request('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    <option
                        value="{{ route('dosen.mahasiswa.exercises', ['id' => $mahasiswa->id, 'type' => request('type'), 'status' => 'published']) }}"
                        {{ request('status') == 'published' ? 'selected' : '' }}>Published</option>
                    <option
                        value="{{ route('dosen.mahasiswa.exercises', ['id' => $mahasiswa->id, 'type' => request('type'), 'status' => 'completed']) }}"
                        {{ request('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                </select>

                <!-- Type Filter -->
                <a href="{{ route('dosen.mahasiswa.exercises', ['id' => $mahasiswa->id, 'status' => request('status')]) }}"
                    class="px-4 py-2 rounded-xl text-sm font-semibold border border-[#436040] transition-all {{ !request('type') ? 'bg-[#1d8f3b] text-white' : 'bg-[#2f3d2c] text-gray-300 hover:bg-[#395035]' }}">
                    All Types
                </a>
                <a href="{{ route('dosen.mahasiswa.exercises', ['id' => $mahasiswa->id, 'type' => 'assignment', 'status' => request('status')]) }}"
                    class="px-4 py-2 rounded-xl text-sm font-semibold border border-[#436040] transition-all {{ request('type') == 'assignment' ? 'bg-[#1d8f3b] text-white' : 'bg-[#2f3d2c] text-gray-300 hover:bg-[#395035]' }}">
                    Assignment
                </a>
                <a href="{{ route('dosen.mahasiswa.exercises', ['id' => $mahasiswa->id, 'type' => 'quiz', 'status' => request('status')]) }}"
                    class="px-4 py-2 rounded-xl text-sm font-semibold border border-[#436040] transition-all {{ request('type') == 'quiz' ? 'bg-[#1d8f3b] text-white' : 'bg-[#2f3d2c] text-gray-300 hover:bg-[#395035]' }}">
                    Quiz
                </a>
            </div>
        </div>
    </div>

    <div class="bg-[#395035] text-white rounded-3xl shadow-xl p-8 border border-[#436040]">
        @php
            $grouped = $exercises->groupBy(function ($item) {
                return optional($item->deadline)?->format('l, d F Y') ?? 'Tanpa Deadline';
            });
        @endphp

        @if($exercises->count() === 0)
            <p class="text-center text-gray-400">Belum ada exercise untuk mahasiswa ini.</p>
        @else
            <div class="space-y-8">
                @foreach($grouped as $date => $items)
                    <div class="date-group">
                        <p class="text-sm text-gray-300 uppercase tracking-wide mb-3">{{ $date }}</p>
                        <div class="space-y-4">
                            @foreach($items as $item)
                                @php
                                    $latestAttempt = $item->attempts->first();
                                    $latestSubmission = $item->submissions->first();
                                @endphp
                                <div
                                    class="exercise-item bg-[#2f3d2c] rounded-2xl p-4 flex flex-col md:flex-row md:items-start md:justify-between gap-4 border border-[#436040] shadow-lg">
                                    <div class="flex items-start gap-3">
                                        <div
                                            class="bg-[#1f2f1f] text-white rounded-xl px-3 py-2 text-xs font-semibold uppercase border border-[#436040]">
                                            {{ strtoupper($item->type) }}
                                        </div>
                                        <div>
                                            <p class="exercise-title font-bold text-lg text-white">{{ $item->title }}</p>
                                            <p class="text-xs text-gray-300 mt-1">
                                                Deadline: {{ optional($item->deadline)?->format('H:i') ?? '—' }}
                                            </p>
                                            @if($item->description)
                                                <p class="text-xs text-gray-300 mt-1">
                                                    {{ \Illuminate\Support\Str::limit($item->description, 120) }}</p>
                                            @endif
                                            <div class="mt-2 flex flex-wrap gap-2">
                                                <span
                                                    class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $item->status === 'completed' ? 'bg-green-600 text-white' : 'bg-yellow-500 text-white' }}">
                                                    {{ strtoupper($item->status) }}
                                                </span>
                                                <a href="{{ route('dosen.exercise.edit', $item->id) }}"
                                                    class="text-yellow-400 hover:text-yellow-300 text-xs font-semibold flex items-center gap-1 bg-[#2f3d2c] px-3 py-1 rounded-full border border-yellow-500/50 hover:bg-yellow-500 hover:text-black transition-all">
                                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                                </a>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex-1">
                                        @if($item->type === 'quiz')
                                            @if($latestAttempt)
                                                <div class="bg-[#1f2f1f] border border-[#436040] rounded-xl p-3">
                                                    <p class="text-sm text-gray-300">Skor terakhir: <span
                                                            class="font-bold text-white">{{ $latestAttempt->score }} / 100</span></p>
                                                    @if($latestAttempt->submitted_at)
                                                        <p class="text-xs text-gray-400">Dikumpulkan:
                                                            {{ $latestAttempt->submitted_at->format('d M Y H:i') }}</p>
                                                    @endif
                                                </div>
                                            @else
                                                <div class="bg-[#1f2f1f] border border-[#436040] rounded-xl p-3">
                                                    <p class="text-sm text-gray-400 mb-2">Belum dikerjakan.</p>
                                                    <div class="mt-2 border-t border-[#436040] pt-2">
                                                        <p class="text-sm font-semibold text-white">Input Nilai Manual</p>
                                                        <form action="{{ route('dosen.assignment.grade_manual', $item->id) }}" method="POST"
                                                            class="mt-2 flex flex-col md:flex-row gap-2 items-start md:items-center">
                                                            @csrf
                                                            <input type="hidden" name="mahasiswa_id" value="{{ $mahasiswa->id }}">
                                                            <input type="number" name="grade" min="0" max="100"
                                                                class="rounded-lg bg-[#2f3d2c] border border-[#436040] text-white p-2 w-24 placeholder-gray-400"
                                                                placeholder="Nilai" required>
                                                            <textarea name="feedback" rows="2"
                                                                class="rounded-lg bg-[#2f3d2c] border border-[#436040] text-white p-2 flex-1 placeholder-gray-400"
                                                                placeholder="Feedback (opsional)"></textarea>
                                                            <button type="submit"
                                                                class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-full transition-colors">Simpan</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            @endif
                                        @else
                                            @if($latestSubmission)
                                                <div class="bg-[#1f2f1f] border border-[#436040] rounded-xl p-3 space-y-2">
                                                    @if($latestSubmission->submitted_at)
                                                        <p class="text-xs text-gray-400">Dikumpulkan:
                                                            {{ $latestSubmission->submitted_at->format('d M Y H:i') }}</p>
                                                    @endif
                                                    <div class="flex flex-wrap gap-2 items-center">
                                                        <span
                                                            class="px-3 py-1 rounded-full text-xs font-semibold {{ $latestSubmission->text_submission ? 'bg-blue-600 text-white' : 'bg-gray-600 text-white' }}">
                                                            {{ $latestSubmission->text_submission ? 'TEXT SUBMITTED' : 'NO TEXT' }}
                                                        </span>
                                                        <span
                                                            class="px-3 py-1 rounded-full text-xs font-semibold {{ $latestSubmission->file_submission ? 'bg-purple-600 text-white' : 'bg-gray-600 text-white' }}">
                                                            {{ $latestSubmission->file_submission ? 'FILE SUBMITTED' : 'NO FILE' }}
                                                        </span>
                                                    </div>
                                                    @if($latestSubmission->text_submission)
                                                        <p class="text-sm mt-2 text-gray-300">Jawaban Teks: <span
                                                                class="text-white">{{ \Illuminate\Support\Str::limit($latestSubmission->text_submission, 160) }}</span>
                                                        </p>
                                                    @endif
                                                    @if($latestSubmission->file_submission)
                                                        <a href="{{ route('dosen.assignment.download', $latestSubmission->id) }}"
                                                            class="inline-flex items-center gap-2 text-blue-400 hover:text-blue-300 text-sm font-semibold transition-colors">
                                                            <i class="fa-solid fa-download"></i>
                                                            Download File Jawaban
                                                        </a>
                                                    @endif

                                                    <div class="mt-3">
                                                        <p class="text-sm font-semibold text-white">Penilaian</p>
                                                        @if(!is_null($latestSubmission->grade))
                                                            <div id="gradeDisplay-{{ $latestSubmission->id }}" class="space-y-1">
                                                                <p class="text-sm text-gray-300">Nilai: <span
                                                                        class="font-bold text-white">{{ $latestSubmission->grade }}</span></p>
                                                                @if($latestSubmission->feedback)
                                                                    <p class="text-sm text-gray-300">Feedback: <span
                                                                            class="text-white">{{ $latestSubmission->feedback }}</span></p>
                                                                @endif
                                                                <button type="button" onclick="toggleGradeForm({{ $latestSubmission->id }}, true)"
                                                                    class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1 rounded-full transition-colors">Edit</button>
                                                            </div>
                                                            <form id="gradeForm-{{ $latestSubmission->id }}"
                                                                action="{{ route('dosen.assignment.grade', $latestSubmission->id) }}" method="POST"
                                                                class="mt-2 flex flex-col md:flex-row gap-2 items-start md:items-center hidden">
                                                                @csrf
                                                                <input type="number" name="grade" min="0" max="100"
                                                                    class="rounded-lg bg-[#2f3d2c] border border-[#436040] text-white p-2 w-24 placeholder-gray-400"
                                                                    placeholder="Nilai" value="{{ $latestSubmission->grade ?? '' }}" required>
                                                                <textarea name="feedback" rows="2"
                                                                    class="rounded-lg bg-[#2f3d2c] border border-[#436040] text-white p-2 flex-1 placeholder-gray-400"
                                                                    placeholder="Feedback (opsional)">{{ $latestSubmission->feedback ?? '' }}</textarea>
                                                                <div class="flex gap-2">
                                                                    <button type="submit"
                                                                        class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-full transition-colors">Simpan</button>
                                                                    <button type="button"
                                                                        onclick="toggleGradeForm({{ $latestSubmission->id }}, false)"
                                                                        class="bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold px-4 py-2 rounded-full transition-colors">Batal</button>
                                                                </div>
                                                            </form>
                                                        @else
                                                            <form action="{{ route('dosen.assignment.grade', $latestSubmission->id) }}"
                                                                method="POST"
                                                                class="mt-2 flex flex-col md:flex-row gap-2 items-start md:items-center">
                                                                @csrf
                                                                <input type="number" name="grade" min="0" max="100"
                                                                    class="rounded-lg bg-[#2f3d2c] border border-[#436040] text-white p-2 w-24 placeholder-gray-400"
                                                                    placeholder="Nilai" required>
                                                                <textarea name="feedback" rows="2"
                                                                    class="rounded-lg bg-[#2f3d2c] border border-[#436040] text-white p-2 flex-1 placeholder-gray-400"
                                                                    placeholder="Feedback (opsional)"></textarea>
                                                                <button type="submit"
                                                                    class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-full transition-colors">Simpan</button>
                                                            </form>
                                                        @endif
                                                    </div>
                                                </div>
                                            @else
                                                <p class="text-sm text-gray-400">Belum dikerjakan.</p>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
@endsection

@push('scripts')
    <script>
        function toggleGradeForm(id, show) {
            const displayEl = document.getElementById('gradeDisplay-' + id);
            const formEl = document.getElementById('gradeForm-' + id);
            if (!displayEl || !formEl) return;
            if (show) {
                displayEl.classList.add('hidden');
                formEl.classList.remove('hidden');
            } else {
                formEl.classList.add('hidden');
                displayEl.classList.remove('hidden');
            }
        }
    </script>
    <script>
        // Search functionality
        document.getElementById('exerciseSearch').addEventListener('keyup', function () {
            const filter = this.value.toLowerCase();
            const items = document.querySelectorAll('.exercise-item');
            const dateGroups = document.querySelectorAll('.date-group');
            let hasVisibleItems = false;

            items.forEach(item => {
                const title = item.querySelector('.exercise-title').textContent.toLowerCase();
                if (title.includes(filter)) {
                    item.style.display = '';
                    hasVisibleItems = true;
                } else {
                    item.style.display = 'none';
                }
            });

            // Hide date groups if no visible items in that group
            dateGroups.forEach(group => {
                const itemsInGroup = group.querySelectorAll('.exercise-item');
                let hasVisible = false;
                itemsInGroup.forEach(item => {
                    if (item.style.display !== 'none') {
                        hasVisible = true;
                    }
                });

                if (!hasVisible) {
                    group.style.display = 'none';
                } else {
                    group.style.display = '';
                }
            });
        });
    </script>
@endpush
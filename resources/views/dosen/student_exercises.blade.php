<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        .profile-popup { display: none; position: absolute; top: 60px; right: 20px; background-color: #1f2f1f; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); z-index: 1000; min-width: 250px; padding: 20px; }
        .profile-popup.show { display: block; }
        .popup-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 999; }
        .popup-overlay.show { display: block; }
        .profile-item { padding: 12px 15px; border-radius: 8px; cursor: pointer; transition: background-color 0.2s; }
        .profile-item:hover { background-color: rgba(255,255,255,0.1); }
        .profile-divider { height: 1px; background-color: rgba(255,255,255,0.1); margin: 10px 0; }
        .language-submenu { display: none; margin-left: 20px; }
        .language-submenu.show { display: block; }
        .notification-badge { position: absolute; top: -5px; right: -5px; background-color: #e74c3c; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 12px; display: flex; align-items: center; justify-content: center; }
        .notification-popup { display: none; position: absolute; top: 60px; right: 70px; background-color: #1f2f1f; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); z-index: 1000; min-width: 300px; max-width: 350px; padding: 30px; text-align: center; }
        .notification-popup.show { display: block; }
        .logo-container { cursor: pointer; }
    </style>
</head>
<body class="bg-[#4b5b3b] text-white font-sans relative overflow-x-hidden">
    <div class="fixed inset-0 z-0 overflow-hidden">
        <img src="{{ asset('line.png') }}" alt="Decorative Line" class="w-full h-full object-cover opacity-10 scale-150">
    </div>
    <div class="relative z-10 min-h-screen">
        <header class="w-full bg-[#1f2f1f] text-white flex items-center justify-between px-8 py-4">
            <div class="text-2xl font-bold">{{ Auth::user()->name }}</div>
            <div class="flex items-center justify-center absolute left-1/2 transform -translate-x-1/2 logo-container" onclick="window.location.href='{{ route('dosen.dashboard') }}'">
                <img src="{{ asset('logo.png') }}" class="w-24 h-24 filter brightness-0 invert" />
            </div>
            <div class="flex gap-6 text-3xl relative">
                <div class="relative cursor-pointer" id="bellIcon">
                    <i class="fa-regular fa-bell"></i>
                    <span class="notification-badge">3</span>
                </div>
                <div class="cursor-pointer" id="gearIcon">
                    <i class="fa-solid fa-gear"></i>
                </div>
            </div>
        </header>
        <div class="popup-overlay" id="popupOverlay" onclick="closeAllPopups()"></div>
        <div class="notification-popup" id="notificationPopup">
            <div class="mb-4">
                <i class="fa-regular fa-bell text-3xl text-gray-400 mb-3"></i>
                <h3 class="font-bold text-xl mb-2">Notifications</h3>
                <p class="text-gray-400">Notifications will be displayed here</p>
            </div>
        </div>
        <div class="profile-popup" id="profilePopup">
            <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-700">
                <div class="bg-gray-700 rounded-full w-12 h-12 flex items-center justify-center">
                    <i class="fa-solid fa-user text-xl"></i>
                </div>
                <div>
                    <p class="font-semibold">{{ Auth::user()->name }}</p>
                    <p class="text-sm text-gray-400">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <div class="profile-item">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-user-gear"></i>
                    <span>Profile Settings</span>
                </div>
            </div>
            <div class="profile-divider"></div>
            <div class="profile-item" onclick="toggleLanguageMenu()">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-language"></i>
                        <span>Language</span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-gray-400"></i>
                </div>
            </div>
            <div class="language-submenu" id="languageMenu">
                <div class="profile-item">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-flag-usa"></i>
                        <span>English</span>
                    </div>
                </div>
                <div class="profile-item">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-flag"></i>
                        <span>Indonesian</span>
                    </div>
                </div>
            </div>
            <div class="profile-divider"></div>
            <div class="profile-item" onclick="window.location.href='{{ route('logout') }}'">
                <div class="flex items-center gap-3 text-red-400">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
                </div>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-6 py-10">
            @if(session('success'))
                <div class="mb-4 bg-[#2f3d2c] text-green-400 border border-green-500 rounded-xl px-4 py-3 shadow-md">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-[#3d2c2c] text-red-400 border border-red-500 rounded-xl px-4 py-3 shadow-md">{{ session('error') }}</div>
            @endif
            <div class="mb-6">
                <p class="text-sm text-gray-300">Mahasiswa</p>
                <h1 class="text-3xl font-bold">{{ $mahasiswa->nama }} ({{ $mahasiswa->user->email }})</h1>
            </div>

            <div class="bg-[#395035] text-white rounded-3xl shadow-xl p-6 border border-[#436040] mb-8">
                <div class="relative">
                    <i class="fa-solid fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input type="text" id="exerciseSearch" placeholder="Cari assignment atau quiz..." class="w-full pl-12 pr-4 py-3 rounded-xl bg-[#2f3d2c] text-white border border-[#436040] placeholder-gray-400 focus:outline-none focus:border-[#48bb78] shadow-md transition-all">
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
                                        <div class="exercise-item bg-[#2f3d2c] rounded-2xl p-4 flex flex-col md:flex-row md:items-start md:justify-between gap-4 border border-[#436040] shadow-lg">
                                            <div class="flex items-start gap-3">
                                                <div class="bg-[#1f2f1f] text-white rounded-xl px-3 py-2 text-xs font-semibold uppercase border border-[#436040]">
                                                    {{ strtoupper($item->type) }}
                                                </div>
                                                <div>
                                                    <p class="exercise-title font-bold text-lg text-white">{{ $item->title }}</p>
                                                    <p class="text-xs text-gray-300 mt-1">
                                                        Deadline: {{ optional($item->deadline)?->format('H:i') ?? '—' }}
                                                    </p>
                                                    @if($item->description)
                                                        <p class="text-xs text-gray-300 mt-1">{{ \Illuminate\Support\Str::limit($item->description, 120) }}</p>
                                                    @endif
                                                    <div class="mt-2 flex flex-wrap gap-2">
                                                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold {{ $item->status === 'completed' ? 'bg-green-600 text-white' : 'bg-yellow-500 text-white' }}">
                                                            {{ strtoupper($item->status) }}
                                                        </span>
                                                        <a href="{{ route('dosen.exercise.edit', $item->id) }}" class="text-yellow-400 hover:text-yellow-300 text-xs font-semibold flex items-center gap-1 bg-[#2f3d2c] px-3 py-1 rounded-full border border-yellow-500/50 hover:bg-yellow-500 hover:text-black transition-all">
                                                            <i class="fa-solid fa-pen-to-square"></i> Edit
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="flex-1">
                                                @if($item->type === 'quiz')
                                                    @if($latestAttempt)
                                                        <div class="bg-[#1f2f1f] border border-[#436040] rounded-xl p-3">
                                                            <p class="text-sm text-gray-300">Skor terakhir: <span class="font-bold text-white">{{ $latestAttempt->score }} / 100</span></p>
                                                            @if($latestAttempt->submitted_at)
                                                                <p class="text-xs text-gray-400">Dikumpulkan: {{ $latestAttempt->submitted_at->format('d M Y H:i') }}</p>
                                                            @endif
                                                        </div>
                                                    @else
                                                        <div class="bg-[#1f2f1f] border border-[#436040] rounded-xl p-3">
                                                            <p class="text-sm text-gray-400 mb-2">Belum dikerjakan.</p>
                                                            <div class="mt-2 border-t border-[#436040] pt-2">
                                                                <p class="text-sm font-semibold text-white">Input Nilai Manual</p>
                                                                <form action="{{ route('dosen.assignment.grade_manual', $item->id) }}" method="POST" class="mt-2 flex flex-col md:flex-row gap-2 items-start md:items-center">
                                                                    @csrf
                                                                    <input type="hidden" name="mahasiswa_id" value="{{ $mahasiswa->id }}">
                                                                    <input type="number" name="grade" min="0" max="100" class="rounded-lg bg-[#2f3d2c] border border-[#436040] text-white p-2 w-24 placeholder-gray-400" placeholder="Nilai" required>
                                                                    <textarea name="feedback" rows="2" class="rounded-lg bg-[#2f3d2c] border border-[#436040] text-white p-2 flex-1 placeholder-gray-400" placeholder="Feedback (opsional)"></textarea>
                                                                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-full transition-colors">Simpan</button>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    @endif
                                                @else
                                                    @if($latestSubmission)
                                                        <div class="bg-[#1f2f1f] border border-[#436040] rounded-xl p-3 space-y-2">
                                                            @if($latestSubmission->submitted_at)
                                                                <p class="text-xs text-gray-400">Dikumpulkan: {{ $latestSubmission->submitted_at->format('d M Y H:i') }}</p>
                                                            @endif
                                                            <div class="flex flex-wrap gap-2 items-center">
                                                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $latestSubmission->text_submission ? 'bg-blue-600 text-white' : 'bg-gray-600 text-white' }}">
                                                                    {{ $latestSubmission->text_submission ? 'TEXT SUBMITTED' : 'NO TEXT' }}
                                                                </span>
                                                                <span class="px-3 py-1 rounded-full text-xs font-semibold {{ $latestSubmission->file_submission ? 'bg-purple-600 text-white' : 'bg-gray-600 text-white' }}">
                                                                    {{ $latestSubmission->file_submission ? 'FILE SUBMITTED' : 'NO FILE' }}
                                                                </span>
                                                            </div>
                                                            @if($latestSubmission->text_submission)
                                                                <p class="text-sm mt-2 text-gray-300">Jawaban Teks: <span class="text-white">{{ \Illuminate\Support\Str::limit($latestSubmission->text_submission, 160) }}</span></p>
                                                            @endif
                                                            @if($latestSubmission->file_submission)
                                                                <a href="{{ route('dosen.assignment.download', $latestSubmission->id) }}" class="inline-flex items-center gap-2 text-blue-400 hover:text-blue-300 text-sm font-semibold transition-colors">
                                                                    <i class="fa-solid fa-download"></i>
                                                                    Download File Jawaban
                                                                </a>
                                                            @endif

                                                            <div class="mt-3">
                                                                <p class="text-sm font-semibold text-white">Penilaian</p>
                                                                @if(!is_null($latestSubmission->grade))
                                                                    <div id="gradeDisplay-{{ $latestSubmission->id }}" class="space-y-1">
                                                                        <p class="text-sm text-gray-300">Nilai: <span class="font-bold text-white">{{ $latestSubmission->grade }}</span></p>
                                                                        @if($latestSubmission->feedback)
                                                                            <p class="text-sm text-gray-300">Feedback: <span class="text-white">{{ $latestSubmission->feedback }}</span></p>
                                                                        @endif
                                                                        <button type="button" onclick="toggleGradeForm({{ $latestSubmission->id }}, true)" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1 rounded-full transition-colors">Edit</button>
                                                                    </div>
                                                                    <form id="gradeForm-{{ $latestSubmission->id }}" action="{{ route('dosen.assignment.grade', $latestSubmission->id) }}" method="POST" class="mt-2 flex flex-col md:flex-row gap-2 items-start md:items-center hidden">
                                                                        @csrf
                                                                        <input type="number" name="grade" min="0" max="100" class="rounded-lg bg-[#2f3d2c] border border-[#436040] text-white p-2 w-24 placeholder-gray-400" placeholder="Nilai" value="{{ $latestSubmission->grade ?? '' }}" required>
                                                                        <textarea name="feedback" rows="2" class="rounded-lg bg-[#2f3d2c] border border-[#436040] text-white p-2 flex-1 placeholder-gray-400" placeholder="Feedback (opsional)">{{ $latestSubmission->feedback ?? '' }}</textarea>
                                                                        <div class="flex gap-2">
                                                                            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-full transition-colors">Simpan</button>
                                                                            <button type="button" onclick="toggleGradeForm({{ $latestSubmission->id }}, false)" class="bg-gray-600 hover:bg-gray-700 text-white text-sm font-semibold px-4 py-2 rounded-full transition-colors">Batal</button>
                                                                        </div>
                                                                    </form>
                                                                @else
                                                                    <form action="{{ route('dosen.assignment.grade', $latestSubmission->id) }}" method="POST" class="mt-2 flex flex-col md:flex-row gap-2 items-start md:items-center">
                                                                        @csrf
                                                                        <input type="number" name="grade" min="0" max="100" class="rounded-lg bg-[#2f3d2c] border border-[#436040] text-white p-2 w-24 placeholder-gray-400" placeholder="Nilai" required>
                                                                        <textarea name="feedback" rows="2" class="rounded-lg bg-[#2f3d2c] border border-[#436040] text-white p-2 flex-1 placeholder-gray-400" placeholder="Feedback (opsional)"></textarea>
                                                                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white text-sm font-semibold px-4 py-2 rounded-full transition-colors">Simpan</button>
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
        </div>
    </div>
</body>
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
    document.addEventListener('DOMContentLoaded', function() {
        const bell = document.getElementById('bellIcon');
        const gear = document.getElementById('gearIcon');
        if (bell) { bell.addEventListener('click', function(e) { e.stopPropagation(); toggleNotificationPopup(); }); }
        if (gear) { gear.addEventListener('click', function(e) { e.stopPropagation(); toggleProfilePopup(); }); }
    });
    function toggleProfilePopup() {
        document.getElementById('notificationPopup').classList.remove('show');
        const popup = document.getElementById('profilePopup');
        const overlay = document.getElementById('popupOverlay');
        popup.classList.toggle('show');
        overlay.classList.toggle('show');
        if (!popup.classList.contains('show')) { document.getElementById('languageMenu').classList.remove('show'); }
    }
    function toggleNotificationPopup() {
        document.getElementById('profilePopup').classList.remove('show');
        const popup = document.getElementById('notificationPopup');
        const overlay = document.getElementById('popupOverlay');
        popup.classList.toggle('show');
        overlay.classList.toggle('show');
    }
    function closeAllPopups() {
        document.getElementById('profilePopup').classList.remove('show');
        document.getElementById('notificationPopup').classList.remove('show');
        document.getElementById('languageMenu').classList.remove('show');
        document.getElementById('popupOverlay').classList.remove('show');
    }
    function toggleLanguageMenu() { const languageMenu = document.getElementById('languageMenu'); languageMenu.classList.toggle('show'); }
    document.addEventListener('click', function(event) {
        const profilePopup = document.getElementById('profilePopup');
        const notificationPopup = document.getElementById('notificationPopup');
        const bellIcon = document.getElementById('bellIcon');
        const gearIcon = document.getElementById('gearIcon');
        if (!profilePopup.contains(event.target) && !notificationPopup.contains(event.target) && !bellIcon.contains(event.target) && !gearIcon.contains(event.target) && (profilePopup.classList.contains('show') || notificationPopup.classList.contains('show'))) { closeAllPopups(); }
    });
</script>
<script>
    // Search functionality
    document.getElementById('exerciseSearch').addEventListener('keyup', function() {
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

        // Show "No results" message if needed (optional, but good UX)
        // Check if we need to add a no results placeholder
    });
</script>
</html>
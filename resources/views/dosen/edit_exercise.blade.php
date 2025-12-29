<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Assignment - {{ $mahasiswa->user->name ?? 'Mahasiswa' }}</title>
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
    <!-- Decorative Line Background -->
    <div class="fixed inset-0 z-0 overflow-hidden">
        <img src="{{ asset('line.png') }}" alt="Decorative Line" class="w-full h-full object-cover opacity-10 scale-150">
    </div>

    <div class="relative z-10 min-h-screen">
        <!-- Header -->
        <header class="w-full bg-[#1f2f1f] text-white flex items-center justify-between px-8 py-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('dosen.mahasiswa.exercises', $mahasiswa->id) }}" class="text-gray-300 hover:text-white transition-colors">
                    <i class="fa-solid fa-arrow-left text-xl"></i>
                </a>
                <div class="text-2xl font-bold">{{ Auth::user()->name ?? 'Dosen' }}</div>
            </div>
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

        <div class="max-w-4xl mx-auto px-6 py-8">
            <div class="mb-6">
                <button onclick="window.location.href='{{ route('dosen.dashboard') }}'" class="flex items-center gap-2 text-white hover:text-gray-300 transition-colors">
                    <i class="fa-solid fa-arrow-left"></i>
                    <span>Back to Dashboard</span>
                </button>
            </div>
        </div>

        <!-- Popups -->
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
                    @if(Auth::user() && Auth::user()->profile_picture)
                        <img src="{{ asset('storage/profile_pictures/' . Auth::user()->profile_picture) }}" alt="Profile" class="rounded-full w-12 h-12 object-cover">
                    @else
                        <i class="fa-solid fa-user text-xl"></i>
                    @endif
                </div>
                <div>
                    <p class="font-semibold">{{ Auth::user()->name ?? 'Dosen' }}</p>
                    <p class="text-sm text-gray-400">{{ Auth::user()->email ?? '' }}</p>
                </div>
            </div>
            <div class="profile-item" onclick="window.location.href='{{ route('profile.settings') }}'">
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

        <main class="max-w-4xl mx-auto px-6 py-10">
            <div class="flex items-center justify-between mb-6">
                <div>
                    <p class="text-sm text-gray-200">Edit Assignment untuk</p>
                    <h1 class="text-2xl font-bold">{{ $mahasiswa->user->name ?? $mahasiswa->nama ?? 'Mahasiswa' }}</h1>
                </div>
            </div>

            @if ($errors->any())
                <div class="mb-6 bg-red-100 text-red-800 p-4 rounded-lg">
                    <ul class="list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('error'))
                <div class="mb-6 bg-[#3d2c2c] text-red-400 border border-red-500 rounded-xl px-4 py-3 shadow-md">{{ session('error') }}</div>
            @endif

            <form action="{{ route('dosen.exercise.update', $exercise->id) }}" method="POST" enctype="multipart/form-data" class="bg-[#2f3d2c] border border-[#3c4c39] rounded-2xl p-6 shadow-xl space-y-4">
                @csrf
                @method('PUT')
                
                <div>
                    <label class="block text-sm text-gray-200 mb-1">Status</label>
                    <select name="status" class="w-full rounded-lg bg-[#1f2f1f] text-white border border-[#436040] p-3 placeholder-gray-400">
                        <option value="draft" {{ old('status', $exercise->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ old('status', $exercise->status) == 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm text-gray-200 mb-1">Judul</label>
                    <input type="text" name="title" value="{{ old('title', $exercise->title) }}" class="w-full rounded-lg bg-[#1f2f1f] text-white border border-[#436040] p-3 placeholder-gray-400" placeholder="Contoh: Assignment 1 - Introduction to UI/UX Design" required>
                </div>

                <div>
                    <label class="block text-sm text-gray-200 mb-1">Deskripsi (opsional)</label>
                    <textarea name="description" rows="3" class="w-full rounded-lg bg-[#1f2f1f] text-white border border-[#436040] p-3 placeholder-gray-400" placeholder="Ringkasan soal atau instruksi">{{ old('description', $exercise->description) }}</textarea>
                </div>

                <div>
                    <label class="block text-sm text-gray-200 mb-1">Deadline (opsional)</label>
                    <input type="datetime-local" name="deadline" value="{{ old('deadline', $exercise->deadline ? $exercise->deadline->format('Y-m-d\TH:i') : '') }}" class="w-full rounded-lg bg-[#1f2f1f] text-white border border-[#436040] p-3 placeholder-gray-400">
                </div>

                <div>
                    <label class="block text-sm text-gray-200 mb-1">Link (opsional)</label>
                    <input type="url" name="link" value="{{ old('link', $exercise->link) }}" class="w-full rounded-lg bg-[#1f2f1f] text-white border border-[#436040] p-3 placeholder-gray-400" placeholder="https://example.com">
                </div>

                <div>
                    <label class="block text-sm text-gray-200 mb-1">File Assignment (opsional)</label>
                    @if($exercise->file_attachment)
                        <div class="mb-2 p-3 bg-[#1f2f1f] border border-[#436040] rounded-lg">
                            <p class="text-sm text-gray-300 mb-1">File saat ini:</p>
                            <a href="{{ asset('storage/' . $exercise->file_attachment) }}" target="_blank" class="text-blue-400 hover:text-blue-300 text-sm font-semibold">
                                <i class="fa-solid fa-file"></i> {{ basename($exercise->file_attachment) }}
                            </a>
                        </div>
                    @endif
                    <input type="file" name="file_attachment" class="w-full rounded-lg bg-[#1f2f1f] text-white border border-[#436040] p-3 placeholder-gray-400">
                    <p class="text-xs text-gray-200 mt-1">Upload file baru untuk mengganti file yang ada (PDF, DOC, DOCX, ZIP, etc.)</p>
                </div>

                <div class="pt-2 flex gap-3">
                    <button type="submit" class="flex-1 bg-[#1d8f3b] hover:bg-[#167731] text-white font-semibold py-3 rounded-xl transition">Perbarui Assignment</button>
                    <a href="{{ route('dosen.mahasiswa.exercises', $mahasiswa->id) }}" class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 rounded-xl transition text-center">Batal</a>
                </div>
            </form>
        </main>
    </div>

    <script>
        function toggleProfilePopup() {
            const popup = document.getElementById('profilePopup');
            const overlay = document.getElementById('popupOverlay');
            
            if (popup.classList.contains('show')) {
                popup.classList.remove('show');
                overlay.classList.remove('show');
            } else {
                document.getElementById('notificationPopup').classList.remove('show');
                popup.classList.add('show');
                overlay.classList.add('show');
            }
        }
        
        function toggleNotificationPopup() {
            const popup = document.getElementById('notificationPopup');
            const overlay = document.getElementById('popupOverlay');
            
            if (popup.classList.contains('show')) {
                popup.classList.remove('show');
                overlay.classList.remove('show');
            } else {
                document.getElementById('profilePopup').classList.remove('show');
                popup.classList.add('show');
                overlay.classList.add('show');
            }
        }
        
        function closeAllPopups() {
            document.getElementById('profilePopup').classList.remove('show');
            document.getElementById('notificationPopup').classList.remove('show');
            document.getElementById('popupOverlay').classList.remove('show');
        }
        
        function toggleLanguageMenu() {
            const menu = document.getElementById('languageMenu');
            menu.classList.toggle('show');
        }
        
        document.addEventListener('DOMContentLoaded', function() {
            const gearIcon = document.getElementById('gearIcon');
            if (gearIcon) {
                gearIcon.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleProfilePopup();
                });
            }
            
            const bellIcon = document.getElementById('bellIcon');
            if (bellIcon) {
                bellIcon.addEventListener('click', function(e) {
                    e.stopPropagation();
                    toggleNotificationPopup();
                });
            }
        });
    </script>
</body>
</html>


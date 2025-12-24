<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Learning Progress - {{ $mahasiswa->user->name }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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

    <main class="max-w-7xl mx-auto px-4 py-8">
        <div class="mb-8">
            <h2 class="text-2xl font-bold mb-2">Learning Progress untuk {{ $mahasiswa->user->name }}</h2>
        </div>

        <!-- Stats Overview -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white text-black rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="rounded-full bg-blue-100 p-3 mr-4">
                        <i class="fas fa-book text-blue-500 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Total Learning Difficulties</p>
                        <p class="text-2xl font-bold">{{ $mahasiswa->learningDifficulties->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white text-black rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="rounded-full bg-green-100 p-3 mr-4">
                        <i class="fas fa-check-circle text-green-500 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Resolved Difficulties</p>
                        <p class="text-2xl font-bold">{{ $mahasiswa->learningDifficulties->where('status', 'resolved')->count() }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white text-black rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="rounded-full bg-yellow-100 p-3 mr-4">
                        <i class="fas fa-clock text-yellow-500 text-xl"></i>
                    </div>
                    <div>
                        <p class="text-gray-500 text-sm">Pending Difficulties</p>
                        <p class="text-2xl font-bold">{{ $mahasiswa->learningDifficulties->where('status', '!=', 'resolved')->count() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Learning Difficulties Section -->
        <div class="bg-white text-black rounded-lg shadow mb-8">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-medium text-gray-800">Learning Difficulties & AI Recommendation</h3>
            </div>
            <div class="p-6">
                @if($mahasiswa->learningDifficulties->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 text-black">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Subject</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Reported</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">AI Recommendation</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($learningRecommendations as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        {{ $item['difficulty']->subject ?? $item['difficulty']->title }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {{ Str::limit($item['difficulty']->description, 80) }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if(($item['difficulty']->status ?? null) === 'resolved')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Resolved
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                Pending
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ optional($item['difficulty']->created_at)->format('M d, Y') ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-900">
                                        {!! nl2br(e($item['ai_result'])) !!}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <p class="text-gray-600 text-center py-4">Belum ada learning difficulties yang dilaporkan.</p>
                @endif
            </div>
        </div>

        
    </main>
    </div>
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
</body>
</html>
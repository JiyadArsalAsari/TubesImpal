<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyFlow</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <!-- FontAwesome Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            background-color: #4b5b3b;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            color: white;
            overflow-x: hidden;
        }
        
        /* Decorative Line Background */
        .decorative-line {
            position: fixed;
            inset: 0;
            z-index: 0;
            overflow: hidden;
        }
        
        .decorative-line img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.1;
            transform: scale(1.5);
        }
        
        .content-container {
            position: relative;
            z-index: 10;
            min-height: 100vh;
        }
        
        .header {
            background-color: #1f2f1f;
            height: 80px;
        }
        
        .search-bar {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .card-item {
            background: rgba(47, 59, 38, 0.8); /* #2f3b26 with opacity */
            border-radius: 0.75rem;
            transition: all 0.3s ease;
        }
        
        .card-item:hover {
            background: rgba(47, 59, 38, 0.95);
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.2);
        }
        
        .logo-container {
            cursor: pointer;
        }
        
        /* Profile Popup Styles */
        .profile-popup {
            display: none;
            position: absolute;
            top: 60px;
            right: 20px;
            background-color: #1f2f1f;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            min-width: 250px;
            padding: 20px;
        }
        
        .profile-popup.show {
            display: block;
        }
        
        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 999;
        }
        
        .popup-overlay.show {
            display: block;
        }
        
        .profile-item {
            padding: 12px 15px;
            border-radius: 8px;
            cursor: pointer;
            transition: background-color 0.2s;
        }
        
        .profile-item:hover {
            background-color: rgba(255, 255, 255, 0.1);
        }
        
        .profile-divider {
            height: 1px;
            background-color: rgba(255, 255, 255, 0.1);
            margin: 10px 0;
        }
        
        /* Language submenu */
        .language-submenu {
            display: none;
            margin-left: 20px;
        }
        
        .language-submenu.show {
            display: block;
        }
        
        /* Notification badge */
        .notification-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            background-color: #e74c3c;
            color: white;
            border-radius: 50%;
            width: 20px;
            height: 20px;
            font-size: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        /* Notification Popup Styles */
        .notification-popup {
            display: none;
            position: absolute;
            top: 60px;
            right: 70px;
            background-color: #1f2f1f;
            border-radius: 10px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            z-index: 1000;
            min-width: 300px;
            max-width: 350px;
            padding: 30px;
            text-align: center;
        }
        
        .notification-popup.show {
            display: block;
        }
    </style>
</head>
<body>
    <!-- Decorative Line Background -->
    <div class="decorative-line">
        <img src="{{ asset('line.png') }}" alt="Decorative Line">
    </div>
    
    <!-- Popup Overlay -->
    <div class="popup-overlay" id="popupOverlay" onclick="closeAllPopups()"></div>
    
    <!-- Notification Popup -->
    <div class="notification-popup" id="notificationPopup">
        <div class="mb-4">
            <i class="fa-regular fa-bell text-3xl text-gray-400 mb-3"></i>
            <h3 class="font-bold text-xl mb-2">Notifications</h3>
            <p class="text-gray-400">Notifications will be displayed here</p>
        </div>
    </div>
    
    <!-- Profile Popup -->
    <div class="profile-popup" id="profilePopup">
        <!-- User Info -->
        <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-700">
            <div class="bg-gray-700 rounded-full w-12 h-12 flex items-center justify-center">
                <i class="fa-solid fa-user text-xl"></i>
            </div>
            <div>
                <p class="font-semibold">{{ Auth::user()->name ?? 'User' }}</p>
                <p class="text-sm text-gray-400">{{ Auth::user()->email ?? 'user@example.com' }}</p>
            </div>
        </div>
        
        <!-- Profile Menu Items -->
        <div class="profile-item">
            <div class="flex items-center gap-3">
                <i class="fa-solid fa-user-gear"></i>
                <span>Profile Settings</span>
            </div>
        </div>
        
        <div class="profile-divider"></div>
        
        <!-- Language Options -->
        <div class="profile-item" onclick="toggleLanguageMenu()">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-language"></i>
                    <span>Language</span>
                </div>
                <i class="fa-solid fa-chevron-right text-gray-400"></i>
            </div>
        </div>
        
        <!-- Language Submenu -->
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
        
        <!-- Logout -->
        <div class="profile-item" onclick="window.location.href='{{ route('logout') }}'">
            <div class="flex items-center gap-3 text-red-400">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </div>
        </div>
    </div>
    
    <!-- Content Container -->
    <div class="content-container">
        <!-- Header -->
        <header class="w-full bg-[#1f2f1f] text-white flex items-center justify-between px-8 py-4">
            <div class="text-2xl font-bold">{{ Auth::user()->name ?? 'Ahmad Azhar' }}</div>
            <div class="flex items-center justify-center absolute left-1/2 transform -translate-x-1/2 logo-container" onclick="window.location.href='{{ route('mahasiswa.dashboard') }}'">
                <img src="{{ asset('logo.png') }}" class="w-24 h-24 filter brightness-0 invert" />
            </div>
            <div class="flex gap-6 text-3xl relative">
                <!-- Notification Icon -->
                <div class="relative cursor-pointer" id="bellIcon">
                    <i class="fa-regular fa-bell"></i>
                    <span class="notification-badge">3</span>
                </div>
                
                <!-- Settings Icon -->
                <div class="cursor-pointer" id="gearIcon">
                    <i class="fa-solid fa-gear"></i>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="max-w-3xl mx-auto px-6 py-8">
            @yield('content')
        </main>
    </div>

    <script>
        // Add event listeners after DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Add click event to bell icon
            document.getElementById('bellIcon').addEventListener('click', function(e) {
                e.stopPropagation();
                toggleNotificationPopup();
            });
            
            // Add click event to gear icon
            document.getElementById('gearIcon').addEventListener('click', function(e) {
                e.stopPropagation();
                toggleProfilePopup();
            });
        });
        
        // Toggle profile popup visibility
        function toggleProfilePopup() {
            // Close notification popup if open
            document.getElementById('notificationPopup').classList.remove('show');
            
            const popup = document.getElementById('profilePopup');
            const overlay = document.getElementById('popupOverlay');
            
            popup.classList.toggle('show');
            overlay.classList.toggle('show');
            
            // Hide language menu when closing profile popup
            if (!popup.classList.contains('show')) {
                document.getElementById('languageMenu').classList.remove('show');
            }
        }
        
        // Toggle notification popup visibility
        function toggleNotificationPopup() {
            // Close profile popup if open
            document.getElementById('profilePopup').classList.remove('show');
            
            const popup = document.getElementById('notificationPopup');
            const overlay = document.getElementById('popupOverlay');
            
            popup.classList.toggle('show');
            overlay.classList.toggle('show');
        }
        
        // Close all popups
        function closeAllPopups() {
            document.getElementById('profilePopup').classList.remove('show');
            document.getElementById('notificationPopup').classList.remove('show');
            document.getElementById('languageMenu').classList.remove('show');
            document.getElementById('popupOverlay').classList.remove('show');
        }
        
        // Toggle language submenu
        function toggleLanguageMenu() {
            const languageMenu = document.getElementById('languageMenu');
            languageMenu.classList.toggle('show');
        }
        
        // Close popup when clicking outside
        document.addEventListener('click', function(event) {
            const profilePopup = document.getElementById('profilePopup');
            const notificationPopup = document.getElementById('notificationPopup');
            const bellIcon = document.getElementById('bellIcon');
            const gearIcon = document.getElementById('gearIcon');
            
            if (!profilePopup.contains(event.target) && 
                !notificationPopup.contains(event.target) && 
                !bellIcon.contains(event.target) && 
                !gearIcon.contains(event.target) && 
                (profilePopup.classList.contains('show') || notificationPopup.classList.contains('show'))) {
                closeAllPopups();
            }
        });
    </script>
</body>
</html>
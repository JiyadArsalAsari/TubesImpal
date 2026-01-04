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
            background: rgba(47, 59, 38, 0.8);
            /* #2f3b26 with opacity */
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
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.5);
            z-index: 1000;
            width: 400px;
            max-height: 500px;
            border: 1px solid rgba(255, 255, 255, 0.1);
            flex-direction: column;
        }

        .notification-popup.show {
            display: flex;
        }

        .notification-header {
            padding: 16px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #1f2f1f;
            border-radius: 12px 12px 0 0;
        }

        .notification-content {
            overflow-y: auto;
            max-height: 400px;
            padding: 0;
        }

        .notification-item {
            padding: 16px;
            padding-right: 70px; /* Space for icons to prevent overlap */
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
            transition: all 0.2s;
            cursor: pointer;
            position: relative;
        }

        .notification-item:hover {
            background-color: rgba(255, 255, 255, 0.05);
        }

        .notification-item.unread {
            background-color: rgba(43, 89, 43, 0.2);
            border-left: 4px solid #4ade80;
        }

        .notification-item.read {
            opacity: 0.7;
            border-left: 4px solid transparent;
        }

        .notification-item .check-icon {
            opacity: 0;
            transition: opacity 0.2s;
            position: absolute;
            top: 20px;
            right: 16px;
            color: #4ade80;
        }

        .notification-item.read .check-icon {
            opacity: 1;
        }

        .notification-item .delete-icon {
            opacity: 0;
            transition: opacity 0.2s;
            position: absolute;
            top: 20px;
            right: 42px;
            color: #ef4444;
            cursor: pointer;
        }

        .notification-item.read .delete-icon {
            opacity: 1;
        }

        .notification-item.read .delete-icon:hover {
            color: #dc2626;
        }
    </style>
    @stack('styles')
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
        <div class="notification-header">
            <h3 class="text-white font-bold text-lg">Notifications</h3>
            <div class="flex gap-3">
                <form action="{{ route('mahasiswa.notifications.readAll') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs text-green-400 hover:text-green-300 transition font-medium">
                        Mark all as read
                    </button>
                </form>
                <form action="{{ route('mahasiswa.notifications.deleteAll') }}" method="POST" id="deleteAllForm">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                        class="text-xs transition font-medium"
                        id="deleteAllBtn"
                        onclick="return confirm('Are you sure you want to delete all read notifications?')">
                        Delete all notifications
                    </button>
                </form>
            </div>
        </div>

        <div class="notification-content">
            @if(isset($notifications) && count($notifications) > 0)
                @foreach($notifications as $notification)
                    @php
                        $redirectUrl = '';
                        if (isset($notification->data['type'])) {
                            if ($notification->data['type'] == 'schedule') {
                                $redirectUrl = route('mahasiswa.schedule');
                            } elseif ($notification->data['type'] == 'deadline') {
                                $redirectUrl = route('mahasiswa.deadline');
                            } elseif ($notification->data['type'] == 'quiz') {
                                $redirectUrl = route('mahasiswa.exercise');
                            } elseif ($notification->data['type'] == 'assignment') {
                                $redirectUrl = route('mahasiswa.exercise');
                            } elseif ($notification->data['type'] == 'grade') {
                                // Redirect to exercise page with 'reviews' hash to auto-open the completed modal
                                $redirectUrl = route('mahasiswa.exercise') . '#reviews';
                            } elseif ($notification->data['type'] == 'feedback') {
                                $redirectUrl = route('mahasiswa.learning.development');
                            } elseif ($notification->data['type'] == 'request_response') {
                                // Dosen: Request accept/reject notification
                                $redirectUrl = route('dosen.dashboard');
                            } elseif ($notification->data['type'] == 'quiz_submitted') {
                                // Dosen: Quiz completion notification
                                $redirectUrl = route('dosen.mahasiswa.exercises', $notification->data['mahasiswa_id']);
                            } elseif ($notification->data['type'] == 'assignment_submitted') {
                                // Dosen: Assignment submission notification
                                $redirectUrl = route('dosen.mahasiswa.exercises', $notification->data['mahasiswa_id']);
                            }
                        }
                    @endphp
                    <div class="notification-item {{ $notification->read_at ? 'read' : 'unread' }}"
                        onclick="markAsRead('{{ $notification->id }}', this, '{{ $redirectUrl }}')">

                        <div class="flex justify-between items-start mb-1">
                            <h4 class="text-sm font-bold text-white">{{ $notification->data['title'] ?? 'Notification' }}</h4>
                            <span class="text-[10px] text-gray-400 whitespace-nowrap ml-2">
                                {{ $notification->created_at->diffForHumans(null, true, true) }}
                            </span>
                        </div>

                        <p class="text-xs text-gray-300 leading-relaxed mb-2">{{ $notification->data['message'] ?? '' }}</p>

                        <div class="flex items-center gap-2">
                            <span
                                class="text-[10px] uppercase font-bold px-2 py-0.5 rounded
                                                        {{ isset($notification->data['type']) && $notification->data['type'] == 'deadline' ? 'bg-red-900/50 text-red-200' :
                    (isset($notification->data['type']) && $notification->data['type'] == 'schedule' ? 'bg-blue-900/50 text-blue-200' : 'bg-gray-700 text-gray-200') }}">
                                {{ str_replace('_', ' ', $notification->data['type'] ?? 'INFO') }}
                            </span>
                        </div>

                        {{-- Check Icon (Appears when read) --}}
                        <i class="fa-solid fa-check check-icon"></i>

                        {{-- Delete Icon (Appears when read) --}}
                        <i class="fa-solid fa-trash delete-icon" 
                           onclick="event.stopPropagation(); deleteNotification('{{ $notification->id }}', this.closest('.notification-item'))"></i>

                        {{-- Add Accept/Reject buttons for Dosen Request (Only if unread) --}}
                        @if(!$notification->read_at && isset($notification->data['type']) && $notification->data['type'] == 'dosen_request' && isset($notification->data['request_id']))
                            <div class="mt-3 flex gap-2" onclick="event.stopPropagation()">
                                <button onclick="handleDosenRequest('{{ $notification->data['request_id'] }}', 'accept', this)"
                                    class="text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1.5 rounded transition shadow-sm font-medium">
                                    Accept
                                </button>
                                <button onclick="handleDosenRequest('{{ $notification->data['request_id'] }}', 'reject', this)"
                                    class="text-xs bg-red-600 hover:bg-red-700 text-white px-3 py-1.5 rounded transition shadow-sm font-medium">
                                    Reject
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            @else
                <div class="text-center text-gray-400 text-sm py-8 flex flex-col items-center justify-center h-full">
                    <div class="bg-gray-800/50 p-4 rounded-full mb-3">
                        <i class="fa-regular fa-bell-slash text-2xl text-gray-500"></i>
                    </div>
                    <span>No notifications yet</span>
                </div>
            @endif
        </div>
    </div>

    <!-- Profile Popup -->
    <div class="profile-popup" id="profilePopup">
        <!-- User Info -->
        <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-700">
            <div class="bg-gray-700 rounded-full w-12 h-12 flex items-center justify-center">
                @if(Auth::user() && Auth::user()->profile_picture)
                    <img src="{{ asset('storage/profile_pictures/' . Auth::user()->profile_picture) }}" alt="Profile"
                        class="rounded-full w-12 h-12 object-cover">
                @else
                    <i class="fa-solid fa-user text-xl"></i>
                @endif
            </div>
            <div>
                <p class="font-semibold">{{ Auth::user()->username ?? 'User' }}</p>
                <p class="text-sm text-gray-400">{{ Auth::user()->email ?? 'user@example.com' }}</p>
            </div>
        </div>

        <!-- Profile Menu Items -->
        <div class="profile-item" onclick="window.location.href='{{ route('profile.settings') }}'">
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
            <div class="flex items-center gap-3">
                @if(Auth::user() && Auth::user()->profile_picture)
                    <img src="{{ asset('storage/profile_pictures/' . Auth::user()->profile_picture) }}" alt="Profile"
                        class="rounded-full w-10 h-10 object-cover">
                @else
                    <div class="bg-gray-700 rounded-full w-10 h-10 flex items-center justify-center">
                        <i class="fa-solid fa-user"></i>
                    </div>
                @endif
                <div>
                    <div class="font-semibold">{{ Auth::user()->username ?? 'User' }}</div>
                    <div class="text-xs text-gray-400">{{ Auth::user()->role ?? 'User' }}</div>
                </div>
            </div>
            <div class="flex items-center justify-center absolute left-1/2 transform -translate-x-1/2 logo-container"
                onclick="window.location.href='{{ Auth::user() && Auth::user()->role == 'dosen' ? route('dosen.dashboard') : (Auth::user() && Auth::user()->role == 'admin' ? route('admin.dashboard') : route('mahasiswa.dashboard')) }}'">
                <img src="{{ asset('logo.png') }}" class="w-24 h-24 filter brightness-0 invert" />
            </div>
            <div class="flex gap-6 text-3xl relative">
                <!-- Notification Icon -->
                <div class="relative cursor-pointer" id="bellIcon">
                    <i class="fa-regular fa-bell"></i>
                    @if(isset($unreadNotificationsCount) && $unreadNotificationsCount > 0)
                        <span class="notification-badge">{{ $unreadNotificationsCount }}</span>
                    @endif
                </div>

                <!-- Settings Icon -->
                <div class="cursor-pointer" id="gearIcon">
                    <i class="fa-solid fa-gear"></i>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="@yield('container_class', 'max-w-3xl mx-auto px-6 py-8')">
            @yield('content')
        </main>
    </div>

    <script>
        // Add event listeners after DOM is loaded
        document.addEventListener('DOMContentLoaded', function () {
            // Add click event to bell icon
            document.getElementById('bellIcon').addEventListener('click', function (e) {
                e.stopPropagation();
                toggleNotificationPopup();
            });

            // Add click event to gear icon
            document.getElementById('gearIcon').addEventListener('click', function (e) {
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
        document.addEventListener('click', function (event) {
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

        // Mark notification as read
        function markAsRead(notificationId, element, redirectUrl = '') {
            const url = `/mahasiswa/notifications/${notificationId}/read`;

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update UI to show as read
                        if (element) {
                            element.classList.remove('unread');
                            element.classList.add('read');
                        }

                        // Decrease badge count
                        const badge = document.querySelector('.notification-badge');
                        if (badge) {
                            // Only decrease if it was previously unread (checked by class or attribute, 
                            // but simplest is to just rely on server or reload. 
                            // Since we might redirect, this UI update is temporary or skipped.)
                        }

                        // Update delete all button state
                        updateDeleteAllButtonState();

                        // Redirect if URL is provided
                        if (redirectUrl && redirectUrl.trim() !== '') {
                            window.location.href = redirectUrl;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error marking as read:', error);
                    // Even if error (e.g. already read), we should probably redirect if desired
                    if (redirectUrl && redirectUrl.trim() !== '') {
                        window.location.href = redirectUrl;
                    }
                });
        }

        // Delete single notification
        function deleteNotification(notificationId, element) {
            const url = `/mahasiswa/notifications/${notificationId}`;
            
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Show removal animation
                    element.style.opacity = '0';
                    element.style.transform = 'translateX(20px)';
                    setTimeout(() => {
                        location.reload(); // Reload page immediately like mark all as read
                    }, 200);
                }
            })
            .catch(error => console.error('Error deleting notification:', error));
        }

        // Update "Delete all notifications" button state
        function updateDeleteAllButtonState() {
            const deleteAllBtn = document.getElementById('deleteAllBtn');
            if (!deleteAllBtn) return;

            const unreadNotifications = document.querySelectorAll('.notification-item.unread');
            const allNotifications = document.querySelectorAll('.notification-item');
            
            // Enable only if there are notifications and all are read
            if (allNotifications.length > 0 && unreadNotifications.length === 0) {
                deleteAllBtn.classList.remove('opacity-50', 'cursor-not-allowed');
                deleteAllBtn.classList.add('text-red-400', 'hover:text-red-300');
                deleteAllBtn.disabled = false;
            } else {
                deleteAllBtn.classList.add('opacity-50', 'cursor-not-allowed');
                deleteAllBtn.classList.remove('text-red-400', 'hover:text-red-300');
                deleteAllBtn.classList.add('text-gray-500');
                deleteAllBtn.disabled = true;
            }
        }

        // Call on page load
        document.addEventListener('DOMContentLoaded', function() {
            updateDeleteAllButtonState();
        });

        // Handle Dosen Request (Accept/Reject)
        function handleDosenRequest(requestId, action, btnElement) {
            // Disable buttons to prevent double click
            const container = btnElement.parentElement;
            const buttons = container.querySelectorAll('button');
            buttons.forEach(btn => {
                btn.disabled = true;
                btn.classList.add('opacity-50', 'cursor-not-allowed');
            });

            // Determine route based on action
            const url = action === 'accept'
                ? `/mahasiswa/dosen-requests/${requestId}/accept`
                : `/mahasiswa/dosen-requests/${requestId}/reject`;

            // Get CSRF token
            const csrfToken = document.querySelector('meta[name="csrf-token"]')
                ? document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                : '{{ csrf_token() }}'; // Use blade syntax as fallback if meta not present (though meta is safer for JS)

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}' // Injecting directly via blade since we are in a blade file
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update UI
                        container.innerHTML = `<span class="text-xs ${action === 'accept' ? 'text-green-400' : 'text-red-400'} font-bold">
                        ${action === 'accept' ? 'Acccepted' : 'Rejected'}
                    </span>`;

                        // Optional: remove the notification after delay
                        setTimeout(() => {
                            const notificationItem = container.closest('.bg-[#2a3b2a]'); // Find the parent card
                            if (notificationItem) {
                                notificationItem.style.transition = 'opacity 0.5s';
                                notificationItem.style.opacity = '0';
                                setTimeout(() => notificationItem.remove(), 500);
                            }
                        }, 2000);
                    } else {
                        alert('Error: ' + (data.message || 'Something went wrong'));
                        // Re-enable
                        buttons.forEach(btn => {
                            btn.disabled = false;
                            btn.classList.remove('opacity-50', 'cursor-not-allowed');
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Network error occurred');
                    buttons.forEach(btn => {
                        btn.disabled = false;
                        btn.classList.remove('opacity-50', 'cursor-not-allowed');
                    });
                });
        }
    </script>
    @stack('scripts')
</body>

</html>
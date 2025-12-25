<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
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

        /* Dosen Request Notification */
        .request-notification {
            background-color: #2d3748;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .request-actions {
            display: flex;
            gap: 10px;
        }

        .btn-accept {
            background-color: #48bb78;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-reject {
            background-color: #f56565;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }

        .logo-container {
            cursor: pointer;
        }
    </style>
</head>

<body class="bg-[#4b5b3b] text-white font-sans relative overflow-x-hidden">
    <!-- Decorative Line Background -->
    <div class="fixed inset-0 z-0 overflow-hidden">
        <img src="{{ asset('line.png') }}" alt="Decorative Line"
            class="w-full h-full object-cover opacity-10 scale-150">
    </div>

    <!-- Popup Overlay -->
    <div class="popup-overlay" id="popupOverlay" onclick="closeAllPopups()"></div>

    <!-- Notification Popup -->
    <div class="notification-popup" id="notificationPopup">
        <div class="mb-4">
            <i class="fa-regular fa-bell text-3xl text-gray-400 mb-3"></i>
            <h3 class="font-bold text-xl mb-2">Notifications</h3>
            <div id="notificationContent">
                <p class="text-gray-400">Notifications will be displayed here</p>
            </div>
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
                <p class="font-semibold">{{ Auth::user()->username }}</p>
                <p class="text-sm text-gray-400">{{ Auth::user()->email }}</p>
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

        <!-- Logout -->
        <div class="profile-item" onclick="window.location.href='{{ route('logout') }}'">
            <div class="flex items-center gap-3 text-red-400">
                <i class="fa-solid fa-right-from-bracket"></i>
                <span>Logout</span>
            </div>
        </div>
    </div>

    <!-- Feedback Modal (Success/Error) -->
    <div id="feedbackModal"
        class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-[100] hidden transition-opacity duration-300">
        <div
            class="bg-[#1a261a] rounded-xl p-8 w-96 text-center shadow-2xl transform scale-100 transition-transform duration-300 border border-[#2d3a2d]">
            <!-- Icon Circle -->
            <div id="modalIconContainer" class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                <i id="modalIcon" class="fa-solid text-4xl"></i>
            </div>

            <h2 id="modalTitle" class="text-white text-2xl font-bold mb-3"></h2>
            <p id="modalMessage" class="text-gray-300 mb-4 leading-relaxed"></p>

            <!-- Auto-close Progress -->
            <div class="mb-6 w-full max-w-[200px] mx-auto">
                <p id="countdownText" class="text-xs text-gray-400 mb-2 font-medium">Menutup otomatis dalam 3 detik</p>
                <div class="w-full bg-[#2d3a2d] rounded-full h-1.5 overflow-hidden">
                    <div id="countdownBar"
                        class="bg-[#4ade80] h-1.5 rounded-full transition-all duration-[3000ms] ease-linear w-full">
                    </div>
                </div>
            </div>

            <button onclick="closeFeedbackModal()" id="modalButton"
                class="text-white font-semibold py-3 px-8 rounded-full transition w-full">
                Close
            </button>
        </div>
    </div>

    <div class="relative z-10 min-h-screen flex flex-col">
        <!-- HEADER -->
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
                    <div class="font-semibold">{{ Auth::user()->username }}</div>
                    <div class="text-xs text-gray-400">{{ Auth::user()->role }}</div>
                </div>
            </div>
            <div class="flex items-center justify-center absolute left-1/2 transform -translate-x-1/2 logo-container"
                onclick="window.location.href='{{ route('mahasiswa.dashboard') }}'">
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

        <main class="flex-grow flex flex-col justify-center w-full gap-16 pb-12 mt-8">
            <!-- Greeting -->
            <div class="text-center w-full px-4">
                @if(session('success'))
                    <div class="bg-green-500 text-white p-4 rounded-lg mb-6 shadow-lg max-w-5xl mx-auto">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-500 text-white p-4 rounded-lg mb-6 shadow-lg max-w-5xl mx-auto">
                        {{ session('error') }}
                    </div>
                @endif

                <h1 class="text-2xl md:text-3xl font-bold text-white tracking-wide drop-shadow-md">
                    Hello {{ $mahasiswa->nama ?? Auth::user()->username }}, Welcome Back To StudyFlow!
                </h1>
            </div>

            <!-- Cards Section -->
            <div class="max-w-5xl mx-auto px-6 w-full flex flex-col gap-20">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <!-- Schedule Card -->
                    <div class="bg-[#e6e7d9] text-black rounded-3xl p-8 w-full shadow-xl flex flex-col items-center text-center cursor-pointer transform transition-transform hover:scale-105"
                        onclick="openScheduleModal()">
                        <div class="flex items-center gap-4 mb-4">
                            <i class="fa-regular fa-calendar text-2xl"></i>
                            <span class="font-bold text-xl">Your Schedule Today</span>
                        </div>
                        @if($todaysSchedule)
                            <p class="font-bold text-xl mb-2 text-black">{{ $todaysSchedule->subject_name }}</p>
                            <p class="text-lg text-black">{{ $todaysSchedule->room }} — {{ $todaysSchedule->time }}</p>
                        @elseif(isset($nextSchedule) && $nextSchedule)
                            <p class="font-bold text-xl mb-2 text-black">Next: {{ $nextSchedule->subject_name }}</p>
                            <p class="text-lg text-black">{{ $nextSchedule->day }} — {{ $nextSchedule->room }} —
                                {{ $nextSchedule->time }}
                            </p>
                        @else
                            <p class="font-bold text-xl mb-2 text-black">No schedule for today</p>
                        @endif
                    </div>

                    <!-- Deadline Card -->
                    <div class="bg-[#e6e7d9] text-black rounded-3xl p-8 w-full shadow-xl flex flex-col items-center text-center cursor-pointer transform transition-transform hover:scale-105"
                        onclick="openDeadlineModal()">
                        <div class="flex items-center gap-4 mb-4">
                            <i class="fa-solid fa-clock text-2xl"></i>
                            <span class="font-bold text-xl">Your Deadline Today</span>
                        </div>
                        @if($todaysDeadline)
                            <p class="font-bold text-xl mb-2 text-black">{{ $todaysDeadline->subject_name }}</p>
                            <p class="text-lg text-black">{{ $todaysDeadline->time }}</p>
                        @elseif(isset($nextUpcomingDeadline) && $nextUpcomingDeadline)
                            <p class="font-bold text-xl mb-2 text-black">Next: {{ $nextUpcomingDeadline->subject_name }}</p>
                            <p class="text-lg text-black">{{ $nextUpcomingDeadline->date }} —
                                {{ $nextUpcomingDeadline->time }}
                            </p>
                        @else
                            <p class="font-bold text-xl mb-2 text-black">No deadline for today</p>
                        @endif
                    </div>
                </div>

                <!-- Hidden schedule data for modal -->
                <div id="scheduleItems" class="hidden">
                    @if(isset($allTodaysSchedules) && $allTodaysSchedules->count() > 0)
                        @foreach($allTodaysSchedules as $schedule)
                            <div class="mb-4 border-b border-gray-300 pb-2">
                                <p class="font-bold text-lg text-black">{{ $schedule->subject_name }}</p>
                                <div class="flex justify-between text-sm text-black">
                                    <div>
                                        <p class="font-semibold">Room</p>
                                        <p class="text-xs">{{ $schedule->room }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Time</p>
                                        <p class="text-xs">{{ $schedule->time }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-black text-center py-8">No schedules for today.</p>
                    @endif
                </div>

                <!-- Hidden deadline data for modal -->
                <div id="deadlineItems" class="hidden">
                    @if(isset($allDeadlines))
                        @php
                            $todayDate = now()->setTimezone(config('app.timezone', 'Asia/Jakarta'))->toDateString();
                            $todaysDeadlines = $allDeadlines->filter(function ($deadline) use ($todayDate) {
                                return $deadline->date === $todayDate;
                            });
                            $upcomingDeadlines = $allDeadlines->filter(function ($deadline) use ($todayDate) {
                                return $deadline->date >= $todayDate;
                            })->take(5);
                        @endphp
                        @if($todaysDeadlines->count() > 0)
                            @foreach($todaysDeadlines as $deadline)
                                <div class="mb-4 border-b border-gray-300 pb-2">
                                    <p class="font-bold text-lg text-black">{{ $deadline->subject_name }}</p>
                                    <div class="flex justify-between text-sm text-black">
                                        <div>
                                            <p class="font-semibold">Date</p>
                                            <p class="text-xs">{{ $deadline->date }}</p>
                                        </div>
                                        <div>
                                            <p class="font-semibold">Time</p>
                                            <p class="text-xs">{{ $deadline->time }}</p>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <p class="text-black text-center py-8">No deadlines for today.</p>
                        @endif
                    @else
                        <p class="text-black text-center py-8">No deadlines available.</p>
                    @endif
                </div>

                <!-- Schedule Modal -->
                <div id="scheduleModal"
                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                    <div class="bg-[#e6e7d9] rounded-3xl p-8 w-11/12 max-w-4xl max-h-[90vh] overflow-y-auto">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold text-black">Today's Schedule</h2>
                            <button onclick="closeScheduleModal()" class="text-black text-2xl">&times;</button>
                        </div>

                        <div id="modalScheduleList">
                            <!-- Schedule items will be loaded here -->
                        </div>
                    </div>
                </div>

                <!-- Deadline Modal -->
                <div id="deadlineModal"
                    class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                    <div class="bg-[#e6e7d9] rounded-3xl p-8 w-11/12 max-w-4xl max-h-[90vh] overflow-y-auto">
                        <div class="flex justify-between items-center mb-6">
                            <h2 class="text-2xl font-bold text-black">Today's Deadline</h2>
                            <button onclick="closeDeadlineModal()" class="text-black text-2xl">&times;</button>
                        </div>

                        <div id="modalDeadlineList">
                            <!-- Deadline items will be loaded here -->
                        </div>
                    </div>
                </div>



                <!-- Menu Buttons -->

                <div class="grid grid-cols-1 md:grid-cols-2 gap-10">
                    <!-- Learning Difficulties -->
                    <button
                        class="flex items-center gap-5 bg-[#1f2f1f] p-7 rounded-3xl text-white shadow-xl justify-center w-full hover:bg-[#2a3a2a] transition-all duration-300 transform hover:scale-102"
                        onclick="window.location.href='{{ route('mahasiswa.learning.difficulties') }}'">
                        <i class="fa-solid fa-cloud text-2xl"></i>
                        <span class="font-bold text-xl">Learning Difficulties</span>
                    </button>

                    <!-- Schedule -->
                    <button
                        class="flex items-center gap-5 bg-[#1f2f1f] p-7 rounded-3xl text-white shadow-xl justify-center w-full hover:bg-[#2a3a2a] transition-all duration-300 transform hover:scale-102"
                        onclick="window.location.href='{{ route('mahasiswa.schedule') }}'">
                        <i class="fa-regular fa-calendar text-2xl"></i>
                        <span class="font-bold text-xl">Schedule</span>
                    </button>

                    <!-- Learning Recommendation -->
                    <button
                        class="flex items-center gap-5 bg-[#1f2f1f] p-7 rounded-3xl text-white shadow-xl justify-center w-full hover:bg-[#2a3a2a] transition-all duration-300 transform hover:scale-102"
                        onclick="window.location.href='{{ route('mahasiswa.learning.recommendation') }}'">
                        <i class="fa-regular fa-lightbulb text-2xl"></i>
                        <span class="font-bold text-xl">Learning Recommendation</span>
                    </button>

                    <!-- Deadline -->
                    <button
                        class="flex items-center gap-5 bg-[#1f2f1f] p-7 rounded-3xl text-white shadow-xl justify-center w-full hover:bg-[#2a3a2a] transition-all duration-300 transform hover:scale-102"
                        onclick="window.location.href='{{ route('mahasiswa.deadline') }}'">
                        <i class="fa-solid fa-clock text-2xl"></i>
                        <span class="font-bold text-xl">Deadline</span>
                    </button>

                    <!-- Learning Development -->
                    <a href="{{ route('mahasiswa.learning.development') }}"
                        class="flex items-center gap-5 bg-[#1f2f1f] p-7 rounded-3xl text-white shadow-xl justify-center w-full hover:bg-[#2a3a2a] transition-all duration-300 transform hover:scale-102 cursor-pointer">
                        <i class="fa-solid fa-chart-column text-2xl"></i>
                        <span class="font-bold text-xl">Learning Development</span>
                    </a>

                    <!-- Exercise -->
                    <button
                        class="flex items-center gap-5 bg-[#1f2f1f] p-7 rounded-3xl text-white shadow-xl justify-center w-full hover:bg-[#2a3a2a] transition-all duration-300 transform hover:scale-102"
                        onclick="window.location.href='{{ route('mahasiswa.exercise') }}'">
                        <i class="fa-solid fa-list-check text-2xl"></i>
                        <span class="font-bold text-xl">Exercise</span>
                    </button>
                </div>
            </div>
        </main>

    </div>

    <script>
        // Add event listeners after DOM is loaded
        document.addEventListener('DOMContentLoaded', function () {
            // Add click event to bell icon
            document.getElementById('bellIcon').addEventListener('click', function (e) {
                e.stopPropagation();
                toggleNotificationPopup();
                loadDosenRequests();
            });

            // Add click event to gear icon
            document.getElementById('gearIcon').addEventListener('click', function (e) {
                e.stopPropagation();
                toggleProfilePopup();
            });
        });

        // Schedule Modal Functions
        function openScheduleModal() {
            const modal = document.getElementById('scheduleModal');
            modal.classList.remove('hidden');
            loadScheduleData();
        }

        function closeScheduleModal() {
            const modal = document.getElementById('scheduleModal');
            modal.classList.add('hidden');
        }

        function loadScheduleData() {
            // In a real implementation, this would fetch data from the server
            // For now, we'll use the data rendered in the HTML
            const scheduleList = document.getElementById('modalScheduleList');

            // Clear existing content
            scheduleList.innerHTML = '';

            // Add schedule items from pre-rendered HTML
            const scheduleItems = document.getElementById('scheduleItems');
            if (scheduleItems) {
                scheduleList.innerHTML = scheduleItems.innerHTML;
            } else {
                scheduleList.innerHTML = '<p class="text-black text-center py-8">No schedules available.</p>';
            }
        }

        // Deadline Modal Functions
        function openDeadlineModal() {
            const modal = document.getElementById('deadlineModal');
            modal.classList.remove('hidden');
            loadDeadlineData();
        }


        function closeDeadlineModal() {
            const modal = document.getElementById('deadlineModal');
            modal.classList.add('hidden');
        }

        function loadDeadlineData() {
            // In a real implementation, this would fetch data from the server
            // For now, we'll use the data rendered in the HTML
            const deadlineList = document.getElementById('modalDeadlineList');

            // Clear existing content
            deadlineList.innerHTML = '';

            // Add deadline items from pre-rendered HTML
            const deadlineItems = document.getElementById('deadlineItems');
            if (deadlineItems) {
                deadlineList.innerHTML = deadlineItems.innerHTML;
            } else {
                deadlineList.innerHTML = '<p class="text-black text-center py-8">No deadlines available.</p>';
            }
        }

        // Close modal when clicking outside
        window.onclick = function (event) {
            const scheduleModal = document.getElementById('scheduleModal');
            const deadlineModal = document.getElementById('deadlineModal');

            if (event.target == scheduleModal) {
                closeScheduleModal();
            } else if (event.target == deadlineModal) {
                closeDeadlineModal();
            }
        }

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

        // Load dosen requests
        function loadDosenRequests() {
            fetch('{{ route('mahasiswa.dosen.requests') }}')
                .then(response => response.json())
                .then(requests => {
                    const contentDiv = document.getElementById('notificationContent');

                    if (requests.length > 0) {
                        let html = '';
                        requests.forEach(request => {
                            html += `
                                <div class="request-notification">
                                    <div>
                                        <strong>${request.dosen.user.name}</strong> wants to connect with you
                                    </div>
                                    <div class="request-actions">
                                        <button class="btn-accept" onclick="acceptRequest(${request.id})">Accept</button>
                                        <button class="btn-reject" onclick="rejectRequest(${request.id})">Reject</button>
                                    </div>
                                </div>
                            `;
                        });
                        contentDiv.innerHTML = html;
                    } else {
                        contentDiv.innerHTML = '<p class="text-gray-400">No pending requests</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading requests:', error);
                });
        }

        // Accept dosen request
        function acceptRequest(id) {
            fetch(`/mahasiswa/dosen-requests/${id}/accept`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showFeedbackModal('success', 'Success', 'Request accepted successfully!');
                        loadDosenRequests();
                    } else {
                        showFeedbackModal('error', 'Failed', 'Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error accepting request:', error);
                    showFeedbackModal('error', 'Error', 'An error occurred while accepting the request.');
                });
        }

        // Reject dosen request
        function rejectRequest(id) {
            fetch(`/mahasiswa/dosen-requests/${id}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showFeedbackModal('success', 'Success', 'Request rejected successfully!');
                        loadDosenRequests();
                    } else {
                        showFeedbackModal('error', 'Failed', 'Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error rejecting request:', error);
                    showFeedbackModal('error', 'Error', 'An error occurred while rejecting the request.');
                });
        }

        // Timer Variables
        let autoCloseTimer;
        let countdownInterval;

        // Function to show Feedback Modal
        function showFeedbackModal(type, title, message) {
            const modal = document.getElementById('feedbackModal');
            const iconContainer = document.getElementById('modalIconContainer');
            const icon = document.getElementById('modalIcon');
            const modalTitle = document.getElementById('modalTitle');
            const modalMessage = document.getElementById('modalMessage');
            const modalButton = document.getElementById('modalButton');

            // Progress ELements
            const countdownBar = document.getElementById('countdownBar');
            const countdownText = document.getElementById('countdownText');

            // Set Content
            modalTitle.textContent = title;
            modalMessage.textContent = message;

            // Reset Progress Bar State
            countdownBar.style.transition = 'none';
            countdownBar.style.width = '100%';

            // Reset Text
            countdownText.innerText = 'Menutup otomatis dalam 3 detik';

            // Reset Classes
            iconContainer.className = 'w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6';
            icon.className = 'fa-solid text-4xl text-white';
            modalButton.className = 'text-white font-semibold py-3 px-8 rounded-full transition w-full';

            // Shared color variables
            const successGreen = '#327B3D'; // Custom success color
            const errorRed = '#ef4444';

            if (type === 'success') {
                // Success Styling (Green)
                iconContainer.classList.add('bg-[#327B3D]');
                icon.classList.add('fa-check');
                modalButton.classList.add('bg-[#327B3D]', 'hover:bg-[#286331]');
                countdownBar.style.backgroundColor = successGreen;
            } else {
                // Error Styling (Red)
                iconContainer.classList.add('bg-[#ef4444]'); // Red-500
                icon.classList.add('fa-xmark');
                modalButton.classList.add('bg-[#ef4444]', 'hover:bg-[#dc2626]');
                countdownBar.style.backgroundColor = errorRed;
            }

            // Show Modal
            modal.classList.remove('hidden');

            // Force Reflow to ensure transition works
            void countdownBar.offsetWidth;

            // Start Animation
            countdownBar.style.transition = 'width 3s linear';
            countdownBar.style.width = '0%';

            // Start Countdown Text
            let secondsLeft = 3;
            if (countdownInterval) clearInterval(countdownInterval);
            countdownInterval = setInterval(() => {
                secondsLeft--;
                if (secondsLeft >= 0) {
                    countdownText.innerText = `Menutup otomatis dalam ${secondsLeft} detik`;
                }
            }, 1000);

            // Auto Close Timer
            if (autoCloseTimer) clearTimeout(autoCloseTimer);
            autoCloseTimer = setTimeout(() => {
                closeFeedbackModal();
            }, 3000);
        }

        function closeFeedbackModal() {
            const modal = document.getElementById('feedbackModal');

            // Clear Timers
            if (autoCloseTimer) clearTimeout(autoCloseTimer);
            if (countdownInterval) clearInterval(countdownInterval);

            modal.classList.add('hidden');
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
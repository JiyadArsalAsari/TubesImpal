<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
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
        
        .logo-container {
            cursor: pointer;
        }
    </style>
</head>

<body class="bg-[#4b5b3b] text-white font-sans relative overflow-x-hidden">
    <!-- Decorative Line Background -->
    <div class="fixed inset-0 z-0 overflow-hidden">
        <img src="{{ asset('line.png') }}" alt="Decorative Line" class="w-full h-full object-cover opacity-10 scale-150">
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
                <p class="font-semibold">{{ Auth::user()->name }}</p>
                <p class="text-sm text-gray-400">{{ Auth::user()->email }}</p>
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

    <div class="relative z-10 min-h-screen">
        <!-- HEADER -->
        <header class="w-full bg-[#1f2f1f] text-white flex items-center justify-between px-8 py-4">
            <div class="text-2xl font-bold">{{ Auth::user()->name }}</div>
            <div class="flex items-center justify-center absolute left-1/2 transform -translate-x-1/2 logo-container" onclick="window.location.href='{{ route('mahasiswa.dashboard') }}'">
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

        <!-- Greeting -->
        <h2 class="text-center text-3xl font-bold mt-10 mb-12">
            Hello {{ Auth::user()->name }}, Welcome Back To StudyFlow!
        </h2>

        <!-- Cards Section -->
        <div class="flex flex-col md:flex-row justify-center gap-40 mb-16 mx-10">
            <!-- Schedule Card -->
            <div class="bg-[#e6e7d9] text-black rounded-3xl p-10 w-96 shadow-xl flex flex-col items-center text-center cursor-pointer" onclick="openScheduleModal()">
                <div class="flex items-center gap-4 mb-4">
                    <i class="fa-regular fa-calendar text-2xl"></i>
                    <span class="font-bold text-xl">Your Schedule Today</span>
                </div>
                @if($todaysSchedule)
                    <p class="font-bold text-xl mb-2 text-black">{{ $todaysSchedule->subject_name }}</p>
                    <p class="text-lg text-black">{{ $todaysSchedule->room }} — {{ $todaysSchedule->time }}</p>
                @else
                    <p class="font-bold text-xl mb-2 text-black">No schedule for today</p>
                @endif
            </div>

            <!-- Hidden schedule data for modal -->
            <div id="scheduleItems" class="hidden">
                @if(isset($allTodaysSchedules))
                    @if($allTodaysSchedules->count() > 0)
                        @foreach($allTodaysSchedules as $schedule)
                            <div class="bg-white rounded-2xl p-6 mb-4 shadow-md">
                                <h3 class="font-bold text-xl mb-2 text-black">{{ $schedule->subject_name }}</h3>
                                <div class="flex justify-between text-black">
                                    <div>
                                        <p class="font-semibold">Room</p>
                                        <p>{{ $schedule->room }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Time</p>
                                        <p>{{ $schedule->time }}</p>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <p class="text-black text-center py-8">No schedules for today.</p>
                    @endif
                @else
                    <p class="text-black text-center py-8">No schedules available.</p>
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
                    @endphp
                    @if($todaysDeadlines->count() > 0)
                        @foreach($todaysDeadlines as $deadline)
                            <div class="bg-white rounded-2xl p-6 mb-4 shadow-md">
                                <h3 class="font-bold text-xl mb-2 text-black">{{ $deadline->subject_name }}</h3>
                                <div class="flex justify-between text-black">
                                    <div>
                                        <p class="font-semibold">Date</p>
                                        <p>{{ $deadline->date }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Time</p>
                                        <p>{{ $deadline->time }}</p>
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
            <div id="scheduleModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
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
            <div id="deadlineModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
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

            <!-- Deadline Card -->
            <div class="bg-[#e6e7d9] text-black rounded-3xl p-10 w-96 shadow-xl flex flex-col items-center text-center cursor-pointer" onclick="openDeadlineModal()">
                <div class="flex items-center gap-4 mb-4">
                    <i class="fa-regular fa-clock text-2xl"></i>
                    <span class="font-bold text-xl">Your Deadline Today</span>
                </div>
                @if($todaysDeadline)
                    <p class="font-bold text-xl mb-2 text-black">{{ $todaysDeadline->subject_name }}</p>
                    <p class="text-lg text-black">{{ $todaysDeadline->time }}</p>
                @else
                    <p class="font-bold text-xl mb-2 text-black">No deadline for today</p>
                @endif
            </div>
        </div>

        <!-- Menu Buttons -->
        <div class="flex flex-col md:flex-row justify-center gap-40 mb-16 mx-10">
            <!-- Left Column -->
            <div class="flex flex-col gap-6">
                <button class="flex items-center gap-5 bg-[#1f2f1f] p-7 rounded-3xl text-white shadow-xl justify-center w-[392px] hover:bg-[#2a3a2a] transition-all duration-300 whitespace-nowrap" onclick="window.location.href='{{ route('mahasiswa.learning.difficulties') }}'">
                    <i class="fa-solid fa-cloud text-2xl"></i>
                    <span class="font-bold text-xl">Learning Difficulties</span>
                </button>

                <button class="flex items-center gap-5 bg-[#1f2f1f] p-7 rounded-3xl text-white shadow-xl justify-center w-[392px] hover:bg-[#2a3a2a] transition-all duration-300 whitespace-nowrap" onclick="window.location.href='{{ route('mahasiswa.learning.recommendation') }}'">
                    <i class="fa-regular fa-lightbulb text-2xl"></i>
                    <span class="font-bold text-xl">Learning Recommendation</span>
                </button>

                <button class="flex items-center gap-5 bg-[#1f2f1f] p-7 rounded-3xl text-white shadow-xl justify-center w-[392px] hover:bg-[#2a3a2a] transition-all duration-300 whitespace-nowrap">
                    <i class="fa-solid fa-chart-column text-2xl"></i>
                    <span class="font-bold text-xl">Learning Development</span>
                </button>
            </div>

            <!-- Right Column -->
            <div class="flex flex-col gap-6">
                <button class="flex items-center gap-5 bg-[#1f2f1f] p-7 rounded-3xl text-white shadow-xl justify-center w-[392px] hover:bg-[#2a3a2a] transition-all duration-300 whitespace-nowrap" onclick="window.location.href='{{ route('mahasiswa.schedule') }}'">
                    <i class="fa-regular fa-calendar text-2xl"></i>
                    <span class="font-bold text-xl">Schedule</span>
                </button>

                <button class="flex items-center gap-5 bg-[#1f2f1f] p-7 rounded-3xl text-white shadow-xl justify-center w-[392px] hover:bg-[#2a3a2a] transition-all duration-300 whitespace-nowrap" onclick="window.location.href='{{ route('mahasiswa.deadline') }}'">
                    <i class="fa-solid fa-clock text-2xl"></i>
                    <span class="font-bold text-xl">Deadline</span>
                </button>

                <button class="flex items-center gap-5 bg-[#1f2f1f] p-7 rounded-3xl text-white shadow-xl justify-center w-[392px] hover:bg-[#2a3a2a] transition-all duration-300 whitespace-nowrap">
                    <i class="fa-solid fa-list-check text-2xl"></i>
                    <span class="font-bold text-xl">Exercise</span>
                </button>
            </div>
        </div>
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
        window.onclick = function(event) {
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
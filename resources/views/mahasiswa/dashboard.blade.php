@extends('layouts.app')

@push('styles')
    <style>
        /* Custom Scrollbar for Notifications */
        .notification-scroll::-webkit-scrollbar {
            width: 8px;
        }

        .notification-scroll::-webkit-scrollbar-track {
            background: #1f2f1f;
            border-radius: 4px;
        }

        .notification-scroll::-webkit-scrollbar-thumb {
            background: #4b5b3b;
            border-radius: 4px;
        }

        .notification-scroll::-webkit-scrollbar-thumb:hover {
            background: #5c704a;
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
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.8rem;
        }

        .btn-reject {
            background-color: #f56565;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.8rem;
        }
    </style>
@endpush

@section('content')
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

    <div class="flex-grow flex flex-col justify-center w-full gap-16 pb-12 mt-8">
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
    </div>
@endsection

@push('scripts')
    <script>
        // Add event listeners after DOM is loaded
        document.addEventListener('DOMContentLoaded', function () {
            const markAllForm = document.getElementById('markAllReadForm');
            if (markAllForm) {
                markAllForm.addEventListener('submit', async function (e) {
                    e.preventDefault();
                    await markAllNotificationsAsRead();
                });
            }
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
            const scheduleList = document.getElementById('modalScheduleList');
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
            const deadlineList = document.getElementById('modalDeadlineList');
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

            // Force Reflow
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

        // Show popup based on session messages
        @if(session('success'))
            showFeedbackModal('success', 'Success!', "{{ session('success') }}");
        @endif
        @if(session('error'))
            showFeedbackModal('error', 'Error!', "{{ session('error') }}");
        @endif
    </script>
@endpush
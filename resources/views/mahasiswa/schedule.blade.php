@extends('layouts.app')

@section('content')



    <style>
        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .overflow-x-auto.active {
            user-select: none;
        }
    </style>
    <div class="min-h-screen w-full bg-[#44543D] bg-cover bg-center p-6 relative">

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

        <!-- Your Schedule Title -->
        <h2 class="text-center text-white mt-8 text-lg font-semibold">Your Schedule:</h2>

        <!-- Schedule Card List -->
        <div class="flex overflow-x-auto mt-4 gap-4 pb-4 scrollbar-hide">
            @if(isset($schedules) && $schedules->count() > 0)
                @php
                    $previousDay = null;
                @endphp
                @foreach($schedules as $schedule)
                    <!-- Day Separator -->
                    @if($previousDay !== null && $previousDay !== $schedule->day)
                        <div class="border-r-2 border-white h-auto my-2"></div>
                    @endif

                    <!-- Schedule Card -->
                    <div class="bg-[#ECEFD9] w-[300px] flex-shrink-0 rounded-xl px-4 py-6 shadow-md text-black">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="font-semibold">{{ $schedule->subject_name }}</h3>
                            <span class="bg-[#233122] text-white text-xs px-2 py-1 rounded-full">{{ $schedule->day }}</span>
                        </div>

                        <div class="flex justify-between text-sm">
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

                    @php
                        $previousDay = $schedule->day;
                    @endphp
                @endforeach
            @else
                <!-- Empty State Message -->
                <div class="text-center text-white w-full py-8">
                    <p>You haven't added any schedules yet.</p>
                    <p class="mt-2 text-sm">Add your first schedule using the form below!</p>
                </div>
            @endif
        </div>

        <!-- View All Schedule Button -->
        <div class="flex justify-center mt-6">
            <button onclick="openAllScheduleModal()"
                class="px-6 py-2 bg-[#233122] text-white rounded-full hover:bg-[#1b271b] transition">
                View All Your Schedule
            </button>
        </div>

        <!-- Divider -->
        <div class="w-full h-px bg-white opacity-40 my-10"></div>

        <!-- All Schedule Modal -->
        <div id="allScheduleModal"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-[#44543D] rounded-3xl p-8 w-11/12 max-w-4xl max-h-[90vh] overflow-y-auto">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-bold text-white">All Your Schedule</h2>
                    <button onclick="closeAllScheduleModal()" class="text-white text-2xl">&times;</button>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if(isset($schedules) && $schedules->count() > 0)
                        @php
                            $previousDay = null;
                        @endphp
                        @foreach($schedules as $schedule)
                            <!-- Day Separator -->
                            @if($previousDay !== null && $previousDay !== $schedule->day)
                                <div class="col-span-2 border-t-2 border-white my-4"></div>
                            @endif

                            <div class="bg-[#ECEFD9] rounded-2xl p-6 shadow-md text-black relative">
                                <div class="flex justify-between items-start">
                                    <h3 class="font-bold text-xl mb-2">{{ $schedule->subject_name }}</h3>
                                    <span class="bg-[#233122] text-white text-xs px-2 py-1 rounded-full">{{ $schedule->day }}</span>
                                </div>
                                <div class="flex justify-between text-black mt-4">
                                    <div>
                                        <p class="font-semibold">Room</p>
                                        <p>{{ $schedule->room }}</p>
                                    </div>
                                    <div>
                                        <p class="font-semibold">Time</p>
                                        <p>{{ $schedule->time }}</p>
                                    </div>
                                </div>

                                <!-- Delete Button -->
                                <form action="{{ route('mahasiswa.schedule.destroy', $schedule->id) }}" method="POST"
                                    class="absolute top-2 right-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-600 hover:text-red-800"
                                        onclick="return confirm('Are you sure you want to delete this schedule?')">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                            </div>

                            @php
                                $previousDay = $schedule->day;
                            @endphp
                        @endforeach
                    @else
                        <p class="text-white text-center py-8 col-span-2">You haven't added any schedules yet.</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Add New Schedule Section -->
        <h2 class="text-center text-white text-lg font-semibold">Add New Schedule</h2>

        <!-- Validation Errors -->
        @if ($errors->any())
            <div class="flex justify-center mt-4">
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg w-[600px]">
                    <strong>Whoops!</strong> There were some problems with your input.<br><br>
                    <ul class="list-disc list-inside">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif

        <div class="flex justify-center mt-6">
            <div class="bg-[#ECEFD9] w-[600px] rounded-xl p-6 shadow-lg">
                <form action="{{ route('mahasiswa.schedule.store') }}" method="POST">
                    @csrf
                    <!-- Subject Name -->
                    <label class="font-semibold text-black">Subject Name</label>
                    <input type="text" name="subject_name"
                        class="w-full mt-2 p-3 rounded-lg border focus:ring focus:ring-green-300 text-black"
                        placeholder="Enter subject name..." required>

                    <div class="flex gap-6 mt-4">
                        <!-- Day -->
                        <div class="w-1/2">
                            <label class="font-semibold text-black">Day</label>
                            <select name="day" class="w-full p-3 mt-2 rounded-lg border text-black" required>
                                <option value="">Select Day</option>
                                <option>Monday</option>
                                <option>Tuesday</option>
                                <option>Wednesday</option>
                                <option>Thursday</option>
                                <option>Friday</option>
                                <option>Saturday</option>
                            </select>
                        </div>

                        <!-- Time -->
                        <div class="w-1/2">
                            <label class="font-semibold text-black">Time</label>
                            <input type="text" name="time" class="w-full p-3 mt-2 rounded-lg border text-black"
                                placeholder="e.g. 08:00 - 10:00" required>
                        </div>
                    </div>

                    <!-- Room -->
                    <div class="mt-4">
                        <label class="font-semibold text-black">Room</label>
                        <input type="text" name="room" class="w-full mt-2 p-3 rounded-lg border text-black"
                            placeholder="Enter room..." required>
                    </div>

                    <!-- Submit Button -->
                    <div class="flex justify-center mt-6">
                        <button type="submit"
                            class="px-10 py-3 bg-[#233122] text-white rounded-full hover:bg-[#1b271b] transition">
                            Submit
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <script>
        // Enable horizontal scroll with drag
        const scheduleContainer = document.querySelector('.overflow-x-auto');
        let isDown = false;
        let startX;
        let scrollLeft;

        scheduleContainer.addEventListener('mousedown', (e) => {
            isDown = true;
            scheduleContainer.classList.add('active');
            startX = e.pageX - scheduleContainer.offsetLeft;
            scrollLeft = scheduleContainer.scrollLeft;
        });

        scheduleContainer.addEventListener('mouseleave', () => {
            isDown = false;
            scheduleContainer.classList.remove('active');
        });

        scheduleContainer.addEventListener('mouseup', () => {
            isDown = false;
            scheduleContainer.classList.remove('active');
        });

        scheduleContainer.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - scheduleContainer.offsetLeft;
            const walk = (x - startX) * 2;
            scheduleContainer.scrollLeft = scrollLeft - walk;
        });

        // Add cursor style when dragging is possible
        scheduleContainer.style.cursor = 'grab';
        scheduleContainer.addEventListener('mousedown', () => {
            scheduleContainer.style.cursor = 'grabbing';
        });
        scheduleContainer.addEventListener('mouseup', () => {
            scheduleContainer.style.cursor = 'grab';
        });

        // All Schedule Modal Functions
        function openAllScheduleModal() {
            const modal = document.getElementById('allScheduleModal');
            modal.classList.remove('hidden');
        }

        function closeAllScheduleModal() {
            const modal = document.getElementById('allScheduleModal');
            modal.classList.add('hidden');
        }

        // Close modal when clicking outside
        window.onclick = function (event) {
            const allScheduleModal = document.getElementById('allScheduleModal');
            const scheduleModal = document.getElementById('scheduleModal');

            if (event.target == allScheduleModal) {
                closeAllScheduleModal();
            } else if (event.target == scheduleModal) {
                closeScheduleModal();
            }
        }

        // Function to close schedule modal (if needed elsewhere)
        function closeScheduleModal() {
            // This function is kept for compatibility but not used in this view
        }

        // Show popup based on session messages
        window.onload = function() {
            @if(session('success'))
                showFeedbackModal('success', 'Success!', "{{ session('success') }}");
            @endif
            
            @if(session('error'))
                showFeedbackModal('error', 'Error!', "{{ session('error') }}");
            @endif

            @if($errors->any())
                let errorMessages = "";
                @foreach ($errors->all() as $error)
                    errorMessages += "{{ $error }} ";
                @endforeach
                showFeedbackModal('error', 'Submission Failed!', errorMessages);
            @endif
        };
        
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
    </script>
@endsection
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
<div class="min-h-screen w-full bg-[#44543D] bg-cover bg-center p-6">

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
        <button onclick="openAllScheduleModal()" class="px-6 py-2 bg-[#233122] text-white rounded-full hover:bg-[#1b271b] transition">
            View All Your Schedule
        </button>
    </div>

    <!-- Divider -->
    <div class="w-full h-px bg-white opacity-40 my-10"></div>

    <!-- All Schedule Modal -->
    <div id="allScheduleModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
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
                        
                        <div class="bg-[#ECEFD9] rounded-2xl p-6 shadow-md text-black">
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

    <div class="flex justify-center mt-6">
        <div class="bg-[#ECEFD9] w-[600px] rounded-xl p-6 shadow-lg">
            <form action="{{ route('mahasiswa.schedule.store') }}" method="POST">
                @csrf
                <!-- Subject Name -->
                <label class="font-semibold text-black">Subject Name</label>
                <input type="text" name="subject_name" class="w-full mt-2 p-3 rounded-lg border focus:ring focus:ring-green-300 text-black" placeholder="Enter subject name..." required>

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
                        <input type="text" name="time" class="w-full p-3 mt-2 rounded-lg border text-black" placeholder="e.g. 08:00 - 10:00" required>
                    </div>
                </div>

                <!-- Room -->
                <div class="mt-4">
                    <label class="font-semibold text-black">Room</label>
                    <input type="text" name="room" class="w-full mt-2 p-3 rounded-lg border text-black" placeholder="Enter room..." required>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-center mt-6">
                    <button type="submit" class="px-10 py-3 bg-[#233122] text-white rounded-full hover:bg-[#1b271b] transition">
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
        window.onclick = function(event) {
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
    </script>
@endsection
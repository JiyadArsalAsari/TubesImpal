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
    
    /* Success Popup */
    .popup {
        position: fixed;
        top: 80px; /* Height of the header */
        right: 20px;
        padding: 15px 20px;
        border-radius: 5px;
        color: white;
        font-weight: bold;
        z-index: 1000;
        box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        display: none;
    }
    
    .popup.success {
        background-color: #4CAF50;
    }
    
    .popup.error {
        background-color: #f44336;
    }
</style>
<div class="min-h-screen w-full bg-[#44543D] bg-cover bg-center p-6">
    
    <!-- Success Popup -->
    <div id="successPopup" class="popup success">
        @if(session('success'))
            @if(strpos(session('success'), 'added') !== false)
                Your deadline added successfully!
            @else
                Deadline deleted successfully!
            @endif
        @else
            Deadline deleted successfully!
        @endif
    </div>
    
    <!-- Error Popup -->
    <div id="errorPopup" class="popup error">
        @if(session('error'))
            {{ session('error') }}
        @else
            Error deleting deadline!
        @endif
    </div>
    
    <div class="flex justify-between items-center mb-10">
        <h1 class="text-white text-3xl font-bold">Your Deadlines</h1>
    </div>
    
    <!-- Horizontal Scroll Container -->
    <div class="overflow-x-auto scrollbar-hide">
        <div class="flex space-x-6 pb-4" style="min-width: max-content;">
            @if(isset($deadlines) && $deadlines->count() > 0)
                @php
                    $previousDay = null;
                @endphp
                @foreach($deadlines as $deadline)
                    <!-- Day Separator -->
                    @if($previousDay !== null && $previousDay !== $deadline->day)
                        <div class="border-r-2 border-white h-auto my-2"></div>
                    @endif
                    
                    <!-- Deadline Card -->
                    <div class="bg-[#ECEFD9] w-[300px] flex-shrink-0 rounded-xl px-4 py-6 shadow-md text-black">
                        <div class="flex justify-between items-start mb-3">
                            <h3 class="font-semibold">{{ $deadline->subject_name }}</h3>
                            <span class="bg-[#233122] text-white text-xs px-2 py-1 rounded-full">{{ $deadline->day }}</span>
                        </div>

                        <div class="flex justify-between text-sm">
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
                    
                    @php
                        $previousDay = $deadline->day;
                    @endphp
                @endforeach
            @else
                <!-- Empty State Message -->
                <div class="text-center text-white w-full py-8">
                    <p>You haven't added any deadlines yet.</p>
                    <p class="mt-2 text-sm">Add your first deadline using the form below!</p>
                </div>
            @endif
        </div>
    </div>

    <!-- All Deadline Modal -->
    <div class="flex justify-center mt-6">
        <button onclick="openAllDeadlineModal()" class="px-6 py-2 bg-[#233122] text-white rounded-full hover:bg-[#1b271b] transition">
            View All Your Deadlines
        </button>
    </div>

    <!-- Divider -->
    <div class="w-full h-px bg-white opacity-40 my-10"></div>

    <!-- All Deadline Modal -->
    <div id="allDeadlineModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-[#44543D] rounded-3xl p-8 w-11/12 max-w-4xl max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-white">All Your Deadlines</h2>
                <button onclick="closeAllDeadlineModal()" class="text-white text-2xl">&times;</button>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                @if(isset($deadlines) && $deadlines->count() > 0)
                    @php
                        $previousDay = null;
                    @endphp
                    @foreach($deadlines as $deadline)
                        <!-- Day Separator -->
                        @if($previousDay !== null && $previousDay !== $deadline->day)
                            <div class="col-span-2 border-t-2 border-white my-4"></div>
                        @endif
                        
                        <div class="bg-[#ECEFD9] rounded-2xl p-6 shadow-md text-black relative">
                            <div class="flex justify-between items-start">
                                <h3 class="font-bold text-xl mb-2">{{ $deadline->subject_name }}</h3>
                                <span class="bg-[#233122] text-white text-xs px-2 py-1 rounded-full">{{ $deadline->day }}</span>
                            </div>
                            <div class="flex justify-between text-black mt-4">
                                <div>
                                    <p class="font-semibold">Date</p>
                                    <p>{{ $deadline->date }}</p>
                                </div>
                                <div>
                                    <p class="font-semibold">Time</p>
                                    <p>{{ $deadline->time }}</p>
                                </div>
                            </div>
                            
                            <!-- Delete Button -->
                            <form action="{{ route('mahasiswa.deadline.destroy', $deadline->id) }}" method="POST" class="absolute top-2 right-2">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800" onclick="return confirm('Are you sure you want to delete this deadline?')">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                        </div>
                        
                        @php
                            $previousDay = $deadline->day;
                        @endphp
                    @endforeach
                @else
                    <p class="text-white text-center py-8 col-span-2">You haven't added any deadlines yet.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Add New Deadline Section -->
    <h2 class="text-center text-white text-lg font-semibold">Add New Deadline</h2>

    <!-- Validation Errors -->
    @if ($errors->any())
        <div class="flex justify-center mt-4">
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg w-full max-w-2xl">
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
        <div class="bg-[#ECEFD9] rounded-2xl p-8 w-full max-w-2xl">
            <form action="{{ route('mahasiswa.deadline.store') }}" method="POST">
                @csrf
                
                <!-- Subject Name -->
                <div>
                    <label class="font-semibold text-black">Subject Name</label>
                    <input type="text" name="subject_name" class="w-full mt-2 p-3 rounded-lg border text-black" placeholder="Enter subject name..." required>
                </div>

                <!-- Date -->
                <div class="mt-4">
                    <label class="font-semibold text-black">Date</label>
                    <input type="date" name="date" id="deadlineDate" class="w-full p-3 mt-2 rounded-lg border text-black" required>
                    <input type="hidden" name="day" id="deadlineDay">
                </div>

                <div class="flex mt-4 space-x-4">
                    <!-- Time -->
                    <div class="w-1/2">
                        <label class="font-semibold text-black">Time</label>
                        <input type="text" name="time" class="w-full p-3 mt-2 rounded-lg border text-black" placeholder="e.g. 08:00" required>
                    </div>
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
        const deadlineContainer = document.querySelector('.overflow-x-auto');
        let isDown = false;
        let startX;
        let scrollLeft;
        
        deadlineContainer.addEventListener('mousedown', (e) => {
            isDown = true;
            deadlineContainer.classList.add('active');
            startX = e.pageX - deadlineContainer.offsetLeft;
            scrollLeft = deadlineContainer.scrollLeft;
        });
        
        deadlineContainer.addEventListener('mouseleave', () => {
            isDown = false;
            deadlineContainer.classList.remove('active');
        });
        
        deadlineContainer.addEventListener('mouseup', () => {
            isDown = false;
            deadlineContainer.classList.remove('active');
        });
        
        deadlineContainer.addEventListener('mousemove', (e) => {
            if (!isDown) return;
            e.preventDefault();
            const x = e.pageX - deadlineContainer.offsetLeft;
            const walk = (x - startX) * 2;
            deadlineContainer.scrollLeft = scrollLeft - walk;
        });
        
        // Add cursor style when dragging is possible
        deadlineContainer.style.cursor = 'grab';
        deadlineContainer.addEventListener('mousedown', () => {
            deadlineContainer.style.cursor = 'grabbing';
        });
        deadlineContainer.addEventListener('mouseup', () => {
            deadlineContainer.style.cursor = 'grab';
        });
        
        // Auto-populate day based on selected date
        document.getElementById('deadlineDate').addEventListener('change', function() {
            const selectedDate = this.value;
            if (selectedDate) {
                const date = new Date(selectedDate);
                const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
                const dayName = days[date.getDay()];
                document.getElementById('deadlineDay').value = dayName;
            }
        });
        
        // All Deadline Modal Functions
        function openAllDeadlineModal() {
            const modal = document.getElementById('allDeadlineModal');
            modal.classList.remove('hidden');
        }
        
        function closeAllDeadlineModal() {
            const modal = document.getElementById('allDeadlineModal');
            modal.classList.add('hidden');
        }
        
        // Close modal when clicking outside
        window.onclick = function(event) {
            const allDeadlineModal = document.getElementById('allDeadlineModal');
            
            if (event.target == allDeadlineModal) {
                closeAllDeadlineModal();
            }
        }
        
        // Show popup based on session messages
        window.onload = function() {
            @if(session('success'))
                showPopup('successPopup');
            @endif
            
            @if(session('error'))
                showPopup('errorPopup');
            @endif
        };
        
        // Function to show popup
        function showPopup(popupId) {
            const popup = document.getElementById(popupId);
            if (popup) {
                popup.style.display = 'block';
                setTimeout(() => {
                    popup.style.display = 'none';
                }, 3000); // Hide after 3 seconds
            }
        }
    </script>
@endsection
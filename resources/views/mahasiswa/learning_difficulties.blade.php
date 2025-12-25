@extends('layouts.app')

@section('content')
    <div class="min-h-screen bg-[#44533E] relative">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-20 pointer-events-none"
            style="background-image: url('/images/pattern.svg'); background-size: cover;">
        </div>

        <!-- Content -->
        <div class="relative z-10 max-w-6xl mx-auto px-4 py-8">
            <!-- Header -->
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-4xl font-bold text-white">Learning Difficulties</h1>
                <a href="{{ route('mahasiswa.learning.difficulties.create') }}"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-full flex items-center">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Add New Difficulty
                </a>
            </div>

            <!-- Feedback Modal (Success/Error) -->
            <div id="feedbackModal"
                class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-[100] hidden transition-opacity duration-300">
                <div
                    class="bg-[#1a261a] rounded-xl p-8 w-96 text-center shadow-2xl transform scale-100 transition-transform duration-300 border border-[#2d3a2d]">
                    <!-- Icon Circle -->
                    <div id="modalIconContainer"
                        class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6">
                        <i id="modalIcon" class="fa-solid text-4xl"></i>
                    </div>

                    <h2 id="modalTitle" class="text-white text-2xl font-bold mb-3"></h2>
                    <p id="modalMessage" class="text-gray-300 mb-4 leading-relaxed"></p>

                    <!-- Auto-close Progress -->
                    <div class="mb-6 w-full max-w-[200px] mx-auto">
                        <p id="countdownText" class="text-xs text-gray-400 mb-2 font-medium">Menutup otomatis dalam 3 detik
                        </p>
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

            <!-- Learning Difficulties List -->
            @if($learningDifficulties->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($learningDifficulties as $difficulty)
                        <div class="bg-[#1F2B1E] rounded-2xl p-6 shadow-lg border border-[#2D3A2D]">
                            <div class="flex justify-between items-start mb-4">
                                <h3 class="text-lg font-bold text-white">{{ $difficulty->title }}</h3>
                                <span class="text-xs text-gray-400">{{ $difficulty->created_at->format('M d, Y') }}</span>
                            </div>
                            <p class="text-gray-300 mb-4">{{ $difficulty->description }}</p>
                            <div class="flex justify-end">
                                <button class="text-blue-400 hover:text-blue-300 text-sm font-medium">
                                    View Details
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-[#1F2B1E] rounded-2xl p-12 text-center border border-[#2D3A2D]">
                    <div class="flex justify-center mb-6">
                        <div class="bg-gray-800 rounded-full w-16 h-16 flex items-center justify-center">
                            <i class="fa-solid fa-exclamation-circle text-white text-2xl"></i>
                        </div>
                    </div>
                    <h3 class="text-white font-bold text-3xl mb-4">No Learning Difficulties Found</h3>
                    <p class="text-gray-300 text-xl mb-8">You haven't submitted any learning difficulties yet.</p>
                    <a href="{{ route('mahasiswa.learning.difficulties.create') }}"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-full inline-flex items-center transition">
                        <i class="fa-solid fa-plus mr-2"></i>
                        Add Your First Difficulty
                    </a>
                </div>
            @endif
        </div>
    </div>
    <script>
        // Show popup based on session messages
        window.onload = function () {
            @if(session('success'))
                showFeedbackModal('success', 'Success!', "{{ session('success') }}");
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
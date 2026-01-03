@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <button onclick="window.location.href='{{ route('mahasiswa.dashboard') }}'" class="inline-flex items-center gap-2 px-5 py-2.5 bg-[#485A48]/10 hover:bg-[#485A48] text-[#485A48] hover:text-white rounded-full transition-all duration-300 font-semibold group">
            <i class="fa-solid fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
            <span>Back to Dashboard</span>
        </button>
    </div>
    <div class="min-h-screen bg-[#44533E] relative">
        <!-- Background Pattern -->
        <div class="absolute inset-0 opacity-20 pointer-events-none"
            style="background-image: url('/images/pattern.svg'); background-size: cover;">
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

        <!-- Content -->
        <div class="relative z-10 flex flex-col items-center mt-20 px-4">
            <form action="{{ route('mahasiswa.learning.difficulties.store') }}" method="POST" class="w-full">
                @csrf

                <!-- Subject Name -->
                <h2 class="text-white text-3xl font-bold mb-6 text-center">Subject Name:</h2>

                <div class="w-full max-w-2xl relative mb-12">
                    <input type="text" name="subject_name"
                        class="w-full py-4 pl-6 pr-6 rounded-full bg-white shadow-lg outline-none relative z-10 text-gray-800 text-lg font-medium focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Type subject name here..." required />
                </div>

                <!-- Difficulty Question -->
                <h2 class="text-white text-3xl font-bold mb-6 text-center">What Makes This Subject Difficult For You?</h2>

                <div class="w-full max-w-3xl relative mb-12">
                    <textarea name="description"
                        class="w-full h-48 p-6 pr-6 rounded-3xl bg-white shadow-lg resize-none outline-none relative z-10 text-gray-800 text-lg font-medium focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        placeholder="Describe what makes this subject difficult for you..." required></textarea>
                </div>

                <!-- Submit Button -->
                <div class="flex justify-center gap-4">
                    <a href="{{ route('mahasiswa.learning.difficulties') }}"
                        class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-8 rounded-full">
                        Cancel
                    </a>
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-full">
                        Submit Difficulty
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>
        // Show popup based on session messages or validation errors
        window.onload = function () {
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
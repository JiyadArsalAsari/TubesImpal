<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="bg-[#4b5b3b] text-white font-sans relative overflow-x-hidden">
    <!-- Decorative Line Background -->
    <div class="fixed inset-0 z-0 overflow-hidden">
        <img src="{{ asset('line.png') }}" alt="Decorative Line"
            class="w-full h-full object-cover opacity-10 scale-150">
    </div>

    <div class="relative z-10 min-h-screen">
        <!-- HEADER -->
        <header class="w-full bg-[#1f2f1f] text-white flex items-center justify-between px-8 py-4">
            <div class="text-2xl font-bold">{{ Auth::user()->name }}</div>
            <div class="flex items-center justify-center absolute left-1/2 transform -translate-x-1/2 logo-container"
                onclick="window.location.href='{{ route('mahasiswa.dashboard') }}'">
                <img src="{{ asset('logo.png') }}" class="w-20 h-20 filter brightness-0 invert" />
            </div>
            <div class="flex gap-6 text-3xl relative">
                <div class="relative cursor-pointer">
                    <i class="fa-regular fa-bell"></i>
                </div>
                <div class="cursor-pointer">
                    <i class="fa-solid fa-gear"></i>
                </div>
            </div>
        </header>

        <div class="max-w-4xl mx-auto px-6 py-12">
            <div class="bg-[#2f3d2c] rounded-3xl shadow-xl p-8 border border-[#3c4c39]">
                <div class="flex items-center justify-between mb-6">
                    <h1 class="text-2xl font-bold">Assignment Attempt</h1>
                    <a href="{{ route('mahasiswa.exercise') }}" class="text-sm text-gray-300 hover:text-white">
                        <i class="fa-solid fa-arrow-left mr-1"></i> Back to Exercises
                    </a>
                </div>

                <div class="bg-[#395035] rounded-2xl p-6 border border-[#436040] mb-6">
                    <div class="flex items-start gap-3 mb-4">
                        <div class="bg-[#4a6b46] text-white rounded-xl px-3 py-2 text-xs font-semibold uppercase">
                            {{ strtoupper($exercise->type) }}
                        </div>
                        <div>
                            <h2 class="font-semibold text-lg">{{ $exercise->title }}</h2>
                            @if($exercise->deadline)
                                <p class="text-xs text-gray-200 mt-1">
                                    Deadline: {{ $exercise->deadline->format('l, d F Y H:i') }}
                                </p>
                            @endif
                        </div>
                    </div>

                    @if($exercise->description)
                        <div class="mt-4">
                            <h3 class="font-semibold mb-2">Description</h3>
                            <p class="text-gray-200">{{ $exercise->description }}</p>
                        </div>
                    @endif

                    @if($exercise->file_attachment)
                        <div class="mt-6">
                            <h3 class="font-semibold mb-2">Attachment</h3>
                            <a href="{{ route('mahasiswa.assignment.download', $exercise->id) }}"
                                class="inline-flex items-center gap-2 bg-[#202c23] text-white text-sm font-semibold px-4 py-2 rounded-full border border-[#6fbf69] hover:bg-[#26402d] transition">
                                <i class="fa-solid fa-download"></i>
                                Download File
                            </a>
                        </div>
                    @endif

                    @if($exercise->link)
                        <div class="mt-6">
                            <h3 class="font-semibold mb-2">External Link</h3>
                            <a href="{{ $exercise->link }}" target="_blank"
                                class="inline-flex items-center gap-2 bg-[#202c23] text-white text-sm font-semibold px-4 py-2 rounded-full border border-[#6fbf69] hover:bg-[#26402d] transition">
                                <i class="fa-solid fa-external-link-alt"></i>
                                Open Link
                            </a>
                        </div>
                    @endif
                </div>

                <div class="bg-[#395035] rounded-2xl p-6 border border-[#436040]">
                    <h3 class="font-semibold mb-4">Submission</h3>
                    <p class="text-gray-200 mb-4">Please complete this assignment and submit it according to your
                        instructor's guidelines.</p>

                    <form action="{{ route('mahasiswa.assignment.submit', $exercise->id) }}" method="POST"
                        enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm text-gray-200 mb-1">Text Answer</label>
                            <textarea name="text_answer" rows="5" class="w-full rounded-lg text-black p-3"
                                placeholder="Enter your text answer here..."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm text-gray-200 mb-1">File Answer</label>
                            <input type="file" name="file_answer" class="w-full rounded-lg text-black p-3"
                                accept=".pdf,.doc,.docx,.zip,.rar,.jpg,.jpeg,.png">
                            <p class="text-xs text-gray-400 mt-1">Supported formats: PDF, DOC, DOCX, ZIP, RAR, JPG,
                                JPEG, PNG (Max 10MB)</p>
                        </div>

                        <div class="flex gap-3 pt-4">
                            <button type="submit"
                                class="bg-[#1d8f3b] hover:bg-[#167731] text-white font-semibold px-6 py-3 rounded-full shadow-lg transition">
                                Submit Assignment
                            </button>
                        </div>
                    </form>
                </div>
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

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const assignmentForm = document.querySelector('form[action*="assignment/submit"]');

            if (assignmentForm) {
                assignmentForm.addEventListener('submit', function (e) {
                    const textAnswer = document.querySelector('textarea[name="text_answer"]').value.trim();
                    const fileAnswer = document.querySelector('input[name="file_answer"]').files.length;

                    // Check if at least one field is filled
                    if (!textAnswer && fileAnswer === 0) {
                        e.preventDefault();
                        showFeedbackModal('error', 'Submit Failed', 'Harap isi jawaban teks atau unggah file jawaban sebelum mengirim.');
                    }
                });
            }

            // Show popup based on session messages
            @if(session('error'))
                showFeedbackModal('error', 'Error!', "{{ session('error') }}");
            @endif

            @if(session('success'))
                showFeedbackModal('success', 'Success!', "{{ session('success') }}");
            @endif
        });

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
</body>

</html>
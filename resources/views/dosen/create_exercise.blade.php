@extends('layouts.app')

@section('container_class', 'max-w-4xl mx-auto px-6 py-10')

@push('styles')
    <style>
        /* Specific page styles moved from original file */
    </style>
@endpush

@section('content')
    <div class="mb-6">
        <button onclick="window.location.href='{{ route('dosen.dashboard') }}'"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-500/30 hover:bg-gray-500/50 text-white hover:text-gray-200 rounded-full transition-all duration-300 font-semibold group">
            <i class="fa-solid fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
            <span>Back to Dashboard</span>
        </button>
    </div>

    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-gray-200">Buat Assignment untuk</p>
            <h1 class="text-2xl font-bold">{{ $mahasiswa->user->name ?? $mahasiswa->nama ?? 'Mahasiswa' }}</h1>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-100 text-red-800 p-4 rounded-lg">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('dosen.exercise.store', $mahasiswa->id) }}" method="POST" enctype="multipart/form-data"
        class="bg-[#2f3d2c] border border-[#3c4c39] rounded-2xl p-6 shadow-xl space-y-4">
        @csrf
        <input type="hidden" name="type" value="assignment">
        <div>
            <label class="block text-sm text-gray-200 mb-1">Status</label>
            <select name="status" class="w-full rounded-lg bg-[#1f2f1f] text-white border border-[#436040] p-3">
                <option value="published" class="bg-[#1f2f1f] text-white">Published</option>
                <option value="draft" class="bg-[#1f2f1f] text-white">Draft</option>
            </select>
        </div>

        <div>
            <label class="block text-sm text-gray-200 mb-1">Judul</label>
            <input type="text" name="title"
                class="w-full rounded-lg bg-[#1f2f1f] text-white border border-[#436040] p-3 placeholder-gray-400"
                placeholder="Contoh: Quiz 1 - Introduction to UI/UX Design" required>
        </div>

        <div>
            <label class="block text-sm text-gray-200 mb-1">Deskripsi (opsional)</label>
            <textarea name="description" rows="3"
                class="w-full rounded-lg bg-[#1f2f1f] text-white border border-[#436040] p-3 placeholder-gray-400"
                placeholder="Ringkasan soal atau instruksi"></textarea>
        </div>

        <div>
            <label class="block text-sm text-gray-200 mb-1">Deadline (opsional)</label>
            <input type="datetime-local" name="deadline"
                class="w-full rounded-lg bg-[#1f2f1f] text-white border border-[#436040] p-3 placeholder-gray-400">
        </div>

        <div>
            <label class="block text-sm text-gray-200 mb-1">Maksimal Percobaan</label>
            <input type="number" name="max_attempts" min="1" max="10"
                class="w-full rounded-lg bg-[#1f2f1f] text-white border border-[#436040] p-3 placeholder-gray-400"
                placeholder="Misal 3" value="1">
        </div>

        <div>
            <label class="block text-sm text-gray-200 mb-1">File Assignment</label>
            <input type="file" name="file_attachment" id="fileAttachment"
                class="w-full rounded-lg bg-[#1f2f1f] text-white border border-[#436040] p-3 placeholder-gray-400">
            <p class="text-xs text-gray-200 mt-1">Upload file assignment (PDF, DOC, DOCX, ZIP, etc.)</p>
        </div>

        <div class="pt-2">
            <button type="submit"
                class="w-full bg-[#1d8f3b] hover:bg-[#167731] text-white font-semibold py-3 rounded-xl transition">Simpan
                Exercise</button>
        </div>
    </form>

    <!-- Feedback Modal -->
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
@endsection

@push('scripts')
    <script>
        // Form Validation and Feedback Modal Logic
        document.addEventListener('DOMContentLoaded', function () {
            const exerciseForm = document.querySelector('form[action*="dosen/mahasiswa"]'); // Select the form

            if (exerciseForm) {
                exerciseForm.addEventListener('submit', function (e) {
                    const fileInput = document.getElementById('fileAttachment');

                    // Check if file is selected
                    if (!fileInput || !fileInput.files || fileInput.files.length === 0) {
                        e.preventDefault(); // Prevent submission
                        showFeedbackModal('error', 'Validasi Gagal', 'Harap upload file assignment.');
                    }
                });
            }

            // Show popup based on session messages or validation errors from server
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
@endpush
@extends('layouts.app')

@section('container_class', 'w-full py-10')

@push('styles')
    <style>
        /* Custom calendar icon color for date inputs */
        input[type="datetime-local"]::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }
    </style>
@endpush

@section('content')
    <div class="flex justify-center w-full">
        <div class="max-w-4xl w-full px-6">
            <div class="mb-6">
                <button onclick="window.location.href='{{ route('dosen.dashboard') }}'" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-500/30 hover:bg-gray-500/50 text-white hover:text-gray-200 rounded-full transition-all duration-300 font-semibold group">
                    <i class="fa-solid fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
                    <span>Back to Dashboard</span>
                </button>
            </div>

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h1 class="text-2xl font-bold">Buat Quiz Baru</h1>
                </div>
            </div>

            <form id="quizForm" action="{{ route('dosen.quiz.store', $mahasiswaId) }}" method="POST"
                class="bg-[#2f3d2c] border border-[#3c4c39] rounded-2xl p-6 shadow-xl space-y-4">
                @csrf
                <input type="hidden" name="debug_test" value="1">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm text-gray-200 mb-1">Judul</label>
                        <input type="text" name="title"
                            class="w-full rounded-lg bg-[#1f2f1f] text-white border border-[#436040] p-3 placeholder-gray-400"
                            placeholder="Quiz 1 - Topik" required>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-200 mb-1">Deadline (opsional)</label>
                        <input type="datetime-local" name="deadline"
                            class="w-full rounded-lg bg-[#1f2f1f] text-white border border-[#436040] p-3 placeholder-gray-400">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm text-gray-200 mb-1">Durasi (menit, opsional)</label>
                        <input type="number" name="duration_minutes" min="1"
                            class="w-full rounded-lg bg-[#1f2f1f] text-white border border-[#436040] p-3 placeholder-gray-400"
                            placeholder="Misal 20">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-200 mb-1">Jumlah Soal</label>
                        <select name="number_of_questions" id="number-of-questions"
                            class="w-full rounded-lg bg-[#1f2f1f] text-white border border-[#436040] p-3 placeholder-gray-400">
                            <option value="0" selected>0 soal</option>
                            @for ($i = 1; $i <= env('MAX_QUIZ_QUESTIONS', 50); $i++)
                                <option value="{{ $i }}">{{ $i }} soal</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm text-gray-200 mb-1">Maksimal Percobaan</label>
                        <input type="number" name="max_attempts" min="1" max="10"
                            class="w-full rounded-lg bg-[#1f2f1f] text-white border border-[#436040] p-3 placeholder-gray-400"
                            placeholder="Misal 3" value="1">
                    </div>
                    <div>
                        <label class="block text-sm text-gray-200 mb-1">Deskripsi (opsional)</label>
                        <textarea name="description" rows="2"
                            class="w-full rounded-lg bg-[#1f2f1f] text-white border border-[#436040] p-3 placeholder-gray-400"
                            placeholder="Ringkasan quiz"></textarea>
                    </div>
                </div>

                <div class="space-y-4">
                    <h2 class="font-semibold text-lg">Soal Quiz</h2>
                    <p class="text-sm text-gray-200">Gunakan minimal 2 opsi per soal, tandai jawaban benar.</p>

                    @php
                        $maxQuestions = env('MAX_QUIZ_QUESTIONS', 50);
                    @endphp

                    <div id="questions-container">
                        @for ($i = 0; $i < $maxQuestions; $i++)
                            <div class="question-field bg-[#395035] border border-[#436040] rounded-xl p-4 space-y-3 mb-4"
                                style="display: none;">
                                <label class="block text-sm text-gray-200 mb-1">Soal {{ $i + 1 }}</label>
                                <textarea name="questions[{{ $i }}][question]" rows="2"
                                    class="w-full rounded-lg bg-[#1f2f1f] text-white border border-[#436040] p-3 placeholder-gray-400"
                                    placeholder="Tulis pertanyaan"></textarea>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @for ($j = 0; $j < 4; $j++)
                                        <div class="flex items-center gap-2">
                                            <input type="radio" name="questions[{{ $i }}][correct_option]" value="{{ $j }}"
                                                class="w-4 h-4">
                                            <input type="text" name="questions[{{ $i }}][options][{{ $j }}][option_text]"
                                                class="flex-1 rounded-lg bg-[#1f2f1f] text-white border border-[#436040] p-3 placeholder-gray-400"
                                                placeholder="Opsi {{ chr(65 + $j) }}">
                                        </div>
                                    @endfor
                                </div>
                                <p class="text-xs text-gray-200">Pilih satu jawaban benar untuk soal ini.</p>
                            </div>
                        @endfor
                    </div>

                    <p class="text-sm text-gray-200">Maksimal {{ $maxQuestions }} soal.</p>
                </div>

                <div class="pt-2">
                    <button type="submit"
                        class="w-full bg-[#1d8f3b] hover:bg-[#167731] text-white font-semibold py-3 rounded-xl transition">Simpan
                        Quiz</button>
                </div>
            </form>
        </div>
    </div>

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
            if(!modal) return;
            
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
            if(!modal) return;

            // Clear Timers
            if (autoCloseTimer) clearTimeout(autoCloseTimer);
            if (countdownInterval) clearInterval(countdownInterval);

            modal.classList.add('hidden');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const numberOfQuestionsSelect = document.getElementById('number-of-questions');
            const questionFields = document.querySelectorAll('.question-field');

            // Initialize with default value (0 questions)
            updateQuestionVisibility(0);

            if (numberOfQuestionsSelect) {
                numberOfQuestionsSelect.addEventListener('change', function () {
                    const numberOfQuestions = parseInt(this.value);
                    updateQuestionVisibility(numberOfQuestions);
                });
            }

            // Add form submission validation
            const quizForm = document.getElementById('quizForm');
            if (quizForm) {
                quizForm.addEventListener('submit', function (e) {
                    const numberOfQuestions = parseInt(document.getElementById('number-of-questions').value) || 0;
                    let hasError = false;

                    // Loop through visible questions
                    for (let i = 0; i < numberOfQuestions; i++) {
                        const questionTextarea = document.querySelector(`textarea[name="questions[${i}][question]"]`);
                        const questionText = questionTextarea ? questionTextarea.value.trim() : '';

                        // Only validate if question text is not empty
                        if (questionText) {
                            // Check if a correct option is selected
                            const selectedOption = document.querySelector(`input[name="questions[${i}][correct_option]"]:checked`);

                            if (!selectedOption) {
                                e.preventDefault();
                                showFeedbackModal('error', 'Validasi Gagal', `Silakan pilih jawaban benar untuk Soal ${i + 1}.`);
                                hasError = true;
                                break; // Stop loop on first error
                            }

                            // Check if the selected option has text (optional but recommended)
                            const optionIndex = selectedOption.value;
                            const optionInput = document.querySelector(`input[name="questions[${i}][options][${optionIndex}][option_text]"]`);
                            if (!optionInput || !optionInput.value.trim()) {
                                e.preventDefault();
                                showFeedbackModal('error', 'Validasi Gagal', `Opsi jawaban benar untuk Soal ${i + 1} tidak boleh kosong.`);
                                hasError = true;
                                break;
                            }

                            // Check if at least 2 options are filled given the controller warning "Gunakan minimal 2 opsi per soal"
                            let filledOptionsCount = 0;
                            for (let j = 0; j < 4; j++) {
                                const opt = document.querySelector(`input[name="questions[${i}][options][${j}][option_text]"]`);
                                if (opt && opt.value.trim()) {
                                    filledOptionsCount++;
                                }
                            }

                            if (filledOptionsCount < 2) {
                                e.preventDefault();
                                showFeedbackModal('error', 'Validasi Gagal', `Soal ${i + 1} harus memiliki minimal 2 opsi jawaban.`);
                                hasError = true;
                                break;
                            }
                        }
                    }

                    if (!hasError) {
                        console.log('Quiz form validation passed, submitting...');
                    }
                });
            }

            function updateQuestionVisibility(numberOfQuestions) {
                questionFields.forEach((field, index) => {
                    if (index < numberOfQuestions) {
                        field.style.display = 'block';
                    } else {
                        field.style.display = 'none';
                    }
                });
            }
        });
    </script>
@endpush
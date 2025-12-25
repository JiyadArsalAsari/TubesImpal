<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
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

<body class="bg-[#4b5b3b] text-white font-sans min-h-screen relative overflow-x-hidden">
    <!-- Decorative Line Background -->
    <div class="fixed inset-0 z-0 overflow-hidden">
        <img src="{{ asset('line.png') }}" alt="Decorative Line"
            class="w-full h-full object-cover opacity-10 scale-150">
    </div>

    <!-- Popup Overlay -->
    <div class="popup-overlay" id="popupOverlay" onclick="closeAllPopups()"></div>

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
                <p class="font-semibold">{{ Auth::user()->name ?? 'Dosen' }}</p>
                <p class="text-sm text-gray-400">{{ Auth::user()->email ?? 'user@example.com' }}</p>
            </div>
        </div>

        <!-- Profile Menu Items -->
        <div class="profile-item" onclick="window.location.href='{{ route('profile.settings') }}'">
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

    <div class="relative z-10">
        <!-- HEADER -->
        <header class="w-full bg-[#1f2f1f] text-white flex items-center justify-between px-8 py-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('dosen.dashboard') }}" class="text-gray-300 hover:text-white transition-colors">
                    <i class="fa-solid fa-arrow-left text-xl"></i>
                </a>
                <div class="text-2xl font-bold">{{ Auth::user()->name ?? 'Dosen' }}</div>
            </div>
            <div class="flex items-center justify-center absolute left-1/2 transform -translate-x-1/2 logo-container"
                onclick="window.location.href='{{ route('dosen.dashboard') }}'">
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

        <div class="flex justify-center w-full py-10">
            <div class="max-w-4xl w-full px-6">
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

                // Add event listeners for header icons
                const bellIcon = document.getElementById('bellIcon');
                if (bellIcon) {
                    bellIcon.addEventListener('click', function (e) {
                        e.stopPropagation();
                        toggleNotificationPopup();
                    });
                }

                const gearIcon = document.getElementById('gearIcon');
                if (gearIcon) {
                    gearIcon.addEventListener('click', function (e) {
                        e.stopPropagation();
                        toggleProfilePopup();
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
            document.addEventListener('click', function (event) {
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
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Exercise - {{ $mahasiswa->user->name ?? 'Mahasiswa' }}</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
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

        .language-submenu {
            display: none;
            margin-left: 20px;
        }

        .language-submenu.show {
            display: block;
        }

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

<body class="bg-[#4b5b3b] text-white font-sans relative overflow-x-hidden">
    <!-- Decorative Line Background -->
    <div class="fixed inset-0 z-0 overflow-hidden">
        <img src="{{ asset('line.png') }}" alt="Decorative Line"
            class="w-full h-full object-cover opacity-10 scale-150">
    </div>

    <div class="relative z-10 min-h-screen">
        <!-- Header -->
        <header class="w-full bg-[#1f2f1f] text-white flex items-center justify-between px-8 py-4">
            <div class="text-2xl font-bold">{{ Auth::user()->name ?? 'Dosen' }}</div>
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

        <!-- Popups -->
        <div class="popup-overlay" id="popupOverlay" onclick="closeAllPopups()"></div>

        <div class="notification-popup" id="notificationPopup">
            <div class="mb-4">
                <i class="fa-regular fa-bell text-3xl text-gray-400 mb-3"></i>
                <h3 class="font-bold text-xl mb-2">Notifications</h3>
                <p class="text-gray-400">Notifications will be displayed here</p>
            </div>
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

        <div class="profile-popup" id="profilePopup">
            <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-700">
                <div class="bg-gray-700 rounded-full w-12 h-12 flex items-center justify-center">
                    @if(Auth::user() && Auth::user()->profile_picture)
                        <img src="{{ asset('storage/profile_pictures/' . Auth::user()->profile_picture) }}" alt="Profile"
                            class="rounded-full w-12 h-12 object-cover">
                    @else
                        <i class="fa-solid fa-user text-xl"></i>
                    @endif
                </div>
                <div>
                    <p class="font-semibold">{{ Auth::user()->name ?? 'Dosen' }}</p>
                    <p class="text-sm text-gray-400">{{ Auth::user()->email ?? '' }}</p>
                </div>
            </div>
            <div class="profile-item" onclick="window.location.href='{{ route('profile.settings') }}'">
                <div class="flex items-center gap-3">
                    <i class="fa-solid fa-user-gear"></i>
                    <span>Profile Settings</span>
                </div>
            </div>
            <div class="profile-divider"></div>
            <div class="profile-item" onclick="toggleLanguageMenu()">
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="fa-solid fa-language"></i>
                        <span>Language</span>
                    </div>
                    <i class="fa-solid fa-chevron-right text-gray-400"></i>
                </div>
            </div>
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
            <div class="profile-item" onclick="window.location.href='{{ route('logout') }}'">
                <div class="flex items-center gap-3 text-red-400">
                    <i class="fa-solid fa-right-from-bracket"></i>
                    <span>Logout</span>
                </div>
            </div>
        </div>

        <main class="max-w-4xl mx-auto px-6 py-10">
            <div class="mb-6">
                <button onclick="window.location.href='{{ route('dosen.dashboard') }}'" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-500/30 hover:bg-gray-500/50 text-white hover:text-gray-200 rounded-full transition-all duration-300 font-semibold group">
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

            <form action="{{ route('dosen.exercise.store', $mahasiswa->id) }}" method="POST"
                enctype="multipart/form-data"
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
        </main>
    </div>

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
        function toggleProfilePopup() {
            const popup = document.getElementById('profilePopup');
            const overlay = document.getElementById('popupOverlay');

            if (popup.classList.contains('show')) {
                popup.classList.remove('show');
                overlay.classList.remove('show');
            } else {
                document.getElementById('notificationPopup').classList.remove('show');
                popup.classList.add('show');
                overlay.classList.add('show');
            }
        }

        function toggleNotificationPopup() {
            const popup = document.getElementById('notificationPopup');
            const overlay = document.getElementById('popupOverlay');

            if (popup.classList.contains('show')) {
                popup.classList.remove('show');
                overlay.classList.remove('show');
            } else {
                document.getElementById('profilePopup').classList.remove('show');
                popup.classList.add('show');
                overlay.classList.add('show');
            }
        }

        function closeAllPopups() {
            document.getElementById('profilePopup').classList.remove('show');
            document.getElementById('notificationPopup').classList.remove('show');
            document.getElementById('popupOverlay').classList.remove('show');
        }

        function toggleLanguageMenu() {
            const menu = document.getElementById('languageMenu');
            menu.classList.toggle('show');
        }

        document.addEventListener('DOMContentLoaded', function () {
            const gearIcon = document.getElementById('gearIcon');
            if (gearIcon) {
                gearIcon.addEventListener('click', function (e) {
                    e.stopPropagation();
                    toggleProfilePopup();
                });
            }

            const bellIcon = document.getElementById('bellIcon');
            if (bellIcon) {
                bellIcon.addEventListener('click', function (e) {
                    e.stopPropagation();
                    toggleNotificationPopup();
                });
            }
        });
    </script>
</body>

</html>
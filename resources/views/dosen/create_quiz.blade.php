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
    </style>
</head>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const numberOfQuestionsSelect = document.getElementById('number-of-questions');
        const questionFields = document.querySelectorAll('.question-field');
        
        // Initialize with default value (3 questions)
        updateQuestionVisibility(3);
        
        numberOfQuestionsSelect.addEventListener('change', function() {
            const numberOfQuestions = parseInt(this.value);
            updateQuestionVisibility(numberOfQuestions);
        });
        
        // Add event listeners for header icons
        document.getElementById('bellIcon').addEventListener('click', function(e) {
            e.stopPropagation();
            toggleNotificationPopup();
        });
        
        document.getElementById('gearIcon').addEventListener('click', function(e) {
            e.stopPropagation();
            toggleProfilePopup();
        });
        
        // Add form submission logging
        const quizForm = document.getElementById('quizForm');
        if (quizForm) {
            quizForm.addEventListener('submit', function(e) {
                console.log('Quiz form is being submitted');
                // You can uncomment the next line to prevent actual submission for testing
                // e.preventDefault();
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
    document.addEventListener('click', function(event) {
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
<body class="bg-[#4b5b3b] text-white font-sans min-h-screen relative overflow-x-hidden">
    <!-- Decorative Line Background -->
    <div class="fixed inset-0 z-0 overflow-hidden">
        <img src="{{ asset('line.png') }}" alt="Decorative Line" class="w-full h-full object-cover opacity-10 scale-150">
    </div>
    
    <!-- Popup Overlay -->
    <div class="popup-overlay" id="popupOverlay" onclick="closeAllPopups()"></div>
    
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
        <div class="profile-item">
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
        <div class="text-2xl font-bold">{{ Auth::user()->name ?? 'Dosen' }}</div>
        <div class="flex items-center justify-center absolute left-1/2 transform -translate-x-1/2" onclick="window.location.href='{{ route('dosen.dashboard') }}'">
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
        <div class="max-w-4xl w-full px-20">
            <div class="flex items-center justify-between mb-6">
         
        <form id="quizForm" action="{{ route('dosen.quiz.store', $mahasiswaId) }}" method="POST" class="bg-[#2f3d2c] border border-[#3c4c39] rounded-2xl p-6 shadow-xl space-y-4">
            @csrf
            <input type="hidden" name="debug_test" value="1">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm text-gray-200 mb-1">Judul</label>
                    <input type="text" name="title" class="w-full rounded-lg text-black p-3" placeholder="Quiz 1 - Topik" required>
                </div>
                <div>
                    <label class="block text-sm text-gray-200 mb-1">Deadline (opsional)</label>
                    <input type="datetime-local" name="deadline" class="w-full rounded-lg text-black p-3">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm text-gray-200 mb-1">Durasi (menit, opsional)</label>
                    <input type="number" name="duration_minutes" min="1" class="w-full rounded-lg text-black p-3" placeholder="Misal 20">
                </div>
                <div>
                    <label class="block text-sm text-gray-200 mb-1">Jumlah Soal</label>
                    <select name="number_of_questions" id="number-of-questions" class="w-full rounded-lg text-black p-3">
                        @for ($i = 1; $i <= env('MAX_QUIZ_QUESTIONS', 50); $i++)
                            <option value="{{ $i }}">{{ $i }} soal</option>
                        @endfor
                    </select>
                </div>
                <div>
                    <label class="block text-sm text-gray-200 mb-1">Deskripsi (opsional)</label>
                    <textarea name="description" rows="2" class="w-full rounded-lg text-black p-3" placeholder="Ringkasan quiz"></textarea>
                </div>
            </div>

            <div class="space-y-4">
                <h2 class="font-semibold text-lg">Soal (Minimal 1)</h2>
                <p class="text-sm text-gray-200">Gunakan minimal 2 opsi per soal, tandai jawaban benar.</p>
                
                @php
                    $maxQuestions = env('MAX_QUIZ_QUESTIONS', 50);
                @endphp
                
                <div id="questions-container">
                    @for ($i = 0; $i < $maxQuestions; $i++)
                        <div class="question-field bg-[#395035] border border-[#436040] rounded-xl p-4 space-y-3 mb-4" style="display: {{ $i < 3 ? 'block' : 'none' }};">
                            <label class="block text-sm text-gray-200 mb-1">Soal {{ $i+1 }}</label>
                            <textarea name="questions[{{ $i }}][question]" rows="2" class="w-full rounded-lg text-black p-3" placeholder="Tulis pertanyaan"></textarea>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                @for ($j = 0; $j < 4; $j++)
                                    <div class="flex items-center gap-2">
                                        <input type="radio" name="questions[{{ $i }}][correct_option]" value="{{ $j }}" class="w-4 h-4">
                                        <input type="text" name="questions[{{ $i }}][options][{{ $j }}][option_text]" class="flex-1 rounded-lg text-black p-3" placeholder="Opsi {{ chr(65+$j) }}">
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
                <button type="submit" class="w-full bg-[#1d8f3b] hover:bg-[#167731] text-white font-semibold py-3 rounded-xl transition">Simpan Quiz</button>
            </div>
        </form>
        </div>
    </div>
</body>
</html>


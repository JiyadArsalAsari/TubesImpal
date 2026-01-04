@extends('layouts.app')

@section('container_class', 'w-full')

@push('styles')
    <style>
        /* Specific page styles moved from original file */

        /* Profile Card Styles */
        .profile-card {
            background-color: #e6e7d9;
            border-radius: 12px;
            padding: 24px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            transition: all 0.3s ease;
            cursor: default;
            color: #1e293b;
            border: 1px solid #d1d5db;
        }

        .profile-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
            border-color: #9ca3af;
        }

        /* Button Styles */
        .btn-action {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.2s ease;
            width: 100%;
        }

        .btn-action:active {
            transform: scale(0.98);
        }

        .btn-primary-action {
            background-color: #15803d;
            /* Green-700 */
            color: white;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
        }

        .btn-primary-action:hover {
            background-color: #166534;
            /* Green-800 */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .btn-secondary-action {
            background-color: white;
            color: #334155;
            /* Slate-700 */
            border: 1px solid #cbd5e1;
        }

        .btn-secondary-action:hover {
            background-color: #f1f5f9;
            /* Slate-100 */
            border-color: #94a3b8;
            color: #0f172a;
        }

        /* Dark Green Secondary Option */
        .btn-secondary-dark {
            background-color: #334155;
            /* Slate-700 */
            color: white;
        }

        .btn-secondary-dark:hover {
            background-color: #1e293b;
            /* Slate-800 */
        }

        .btn-danger-action {
            background-color: #fee2e2;
            /* Red-100 */
            color: #991b1b;
            /* Red-800 */
            border: 1px solid #fecaca;
            /* Red-200 */
            font-weight: 700;
        }

        .btn-danger-action:hover {
            background-color: #ef4444;
            /* Red-500 */
            border-color: #ef4444;
            color: white;
            box-shadow: 0 4px 6px -1px rgba(239, 68, 68, 0.3);
        }

        /* Search Bar Styles */
        .search-bar {
            transition: all 0.3s ease;
        }

        .search-bar:focus {
            box-shadow: 0 0 0 3px rgba(72, 187, 120, 0.3);
        }

        /* Add Student Button */
        .add-student-btn {
            position: fixed;
            bottom: 30px;
            right: 30px;
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background-color: #48bb78;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 100;
        }

        .add-student-btn:hover {
            background-color: #38a169;
            transform: scale(1.05);
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #1f2f1f;
            border-radius: 10px;
            padding: 30px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
            color: white;
        }

        .close-modal {
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
            color: #ccc;
        }

        .close-modal:hover {
            color: white;
        }

        .form-input {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #436040;
            margin-bottom: 15px;
            background-color: #2f3d2c;
            color: white;
        }

        .form-input::placeholder {
            color: #aaa;
        }

        .btn-primary {
            background-color: #48bb78;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }

        .btn-primary:hover {
            background-color: #38a169;
        }

        .status-pending {
            background-color: #f6e05e;
            color: #1a202c;
        }

        .status-accepted {
            background-color: #48bb78;
            color: white;
        }

        .status-rejected {
            background-color: #f56565;
            color: white;
        }

        /* Autocomplete dropdown */
        .autocomplete-items {
            position: absolute;
            border: 1px solid #436040;
            border-bottom: none;
            border-top: none;
            z-index: 99;
            top: 100%;
            left: 0;
            right: 0;
            border-radius: 0 0 8px 8px;
            max-height: 200px;
            overflow-y: auto;
        }

        .autocomplete-items div {
            padding: 10px;
            cursor: pointer;
            background-color: #2f3d2c;
            border-bottom: 1px solid #436040;
            color: white;
        }

        .autocomplete-items div:hover {
            background-color: #395035;
        }

        .autocomplete-active {
            background-color: #48bb78 !important;
            color: #ffffff;
        }
    </style>
@endpush

@section('content')
    <!-- Greeting -->
    <h2 class="text-center text-3xl font-bold mt-10 mb-8">
        Halo {{ Auth::user()->username }}, selamat datang di StudyFlow!
    </h2>

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

    <!-- Search Bar -->
    <div class="max-w-3xl mx-auto px-6 mb-10 relative">
        <div class="relative">
            <i class="fa-solid fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
            <input type="text" id="searchInput" placeholder="Search..."
                class="w-full pl-12 pr-4 py-4 rounded-full bg-[#1f2f1f] text-white border border-[#436040] placeholder-gray-400 focus:outline-none focus:border-[#48bb78] search-bar text-lg shadow-lg"
                onkeyup="searchProfiles()">
        </div>
        <div id="autocomplete-list" class="autocomplete-items hidden"></div>
    </div>

    <!-- Profile Cards Section -->
    <div class="max-w-6xl mx-auto px-6 mb-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="profileCardsContainer">
            @foreach($requests as $request)
                <div class="profile-card">
                    <div class="flex items-start justify-between mb-6 pb-4 border-b border-gray-300">
                        <div class="flex items-center gap-4">
                            <div
                                class="bg-[#395035] rounded-full w-14 h-14 flex items-center justify-center border border-[#436040] overflow-hidden">
                                @if($request->mahasiswa && $request->mahasiswa->user && $request->mahasiswa->user->profile_picture)
                                    <img src="{{ asset('storage/profile_pictures/' . $request->mahasiswa->user->profile_picture) }}"
                                        alt="Profile" class="w-full h-full object-cover">
                                @else
                                    <i class="fa-solid fa-graduation-cap text-white text-2xl"></i>
                                @endif
                            </div>
                            <div>
                                <h3 class="font-bold text-lg text-black">{{ $request->mahasiswa_name }}</h3>
                                <p class="text-sm text-gray-700">Mahasiswa S1 Informatika</p>
                            </div>
                        </div>
                        <span class="inline-block px-3 py-1 rounded-full text-xs font-semibold 
                                    @if($request->status == 'pending') bg-yellow-100 text-yellow-800 border border-yellow-300
                                    @elseif($request->status == 'accepted') bg-green-100 text-green-800 border border-green-300
                                    @else bg-red-100 text-red-800 border border-red-300
                                    @endif">
                            {{ ucfirst($request->status) }}
                        </span>
                    </div>

                    <!-- Learning Progress for Accepted Requests -->
                    @if($request->status == 'accepted' && $request->mahasiswa)
                        <div class="space-y-3">
                            <!-- Primary Actions (Create) -->
                            <div class="grid grid-cols-2 gap-3">
                                <button class="btn-action btn-primary-action"
                                    onclick="window.location.href='{{ route('dosen.exercise.create', $request->mahasiswa->id) }}'">
                                    <i class="fa-solid fa-plus"></i>
                                    <span>Assignment</span>
                                </button>
                                <button class="btn-action btn-primary-action"
                                    onclick="window.location.href='{{ route('dosen.quiz.create', $request->mahasiswa->id) }}'">
                                    <i class="fa-solid fa-plus"></i>
                                    <span>Quiz</span>
                                </button>
                            </div>

                            <!-- Secondary Actions (View/Monitor) -->
                            <div class="grid grid-cols-3 gap-2">
                                <button class="btn-action btn-secondary-action"
                                    onclick="viewLearningProgress({{ $request->mahasiswa->id }})">
                                    <i class="fa-solid fa-eye"></i>
                                    <span class="hidden md:inline">Details</span>
                                </button>
                                <button class="btn-action btn-secondary-action"
                                    onclick="window.location.href='{{ route('dosen.mahasiswa.development', $request->mahasiswa->id) }}'">
                                    <i class="fa-solid fa-chart-line"></i>
                                    <span class="hidden md:inline">Dev</span>
                                </button>
                                <button class="btn-action btn-secondary-action"
                                    onclick="window.location.href='{{ route('dosen.mahasiswa.exercises', $request->mahasiswa->id) }}'">
                                    <i class="fa-solid fa-list-check"></i>
                                    <span class="hidden md:inline">List</span>
                                </button>
                            </div>

                            <!-- Destructive Action -->
                            <div class="pt-2 mt-2 border-t border-gray-300">
                                <button type="button"
                                    onclick="openRemoveModal('{{ route('dosen.mahasiswa.remove', $request->id) }}')"
                                    class="btn-action btn-danger-action">
                                    <i class="fa-solid fa-user-minus"></i>
                                    <span>Remove Student</span>
                                </button>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>

    <!-- Remove Student Confirmation Modal -->
    <div id="removeStudentModal"
        class="fixed inset-0 bg-black bg-opacity-70 flex items-center justify-center z-[100] hidden transition-opacity duration-300">
        <div
            class="bg-[#1a261a] rounded-xl p-8 w-96 text-center shadow-2xl transform scale-100 transition-transform duration-300 border border-[#2d3a2d]">
            <!-- Icon Circle -->
            <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-6 bg-red-600">
                <i class="fa-solid fa-user-minus text-4xl text-white"></i>
            </div>

            <h2 class="text-white text-2xl font-bold mb-3">Remove Student?</h2>
            <p class="text-gray-300 mb-6 leading-relaxed">Apakah anda yakin ingin memutus integrasi dengan mahasiswa
                ini?
                Tindakan ini tidak dapat dibatalkan.</p>

            <div class="flex gap-3 justify-center">
                <button onclick="closeRemoveStudentModal()"
                    class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 px-4 rounded-full transition">
                    Cancel
                </button>
                <button id="confirmRemoveBtn"
                    class="flex-1 bg-red-600 hover:bg-red-700 text-white font-semibold py-3 px-4 rounded-full transition">
                    Yes, Remove
                </button>
            </div>
        </div>
    </div>

    <!-- Cancel Request Confirmation Modal -->
    <div id="cancelRequestModal" class="modal">
        <div class="modal-content text-center">
            <span class="close-modal" onclick="closeCancelModal()">&times;</span>
            <div class="mb-4">
                <i class="fa-solid fa-ban text-red-500 text-5xl"></i>
            </div>
            <h2 class="text-2xl font-bold mb-2">Cancel Request?</h2>
            <p class="text-gray-300 mb-6">Are you sure you want to cancel this integration request?</p>
            <div class="flex gap-4 justify-center">
                <button onclick="closeCancelModal()"
                    class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2 rounded-lg transition-colors">No, Keep
                    It</button>
                <button id="confirmCancelBtn"
                    class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg transition-colors">Yes,
                    Cancel</button>
            </div>
        </div>
    </div>

    <!-- Hidden Action Forms -->
    <form id="resendForm" action="" method="POST" style="display: none;">
        @csrf
    </form>

    <form id="cancelForm" action="" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <!-- Hidden Delete Form (Existing) -->
    <form id="deleteForm" action="" method="POST" style="display: none;">
        @csrf
        @method('DELETE')
    </form>

    <!-- Add Student Button -->
    <div class="add-student-btn" onclick="openAddStudentModal()">
        <i class="fa-solid fa-plus"></i>
    </div>

    <!-- Add Student Modal -->
    <div id="addStudentModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeAddStudentModal()">&times;</span>
            <h2 class="text-2xl font-bold mb-6">Add New Student</h2>
            <form id="addStudentForm">
                <div class="mb-4">
                    <label class="block text-gray-300 mb-2" for="studentName">Student Name</label>
                    <input type="text" id="studentName" class="form-input" placeholder="Enter student name" required>
                </div>
                <div class="mb-6">
                    <label class="block text-gray-300 mb-2" for="studentEmail">Student Email</label>
                    <input type="email" id="studentEmail" class="form-input" placeholder="Enter student email" required>
                </div>
                <button type="submit" class="btn-primary">Send Request</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Show popup based on session messages
        window.onload = function () {
            @if(session('success'))
                showFeedbackModal('success', 'Success!', "{{ session('success') }}");
            @endif
            @if(session('error'))
                showFeedbackModal('error', 'Error!', "{{ session('error') }}");
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

        // View profile details
        function viewProfileDetails(name) {
            alert(`Viewing details for ${name}`);
            // In a real implementation, this would redirect to a profile detail page
        }

        // View learning progress details
        function viewLearningProgress(mahasiswaId) {
            // Redirect to the learning progress page
            window.location.href = `/dosen/mahasiswa/${mahasiswaId}/progress`;
        }

        // Open add student modal
        function openAddStudentModal() {
            document.getElementById('addStudentModal').style.display = 'flex';
        }

        // Close add student modal
        function closeAddStudentModal() {
            document.getElementById('addStudentModal').style.display = 'none';
        }

        // Open remove student modal
        function openRemoveModal(url) {
            document.getElementById('deleteForm').action = url;
            document.getElementById('removeStudentModal').classList.remove('hidden');
        }

        // Close remove student modal
        function closeRemoveStudentModal() {
            document.getElementById('removeStudentModal').classList.add('hidden');
        }

        // Handle confirm remove
        document.getElementById('confirmRemoveBtn').addEventListener('click', function () {
            document.getElementById('deleteForm').submit();
        });

        // Handle form submission
        document.getElementById('addStudentForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const name = document.getElementById('studentName').value;
            const email = document.getElementById('studentEmail').value;

            // Send request to server
            fetch('{{ route('dosen.request.add.mahasiswa') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    nama: name,
                    email: email
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Close form modal first
                        closeAddStudentModal();
                        document.getElementById('addStudentForm').reset();

                        // Show success feedback
                        showFeedbackModal('success', 'Success', 'Request sent successfully!');

                        // Reload data after delay to let user see success message
                        setTimeout(() => {
                            location.reload();
                        }, 2000); // 2 second delay
                    } else {
                        // Show error feedback
                        showFeedbackModal('error', 'Failed', 'Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showFeedbackModal('error', 'Error', 'An error occurred while sending the request.');
                });
        });

        // Search profiles
        function searchProfiles() {
            const input = document.getElementById('searchInput');
            const filter = input.value.toLowerCase();
            const container = document.getElementById('profileCardsContainer');
            const cards = container.getElementsByClassName('profile-card');

            // Show all cards first
            for (let i = 0; i < cards.length; i++) {
                cards[i].style.display = "";
            }

            // Filter cards based on search term
            let foundCount = 0;
            for (let i = 0; i < cards.length; i++) {
                const nameElement = cards[i].getElementsByTagName('h3')[0];
                const name = nameElement.textContent || nameElement.innerText;

                if (name.toLowerCase().indexOf(filter) > -1) {
                    cards[i].style.display = "";
                    foundCount++;
                } else {
                    cards[i].style.display = "none";
                }
            }

            // If no cards match, show a message
            if (foundCount === 0 && filter !== '') {
                // You can add a no results message logic here if you want
            }
        }
    </script>
@endpush
@extends('layouts.app')

@push('styles')
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

        /* Dosen Request Notification */
        .request-notification {
            background-color: #2d3748;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .request-actions {
            display: flex;
            gap: 10px;
        }

        .btn-accept {
            background-color: #48bb78;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }

        .btn-reject {
            background-color: #f56565;
            color: white;
            padding: 8px 12px;
            border-radius: 5px;
            cursor: pointer;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
        }

        .modal-content {
            background-color: #2f3d2c;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #3c4c39;
            border-radius: 1.5rem;
            width: 80%;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        }

        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover,
        .close:focus {
            color: white;
            text-decoration: none;
        }
    </style>
@endpush

@section('content')
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
                    <div id="countdownBar" class="bg-[#4ade80] h-1.5 rounded-full transition-all duration-[3000ms] ease-linear w-full"></div>
                </div>
            </div>

            <button onclick="closeFeedbackModal()" id="modalButton"
                class="text-white font-semibold py-3 px-8 rounded-full transition w-full">
                Close
            </button>
        </div>
    </div>

    <!-- Completed Exercises Modal -->
    <div id="completedExercisesModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2 class="text-2xl font-bold mb-4">Completed Exercises</h2>
            <div id="completedExercisesList">
                <!-- Completed exercises will be loaded here -->
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto py-8">
        <div class="mb-6">
            <button onclick="window.location.href='{{ route('mahasiswa.dashboard') }}'" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-500/30 hover:bg-gray-500/50 text-white hover:text-gray-200 rounded-full transition-all duration-300 font-semibold group">
                <i class="fa-solid fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
                <span>Back to Dashboard</span>
            </button>
        </div>

        <div class="bg-[#2f3d2c] rounded-3xl shadow-xl p-8 border border-[#3c4c39]">
            @php
                $grouped = $exercises->groupBy(function ($item) {
                    return optional($item->deadline)?->format('l, d F Y') ?? 'Tanpa Deadline';
                });
            @endphp

            @if($exercises->count() === 0)
                <p class="text-center text-gray-200">Belum ada exercise dari dosen.</p>
            @else
                <div class="space-y-8">
                    @foreach($grouped as $date => $items)
                        <div>
                            <p class="text-sm text-gray-300 uppercase tracking-wide mb-3">{{ $date }}</p>
                            <div class="space-y-4">
                                @foreach($items as $item)
                                    <div
                                        class="bg-[#395035] rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border border-[#436040] shadow-lg">
                                        <div class="flex items-start gap-3 text-white">
                                            <div
                                                class="bg-[#4a6b46] text-white rounded-xl px-3 py-2 text-xs font-semibold uppercase">
                                                {{ strtoupper($item->type) }}
                                            </div>
                                            <div>
                                                <p class="font-semibold">{{ $item->title }}</p>
                                                <p class="text-xs text-gray-200 mt-1">
                                                    Deadline: {{ optional($item->deadline)?->format('H:i') ?? '—' }}
                                                </p>
                                                @if($item->description)
                                                    <p class="text-xs text-gray-300 mt-1">
                                                        {{ \Illuminate\Support\Str::limit($item->description, 120) }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        @if($item->type === 'quiz')
                                            <a href="{{ route('mahasiswa.quiz.attempt', $item->id) }}"
                                                class="bg-[#202c23] text-white text-sm font-semibold px-4 py-2 rounded-full border border-[#6fbf69] hover:bg-[#26402d] transition text-center">
                                                Attempt
                                            </a>
                                        @elseif($item->type === 'assignment')
                                            <a href="{{ route('mahasiswa.assignment.attempt', $item->id) }}"
                                                class="bg-[#202c23] text-white text-sm font-semibold px-4 py-2 rounded-full border border-[#6fbf69] hover:bg-[#26402d] transition text-center">
                                                Attempt
                                            </a>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-6 flex flex-col items-center gap-3">
                    <button class="text-sm font-semibold text-white underline decoration-dotted">Show More</button>
                </div>
            @endif

            @if($hasCompleted)
                <div class="mt-6 flex flex-col items-center gap-3">
                    <button id="showCompletedBtn"
                        class="bg-[#1d8f3b] hover:bg-[#167731] text-white font-semibold px-6 py-3 rounded-full shadow-lg transition">
                        Review Your Completed Quiz and Assignments
                    </button>
                </div>
            @endif
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Load dosen requests
        function loadDosenRequests() {
            fetch('{{ route('mahasiswa.dosen.requests') }}')
                .then(response => response.json())
                .then(requests => {
                    // Update main layout notification content if accessible?
                    // Or keep this here if we want to support the specific functionality
                    // Note: The main layout handles notifications differently. 
                    // We need to check if we need to port this specific logic to the main layout or keep it here.
                    // For now, I'll comment out the DOM update part as the target ID might be different in main layout.
                    /*
                    const contentDiv = document.getElementById('notificationContent'); 
                    if (requests.length > 0) {
                         // ... logic ...
                    } */
                })
                .catch(error => {
                    console.error('Error loading requests:', error);
                });
        }

        // Accept dosen request
        function acceptRequest(id) {
            fetch(`/mahasiswa/dosen-requests/${id}/accept`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showFeedbackModal('success', 'Success', 'Request accepted successfully!');
                        // Reload notifications ?
                    } else {
                        showFeedbackModal('error', 'Failed', 'Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error accepting request:', error);
                    showFeedbackModal('error', 'Error', 'An error occurred while accepting the request.');
                });
        }

        // Reject dosen request
        function rejectRequest(id) {
            fetch(`/mahasiswa/dosen-requests/${id}/reject`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showFeedbackModal('success', 'Success', 'Request rejected successfully!');
                         // Reload notifications ?
                    } else {
                        showFeedbackModal('error', 'Failed', 'Error: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error rejecting request:', error);
                    showFeedbackModal('error', 'Error', 'An error occurred while rejecting the request.');
                });
        }

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

        // Get modal elements
        const modal = document.getElementById("completedExercisesModal");
        const btn = document.getElementById("showCompletedBtn");
        const span = document.getElementsByClassName("close")[0];

        // When the user clicks the button, open the modal 
        if (btn) {
            btn.onclick = function () {
                modal.style.display = "block";
                loadCompletedExercises();
            }
        }

        // When the user clicks on <span> (x), close the modal
        if (span) {
            span.onclick = function () {
                modal.style.display = "none";
            }
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function (event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }

        // Auto-open modal if hash is #reviews
        if (window.location.hash === '#reviews') {
            if (btn) {
                // Simulate click on the button to open modal
                // delay slightly to ensure UI is ready
                setTimeout(() => {
                    btn.click();
                    // Optional: remove hash to clean URL or keep it
                }, 300);
            } else {
                // If button is missing (e.g. no completed items?), maybe we should show message?
                // But btn is conditioned on $hasCompleted in blade.
                // If $hasCompleted is false, btn is null. 
                // We could just try to open modal manually if it exists, but modal usually exists only in structure?
                // The modal HTML is always there (lines 142-150).
                // But if $hasCompleted is false, user shouldn't really see it? 
                // Wait, if notification says grade published, then at least one is completed! So $hasCompleted must be true.
                // So button should exist.
            }
        }

        // Function to load completed exercises via AJAX
        function loadCompletedExercises() {
            fetch('{{ route('mahasiswa.completed.exercises.json') }}')
                .then(response => response.json())
                .then(data => {
                    displayCompletedExercises(data.exercises);
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('completedExercisesList').innerHTML = '<p>Error loading completed exercises.</p>';
                });
        }

        // Function to display completed exercises in the modal
        function displayCompletedExercises(exercises) {
            const container = document.getElementById('completedExercisesList');

            if (exercises.length === 0) {
                container.innerHTML = '<p class="text-center text-gray-200">You haven\'t completed any exercises yet.</p>';
                return;
            }

            // Group exercises by date
            const grouped = {};
            exercises.forEach(exercise => {
                const date = new Date(exercise.updated_at).toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });

                if (!grouped[date]) {
                    grouped[date] = [];
                }
                grouped[date].push(exercise);
            });

            // Generate HTML for grouped exercises
            let html = '';
            for (const [date, items] of Object.entries(grouped)) {
                html += `
                    <div>
                        <p class="text-sm text-gray-300 uppercase tracking-wide mb-3">${date}</p>
                        <div class="space-y-4">
                `;

                items.forEach(item => {
                    const completedTime = new Date(item.updated_at).toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit'
                    });

                    html += `
                        <div class="bg-[#395035] rounded-2xl p-4 border border-[#436040] shadow-lg">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                                <div class="flex items-start gap-3 text-white">
                                    <div class="bg-[#4a6b46] text-white rounded-xl px-3 py-2 text-xs font-semibold uppercase">
                                        ${item.type.toUpperCase()}
                                    </div>
                                    <div>
                                        <p class="font-semibold">${item.title}</p>
                                        <p class="text-xs text-gray-200 mt-1">
                                            Completed: ${completedTime}
                                        </p>
                                        ${item.description ? `<p class="text-xs text-gray-300 mt-1">${item.description.substring(0, 120)}${item.description.length > 120 ? '...' : ''}</p>` : ''}
                                    </div>
                                </div>
                                <div class="flex flex-col items-end gap-2">
                                    <span class="bg-green-600 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                        COMPLETED
                                    </span>
                                    ${item.grade !== undefined && item.grade !== null ? `
                                        <span class="bg-blue-600 text-white text-sm font-bold px-3 py-1 rounded-full">
                                            Score: ${item.grade}/100
                                        </span>
                                    ` : ''}
                                    ${item.attempts_count < item.max_attempts ? `
                                        <a href="${item.type === 'quiz' ? `/mahasiswa/quiz/${item.id}/attempt` : `/mahasiswa/assignment/${item.id}/attempt`}" 
                                           class="bg-[#eda12f] hover:bg-[#d68f28] text-white text-xs font-bold px-3 py-1 rounded-full transition cursor-pointer no-underline text-center">
                                           Attempt Again (${item.attempts_count}/${item.max_attempts})
                                        </a>
                                    ` : ''}
                                </div>
                            </div>
                            ${item.feedback ? `
                                <div class="w-full mt-3 pt-3 border-t border-gray-600/50">
                                    <p class="text-xs text-gray-300 font-semibold mb-1"><i class="fa-solid fa-comment-dots mr-2"></i>Feedback:</p>
                                    <p class="text-sm text-gray-200 italic">"${item.feedback}"</p>
                                </div>
                            ` : ''}
                        </div>
                    `;
                });

                html += `
                        </div>
                    </div>
                `;
            }

            container.innerHTML = html;
        }
    </script>
@endpush
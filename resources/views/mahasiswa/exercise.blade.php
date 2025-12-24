<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
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
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
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
        .profile-popup { display: none; position: absolute; top: 60px; right: 20px; background-color: #1f2f1f; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); z-index: 1000; min-width: 250px; padding: 20px; }
        .profile-popup.show { display: block; }
        .popup-overlay { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0,0,0,0.5); z-index: 999; }
        .popup-overlay.show { display: block; }
        .profile-item { padding: 12px 15px; border-radius: 8px; cursor: pointer; transition: background-color 0.2s; }
        .profile-item:hover { background-color: rgba(255,255,255,0.1); }
        .profile-divider { height: 1px; background-color: rgba(255,255,255,0.1); margin: 10px 0; }
        .language-submenu { display: none; margin-left: 20px; }
        .language-submenu.show { display: block; }
        .notification-badge { position: absolute; top: -5px; right: -5px; background-color: #e74c3c; color: white; border-radius: 50%; width: 20px; height: 20px; font-size: 12px; display: flex; align-items: center; justify-content: center; }
        .notification-popup { display: none; position: absolute; top: 60px; right: 70px; background-color: #1f2f1f; border-radius: 10px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); z-index: 1000; min-width: 300px; max-width: 350px; padding: 30px; text-align: center; }
        .notification-popup.show { display: block; }
        .request-notification { background-color: #2d3748; border-radius: 8px; padding: 15px; margin-bottom: 15px; display: flex; justify-content: space-between; align-items: center; }
        .request-actions { display: flex; gap: 10px; }
        .btn-accept { background-color: #48bb78; color: white; padding: 8px 12px; border-radius: 5px; cursor: pointer; }
        .btn-reject { background-color: #f56565; color: white; padding: 8px 12px; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body class="bg-[#4b5b3b] text-white font-sans relative overflow-x-hidden">
    <!-- Decorative Line Background -->
    <div class="fixed inset-0 z-0 overflow-hidden">
        <img src="{{ asset('line.png') }}" alt="Decorative Line" class="w-full h-full object-cover opacity-10 scale-150">
    </div>

    <div class="relative z-10 min-h-screen">
        <!-- HEADER -->
        <header class="w-full bg-[#1f2f1f] text-white flex items-center justify-between px-8 py-4">
            <div class="text-2xl font-bold">{{ Auth::user()->name }}</div>
            <div class="flex items-center justify-center absolute left-1/2 transform -translate-x-1/2 logo-container" onclick="window.location.href='{{ route('mahasiswa.dashboard') }}'">
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

        <div class="popup-overlay" id="popupOverlay" onclick="closeAllPopups()"></div>
        <div class="notification-popup" id="notificationPopup">
            <div class="mb-4">
                <i class="fa-regular fa-bell text-3xl text-gray-400 mb-3"></i>
                <h3 class="font-bold text-xl mb-2">Notifications</h3>
                <div id="notificationContent">
                    <p class="text-gray-400">Notifications will be displayed here</p>
                </div>
            </div>
        </div>
        <div class="profile-popup" id="profilePopup">
            <div class="flex items-center gap-3 mb-4 pb-4 border-b border-gray-700">
                <div class="bg-gray-700 rounded-full w-12 h-12 flex items-center justify-center">
                    <i class="fa-solid fa-user text-xl"></i>
                </div>
                <div>
                    <p class="font-semibold">{{ Auth::user()->name }}</p>
                    <p class="text-sm text-gray-400">{{ Auth::user()->email }}</p>
                </div>
            </div>
            <div class="profile-item">
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

        <div class="max-w-6xl mx-auto px-6 py-12">
            <div class="bg-[#2f3d2c] rounded-3xl shadow-xl p-8 border border-[#3c4c39]">
                @php
                    $grouped = $exercises->groupBy(function ($item) {
                        return optional($item->deadline)?->format('l, d F Y') ?? 'Tanpa Deadline';
                    });
                @endphp

                @if($exercises->count() === 0)
                    <div class="flex flex-col items-center gap-3">
                        <p class="text-center text-gray-200">Belum ada exercise dari dosen.</p>
                        <button id="showCompletedBtn" class="bg-[#1d8f3b] hover:bg-[#167731] text-white font-semibold px-6 py-3 rounded-full shadow-lg transition">
                            Review Your Completed Quiz and Assignments
                        </button>
                    </div>
                @else
                    <div class="space-y-8">
                        @foreach($grouped as $date => $items)
                            <div>
                                <p class="text-sm text-gray-300 uppercase tracking-wide mb-3">{{ $date }}</p>
                                <div class="space-y-4">
                                    @foreach($items as $item)
                                        <div class="bg-[#395035] rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border border-[#436040] shadow-lg">
                                            <div class="flex items-start gap-3 text-white">
                                                <div class="bg-[#4a6b46] text-white rounded-xl px-3 py-2 text-xs font-semibold uppercase">
                                                    {{ strtoupper($item->type) }}
                                                </div>
                                                <div>
                                                    <p class="font-semibold">{{ $item->title }}</p>
                                                    <p class="text-xs text-gray-200 mt-1">
                                                        Deadline: {{ optional($item->deadline)?->format('H:i') ?? '—' }}
                                                    </p>
                                                    @if($item->description)
                                                        <p class="text-xs text-gray-300 mt-1">{{ \Illuminate\Support\Str::limit($item->description, 120) }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($item->type === 'quiz')
                                                <a href="{{ route('mahasiswa.quiz.attempt', $item->id) }}" class="bg-[#202c23] text-white text-sm font-semibold px-4 py-2 rounded-full border border-[#6fbf69] hover:bg-[#26402d] transition text-center">
                                                    Attempt
                                                </a>
                                            @elseif($item->file_attachment)
                                                <a href="{{ route('mahasiswa.assignment.attempt', $item->id) }}" class="bg-[#202c23] text-white text-sm font-semibold px-4 py-2 rounded-full border border-[#6fbf69] hover:bg-[#26402d] transition text-center">
                                                    Attempt
                                                </a>
                                            @elseif($item->link)
                                                <a href="{{ $item->link }}" target="_blank" class="bg-[#202c23] text-white text-sm font-semibold px-4 py-2 rounded-full border border-[#6fbf69] hover:bg-[#26402d] transition text-center">
                                                    Attempt
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-200">Tidak ada link</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6 flex flex-col items-center gap-3">
                        <button class="text-sm font-semibold text-white underline decoration-dotted">Show More</button>
                        <button id="showCompletedBtn" class="bg-[#1d8f3b] hover:bg-[#167731] text-white font-semibold px-6 py-3 rounded-full shadow-lg transition">
                            Review Your Completed Quiz and Assignments
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <script>
        // Get modal elements
        const modal = document.getElementById("completedExercisesModal");
        const btn = document.getElementById("showCompletedBtn");
        const span = document.getElementsByClassName("close")[0];
        
        // When the user clicks the button, open the modal 
        btn.onclick = function() {
            modal.style.display = "block";
            loadCompletedExercises();
        }
        
        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }
        
        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
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
                    const reviewUrl = item.type === 'quiz' 
                        ? `/mahasiswa/quiz/${item.id}/review` 
                        : `/mahasiswa/assignment/${item.id}/review`;
                    
                    html += `
                        <div class="bg-[#395035] rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border border-[#436040] shadow-lg">
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
                            <div class="flex items-center gap-2">
                                <span class="bg-green-600 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                    COMPLETED
                                </span>
                                <a href="${reviewUrl}" class="bg-[#202c23] text-white text-xs font-semibold px-3 py-1 rounded-full border border-[#6fbf69] hover:bg-[#26402d] transition">
                                    Review
                                </a>
                            </div>
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

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('bellIcon').addEventListener('click', function(e) {
                e.stopPropagation();
                toggleNotificationPopup();
                loadDosenRequests();
            });
            document.getElementById('gearIcon').addEventListener('click', function(e) {
                e.stopPropagation();
                toggleProfilePopup();
            });
        });

        function toggleProfilePopup() {
            document.getElementById('notificationPopup').classList.remove('show');
            const popup = document.getElementById('profilePopup');
            const overlay = document.getElementById('popupOverlay');
            popup.classList.toggle('show');
            overlay.classList.toggle('show');
            if (!popup.classList.contains('show')) {
                document.getElementById('languageMenu').classList.remove('show');
            }
        }

        function toggleNotificationPopup() {
            document.getElementById('profilePopup').classList.remove('show');
            const popup = document.getElementById('notificationPopup');
            const overlay = document.getElementById('popupOverlay');
            popup.classList.toggle('show');
            overlay.classList.toggle('show');
        }

        function loadDosenRequests() {
            fetch('{{ route('mahasiswa.dosen.requests') }}')
                .then(response => response.json())
                .then(requests => {
                    const contentDiv = document.getElementById('notificationContent');
                    if (requests.length > 0) {
                        let html = '';
                        requests.forEach(request => {
                            html += `
                                <div class="request-notification">
                                    <div>
                                        <strong>${request.dosen.user.name}</strong> wants to connect with you
                                    </div>
                                    <div class="request-actions">
                                        <button class="btn-accept" onclick="acceptRequest(${request.id})">Accept</button>
                                        <button class="btn-reject" onclick="rejectRequest(${request.id})">Reject</button>
                                    </div>
                                </div>
                            `;
                        });
                        contentDiv.innerHTML = html;
                    } else {
                        contentDiv.innerHTML = '<p class="text-gray-400">No pending requests</p>';
                    }
                })
                .catch(error => {
                    console.error('Error loading requests:', error);
                });
        }

        function acceptRequest(id) {
            fetch(`/mahasiswa/dosen-requests/${id}/accept`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
            })
            .then(response => response.json())
            .then(data => { if (data.success) { loadDosenRequests(); } else { alert('Error: ' + data.message); } })
            .catch(error => { console.error('Error accepting request:', error); alert('An error occurred while accepting the request.'); });
        }

        function rejectRequest(id) {
            fetch(`/mahasiswa/dosen-requests/${id}/reject`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
            })
            .then(response => response.json())
            .then(data => { if (data.success) { loadDosenRequests(); } else { alert('Error: ' + data.message); } })
            .catch(error => { console.error('Error rejecting request:', error); alert('An error occurred while rejecting the request.'); });
        }

        function closeAllPopups() {
            document.getElementById('profilePopup').classList.remove('show');
            document.getElementById('notificationPopup').classList.remove('show');
            document.getElementById('languageMenu').classList.remove('show');
            document.getElementById('popupOverlay').classList.remove('show');
        }

        function toggleLanguageMenu() {
            const languageMenu = document.getElementById('languageMenu');
            languageMenu.classList.toggle('show');
        }

        document.addEventListener('click', function(event) {
            const profilePopup = document.getElementById('profilePopup');
            const notificationPopup = document.getElementById('notificationPopup');
            const bellIcon = document.getElementById('bellIcon');
            const gearIcon = document.getElementById('gearIcon');
            if (!profilePopup.contains(event.target) && !notificationPopup.contains(event.target) && !bellIcon.contains(event.target) && !gearIcon.contains(event.target) && (profilePopup.classList.contains('show') || notificationPopup.classList.contains('show'))) {
                closeAllPopups();
            }
        });
    </script>
</body>
</html>


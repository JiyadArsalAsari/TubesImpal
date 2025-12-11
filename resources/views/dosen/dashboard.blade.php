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
        
        /* Profile Card Styles */
        .profile-card {
            background-color: #e6e7d9;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            cursor: pointer;
        }
        
        .profile-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
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
        }
        
        .close-modal {
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .form-input {
            width: 100%;
            padding: 12px;
            border-radius: 8px;
            border: 1px solid #ccc;
            margin-bottom: 15px;
            background-color: #e6e7d9;
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
            border: 1px solid #d4d4d4;
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
            background-color: #fff;
            border-bottom: 1px solid #d4d4d4;
        }
        
        .autocomplete-items div:hover {
            background-color: #e9e9e9;
        }
        
        .autocomplete-active {
            background-color: #48bb78 !important;
            color: #ffffff;
        }
    </style>
</head>

<body class="bg-[#4b5b3b] text-white font-sans relative overflow-x-hidden">
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
                <p class="font-semibold">{{ Auth::user()->name }}</p>
                <p class="text-sm text-gray-400">{{ Auth::user()->email }}</p>
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

    <div class="relative z-10 min-h-screen">
        <!-- HEADER -->
        <header class="w-full bg-[#1f2f1f] text-white flex items-center justify-between px-8 py-4">
            <div class="text-2xl font-bold">{{ Auth::user()->name }}</div>
            <div class="flex items-center justify-center absolute left-1/2 transform -translate-x-1/2 logo-container" onclick="window.location.href='{{ route('dosen.dashboard') }}'">
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

        <!-- Greeting -->
        <h2 class="text-center text-3xl font-bold mt-10 mb-8">
            Halo {{ Auth::user()->name }}, selamat datang di StudyFlow!
        </h2>
            
        <!-- Role Information -->
        <div class="max-w-3xl mx-auto px-6 mb-6">
            <div class="bg-blue-100 border-l-4 border-blue-500 text-blue-700 p-4" role="alert">
                <p><strong>Debug Info:</strong> You are accessing the dosen dashboard.</p>
                <p>User role: {{ Auth::user()->role ?? 'Unknown' }}</p>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="max-w-3xl mx-auto px-6 mb-6">
                <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4" role="alert">
                    <p>{{ session('success') }}</p>
                </div>
            </div>
        @endif
        
        @if(session('error'))
            <div class="max-w-3xl mx-auto px-6 mb-6">
                <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4" role="alert">
                    <p>{{ session('error') }}</p>
                </div>
            </div>
        @endif

        <!-- Search Bar -->
        <div class="max-w-3xl mx-auto px-6 mb-10 relative">
            <div class="relative">
                <i class="fa-solid fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
                <input 
                    type="text" 
                    id="searchInput"
                    placeholder="Search..." 
                    class="w-full pl-12 pr-4 py-4 rounded-full bg-white text-gray-700 placeholder-gray-400 focus:outline-none search-bar text-lg"
                    onkeyup="searchProfiles()"
                >
            </div>
            <div id="autocomplete-list" class="autocomplete-items hidden"></div>
        </div>

        <!-- Profile Cards Section -->
        <div class="max-w-6xl mx-auto px-6 mb-16">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8" id="profileCardsContainer">
                @foreach($requests as $request)
                <div class="profile-card">
                    <div class="flex items-center gap-4 mb-4">
                        <div class="bg-[#48bb78] rounded-full w-16 h-16 flex items-center justify-center">
                            <i class="fa-solid fa-graduation-cap text-white text-2xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-xl text-black">{{ $request->mahasiswa_name }}</h3>
                            <p class="text-gray-700">Mahasiswa S1 Informatika</p>
                            <span class="inline-block mt-2 px-3 py-1 rounded-full text-xs font-semibold 
                                @if($request->status == 'pending') status-pending 
                                @elseif($request->status == 'accepted') status-accepted 
                                @else status-rejected 
                                @endif">
                                {{ ucfirst($request->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Learning Progress for Accepted Requests -->
                    @if($request->status == 'accepted' && $request->mahasiswa)
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <h4 class="font-semibold text-gray-800 mb-2">Learning Progress:</h4>
                        
                        <!-- Learning Difficulties Count -->
                        <div class="flex items-center text-sm text-gray-600 mb-1">
                            <i class="fa-solid fa-exclamation-circle mr-2"></i>
                            <span>Difficulties: {{ $request->mahasiswa->learningDifficulties->count() }}</span>
                        </div>
                        
                        <!-- Schedules Count -->
                        <div class="flex items-center text-sm text-gray-600 mb-1">
                            <i class="fa-solid fa-calendar-alt mr-2"></i>
                            <span>Schedules: {{ $request->mahasiswa->schedules->count() }}</span>
                        </div>
                        
                        <!-- Deadlines Count -->
                        <div class="flex items-center text-sm text-gray-600 mb-1">
                            <i class="fa-solid fa-clock mr-2"></i>
                            <span>Deadlines: {{ $request->mahasiswa->deadlines->count() }}</span>
                        </div>
                        
                        <!-- View Details Button -->
                        <button class="mt-3 bg-blue-500 hover:bg-blue-600 text-white text-sm py-1 px-3 rounded" 
                                onclick="viewLearningProgress({{ $request->mahasiswa->id }})">
                            View Details
                        </button>
                        
                        <!-- Remove Button -->
                        <form action="{{ route('dosen.mahasiswa.remove', $request->id) }}" method="POST" class="inline-block ml-2" 
                              onsubmit="return confirm('Are you sure you want to remove this student?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="mt-3 bg-red-500 hover:bg-red-600 text-white text-sm py-1 px-3 rounded">
                                Remove
                            </button>
                        </form>
                    </div>
                    @endif
                </div>
                @endforeach
            </div>
        </div>
    </div>

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

    <script>
        // Add event listeners after DOM is loaded
        document.addEventListener('DOMContentLoaded', function() {
            // Add click event to bell icon
            document.getElementById('bellIcon').addEventListener('click', function(e) {
                e.stopPropagation();
                toggleNotificationPopup();
            });
            
            // Add click event to gear icon
            document.getElementById('gearIcon').addEventListener('click', function(e) {
                e.stopPropagation();
                toggleProfilePopup();
            });
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
        
        // Handle form submission
        document.getElementById('addStudentForm').addEventListener('submit', function(e) {
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
                    alert('Request sent successfully!');
                    closeAddStudentModal();
                    document.getElementById('addStudentForm').reset();
                    // Reload the page to show updated requests
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while sending the request.');
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
                container.innerHTML = `
                    <div class="col-span-full text-center py-12">
                        <i class="fa-solid fa-search text-5xl text-gray-400 mb-4"></i>
                        <h3 class="text-2xl font-bold text-gray-300">No profiles found</h3>
                        <p class="text-gray-400 mt-2">Try a different search term</p>
                    </div>
                `;
            } else if (filter === '') {
                // Reload original content when search is cleared
                location.reload();
            }
        }
    </script>
</body>
</html>
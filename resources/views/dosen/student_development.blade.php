<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
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
        .logo-container { cursor: pointer; }
    </style>
</head>

<body class="bg-[#4b5b3b] text-white font-sans relative overflow-x-hidden">
    <!-- Decorative Line Background -->
    <div class="fixed inset-0 z-0 overflow-hidden">
        <img src="{{ asset('line.png') }}" alt="Decorative Line" class="w-full h-full object-cover opacity-10 scale-150">
    </div>

    <div class="relative z-10 min-h-screen flex flex-col">
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
        <div class="popup-overlay" id="popupOverlay" onclick="closeAllPopups()"></div>
        <div class="notification-popup" id="notificationPopup">
            <div class="mb-4">
                <i class="fa-regular fa-bell text-3xl text-gray-400 mb-3"></i>
                <h3 class="font-bold text-xl mb-2">Notifications</h3>
                <p class="text-gray-400">Notifications will be displayed here</p>
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

        <main class="flex-grow flex flex-col items-center w-full px-4 py-8">
            <div class="max-w-5xl w-full">
                <!-- Back Button -->
                <div class="mb-6">
                    <button onclick="window.location.href='{{ route('dosen.dashboard') }}'" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-500/30 hover:bg-gray-500/50 text-white hover:text-gray-200 rounded-full transition-all duration-300 font-semibold group">
                        <i class="fa-solid fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
                        <span>Back to Dashboard</span>
                    </button>
                </div>

                <!-- Page Title -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold">Student Development</h1>
                    <p class="text-xl text-gray-300 mt-2">{{ $mahasiswa->nama }} ({{ $mahasiswa->user->email }})</p>
                </div>

                @if(session('success'))
                    <div class="bg-green-600 text-white p-4 rounded-lg mb-6">
                        {{ session('success') }}
                    </div>
                @endif
                
                @if(session('error'))
                    <div class="bg-red-600 text-white p-4 rounded-lg mb-6">
                        {{ session('error') }}
                    </div>
                @endif

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                    <!-- Overall Average -->
                    <div class="bg-[#1f2f1f] rounded-3xl p-6 shadow-xl text-white">
                        <div class="flex items-center gap-4 mb-2">
                            <div class="bg-[#4b5b3b] p-3 rounded-full">
                                <i class="fa-solid fa-chart-line text-xl"></i>
                            </div>
                            <span class="text-gray-300">Overall Average</span>
                        </div>
                        <h2 class="text-4xl font-bold">{{ number_format($totalAverage, 1) }}</h2>
                        <p class="text-sm text-gray-400 mt-2">Combined Quiz & Assignment</p>
                    </div>

                    <!-- Trend Indicator -->
                    <div class="bg-[#1f2f1f] rounded-3xl p-6 shadow-xl text-white">
                        <div class="flex items-center gap-4 mb-2">
                            <div class="bg-[#4b5b3b] p-3 rounded-full">
                                <i class="fa-solid fa-arrow-trend-{{ $trend >= 0 ? 'up' : 'down' }} text-xl"></i>
                            </div>
                            <span class="text-gray-300">Performance Trend</span>
                        </div>
                        <div class="flex items-end gap-2">
                            <h2 class="text-4xl font-bold {{ $trend >= 0 ? 'text-green-400' : 'text-red-400' }}">
                                {{ $trend >= 0 ? '+' : '' }}{{ number_format($trend, 1) }}%
                            </h2>
                            <span class="text-sm text-gray-400 mb-2">vs. Average</span>
                        </div>
                        <p class="text-sm text-gray-400 mt-2">Based on last 3 activities</p>
                    </div>

                    <!-- Recent Activity -->
                    <div class="bg-[#1f2f1f] rounded-3xl p-6 shadow-xl text-white">
                        <div class="flex items-center gap-4 mb-2">
                            <div class="bg-[#4b5b3b] p-3 rounded-full">
                                <i class="fa-solid fa-history text-xl"></i>
                            </div>
                            <span class="text-gray-300">Recent Average</span>
                        </div>
                        <h2 class="text-4xl font-bold">{{ number_format($recentAverage, 1) }}</h2>
                        <p class="text-sm text-gray-400 mt-2">Last 3 activities</p>
                    </div>
                </div>

                <!-- Main Chart Section -->
                <div class="bg-[#e6e7d9] text-black rounded-3xl p-8 shadow-xl mb-8">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold mb-2">Academic Performance History</h2>
                            <p class="text-gray-600">Track progress over time across Quizzes and Assignments.</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-4 mt-4 md:mt-0 items-center">
                            <!-- Filter Dropdown -->
                            <select id="monthFilter" class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full sm:w-auto p-2.5 shadow-sm">
                                <option value="all">All Time</option>
                            </select>

                            <div class="flex gap-2" id="typeFilterContainer">
                                <button onclick="toggleChartType('assignment')" id="btn-assignment" class="px-4 py-2 bg-blue-600 text-white rounded-lg text-sm font-semibold shadow hover:bg-blue-700 transition-colors flex items-center gap-2">
                                    <i class="fa-solid fa-file-lines"></i> Assignment
                                </button>
                                <button onclick="toggleChartType('quiz')" id="btn-quiz" class="px-4 py-2 bg-green-600 text-white rounded-lg text-sm font-semibold shadow hover:bg-green-700 transition-colors flex items-center gap-2">
                                    <i class="fa-solid fa-clipboard-question"></i> Quiz
                                </button>
                                <button onclick="toggleChartType('all')" id="btn-back" class="hidden px-4 py-2 bg-gray-600 text-white rounded-lg text-sm font-semibold shadow hover:bg-gray-700 transition-colors flex items-center gap-2">
                                    <i class="fa-solid fa-arrow-left"></i> Back to Overview
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="relative h-96 w-full">
                        <canvas id="performanceChart"></canvas>
                    </div>
                </div>

                <!-- Detailed Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8 mb-8">
                    <!-- Quiz Stats -->
                    <div class="bg-[#1f2f1f] rounded-3xl p-6 shadow-xl text-white">
                        <h3 class="font-bold text-xl mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-clipboard-question text-green-400"></i>
                            Quiz Performance
                        </h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center border-b border-gray-700 pb-2">
                                <span class="text-gray-400">Total Attempts</span>
                                <span class="font-bold">{{ $quizAttempts->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-gray-700 pb-2">
                                <span class="text-gray-400">Not Done</span>
                                <span class="font-bold text-red-400">{{ $missedQuizzes }}</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-gray-700 pb-2">
                                <span class="text-gray-400">Average Score</span>
                                <span class="font-bold text-green-400">{{ number_format($quizAverage, 1) }}</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-gray-700 pb-2">
                                <span class="text-gray-400">Highest Score</span>
                                <span class="font-bold">{{ $quizAttempts->max('score') ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Assignment Stats -->
                    <div class="bg-[#1f2f1f] rounded-3xl p-6 shadow-xl text-white">
                        <h3 class="font-bold text-xl mb-4 flex items-center gap-2">
                            <i class="fa-solid fa-file-lines text-blue-400"></i>
                            Assignment Performance
                        </h3>
                        <div class="space-y-4">
                            <div class="flex justify-between items-center border-b border-gray-700 pb-2">
                                <span class="text-gray-400">Total Submissions</span>
                                <span class="font-bold">{{ $assignmentSubmissions->count() }}</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-gray-700 pb-2">
                                <span class="text-gray-400">Not Done</span>
                                <span class="font-bold text-red-400">{{ $missedAssignments }}</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-gray-700 pb-2">
                                <span class="text-gray-400">Average Score</span>
                                <span class="font-bold text-blue-400">{{ number_format($assignmentAverage, 1) }}</span>
                            </div>
                            <div class="flex justify-between items-center border-b border-gray-700 pb-2">
                                <span class="text-gray-400">Highest Score</span>
                                <span class="font-bold">{{ $assignmentSubmissions->max('score') ?? 0 }}</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Add Feedback Section -->
                <div class="bg-[#1f2f1f] rounded-3xl p-6 shadow-xl text-white mb-8">
                    <h3 class="font-bold text-xl mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-pen-to-square text-yellow-400"></i>
                        Add General Feedback
                    </h3>
                    <form action="{{ route('dosen.student.feedback', $mahasiswa->id) }}" method="POST">
                        @csrf
                        <div class="mb-4">
                            <label for="content" class="block text-sm font-medium text-gray-300 mb-2">Your Feedback</label>
                            <textarea id="content" name="content" rows="4" class="w-full bg-[#2a3f2a] border border-gray-600 rounded-xl p-3 text-white focus:ring-yellow-500 focus:border-yellow-500" placeholder="Write your feedback regarding the student's development..."></textarea>
                        </div>
                        <button type="submit" class="bg-yellow-500 hover:bg-yellow-600 text-black font-bold py-2 px-6 rounded-lg transition-colors">
                            Submit Feedback
                        </button>
                    </form>
                </div>

                <!-- Feedback History Section -->
                <div class="bg-[#1f2f1f] rounded-3xl p-6 shadow-xl text-white">
                    <h3 class="font-bold text-xl mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-comment-dots text-yellow-400"></i>
                        Feedback History
                    </h3>
                    
                    @if($generalFeedbacks->isEmpty())
                        <div class="text-center py-8 text-gray-400">
                            <p>No general feedback given yet.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($generalFeedbacks as $feedback)
                                <div class="bg-[#2a3f2a] rounded-xl p-4 border border-gray-700">
                                    <div class="flex justify-between items-start mb-2">
                                        <h4 class="font-bold text-lg text-yellow-300">General Feedback</h4>
                                        <span class="text-xs text-gray-400">{{ $feedback->created_at->format('M d, Y H:i') }}</span>
                                    </div>
                                    <p class="text-gray-200 text-sm italic">"{{ $feedback->content }}"</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </main>
    </div>

    <script>
        // Prepare data for Chart.js
        const rawQuizData = @json($quizAttempts);
        const rawAssignmentData = @json($assignmentSubmissions);
        let chartInstance = null;
        let currentType = 'all';

        function toggleChartType(type) {
            currentType = type;
            
            // Update button visibility
            const btnAssignment = document.getElementById('btn-assignment');
            const btnQuiz = document.getElementById('btn-quiz');
            const btnBack = document.getElementById('btn-back');
            
            if (btnAssignment && btnQuiz && btnBack) {
                if (type === 'all') {
                    btnAssignment.classList.remove('hidden');
                    btnQuiz.classList.remove('hidden');
                    btnBack.classList.add('hidden');
                } else {
                    btnAssignment.classList.add('hidden');
                    btnQuiz.classList.add('hidden');
                    btnBack.classList.remove('hidden');
                }
            }
            
            // Trigger chart update with current date filter
            const monthFilter = document.getElementById('monthFilter');
            if (monthFilter) {
                updateChart(monthFilter.value);
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            // -- Filter Logic Start --
            const filterSelect = document.getElementById('monthFilter');
            
            // Populate Filter Options
            const allDatesSet = new Set([
                ...rawQuizData.map(d => d.date),
                ...rawAssignmentData.map(d => d.date)
            ]);
            
            const uniqueMonths = [...allDatesSet].map(date => {
                return date.substring(0, 7); // YYYY-MM
            }).filter((value, index, self) => self.indexOf(value) === index).sort().reverse();

            const monthNames = ["January", "February", "March", "April", "May", "June",
                "July", "August", "September", "October", "November", "December"
            ];

            uniqueMonths.forEach(ym => {
                const [year, month] = ym.split('-');
                const label = `${monthNames[parseInt(month) - 1]} ${year}`;
                const option = document.createElement('option');
                option.value = ym;
                option.textContent = label;
                filterSelect.appendChild(option);
            });

            // Initial chart render
            updateChart('all');
            
            // Event Listener for Filter
            filterSelect.addEventListener('change', function(e) {
                updateChart(e.target.value);
            });
        });

        // Filter Function
        function updateChart(filterValue) {
            let filteredQuiz = rawQuizData;
            let filteredAssignment = rawAssignmentData;

            if (filterValue !== 'all') {
                filteredQuiz = rawQuizData.filter(d => d.date.startsWith(filterValue));
                filteredAssignment = rawAssignmentData.filter(d => d.date.startsWith(filterValue));
            }

            // Merge and sort dates based on what data we are showing
            let dates = [];
            if (currentType === 'all') {
                dates = [...new Set([
                    ...filteredQuiz.map(item => item.date),
                    ...filteredAssignment.map(item => item.date)
                ])];
            } else if (currentType === 'quiz') {
                dates = [...new Set(filteredQuiz.map(item => item.date))];
            } else if (currentType === 'assignment') {
                dates = [...new Set(filteredAssignment.map(item => item.date))];
            }
            dates.sort();

            // Map data
            const quizScores = dates.map(date => {
                if (currentType === 'assignment') return null;
                const attempt = filteredQuiz.find(q => q.date === date);
                return attempt ? attempt.score : null;
            });

            const assignmentScores = dates.map(date => {
                if (currentType === 'quiz') return null;
                const submission = filteredAssignment.find(a => a.date === date);
                return submission ? submission.score : null;
            });

            renderChart(dates, quizScores, assignmentScores);
        }

        // Render Chart Function
        function renderChart(labels, quizData, assignmentData) {
            const ctx = document.getElementById('performanceChart').getContext('2d');
            
            if (chartInstance) {
                chartInstance.destroy();
            }

            const datasets = [];

            if (currentType === 'all' || currentType === 'assignment') {
                datasets.push({
                    label: 'Assignment',
                    data: assignmentData,
                    borderColor: '#3b82f6', // Blue-500
                    backgroundColor: 'rgba(59, 130, 246, 0.7)',
                    borderWidth: 2,
                    borderRadius: 4,
                    barPercentage: 0.6,
                });
            }

            if (currentType === 'all' || currentType === 'quiz') {
                datasets.push({
                    label: 'Quiz',
                    data: quizData,
                    borderColor: '#22c55e', // Green-500
                    backgroundColor: 'rgba(34, 197, 94, 0.7)',
                    borderWidth: 2,
                    borderRadius: 4,
                    barPercentage: 0.6,
                });
            }

            chartInstance = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: labels.map(date => {
                        const d = new Date(date);
                        return d.toLocaleDateString('en-US', { month: 'short', day: 'numeric' });
                    }),
                    datasets: datasets
                },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: {
                            mode: 'index',
                            intersect: false,
                        },
                        plugins: {
                            legend: {
                                display: false // Custom legend used in HTML
                            },
                            tooltip: {
                                backgroundColor: '#1f2f1f',
                                titleColor: '#fff',
                                bodyColor: '#fff',
                                borderColor: '#4b5b3b',
                                borderWidth: 1,
                                padding: 12,
                                displayColors: true,
                                callbacks: {
                                    label: function(context) {
                                        let label = context.dataset.label || '';
                                        if (label) {
                                            label += ': ';
                                        }
                                        if (context.parsed.y !== null) {
                                            label += context.parsed.y;
                                        }
                                        return label;
                                    }
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                max: 100,
                                grid: {
                                    color: 'rgba(0, 0, 0, 0.1)'
                                },
                                ticks: {
                                    color: '#000',
                                    font: {
                                        weight: 'bold'
                                    }
                                }
                            },
                            x: {
                                grid: {
                                    display: false
                                },
                                ticks: {
                                    color: '#000'
                                }
                            }
                        }
                    }
                });
            }
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const bell = document.getElementById('bellIcon');
            const gear = document.getElementById('gearIcon');
            if (bell) { bell.addEventListener('click', function(e) { e.stopPropagation(); toggleNotificationPopup(); }); }
            if (gear) { gear.addEventListener('click', function(e) { e.stopPropagation(); toggleProfilePopup(); }); }
        });
        function toggleProfilePopup() {
            document.getElementById('notificationPopup').classList.remove('show');
            const popup = document.getElementById('profilePopup');
            const overlay = document.getElementById('popupOverlay');
            popup.classList.toggle('show');
            overlay.classList.toggle('show');
            if (!popup.classList.contains('show')) { document.getElementById('languageMenu').classList.remove('show'); }
        }
        function toggleNotificationPopup() {
            document.getElementById('profilePopup').classList.remove('show');
            const popup = document.getElementById('notificationPopup');
            const overlay = document.getElementById('popupOverlay');
            popup.classList.toggle('show');
            overlay.classList.toggle('show');
        }
        function closeAllPopups() {
            document.getElementById('profilePopup').classList.remove('show');
            document.getElementById('notificationPopup').classList.remove('show');
            document.getElementById('languageMenu').classList.remove('show');
            document.getElementById('popupOverlay').classList.remove('show');
        }
        function toggleLanguageMenu() { const languageMenu = document.getElementById('languageMenu'); languageMenu.classList.toggle('show'); }
        document.addEventListener('click', function(event) {
            const profilePopup = document.getElementById('profilePopup');
            const notificationPopup = document.getElementById('notificationPopup');
            const bellIcon = document.getElementById('bellIcon');
            const gearIcon = document.getElementById('gearIcon');
            if (profilePopup && notificationPopup && bellIcon && gearIcon) {
                if (!profilePopup.contains(event.target) && !notificationPopup.contains(event.target) && !bellIcon.contains(event.target) && !gearIcon.contains(event.target) && (profilePopup.classList.contains('show') || notificationPopup.classList.contains('show'))) { closeAllPopups(); }
            }
        });
    </script>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="bg-[#4b5b3b] text-white font-sans relative overflow-x-hidden">
    <!-- Decorative Line Background -->
    <div class="fixed inset-0 z-0 overflow-hidden">
        <img src="{{ asset('line.png') }}" alt="Decorative Line" class="w-full h-full object-cover opacity-10 scale-150">
    </div>

    <div class="relative z-10 min-h-screen flex flex-col">
        <!-- HEADER -->
        <header class="w-full bg-[#1f2f1f] text-white flex items-center justify-between px-8 py-4">
            <div class="flex items-center gap-3">
                <div class="bg-gray-700 rounded-full w-10 h-10 flex items-center justify-center">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>
                <div>
                    <div class="font-semibold">{{ Auth::user()->username }}</div>
                    <div class="text-xs text-gray-400">Mahasiswa</div>
                </div>
            </div>
            <div class="flex items-center justify-center absolute left-1/2 transform -translate-x-1/2 cursor-pointer" onclick="window.location.href='{{ route('mahasiswa.dashboard') }}'">
                <img src="{{ asset('logo.png') }}" class="w-24 h-24 filter brightness-0 invert" />
            </div>
            <div class="flex gap-6 text-3xl relative">
                <!-- Simple header -->
            </div>
        </header>

        <main class="flex-grow flex flex-col items-center w-full px-4 py-8">
            <div class="max-w-5xl w-full">
                <!-- Back Button -->
                <div class="mb-6">
                    <button onclick="window.location.href='{{ route('mahasiswa.dashboard') }}'" class="flex items-center gap-2 text-white hover:text-gray-300 transition-colors">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span>Back to Dashboard</span>
                    </button>
                </div>

                <!-- Page Title -->
                <div class="mb-8">
                    <h1 class="text-3xl font-bold">My Learning Development</h1>
                    <p class="text-xl text-gray-300 mt-2">{{ $mahasiswa->nama }} ({{ Auth::user()->email }})</p>
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

                @if($quizAttempts->isNotEmpty() || $assignmentSubmissions->isNotEmpty())
                <!-- Main Chart Section -->
                <div class="bg-[#e6e7d9] text-black rounded-3xl p-8 shadow-xl mb-8">
                    <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6">
                        <div>
                            <h2 class="text-2xl font-bold mb-2">Academic Performance History</h2>
                            <p class="text-gray-600">Track progress over time across Quizzes and Assignments.</p>
                        </div>
                        <div class="flex flex-col sm:flex-row gap-4 mt-4 md:mt-0 items-center">
                            <!-- Filter Dropdown -->
                            <select id="monthFilter" class="bg-white border border-gray-300 text-gray-700 text-sm rounded-lg focus:ring-green-500 focus:border-green-500 block w-full sm:w-auto p-2.5 shadow-sm" onchange="updateChart(this.value)">
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
                @endif

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

                <!-- Feedback History Section -->
                <div class="bg-[#1f2f1f] rounded-3xl p-6 shadow-xl text-white">
                    <h3 class="font-bold text-xl mb-4 flex items-center gap-2">
                        <i class="fa-solid fa-comment-dots text-yellow-400"></i>
                        Feedback from Lecturers
                    </h3>
                    
                    @if($generalFeedbacks->isEmpty())
                        <div class="text-center py-8 text-gray-400">
                            <p>No general feedback received yet.</p>
                        </div>
                    @else
                        <div class="space-y-4">
                            @foreach($generalFeedbacks as $feedback)
                                <div class="bg-[#2a3f2a] rounded-xl p-4 border border-gray-700">
                                    <div class="flex justify-between items-start mb-2">
                                        <div class="flex items-center gap-2">
                                            <h4 class="font-bold text-lg text-yellow-300">Feedback</h4>
                                            @if($feedback->dosen)
                                                <span class="text-xs bg-gray-700 px-2 py-1 rounded text-gray-300">from {{ $feedback->dosen->nama }}</span>
                                            @endif
                                        </div>
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
            
            if (type === 'all') {
                btnAssignment.classList.remove('hidden');
                btnQuiz.classList.remove('hidden');
                btnBack.classList.add('hidden');
            } else {
                btnAssignment.classList.add('hidden');
                btnQuiz.classList.add('hidden');
                btnBack.classList.remove('hidden');
            }
            
            // Trigger chart update with current date filter
            const monthFilter = document.getElementById('monthFilter').value;
            updateChart(monthFilter);
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

            // Map data with aggregation (Average per day)
            const quizScores = dates.map(date => {
                if (currentType === 'assignment') return null;
                const attemptsOnDate = filteredQuiz.filter(q => q.date === date);
                if (attemptsOnDate.length === 0) return null;
                
                const avg = attemptsOnDate.reduce((sum, item) => sum + item.score, 0) / attemptsOnDate.length;
                return parseFloat(avg.toFixed(1)); // Return average with 1 decimal
            });

            const assignmentScores = dates.map(date => {
                if (currentType === 'quiz') return null;
                const submissionsOnDate = filteredAssignment.filter(a => a.date === date);
                if (submissionsOnDate.length === 0) return null;
                
                const avg = submissionsOnDate.reduce((sum, item) => sum + item.score, 0) / submissionsOnDate.length;
                return parseFloat(avg.toFixed(1)); // Return average with 1 decimal
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
                                color: '#000',
                                font: {
                                    weight: 'bold'
                                }
                            }
                        }
                    }
                }
            });
        }
    </script>
</body>
</html>
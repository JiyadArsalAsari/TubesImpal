@extends('layouts.app')

@section('content')
<div >
    <main class="px-100 py-100">
        <!-- Title -->
        <h1 class="text-3xl font-bold mb-8">Learning Development</h1>

        <!-- Metrics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <!-- Overall Average -->
            <div class="bg-[#243024] rounded-2xl p-6">
                <div class="text-sm text-gray-400 mb-2">Overall Average</div>
                <div class="text-3xl font-bold mb-1">{{ isset($stats) ? $stats['overall_average'] : '0.0' }}</div>
                <div class="text-xs text-gray-400">Combined Quiz & Assignment</div>
            </div>

            <!-- Performance Trend -->
            <div class="bg-[#243024] rounded-2xl p-6">
                <div class="text-sm text-gray-400 mb-2">Performance Trend</div>
                <div class="text-3xl font-bold mb-1 {{ isset($stats) && $stats['performance_trend'] >= 0 ? 'text-green-400' : 'text-red-400' }}">
                    {{ isset($stats) && $stats['performance_trend'] >= 0 ? '+' : '' }}{{ isset($stats) ? $stats['performance_trend'] : '0.0' }}%
                </div>
                <div class="text-xs text-gray-400">vs. Average (last 3 activities)</div>
            </div>

            <!-- Recent Average -->
            <div class="bg-[#243024] rounded-2xl p-6">
                <div class="text-sm text-gray-400 mb-2">Recent Average</div>
                <div class="text-3xl font-bold mb-1">{{ isset($stats) ? $stats['recent_average'] : '0.0' }}</div>
                <div class="text-xs text-gray-400">Last 3 activities</div>
            </div>
        </div>

        <!-- Academic Performance History -->
        <div class="rounded-2xl p-8 mb-8 text-white">
            <div class="mb-4">
                <h2 class="text-2xl font-bold mb-2">Academic Performance History</h2>
                <p class="text-sm text-gray-300">Track your progress over time across Quizzes and Assignments.</p>
            </div>

            <!-- Filter Buttons -->
            <div class="flex gap-4 mb-6">
                <button id="filterAll" class="px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold filter-btn active" data-filter="all">
                    All Time
                </button>
                <button id="filterAssignment" class="px-4 py-2 bg-[#243024] text-gray-200 hover:bg-[#2a3a2a] rounded-lg font-semibold filter-btn" data-filter="assignment">
                    <span class="w-2 h-2 bg-green-500 rounded-full inline-block mr-2"></span>
                    Assignment
                </button>
                <button id="filterQuiz" class="px-4 py-2 bg-[#243024] text-gray-200 hover:bg-[#2a3a2a] rounded-lg font-semibold filter-btn" data-filter="quiz">
                    <span class="w-2 h-2 bg-green-500 rounded-full inline-block mr-2"></span>
                    Quiz
                </button>
            </div>

            <!-- Chart -->
            @if(isset($chartData) && count($chartData) > 0)
                <div class="rounded-xl bg-[#e6e7d9] border border-black/10 p-4">
                    <div class="grid grid-cols-[36px_1fr] gap-3" style="height: 300px;">
                        <div class="flex flex-col justify-between text-xs text-gray-600 pb-6">
                            <span>100</span>
                            <span>75</span>
                            <span>50</span>
                            <span>25</span>
                            <span>0</span>
                        </div>

                        <div class="relative h-full">
                            <div class="absolute inset-0 pointer-events-none flex flex-col justify-between">
                                <div class="border-t border-black/10"></div>
                                <div class="border-t border-black/10"></div>
                                <div class="border-t border-black/10"></div>
                                <div class="border-t border-black/10"></div>
                                <div class="border-t border-black/10"></div>
                            </div>
                            <div class="absolute left-0 top-0 bottom-6 border-l border-black/20 pointer-events-none"></div>
                            <div class="absolute left-0 right-0 bottom-6 border-b border-black/20 pointer-events-none"></div>

                            <div class="h-full overflow-x-auto">
                                <div class="min-w-max h-full flex items-stretch gap-12 px-6 pb-6">
                                    @foreach($chartData as $data)
                                        <div class="h-full flex flex-col items-center justify-end flex-none chart-bar" data-type="{{ $data['type'] ?? 'all' }}">
                                            <div class="w-16 bg-blue-500 rounded shadow-sm transition-colors hover:bg-blue-600"
                                                 style="height: {{ min(($data['score'] / 100) * 100, 100) }}%; min-height: 5px;"
                                                 title="{{ $data['date'] }}: {{ $data['score'] }}%">
                                            </div>
                                            <span class="mt-2 text-xs font-semibold text-gray-600">{{ $data['date'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="flex items-center justify-center h-64 bg-[#243024] rounded-lg">
                    <div class="text-center">
                        <p class="text-gray-200 text-lg mb-2">Belum ada data exercise yang dikerjakan</p>
                        <p class="text-gray-300 text-sm mb-4">Mulai kerjakan exercise untuk melihat progress belajar Anda</p>
                        <a href="{{ route('mahasiswa.exercise') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-full transition">
                            Lihat Exercise
                        </a>
                    </div>
                </div>
            @endif
        </div>

        <!-- Performance Breakdown -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <!-- Quiz Performance -->
            <div class="bg-[#243024] rounded-2xl p-6">
                <h3 class="text-xl font-bold mb-4">Quiz Performance</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Total Attempts:</span>
                        <span class="font-semibold">{{ isset($stats) ? $stats['quiz']['total_attempts'] : 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Missed:</span>
                        <span class="font-semibold">{{ isset($stats) ? $stats['quiz']['missed'] : 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Average Score:</span>
                        <span class="font-semibold">{{ isset($stats) ? number_format($stats['quiz']['average_score'], 1) : '0.0' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Highest Score:</span>
                        <span class="font-semibold">{{ isset($stats) ? $stats['quiz']['highest_score'] : 0 }}</span>
                    </div>
                </div>
            </div>

            <!-- Assignment Performance -->
            <div class="bg-[#243024] rounded-2xl p-6">
                <h3 class="text-xl font-bold mb-4">Assignment Performance</h3>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-gray-400">Total Submissions:</span>
                        <span class="font-semibold">{{ isset($stats) ? $stats['assignment']['total_submissions'] : 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Missed:</span>
                        <span class="font-semibold">{{ isset($stats) ? $stats['assignment']['missed'] : 0 }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Average Score:</span>
                        <span class="font-semibold">{{ isset($stats) ? number_format($stats['assignment']['average_score'], 1) : '0.0' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-400">Highest Score:</span>
                        <span class="font-semibold">{{ isset($stats) ? $stats['assignment']['highest_score'] : 0 }}</span>
                    </div>
                </div>
            </div>
        </div>

    </main>
</div>

<script>
    // Filter functionality
    document.addEventListener('DOMContentLoaded', function() {
        const filterButtons = document.querySelectorAll('.filter-btn');
        const chartBars = document.querySelectorAll('.chart-bar');

        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');
                
                // Update button styles
                filterButtons.forEach(btn => {
                    btn.classList.remove('active', 'bg-blue-600', 'text-white');
                    btn.classList.add('bg-[#243024]', 'text-gray-200', 'hover:bg-[#2a3a2a]');
                });
                this.classList.add('active', 'bg-blue-600', 'text-white');
                this.classList.remove('bg-[#243024]', 'text-gray-200', 'hover:bg-[#2a3a2a]');

                // Filter chart bars
                chartBars.forEach(bar => {
                    const type = bar.getAttribute('data-type');
                    if (filter === 'all' || type === filter) {
                        bar.style.display = 'flex';
                    } else {
                        bar.style.display = 'none';
                    }
                });
            });
        });
    });
</script>
@endsection

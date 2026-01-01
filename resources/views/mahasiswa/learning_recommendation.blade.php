@extends('layouts.app')

@section('content')
    <div class="mb-6">
        <button onclick="window.location.href='{{ route('mahasiswa.dashboard') }}'" class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-500/30 hover:bg-gray-500/50 text-white hover:text-gray-200 rounded-full transition-all duration-300 font-semibold group">
            <i class="fa-solid fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
            <span>Back to Dashboard</span>
        </button>
    </div>
<div class="min-h-screen bg-[#44533E] relative">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-20 pointer-events-none"
         style="background-image: url('/images/pattern.svg'); background-size: cover;">
    </div>

    <div class="relative z-10 max-w-6xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="mb-8">
            <h1 class="text-4xl font-bold text-white mb-2">Learning Recommendations</h1>
            <p class="text-gray-300 text-lg">Personalized recommendations based on your learning difficulties</p>
        </div>

        <!-- Search Form -->
        <div class="mb-8">
            <form action="{{ route('mahasiswa.learning.recommendation') }}" method="GET" class="relative" id="searchForm">
                <input type="text" id="searchInput" name="search" placeholder="Search by subject..." value="{{ request('search') }}"
                    class="w-full bg-[#1F2B1E] text-white border border-[#2D3A2D] rounded-full py-3 px-6 pl-12 focus:outline-none focus:border-blue-500 shadow-inner transition-all focus:ring-2 focus:ring-blue-500/50">
                <i class="fa-solid fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-400 text-lg"></i>
            </form>
        </div>

        <!-- Recommendations List -->
        <div id="learningRecommendationsList">
            @include('mahasiswa.partials.learning_recommendations_list')
        </div>
    </div>
</div>
<script>
    // Live Search Implementation
    const searchInput = document.getElementById('searchInput');
    const resultsContainer = document.getElementById('learningRecommendationsList');
    let searchTimeout;

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value;

        // Debounce for 300ms
        searchTimeout = setTimeout(() => {
            fetchResults(query);
        }, 300);
    });

    // Prevent form submission on enter
    document.getElementById('searchForm').addEventListener('submit', function(e) {
        e.preventDefault();
        fetchResults(searchInput.value);
    });

    function fetchResults(query) {
        // Use current URL but update search param
        const url = new URL("{{ route('mahasiswa.learning.recommendation') }}");
        if (query) {
            url.searchParams.set('search', query);
        } else {
            url.searchParams.delete('search');
        }

        // Push state to URL without reloading
        window.history.pushState({}, '', url);

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            resultsContainer.innerHTML = html;
        })
        .catch(error => console.error('Error:', error));
    }
</script>
@endsection
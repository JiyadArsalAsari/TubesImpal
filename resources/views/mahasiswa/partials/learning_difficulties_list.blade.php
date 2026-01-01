@if($learningDifficulties->count() > 0)
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($learningDifficulties as $difficulty)
            <div class="bg-[#1F2B1E] rounded-2xl p-6 shadow-lg border border-[#2D3A2D]">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-bold text-white">{{ $difficulty->title }}</h3>
                    <span class="text-xs text-gray-400">{{ $difficulty->created_at->format('M d, Y') }}</span>
                </div>
                <p class="text-gray-300 mb-4">{{ $difficulty->description }}</p>
                <div class="flex justify-end">
                    <button class="text-blue-400 hover:text-blue-300 text-sm font-medium">
                        View Details
                    </button>
                </div>
            </div>
        @endforeach
    </div>
@else
    <div class="bg-[#1F2B1E] rounded-2xl p-12 text-center border border-[#2D3A2D]">
        <div class="flex justify-center mb-6">
            <div class="bg-gray-800 rounded-full w-16 h-16 flex items-center justify-center">
                <i class="fa-solid fa-exclamation-circle text-white text-2xl"></i>
            </div>
        </div>
        <h3 class="text-white font-bold text-3xl mb-4">No Learning Difficulties Found</h3>
        
        @if(request('search'))
            <p class="text-gray-300 text-xl mb-8">No results found for "{{ request('search') }}".</p>
            <a href="{{ route('mahasiswa.learning.difficulties') }}"
                class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-8 rounded-full inline-flex items-center transition">
                <i class="fa-solid fa-times mr-2"></i>
                Clear Search
            </a>
        @else
            <p class="text-gray-300 text-xl mb-8">You haven't submitted any learning difficulties yet.</p>
            <a href="{{ route('mahasiswa.learning.difficulties.create') }}"
                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-full inline-flex items-center transition">
                <i class="fa-solid fa-plus mr-2"></i>
                Add Your First Difficulty
            </a>
        @endif
    </div>
@endif

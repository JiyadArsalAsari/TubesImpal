@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#44533E] relative">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-20 pointer-events-none"
         style="background-image: url('/images/pattern.svg'); background-size: cover;">
    </div>

    <!-- Content -->
    <div class="relative z-10 max-w-6xl mx-auto px-4 py-8">
        <!-- Header -->
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-4xl font-bold text-white">Learning Difficulties</h1>
            <a href="{{ route('mahasiswa.learning.difficulties.create') }}" 
               class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-full flex items-center">
                <i class="fa-solid fa-plus mr-2"></i>
                Add New Difficulty
            </a>
        </div>

        <!-- Success Message -->
        @if(session('success'))
            <div class="bg-green-600 text-white p-4 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <!-- Learning Difficulties List -->
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
                <p class="text-gray-300 text-xl mb-8">You haven't submitted any learning difficulties yet.</p>
                <a href="{{ route('mahasiswa.learning.difficulties.create') }}" 
                   class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-full inline-flex items-center transition">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Add Your First Difficulty
                </a>
            </div>
        @endif
    </div>
</div>
@endsection
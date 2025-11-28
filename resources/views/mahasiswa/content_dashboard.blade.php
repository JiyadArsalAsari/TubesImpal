@extends('layouts.app')

@section('content')
    <!-- Search Bar -->
    <div class="mb-8">
        <div class="relative">
            <i class="fa-solid fa-search absolute left-4 top-1/2 transform -translate-y-1/2 text-gray-500"></i>
            <input 
                type="text" 
                placeholder="Search (Ctrl + F)" 
                class="w-full pl-12 pr-4 py-3 rounded-full bg-white text-gray-700 placeholder-gray-400 focus:outline-none search-bar"
            >
        </div>
    </div>

    <!-- Content Cards -->
    <div class="space-y-4">
        @if(count($contents) > 0)
            <!-- Video Card -->
            @foreach($contents as $index => $content)
                <div class="card-item p-6 flex items-center gap-6 cursor-pointer" onclick="openContent({{ $index }})">
                    <!-- Icon -->
                    <div class="flex-shrink-0">
                        @if($content['type'] === 'video')
                            <div class="bg-gray-800 rounded-xl w-20 h-20 flex items-center justify-center">
                                <i class="fa-solid fa-play text-white text-2xl"></i>
                            </div>
                        @else
                            <div class="bg-gray-800 rounded-xl w-20 h-20 flex items-center justify-center">
                                <i class="fa-solid fa-file-lines text-white text-2xl"></i>
                            </div>
                        @endif
                    </div>

                    <!-- Text Content -->
                    <div class="flex-1">
                        <h3 class="text-white font-semibold text-xl mb-2">
                            {{ $content['title'] }}
                        </h3>
                        <p class="text-gray-300 text-sm">
                            {{ $content['description'] }}
                        </p>
                    </div>

                    <!-- Arrow -->
                    <div class="flex-shrink-0">
                        <i class="fa-solid fa-arrow-right text-white opacity-50"></i>
                    </div>
                </div>
            @endforeach
        @else
            <!-- Empty State -->
            <div class="card-item p-12 text-center">
                <div class="flex justify-center mb-6">
                    <div class="bg-gray-800 rounded-full w-16 h-16 flex items-center justify-center">
                        <i class="fa-solid fa-book-open text-white text-2xl"></i>
                    </div>
                </div>
                <h3 class="text-white font-semibold text-xl mb-2">Belum Ada Rekomendasi Belajar</h3>
                <p class="text-gray-300 mb-6">Admin atau AI belum menambahkan rekomendasi belajar untuk Anda.</p>
                <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-full inline-flex items-center transition" onclick="location.reload()">
                    <i class="fa-solid fa-refresh mr-2"></i>
                    Refresh
                </button>
            </div>
        @endif
    </div>

    <script>
        function openContent(index) {
            // Redirect to content detail page with content index as parameter
            window.location.href = '/mahasiswa/content/' + index;
        }
    </script>
@endsection
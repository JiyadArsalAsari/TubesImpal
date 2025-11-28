@extends('layouts.app')

@section('content')
    <div class="max-w-4xl mx-auto">
        <!-- Back Button -->
        <button onclick="window.location.href='{{ route('mahasiswa.learning.recommendation') }}'" class="flex items-center gap-2 text-gray-300 hover:text-white mb-8 transition">
            <i class="fa-solid fa-arrow-left"></i>
            <span>Kembali ke Rekomendasi</span>
        </button>

        <!-- Content Detail Card -->
        <div class="card-item rounded-2xl p-8">
            <!-- Header -->
            <div class="flex items-start gap-6 mb-8">
                <!-- Icon -->
                <div class="flex-shrink-0">
                    @if($content['type'] === 'video')
                        <div class="bg-gray-800 rounded-xl w-24 h-24 flex items-center justify-center">
                            <i class="fa-solid fa-play text-white text-3xl"></i>
                        </div>
                    @else
                        <div class="bg-gray-800 rounded-xl w-24 h-24 flex items-center justify-center">
                            <i class="fa-solid fa-file-lines text-white text-3xl"></i>
                        </div>
                    @endif
                </div>

                <!-- Title and Description -->
                <div>
                    <h1 class="text-2xl font-bold text-white mb-3">{{ $content['title'] }}</h1>
                    <p class="text-gray-300">{{ $content['description'] }}</p>
                </div>
            </div>

            <!-- Divider -->
            <div class="border-t border-gray-700 my-8"></div>

            <!-- Content Details -->
            <div class="prose prose-invert max-w-none">
                <p class="text-gray-200 text-lg leading-relaxed">
                    {{ $content['details'] }}
                </p>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap gap-4 mt-10">
                <button class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-full inline-flex items-center transition">
                    <i class="fa-solid fa-bookmark mr-2"></i>
                    Simpan ke Daftar Belajar
                </button>
                <button class="bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-full inline-flex items-center transition">
                    <i class="fa-solid fa-share-nodes mr-2"></i>
                    Bagikan
                </button>
            </div>
        </div>
    </div>
@endsection
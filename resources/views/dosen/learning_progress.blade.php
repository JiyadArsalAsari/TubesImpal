@extends('layouts.app')

@section('container_class', 'max-w-7xl mx-auto px-6 py-10')

@push('styles')
    <style>
        /* Specific page styles moved from original file */
    </style>
@endpush

@section('content')
    <div class="mb-6">
        <button onclick="window.location.href='{{ route('dosen.dashboard') }}'"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-gray-500/30 hover:bg-gray-500/50 text-white hover:text-gray-200 rounded-full transition-all duration-300 font-semibold group">
            <i class="fa-solid fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
            <span>Back to Dashboard</span>
        </button>
    </div>
    <div class="mb-8">
        <h2 class="text-2xl font-bold text-white mb-2">Learning Progress untuk {{ $mahasiswa->user->name }}</h2>
        <p class="text-gray-300">Identitas mahasiswa dapat dilihat pada halaman profil mahasiswa.</p>
    </div>

    <!-- Stats Overview -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <div class="bg-[#395035] rounded-2xl p-6 shadow-lg border border-[#436040]">
            <div class="flex items-center">
                <div class="rounded-full bg-[#2f3d2c] p-3 mr-4 border border-[#436040]">
                    <i class="fas fa-book text-blue-400 text-xl"></i>
                </div>
                <div>
                    <p class="text-gray-300 text-sm">Total Learning Difficulties</p>
                    <p class="text-2xl font-bold text-white">{{ $mahasiswa->learningDifficulties->count() }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Learning Difficulties Section -->
    <div class="bg-[#395035] rounded-2xl shadow-lg border border-[#436040] mb-8 overflow-hidden">
        <div class="px-6 py-4 border-b border-[#436040] bg-[#2f3d2c]">
            <h3 class="text-lg font-medium text-white">Learning Difficulties & AI Recommendation</h3>
        </div>
        <div class="p-6">
            @if($mahasiswa->learningDifficulties->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-[#436040]">
                        <thead class="bg-[#2f3d2c]">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                    Subject</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                                    Description</th>

                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">Date
                                    Reported</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">AI
                                    Recommendation</th>
                            </tr>
                        </thead>
                        <tbody class="bg-[#395035] divide-y divide-[#436040]">
                            @foreach($learningRecommendations as $item)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-white font-medium">
                                        {{ $item['difficulty']->subject ?? $item['difficulty']->title }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-300">
                                        {{ Str::limit($item['difficulty']->description, 80) }}
                                    </td>

                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-400">
                                        {{ optional($item['difficulty']->created_at)->format('M d, Y') ?? '-' }}
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-300">
                                        {!! nl2br(e($item['ai_result'])) !!}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-400 text-center py-4">Belum ada learning difficulties yang dilaporkan.</p>
            @endif
        </div>
    </div>
@endsection
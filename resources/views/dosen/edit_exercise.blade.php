@extends('layouts.app')

@section('container_class', 'max-w-4xl mx-auto px-6 py-10')

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

    <div class="flex items-center justify-between mb-6">
        <div>
            <p class="text-sm text-gray-200">Edit Assignment untuk</p>
            <h1 class="text-2xl font-bold">{{ $mahasiswa->user->name ?? $mahasiswa->nama ?? 'Mahasiswa' }}</h1>
        </div>
    </div>

    @if ($errors->any())
        <div class="mb-6 bg-red-100 text-red-800 p-4 rounded-lg">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="mb-6 bg-[#3d2c2c] text-red-400 border border-red-500 rounded-xl px-4 py-3 shadow-md">{{ session('error') }}
        </div>
    @endif

    <form action="{{ route('dosen.exercise.update', $exercise->id) }}" method="POST" enctype="multipart/form-data"
        class="bg-[#2f3d2c] border border-[#3c4c39] rounded-2xl p-6 shadow-xl space-y-4">
        @csrf
        @method('PUT')

        <div>
            <label class="block text-sm text-gray-200 mb-1">Status</label>
            <select name="status"
                class="w-full rounded-lg bg-[#1f2f1f] text-white border border-[#436040] p-3 placeholder-gray-400">
                <option value="draft" {{ old('status', $exercise->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="published" {{ old('status', $exercise->status) == 'published' ? 'selected' : '' }}>Published
                </option>
            </select>
        </div>

        <div>
            <label class="block text-sm text-gray-200 mb-1">Judul</label>
            <input type="text" name="title" value="{{ old('title', $exercise->title) }}"
                class="w-full rounded-lg bg-[#1f2f1f] text-white border border-[#436040] p-3 placeholder-gray-400"
                placeholder="Contoh: Assignment 1 - Introduction to UI/UX Design" required>
        </div>

        <div>
            <label class="block text-sm text-gray-200 mb-1">Deskripsi (opsional)</label>
            <textarea name="description" rows="3"
                class="w-full rounded-lg bg-[#1f2f1f] text-white border border-[#436040] p-3 placeholder-gray-400"
                placeholder="Ringkasan soal atau instruksi">{{ old('description', $exercise->description) }}</textarea>
        </div>

        <div>
            <label class="block text-sm text-gray-200 mb-1">Deadline (opsional)</label>
            <input type="datetime-local" name="deadline"
                value="{{ old('deadline', $exercise->deadline ? $exercise->deadline->format('Y-m-d\TH:i') : '') }}"
                class="w-full rounded-lg bg-[#1f2f1f] text-white border border-[#436040] p-3 placeholder-gray-400">
        </div>

        <div>
            <label class="block text-sm text-gray-200 mb-1">Link (opsional)</label>
            <input type="url" name="link" value="{{ old('link', $exercise->link) }}"
                class="w-full rounded-lg bg-[#1f2f1f] text-white border border-[#436040] p-3 placeholder-gray-400"
                placeholder="https://example.com">
        </div>

        <div>
            <label class="block text-sm text-gray-200 mb-1">File Assignment (opsional)</label>
            @if($exercise->file_attachment)
                <div class="mb-2 p-3 bg-[#1f2f1f] border border-[#436040] rounded-lg">
                    <p class="text-sm text-gray-300 mb-1">File saat ini:</p>
                    <a href="{{ asset('storage/' . $exercise->file_attachment) }}" target="_blank"
                        class="text-blue-400 hover:text-blue-300 text-sm font-semibold">
                        <i class="fa-solid fa-file"></i> {{ basename($exercise->file_attachment) }}
                    </a>
                </div>
            @endif
            <input type="file" name="file_attachment"
                class="w-full rounded-lg bg-[#1f2f1f] text-white border border-[#436040] p-3 placeholder-gray-400">
            <p class="text-xs text-gray-200 mt-1">Upload file baru untuk mengganti file yang ada (PDF, DOC, DOCX, ZIP, etc.)
            </p>
        </div>

        <div class="pt-2 flex gap-3">
            <button type="submit"
                class="flex-1 bg-[#1d8f3b] hover:bg-[#167731] text-white font-semibold py-3 rounded-xl transition">Perbarui
                Assignment</button>
            <a href="{{ route('dosen.mahasiswa.exercises', $mahasiswa->id) }}"
                class="flex-1 bg-gray-600 hover:bg-gray-700 text-white font-semibold py-3 rounded-xl transition text-center">Batal</a>
        </div>
    </form>
@endsection
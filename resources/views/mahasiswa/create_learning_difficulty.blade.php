@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-[#44533E] relative">
    <!-- Background Pattern -->
    <div class="absolute inset-0 opacity-20 pointer-events-none"
         style="background-image: url('/images/pattern.svg'); background-size: cover;">
    </div>

    <!-- Content -->
    <div class="relative z-10 flex flex-col items-center mt-20 px-4">
        <form action="{{ route('mahasiswa.learning.difficulties.store') }}" method="POST" class="w-full">
            @csrf
            
            <!-- Subject Name -->
            <h2 class="text-white text-3xl font-bold mb-6 text-center">Subject Name:</h2>

            <div class="w-full max-w-2xl relative mb-12">
                <input type="text" name="subject_name"
                    class="w-full py-4 pl-6 pr-16 rounded-full bg-white shadow-lg outline-none relative z-10 text-gray-800 text-lg font-medium focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Type subject name here..." required />

                <button type="button" class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-700 z-20">
                    <i class="fa-solid fa-microphone text-xl"></i>
                </button>
            </div>

            <!-- Difficulty Question -->
            <h2 class="text-white text-3xl font-bold mb-6 text-center">What Makes This Subject Difficult For You?</h2>

            <div class="w-full max-w-3xl relative mb-12">
                <textarea name="description"
                    class="w-full h-48 p-6 pr-16 rounded-3xl bg-white shadow-lg resize-none outline-none relative z-10 text-gray-800 text-lg font-medium focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Describe what makes this subject difficult for you..." required></textarea>

                <!-- Attachment Icon -->
                <button type="button" class="absolute left-5 bottom-4 text-gray-700 z-20">
                    <i class="fa-solid fa-file-circle-plus text-2xl"></i>
                </button>

                <!-- Mic Icon -->
                <button type="button" class="absolute right-6 bottom-4 text-gray-700 z-20">
                    <i class="fa-solid fa-microphone text-2xl"></i>
                </button>
            </div>

            <!-- Submit Button -->
            <div class="flex justify-center gap-4">
                <a href="{{ route('mahasiswa.learning.difficulties') }}" 
                   class="bg-gray-600 hover:bg-gray-700 text-white font-bold py-3 px-8 rounded-full">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-8 rounded-full">
                    Submit Difficulty
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="bg-[#4b5b3b] text-white font-sans relative overflow-x-hidden">
    <!-- Decorative Line Background -->
    <div class="fixed inset-0 z-0 overflow-hidden">
        <img src="{{ asset('line.png') }}" alt="Decorative Line" class="w-full h-full object-cover opacity-10 scale-150">
    </div>

    <div class="relative z-10 min-h-screen">
        <!-- HEADER -->
        <header class="w-full bg-[#1f2f1f] text-white flex items-center justify-between px-8 py-4">
            <div class="text-2xl font-bold">{{ Auth::user()->name }}</div>
            <div class="flex items-center justify-center absolute left-1/2 transform -translate-x-1/2">
                <img src="{{ asset('logo.png') }}" class="w-24 h-24 filter brightness-0 invert" />
            </div>
            <div class="flex gap-6 text-3xl">
                <i class="fa-regular fa-bell cursor-pointer"></i>
                <i class="fa-solid fa-gear cursor-pointer"></i>
            </div>
        </header>

        <!-- Greeting -->
        <h2 class="text-center text-3xl font-bold mt-10 mb-12">
            Hello {{ Auth::user()->name }}, Welcome Back To StudyFlow!
        </h2>

        <!-- Cards Section -->
        <div class="flex flex-col md:flex-row justify-center gap-40 mb-16 mx-10">
            <!-- Schedule Card -->
            <div class="bg-[#e6e7d9] text-black rounded-3xl p-10 w-96 shadow-xl flex flex-col items-center text-center">
                <div class="flex items-center gap-4 mb-4">
                    <i class="fa-regular fa-calendar text-2xl"></i>
                    <span class="font-bold text-xl">Your Schedule Today</span>
                </div>
                <p class="font-bold text-xl mb-2">Interaksi Manusia Komputer</p>
                <p class="text-lg">KU1.03.18 — 08.30 - 11.30</p>
            </div>

            <!-- Deadline Card -->
            <div class="bg-[#e6e7d9] text-black rounded-3xl p-10 w-96 shadow-xl flex flex-col items-center text-center">
                <div class="flex items-center gap-4 mb-4">
                    <i class="fa-regular fa-clock text-2xl"></i>
                    <span class="font-bold text-xl">Your Deadline Today</span>
                </div>
                <p class="font-bold text-xl mb-2">Convert Class Diagram to Code</p>
                <p class="text-lg">16.00</p>
            </div>
        </div>

        <!-- Menu Buttons -->
        <div class="flex flex-col md:flex-row justify-center gap-40 mb-16 mx-10">
            <!-- Left Column -->
            <div class="flex flex-col gap-6">
                <button class="flex items-center gap-5 bg-[#1f2f1f] p-7 rounded-3xl text-white shadow-xl justify-center w-[392px] hover:bg-[#2a3a2a] transition-all duration-300 whitespace-nowrap">
                    <i class="fa-solid fa-cloud text-2xl"></i>
                    <span class="font-bold text-xl">Learning Difficulties</span>
                </button>

                <button class="flex items-center gap-5 bg-[#1f2f1f] p-7 rounded-3xl text-white shadow-xl justify-center w-[392px] hover:bg-[#2a3a2a] transition-all duration-300 whitespace-nowrap">
                    <i class="fa-regular fa-lightbulb text-2xl"></i>
                    <span class="font-bold text-xl">Learning Recommendation</span>
                </button>

                <button class="flex items-center gap-5 bg-[#1f2f1f] p-7 rounded-3xl text-white shadow-xl justify-center w-[392px] hover:bg-[#2a3a2a] transition-all duration-300 whitespace-nowrap">
                    <i class="fa-solid fa-chart-column text-2xl"></i>
                    <span class="font-bold text-xl">Learning Development</span>
                </button>
            </div>

            <!-- Right Column -->
            <div class="flex flex-col gap-6">
                <button class="flex items-center gap-5 bg-[#1f2f1f] p-7 rounded-3xl text-white shadow-xl justify-center w-[392px] hover:bg-[#2a3a2a] transition-all duration-300 whitespace-nowrap">
                    <i class="fa-regular fa-calendar text-2xl"></i>
                    <span class="font-bold text-xl">Schedule</span>
                </button>

                <button class="flex items-center gap-5 bg-[#1f2f1f] p-7 rounded-3xl text-white shadow-xl justify-center w-[392px] hover:bg-[#2a3a2a] transition-all duration-300 whitespace-nowrap">
                    <i class="fa-solid fa-clock text-2xl"></i>
                    <span class="font-bold text-xl">Deadline</span>
                </button>

                <button class="flex items-center gap-5 bg-[#1f2f1f] p-7 rounded-3xl text-white shadow-xl justify-center w-[392px] hover:bg-[#2a3a2a] transition-all duration-300 whitespace-nowrap">
                    <i class="fa-solid fa-list-check text-2xl"></i>
                    <span class="font-bold text-xl">Exercise</span>
                </button>
            </div>
        </div>
    </div>
</body>
</html>
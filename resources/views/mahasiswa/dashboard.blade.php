<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome Icon -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="bg-[#4b5b3b] text-white font-sans relative overflow-x-hidden">
    <!-- Decorative Line Background -->
    <div class="fixed inset-0 z-0 overflow-hidden">
        <img src="{{ asset('line.png') }}" alt="Decorative Line" class="w-full h-full object-cover opacity-10 scale-150">
    </div>

    <!-- Content Container -->
    <div class="relative z-10 min-h-screen">
        <!-- HEADER -->
        <div class="w-full bg-[#1f2f1f] text-white py-3 px-6 flex items-center justify-between" style="height: 80px;">
            <!-- Left: Name -->
            <div class="text-lg font-semibold">
                {{ Auth::user()->name }}
            </div>

            <!-- Center: Logo Only -->
            <div class="flex items-center justify-center">
                <img src="{{ asset('logo.png') }}" alt="StudyFlow Logo" class="w-24 h-24 filter brightness-0 invert" style="margin-top: -10px;">
            </div>

            <!-- Right Icons -->
            <div class="flex gap-4 text-2xl">
                <i class="fa-regular fa-bell cursor-pointer"></i>
                <i class="fa-solid fa-gear cursor-pointer"></i>
            </div>
        </div>

        <!-- MAIN CONTENT -->
        <div class="p-6">
            <!-- Greeting -->
            <h2 class="text-center text-2xl font-semibold mb-8">
                Hello {{ Auth::user()->name }}, Welcome Back To StudyFlow!
            </h2>

            <!-- Cards: Schedule & Deadline -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 justify-center max-w-2xl mx-auto">
                <!-- Schedule Card -->
                <div class="bg-[#e6e7d9] text-black shadow-lg rounded-xl p-8 mx-auto w-64">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-regular fa-calendar text-lg"></i>
                        <p class="font-semibold">Your Schedule Today</p>
                    </div>
                    <p class="font-medium">Interaksi Manusia Komputer</p>
                    <p class="text-sm mt-1">KU1.03.18 — 08.30 - 11.30</p>
                </div>

                <!-- Deadline Card -->
                <div class="bg-[#e6e7d9] text-black shadow-lg rounded-xl p-8 mx-auto w-64">
                    <div class="flex items-center gap-2 mb-2">
                        <i class="fa-regular fa-clock text-lg"></i>
                        <p class="font-semibold">Your Deadline Today</p>
                    </div>
                    <p class="font-medium">Convert Class Diagram to Code</p>
                    <p class="text-sm mt-1">16.00</p>
                </div>
            </div>

            <!-- Menu Buttons -->
            <div class="mt-10 grid grid-cols-1 md:grid-cols-2 gap-5 max-w-xl mx-auto">
                <!-- Row 1 -->
                <button class="flex items-center gap-4 bg-[#2f3b26] p-5 rounded-xl shadow text-white">
                    <i class="fa-solid fa-cloud text-xl"></i>
                    <span class="font-medium">Learning Difficulties</span>
                </button>

                <button class="flex items-center gap-4 bg-[#2f3b26] p-5 rounded-xl shadow text-white">
                    <i class="fa-regular fa-calendar text-xl"></i>
                    <span class="font-medium">Schedule</span>
                </button>

                <!-- Row 2 -->
                <button class="flex items-center gap-4 bg-[#2f3b26] p-5 rounded-xl shadow text-white">
                    <i class="fa-regular fa-lightbulb text-xl"></i>
                    <span class="font-medium">Learning Recommendation</span>
                </button>

                <button class="flex items-center gap-4 bg-[#2f3b26] p-5 rounded-xl shadow text-white">
                    <i class="fa-solid fa-clock text-xl"></i>
                    <span class="font-medium">Deadline</span>
                </button>

                <!-- Row 3 -->
                <button class="flex items-center gap-4 bg-[#2f3b26] p-5 rounded-xl shadow text-white">
                    <i class="fa-solid fa-chart-column text-xl"></i>
                    <span class="font-medium">Learning Development</span>
                </button>

                <button class="flex items-center gap-4 bg-[#2f3b26] p-5 rounded-xl shadow text-white">
                    <i class="fa-solid fa-list-check text-xl"></i>
                    <span class="font-medium">Excercise</span>
                </button>
            </div>
        </div>
    </div>
</body>
</html>
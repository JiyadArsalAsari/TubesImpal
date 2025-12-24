<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
        }
        
        .modal-content {
            background-color: #2f3d2c;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #3c4c39;
            border-radius: 1.5rem;
            width: 80%;
            max-width: 800px;
            max-height: 80vh;
            overflow-y: auto;
            box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover,
        .close:focus {
            color: white;
            text-decoration: none;
        }
    </style>
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
            <div class="flex items-center justify-center absolute left-1/2 transform -translate-x-1/2 logo-container" onclick="window.location.href='{{ route('mahasiswa.dashboard') }}'">
                <img src="{{ asset('logo.png') }}" class="w-20 h-20 filter brightness-0 invert" />
            </div>
            <div class="flex gap-6 text-3xl relative">
                <div class="relative cursor-pointer">
                    <i class="fa-regular fa-bell"></i>
                </div>
                <div class="cursor-pointer">
                    <i class="fa-solid fa-gear"></i>
                </div>
            </div>
        </header>

        <!-- Completed Exercises Modal -->
        <div id="completedExercisesModal" class="modal">
            <div class="modal-content">
                <span class="close">&times;</span>
                <h2 class="text-2xl font-bold mb-4">Completed Exercises</h2>
                <div id="completedExercisesList">
                    <!-- Completed exercises will be loaded here -->
                </div>
            </div>
        </div>

        <div class="max-w-6xl mx-auto px-6 py-12">
            <div class="bg-[#2f3d2c] rounded-3xl shadow-xl p-8 border border-[#3c4c39]">
                @php
                    $grouped = $exercises->groupBy(function ($item) {
                        return optional($item->deadline)?->format('l, d F Y') ?? 'Tanpa Deadline';
                    });
                @endphp

                @if($exercises->count() === 0)
                    <p class="text-center text-gray-200">Belum ada exercise dari dosen.</p>
                @else
                    <div class="space-y-8">
                        @foreach($grouped as $date => $items)
                            <div>
                                <p class="text-sm text-gray-300 uppercase tracking-wide mb-3">{{ $date }}</p>
                                <div class="space-y-4">
                                    @foreach($items as $item)
                                        <div class="bg-[#395035] rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border border-[#436040] shadow-lg">
                                            <div class="flex items-start gap-3 text-white">
                                                <div class="bg-[#4a6b46] text-white rounded-xl px-3 py-2 text-xs font-semibold uppercase">
                                                    {{ strtoupper($item->type) }}
                                                </div>
                                                <div>
                                                    <p class="font-semibold">{{ $item->title }}</p>
                                                    <p class="text-xs text-gray-200 mt-1">
                                                        Deadline: {{ optional($item->deadline)?->format('H:i') ?? '—' }}
                                                    </p>
                                                    @if($item->description)
                                                        <p class="text-xs text-gray-300 mt-1">{{ \Illuminate\Support\Str::limit($item->description, 120) }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                            @if($item->type === 'quiz')
                                                <a href="{{ route('mahasiswa.quiz.attempt', $item->id) }}" class="bg-[#202c23] text-white text-sm font-semibold px-4 py-2 rounded-full border border-[#6fbf69] hover:bg-[#26402d] transition text-center">
                                                    Attempt
                                                </a>
                                            @elseif($item->file_attachment)
                                                <a href="{{ route('mahasiswa.assignment.attempt', $item->id) }}" class="bg-[#202c23] text-white text-sm font-semibold px-4 py-2 rounded-full border border-[#6fbf69] hover:bg-[#26402d] transition text-center">
                                                    Attempt
                                                </a>
                                            @elseif($item->link)
                                                <a href="{{ $item->link }}" target="_blank" class="bg-[#202c23] text-white text-sm font-semibold px-4 py-2 rounded-full border border-[#6fbf69] hover:bg-[#26402d] transition text-center">
                                                    Attempt
                                                </a>
                                            @else
                                                <span class="text-xs text-gray-200">Tidak ada link</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-6 flex flex-col items-center gap-3">
                        <button class="text-sm font-semibold text-white underline decoration-dotted">Show More</button>
                        <button id="showCompletedBtn" class="bg-[#1d8f3b] hover:bg-[#167731] text-white font-semibold px-6 py-3 rounded-full shadow-lg transition">
                            Review Your Completed Quiz and Assignments
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <script>
        // Get modal elements
        const modal = document.getElementById("completedExercisesModal");
        const btn = document.getElementById("showCompletedBtn");
        const span = document.getElementsByClassName("close")[0];
        
        // When the user clicks the button, open the modal 
        btn.onclick = function() {
            modal.style.display = "block";
            loadCompletedExercises();
        }
        
        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }
        
        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }
        
        // Function to load completed exercises via AJAX
        function loadCompletedExercises() {
            fetch('{{ route('mahasiswa.completed.exercises.json') }}')
                .then(response => response.json())
                .then(data => {
                    displayCompletedExercises(data.exercises);
                })
                .catch(error => {
                    console.error('Error:', error);
                    document.getElementById('completedExercisesList').innerHTML = '<p>Error loading completed exercises.</p>';
                });
        }
        
        // Function to display completed exercises in the modal
        function displayCompletedExercises(exercises) {
            const container = document.getElementById('completedExercisesList');
            
            if (exercises.length === 0) {
                container.innerHTML = '<p class="text-center text-gray-200">You haven\'t completed any exercises yet.</p>';
                return;
            }
            
            // Group exercises by date
            const grouped = {};
            exercises.forEach(exercise => {
                const date = new Date(exercise.updated_at).toLocaleDateString('en-US', { 
                    weekday: 'long', 
                    year: 'numeric', 
                    month: 'long', 
                    day: 'numeric' 
                });
                
                if (!grouped[date]) {
                    grouped[date] = [];
                }
                grouped[date].push(exercise);
            });
            
            // Generate HTML for grouped exercises
            let html = '';
            for (const [date, items] of Object.entries(grouped)) {
                html += `
                    <div>
                        <p class="text-sm text-gray-300 uppercase tracking-wide mb-3">${date}</p>
                        <div class="space-y-4">
                `;
                
                items.forEach(item => {
                    const completedTime = new Date(item.updated_at).toLocaleTimeString('en-US', { 
                        hour: '2-digit', 
                        minute: '2-digit' 
                    });
                    
                    html += `
                        <div class="bg-[#395035] rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 border border-[#436040] shadow-lg">
                            <div class="flex items-start gap-3 text-white">
                                <div class="bg-[#4a6b46] text-white rounded-xl px-3 py-2 text-xs font-semibold uppercase">
                                    ${item.type.toUpperCase()}
                                </div>
                                <div>
                                    <p class="font-semibold">${item.title}</p>
                                    <p class="text-xs text-gray-200 mt-1">
                                        Completed: ${completedTime}
                                    </p>
                                    ${item.description ? `<p class="text-xs text-gray-300 mt-1">${item.description.substring(0, 120)}${item.description.length > 120 ? '...' : ''}</p>` : ''}
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="bg-green-600 text-white text-xs font-semibold px-3 py-1 rounded-full">
                                    COMPLETED
                                </span>
                            </div>
                        </div>
                    `;
                });
                
                html += `
                        </div>
                    </div>
                `;
            }
            
            container.innerHTML = html;
        }
    </script>
</body>
</html>


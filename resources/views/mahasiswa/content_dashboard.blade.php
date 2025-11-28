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

    <!-- Recommendation Content -->
    <div class="space-y-4">
        @if($recommendation)
            <!-- Existing Recommendation -->
            <div class="card-item p-6">
                <h3 class="text-white font-semibold text-xl mb-4">Rekomendasi Sebelumnya</h3>
                <div class="bg-gray-800 rounded-lg p-4 mb-4">
                    <p class="text-gray-300 whitespace-pre-line">{{ $recommendation->result }}</p>
                </div>
                <p class="text-gray-400 text-sm mb-4">Generated on: {{ $recommendation->created_at->format('d M Y, H:i') }}</p>
                <button id="generateBtn" class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-6 rounded-full inline-flex items-center transition">
                    <i class="fa-solid fa-refresh mr-2"></i>
                    Generate Ulang
                </button>
            </div>
        @else
            <!-- No Recommendation - Generate Form -->
            <div class="card-item p-8">
                <h3 class="text-white font-semibold text-xl mb-6 text-center">Generate Rekomendasi Belajar</h3>
                
                <form id="recommendationForm">
                    @csrf
                    <div class="mb-6">
                        <label class="block text-gray-300 mb-2">Mata Kuliah</label>
                        <input 
                            type="text" 
                            name="subject" 
                            placeholder="Masukkan mata kuliah" 
                            class="w-full px-4 py-3 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required
                        >
                    </div>
                    
                    <div class="mb-6">
                        <label class="block text-gray-300 mb-2">Deskripsi Kesulitan</label>
                        <textarea 
                            name="description" 
                            rows="4" 
                            placeholder="Jelaskan kesulitan yang Anda hadapi dalam belajar..." 
                            class="w-full px-4 py-3 rounded-lg bg-gray-700 text-white placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required
                        ></textarea>
                    </div>
                    
                    <button type="submit" id="submitBtn" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-3 px-6 rounded-full transition">
                        <i class="fa-solid fa-robot mr-2"></i>
                        Generate Rekomendasi dengan AI
                    </button>
                </form>
            </div>
        @endif
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('recommendationForm');
            const generateBtn = document.getElementById('generateBtn');
            
            if (form) {
                form.addEventListener('submit', function(e) {
                    e.preventDefault();
                    
                    const submitBtn = document.getElementById('submitBtn');
                    const originalText = submitBtn.innerHTML;
                    submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin mr-2"></i> Generating...';
                    submitBtn.disabled = true;
                    
                    const formData = new FormData(form);
                    
                    fetch('{{ route("mahasiswa.recommendation.generate") }}', {
                        method: 'POST',
                        body: formData,
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Reload the page to show the new recommendation
                            location.reload();
                        } else {
                            alert('Failed to generate recommendation: ' + data.message);
                            submitBtn.innerHTML = originalText;
                            submitBtn.disabled = false;
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('An error occurred while generating recommendation');
                        submitBtn.innerHTML = originalText;
                        submitBtn.disabled = false;
                    });
                });
            }
            
            if (generateBtn) {
                generateBtn.addEventListener('click', function() {
                    // Show the form to generate a new recommendation
                    location.reload();
                });
            }
        });
    </script>
@endsection
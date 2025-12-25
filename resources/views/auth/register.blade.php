<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyFlow - Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="h-screen flex">
    <div class="container flex w-full">
        <!-- LEFT SIDE -->
        <div class="w-1/2 bg-[#EDEEDF] flex items-center justify-center relative">
            <!-- Back Button -->
            <a href="{{ route('landing') }}" class="absolute top-8 left-8 inline-flex items-center gap-2 px-5 py-2.5 bg-[#485A48]/10 hover:bg-[#485A48] text-[#485A48] hover:text-white rounded-full transition-all duration-300 font-semibold group">
                <i class="fa-solid fa-arrow-left transition-transform group-hover:-translate-x-1"></i>
                <span>Back to Home</span>
            </a>

            <div class="logo-box text-center">
                <img src="{{ asset('logo.png') }}" class="logo-icon w-80 opacity-100 mx-auto">
            </div>
        </div>

        <!-- RIGHT SIDE -->
        <div class="w-1/2 bg-[#485A48] text-white flex flex-col justify-center px-16 relative">
                    <img src="{{ asset('line.png') }}" alt="Decorative Line" class="absolute top-0 left-0 w-full h-full object-cover opacity-20 z-0">
            <h2 class="title text-2xl font-bold text-center mb-8 relative z-10">USER REGISTER</h2>

            <form method="POST" action="{{ route('register') }}" class="register-form w-full relative z-10">
                @csrf
                <div class="input-group relative mb-5">
                    <span class="icon absolute top-1/2 left-3 transform -translate-y-1/2 text-xl opacity-70"><img src="{{ asset('logoUser.png') }}" class="w-5 h-5"></span>
                    <input type="text" name="nama" placeholder="Full Name" value="{{ old('nama') }}" required 
                        class="w-full pl-12 pr-4 py-3.5 rounded-lg focus:outline-none {{ $errors->has('nama') ? 'border-2 border-red-500 text-red-500 placeholder-red-400' : 'text-gray-700' }}">
                    @error('nama')
                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-red-500 text-xs font-bold">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input-group relative mb-5">
                    <span class="icon absolute top-1/2 left-3 transform -translate-y-1/2 text-xl opacity-70"><img src="{{ asset('logoMail.png') }}" class="w-5 h-5"></span>
                    <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required 
                        class="w-full pl-12 pr-4 py-3.5 rounded-lg focus:outline-none {{ $errors->has('email') ? 'border-2 border-red-500 text-red-500 placeholder-red-400' : 'text-gray-700' }}">
                    @error('email')
                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-red-500 text-xs font-bold">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input-group relative mb-5">
                    <span class="icon absolute top-1/2 left-3 transform -translate-y-1/2 text-xl opacity-70"><img src="{{ asset('logoUser.png') }}" class="w-5 h-5"></span>
                    <input type="text" name="username" placeholder="Username" value="{{ old('username') }}" required 
                        class="w-full pl-12 pr-4 py-3.5 rounded-lg focus:outline-none {{ $errors->has('username') ? 'border-2 border-red-500 text-red-500 placeholder-red-400' : 'text-gray-700' }}">
                    @error('username')
                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-red-500 text-xs font-bold">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input-group relative mb-5">
                    <span class="icon absolute top-1/2 left-3 transform -translate-y-1/2 text-xl opacity-70"><img src="{{ asset('logoPass.png') }}" class="w-5 h-5"></span>
                    <input type="password" name="password" placeholder="Password" required 
                        class="w-full pl-12 pr-4 py-3.5 rounded-lg focus:outline-none {{ $errors->has('password') ? 'border-2 border-red-500 text-red-500 placeholder-red-400' : 'text-gray-700' }}">
                    @error('password')
                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-red-500 text-xs font-bold">{{ $message }}</span>
                    @enderror
                </div>

                <div class="input-group relative mb-5">
                    <span class="icon absolute top-1/2 left-3 transform -translate-y-1/2 text-xl opacity-70"><img src="{{ asset('logoPass.png') }}" class="w-5 h-5"></span>
                    <input type="password" name="password_confirmation" placeholder="Confirm Password" required class="w-full pl-12 pr-4 py-3.5 rounded-lg text-gray-700 focus:outline-none">
                </div>

                <div class="input-group relative mb-5">
                    <span class="icon absolute top-1/2 left-3 transform -translate-y-1/2 text-xl opacity-70"><img src="{{ asset('logoRole.png') }}" class="w-5 h-5"></span>
                    <select name="role" required 
                        class="w-full pl-12 pr-4 py-3.5 rounded-lg focus:outline-none {{ $errors->has('role') ? 'border-2 border-red-500 text-red-500' : 'text-gray-700' }}">
                        <option value="" disabled selected>Select Role</option>
                        <option value="mahasiswa" {{ old('role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                        <option value="dosen" {{ old('role') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                    </select>
                    @error('role')
                        <span class="absolute right-4 top-1/2 transform -translate-y-1/2 text-red-500 text-xs font-bold">{{ $message }}</span>
                    @enderror
                </div>

                <button type="submit" class="btn-register w-32 mx-auto block py-2.5 bg-white text-[#2f372d] font-semibold rounded-full hover:opacity-90 transition-opacity cursor-pointer">Register</button>

                <p class="login text-center mt-4">Already have an account? <a href="{{ route('login') }}" class="text-white font-semibold no-underline">Login here</a></p>
            </form>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('input, select');
            
            inputs.forEach(input => {
                const clearError = function() {
                    // Remove error classes from input
                    this.classList.remove('border-2', 'border-red-500', 'text-red-500', 'placeholder-red-400');
                    this.classList.add('text-gray-700');
                    
                    // Hide the error message span within the same input-group
                    const errorSpan = this.parentElement.querySelector('span.text-red-500');
                    if (errorSpan) {
                        errorSpan.style.display = 'none';
                    }
                };

                input.addEventListener('input', clearError);
                if (input.tagName === 'SELECT') {
                    input.addEventListener('change', clearError);
                }
            });
        });
    </script>
</body>
</html>
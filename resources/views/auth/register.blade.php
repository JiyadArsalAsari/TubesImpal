<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyFlow - Register</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
        }
    </style>
</head>
<body class="h-screen flex">
    <div class="container flex w-full">
        <!-- LEFT SIDE -->
        <div class="w-1/2 bg-[#EDEEDF] flex items-center justify-center">
            <div class="logo-box text-center">
                <img src="https://cdn-icons-png.flaticon.com/512/326/326600.png" class="logo-icon w-32 opacity-75 mx-auto">
                <h1 class="logo-text text-5xl font-semibold mt-5 text-gray-700">StudyFlow</h1>
            </div>
        </div>

        <!-- RIGHT SIDE -->
        <div class="w-1/2 bg-[#2f372d] text-white flex flex-col justify-center px-16">
            <h2 class="title text-2xl font-bold text-center mb-8">USER REGISTER</h2>

            <form method="POST" action="{{ route('register') }}" class="register-form w-full">
                @csrf
                <div class="input-group relative mb-5">
                    <span class="icon absolute top-1/2 left-3 transform -translate-y-1/2 text-xl opacity-70">👤</span>
                    <input type="text" name="nama" placeholder="Full Name" value="{{ old('nama') }}" required class="w-full pl-12 pr-4 py-3.5 rounded-lg text-gray-700 focus:outline-none">
                    @error('nama')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="input-group relative mb-5">
                    <span class="icon absolute top-1/2 left-3 transform -translate-y-1/2 text-xl opacity-70">✉️</span>
                    <input type="email" name="email" placeholder="Email" value="{{ old('email') }}" required class="w-full pl-12 pr-4 py-3.5 rounded-lg text-gray-700 focus:outline-none">
                    @error('email')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="input-group relative mb-5">
                    <span class="icon absolute top-1/2 left-3 transform -translate-y-1/2 text-xl opacity-70">👤</span>
                    <input type="text" name="username" placeholder="Username" value="{{ old('username') }}" required class="w-full pl-12 pr-4 py-3.5 rounded-lg text-gray-700 focus:outline-none">
                    @error('username')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="input-group relative mb-5">
                    <span class="icon absolute top-1/2 left-3 transform -translate-y-1/2 text-xl opacity-70">🔒</span>
                    <input type="password" name="password" placeholder="Password" required class="w-full pl-12 pr-4 py-3.5 rounded-lg text-gray-700 focus:outline-none">
                    @error('password')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="input-group relative mb-5">
                    <span class="icon absolute top-1/2 left-3 transform -translate-y-1/2 text-xl opacity-70">🔒</span>
                    <input type="password" name="password_confirmation" placeholder="Confirm Password" required class="w-full pl-12 pr-4 py-3.5 rounded-lg text-gray-700 focus:outline-none">
                </div>

                <div class="input-group relative mb-5">
                    <span class="icon absolute top-1/2 left-3 transform -translate-y-1/2 text-xl opacity-70">👥</span>
                    <select name="role" required class="w-full pl-12 pr-4 py-3.5 rounded-lg text-gray-700 focus:outline-none">
                        <option value="" disabled selected>Select Role</option>
                        <option value="mahasiswa" {{ old('role') == 'mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                        <option value="dosen" {{ old('role') == 'dosen' ? 'selected' : '' }}>Dosen</option>
                    </select>
                    @error('role')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <button type="submit" class="btn-register w-32 mx-auto block py-2.5 bg-white text-[#2f372d] font-semibold rounded-full hover:opacity-90 transition-opacity cursor-pointer">Register</button>

                <p class="login text-center mt-4">Already have an account? <a href="{{ route('login') }}" class="text-white font-semibold no-underline">Login here</a></p>
            </form>
        </div>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyFlow - Login</title>
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

                <img src="{{ asset('logo.png') }}" class="logo-icon w-80 opacity-100 mx-auto">
            </div>
        </div>

        <!-- RIGHT SIDE -->


        <div class="w-1/2 bg-[#485A48] text-white flex flex-col justify-center px-16 relative">
            <img src="{{ asset('line.png') }}" alt="Decorative Line" class="absolute top-0 left-0 w-full h-full object-cover opacity-20 z-0">
            <h2 class="title text-2xl font-bold text-center mb-8 relative z-10">USER LOGIN</h2>

            <form method="POST" action="{{ route('login') }}" class="login-form w-full relative z-10">
                @csrf
                <div class="input-group relative mb-5">
                    <span class="icon absolute top-1/2 left-3 transform -translate-y-1/2 text-xl opacity-70"><img src="{{ asset('logoUser.png') }}" class="w-5 h-5"></span>
                    <input type="text" name="username_or_email" placeholder="Email or Username" value="{{ old('username_or_email') }}" required class="w-full pl-12 pr-4 py-3.5 rounded-lg text-gray-700 focus:outline-none">
                    @error('username_or_email')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <div class="input-group relative mb-5">
                    <span class="icon absolute top-1/2 left-3 transform -translate-y-1/2 text-xl opacity-70"><img src="{{ asset('logoPass.png') }}" class="w-5 h-5"></span>
                    <input type="password" name="password" placeholder="Password" required class="w-full pl-12 pr-4 py-3.5 rounded-lg text-gray-700 focus:outline-none">
                    @error('password')
                        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
                    @enderror
                </div>

                @if ($errors->has('username_or_email'))
                    <div class="text-red-500 text-sm mb-4 text-center">
                        {{ $errors->first('username_or_email') }}
                    </div>
                @endif

                <button type="submit" class="btn-login w-32 mx-auto block py-2.5 bg-white text-[#2f372d] font-semibold rounded-full hover:opacity-90 transition-opacity cursor-pointer">Login</button>

                
                <p class="register text-center mt-4">Don't have an account? <a href="{{ route('register') }}" class="text-white font-semibold no-underline">Register here</a></p>
            </form>
        </div>
    </div>
</body>
</html>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>StudyFlow - Platform Pembelajaran Mahasiswa & Dosen</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#1A3C2B', // Hijau Tua
                        secondary: '#F2F2EB', // Krem Lembut
                        accent: '#4C8456', // Hijau Lebih Terang
                        surface: '#FFFFFF',
                    },
                    fontFamily: {
                        sans: ['Poppins', 'sans-serif'],
                    }
                }
            }
        }
    </script>
    <style>
        .scroll-smooth {
            scroll-behavior: smooth;
        }
    </style>
</head>
<body class="bg-secondary font-sans text-gray-800 scroll-smooth antialiased overflow-x-hidden">

    <!-- Navbar (Sticky) -->
    <nav class="fixed w-full z-50 bg-primary/95 backdrop-blur-md border-b border-white/5 transition-all duration-300">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <!-- Logo -->
                <a href="#" class="flex-shrink-0 flex items-center gap-3 group">
                    <img src="{{ asset('logo.png') }}" alt="StudyFlow" class="h-10 w-auto brightness-0 invert opacity-90 group-hover:opacity-100 transition-opacity">
                    <span class="text-white font-medium text-xl tracking-wide opacity-90 group-hover:opacity-100 transition-opacity">StudyFlow</span>
                </a>

                <!-- Desktop Menu -->
                <div class="flex items-center space-x-6">
                    <!-- Sign In Dropdown -->
                    <div class="relative group">
                        <button class="text-gray-300 hover:text-white text-sm font-medium transition flex items-center gap-1 focus:outline-none py-2">
                            Masuk
                        </button>
                        <div class="absolute right-0 mt-1 w-48 bg-white rounded-lg shadow-xl py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 transform origin-top-right border border-gray-100">
                            <div class="px-4 py-2 border-b border-gray-50 text-xs font-semibold text-gray-400 uppercase tracking-wider">
                                Masuk Sebagai
                            </div>
                            <a href="{{ route('login') }}?role=mahasiswa" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-green-50 hover:text-primary transition-colors">
                                <i class="fas fa-user-graduate mr-2 w-5 text-accent"></i> Mahasiswa
                            </a>
                            <a href="{{ route('login') }}?role=dosen" class="block px-4 py-2.5 text-sm text-gray-700 hover:bg-green-50 hover:text-primary transition-colors">
                                <i class="fas fa-chalkboard-teacher mr-2 w-5 text-accent"></i> Dosen
                            </a>
                        </div>
                    </div>

                    <!-- Sign Up Button -->
                    <a href="{{ route('register') }}" class="bg-white/10 hover:bg-white/20 text-white border border-white/20 px-5 py-2 rounded-full text-sm font-medium transition-all backdrop-blur-sm hover:shadow-lg hover:-translate-y-0.5">
                        Daftar Sekarang
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative pt-32 pb-20 lg:pt-48 lg:pb-32">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
            
            <!-- Main Logo / Icon -->
            <div class="mb-8 flex justify-center animate-fade-in-up">
                <img src="{{ asset('logo.png') }}" alt="StudyFlow Logo" class="h-64 w-auto hover:scale-105 transition duration-500 drop-shadow-2xl">
            </div>

            <h1 class="text-4xl md:text-6xl font-bold text-primary mb-6 leading-tight">
                Welcome to <span class="text-accent">StudyFlow</span>
            </h1>
            
            <p class="mt-4 max-w-2xl mx-auto text-xl text-gray-600 mb-10 leading-relaxed">
                Platform pembelajaran terintegrasi untuk membantu mahasiswa dalam meningkatkan efektivitas proses belajar melalui pemanfaatan teknologi Artificial Intelligence (AI) dalam mengelola tugas, latihan, dan memantau progres belajar dengan lebih efektif dan terstruktur.
            </p>

            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('register') }}" class="px-8 py-4 bg-primary text-white rounded-full font-bold text-lg shadow-lg hover:bg-green-900 transition transform hover:-translate-y-1 hover:shadow-xl">
                    Mulai Belajar
                </a>
                <div class="relative group">
                     <button class="px-8 py-4 bg-white text-primary border-2 border-primary rounded-full font-bold text-lg shadow-sm hover:bg-green-50 transition transform hover:-translate-y-1">
                        Masuk Akun
                    </button>
                    <!-- Dropdown for Login Button in Hero -->
                    <div class="absolute left-1/2 transform -translate-x-1/2 mt-2 w-56 bg-white rounded-xl shadow-xl py-2 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-20 border border-gray-100">
                        <a href="{{ route('login') }}?role=mahasiswa" class="block px-4 py-3 text-gray-700 hover:bg-green-50 text-left">
                            <div class="font-semibold">Mahasiswa</div>
                            <div class="text-xs text-gray-500">Akses materi & tugas</div>
                        </a>
                        <div class="border-t border-gray-100"></div>
                        <a href="{{ route('login') }}?role=dosen" class="block px-4 py-3 text-gray-700 hover:bg-green-50 text-left">
                            <div class="font-semibold">Dosen</div>
                            <div class="text-xs text-gray-500">Kelola perkembangan & nilai</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Decorative Background Elements -->
        <div class="absolute top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none opacity-40">
            <div class="absolute -top-20 -left-20 w-96 h-96 bg-green-200 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
            <div class="absolute top-1/2 -right-20 w-72 h-72 bg-yellow-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
            <div class="absolute -bottom-20 left-1/3 w-80 h-80 bg-green-100 rounded-full mix-blend-multiply filter blur-3xl opacity-30"></div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="fitur-unggulan" class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-primary">Fitur Unggulan</h2>
                <p class="mt-4 text-gray-600 max-w-2xl mx-auto">Kami menyediakan berbagai fitur untuk mendukung proses pembelajaran yang optimal.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Feature 1 -->
                <div class="bg-secondary rounded-xl p-8 text-center hover:shadow-lg transition duration-300 border border-transparent hover:border-green-100">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm text-accent text-2xl">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Manajemen Jadwal</h3>
                    <p class="text-gray-600 text-sm">Kelola semua jadwal kuliah dengan rapi dan terorganisir dalam satu dashboard.</p>
                </div>

                <!-- Feature 2 -->
                <div class="bg-secondary rounded-xl p-8 text-center hover:shadow-lg transition duration-300 border border-transparent hover:border-green-100">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm text-accent text-2xl">
                        <i class="fas fa-clipboard-check"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Quiz & Assignment</h3>
                    <p class="text-gray-600 text-sm">Latihan soal interaktif untuk menguji pemahaman materi secara berkala.</p>
                </div>

                <!-- Feature 3 -->
                <div class="bg-secondary rounded-xl p-8 text-center hover:shadow-lg transition duration-300 border border-transparent hover:border-green-100">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm text-accent text-2xl">
                        <i class="fas fa-clock"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Pengingat Deadline</h3>
                    <p class="text-gray-600 text-sm">Notifikasi otomatis agar tidak ada tugas yang terlewat dari jadwal pengumpulan.</p>
                </div>

                <!-- Feature 4 -->
                <div class="bg-secondary rounded-xl p-8 text-center hover:shadow-lg transition duration-300 border border-transparent hover:border-green-100">
                    <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-6 shadow-sm text-accent text-2xl">
                        <i class="fas fa-chart-line"></i>
                    </div>
                    <h3 class="text-xl font-bold text-gray-800 mb-3">Monitoring Progress</h3>
                    <p class="text-gray-600 text-sm">Pantau perkembangan belajar Anda melalui grafik visual yang mudah dipahami.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Role Section -->
    <section class="py-20 bg-secondary relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-3xl font-bold text-primary">Solusi Untuk Semua</h2>
                <p class="mt-4 text-gray-600">Platform ini didesain untuk memenuhi kebutuhan Dosen dan Mahasiswa.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-12">
                <!-- Student Card -->
                <div class="bg-white rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition duration-300 transform hover:-translate-y-1 flex flex-col h-full">
                    <div class="h-2 bg-accent"></div>
                    <div class="p-8 flex-grow">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-primary text-xl">
                                <i class="fas fa-user-graduate"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-primary">Mahasiswa</h3>
                        </div>
                        <ul class="space-y-4 text-gray-600 mb-8">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-accent mt-1 mr-3"></i>
                                <span>Akses materi pembelajaran kapan saja</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-accent mt-1 mr-3"></i>
                                <span>Kerjakan tugas dan kuis secara online</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-accent mt-1 mr-3"></i>
                                <span>Lihat nilai dan feedback langsung dari dosen</span>
                            </li>
                        </ul>
                    </div>
                    <div class="p-8 pt-0 mt-auto">
                        <a href="{{ route('login') }}?role=mahasiswa" class="block w-full text-center py-3 border border-primary text-primary rounded-lg font-semibold hover:bg-primary hover:text-white transition">
                            Masuk sebagai Mahasiswa
                        </a>
                    </div>
                </div>

                <!-- Lecturer Card -->
                <div class="bg-white rounded-2xl overflow-hidden shadow-xl hover:shadow-2xl transition duration-300 transform hover:-translate-y-1 flex flex-col h-full">
                    <div class="h-2 bg-primary"></div>
                    <div class="p-8 flex-grow">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center text-primary text-xl">
                                <i class="fas fa-chalkboard-teacher"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-primary">Dosen</h3>
                        </div>
                        <ul class="space-y-4 text-gray-600 mb-8">
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-primary mt-1 mr-3"></i>
                                <span>Kelola tugas, kuis, dan penilaian otomatis</span>
                            </li>
                            <li class="flex items-start">
                                <i class="fas fa-check-circle text-primary mt-1 mr-3"></i>
                                <span>Pantau keaktifan dan progress mahasiswa</span>
                            </li>
                        </ul>
                    </div>
                    <div class="p-8 pt-0 mt-auto">
                        <a href="{{ route('login') }}?role=dosen" class="block w-full text-center py-3 bg-primary text-white rounded-lg font-semibold hover:bg-green-900 transition">
                            Masuk sebagai Dosen
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Preview / Mockup Section -->
    <section class="py-20 bg-white overflow-hidden">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-bold text-primary">Tampilan Dashboard Intuitif</h2>
                <p class="mt-4 text-gray-600">Desain antarmuka yang bersih memudahkan Anda fokus pada hal yang penting.</p>
            </div>
            
            <!-- Dashboard Tabs & Preview -->
            <div class="mt-12" x-data="{ tab: 'mahasiswa' }">
                <!-- Tab Navigation -->
                <div class="flex justify-center mb-8">
                    <div class="bg-secondary p-1 rounded-full inline-flex relative shadow-inner">
                        <div class="w-1/2 h-full absolute top-0 left-0 bg-primary rounded-full transition-all duration-300 transform"
                             :class="tab === 'mahasiswa' ? 'translate-x-0' : 'translate-x-full'"></div>
                        
                        <button @click="tab = 'mahasiswa'" 
                                class="relative z-10 px-8 py-3 rounded-full text-sm font-bold transition-colors duration-300 flex items-center gap-2"
                                :class="tab === 'mahasiswa' ? 'text-white' : 'text-gray-600 hover:text-primary'">
                            <i class="fas fa-user-graduate"></i> Tampilan Mahasiswa
                        </button>
                        <button @click="tab = 'dosen'" 
                                class="relative z-10 px-8 py-3 rounded-full text-sm font-bold transition-colors duration-300 flex items-center gap-2"
                                :class="tab === 'dosen' ? 'text-white' : 'text-gray-600 hover:text-primary'">
                            <i class="fas fa-chalkboard-teacher"></i> Tampilan Dosen
                        </button>
                    </div>
                </div>

                <!-- Tab Content -->
                <div class="relative mx-auto max-w-6xl">
                    <!-- Mahasiswa View -->
                    <div x-show="tab === 'mahasiswa'" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         class="bg-gray-900 rounded-xl shadow-2xl border-4 border-gray-800 p-2 overflow-hidden">
                        
                        <div class="w-full relative group rounded-lg overflow-hidden">
                            <img src="{{ asset('dashboard-mahasiswa.png') }}" alt="Dashboard Mahasiswa" class="w-full h-auto shadow-lg hover:scale-[1.01] transition duration-700">
                        </div>
                    </div>

                    <!-- Dosen View -->
                    <div x-show="tab === 'dosen'" 
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100"
                         style="display: none;"
                         class="bg-gray-900 rounded-xl shadow-2xl border-4 border-gray-800 p-2 overflow-hidden">
                        
                        <div class="w-full relative group rounded-lg overflow-hidden">
                            <img src="{{ asset('dashboard-dosen.png') }}" alt="Dashboard Dosen" class="w-full h-auto shadow-lg hover:scale-[1.01] transition duration-700">
                        </div>
                    </div>

                    <!-- Decorative Elements -->
                    <div class="absolute -z-10 -bottom-10 -right-10 w-64 h-64 bg-accent rounded-full opacity-20 filter blur-3xl animate-pulse"></div>
                    <div class="absolute -z-10 -top-10 -left-10 w-64 h-64 bg-primary rounded-full opacity-20 filter blur-3xl animate-pulse" style="animation-delay: 1s;"></div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-primary text-white pt-16 pb-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-12 mb-12">
                <div class="col-span-1 md:col-span-1">
                    <div class="flex items-center gap-3 mb-4">
                        <img src="{{ asset('logo.png') }}" alt="StudyFlow" class="h-10 w-auto brightness-0 invert opacity-90">
                        <span class="font-bold text-2xl tracking-wide">StudyFlow</span>
                    </div>
                    <p class="text-gray-300 text-sm leading-relaxed">
                        Platform pembelajaran modern untuk masa depan pendidikan yang lebih baik. Belajar efektif, terstruktur, dan menyenangkan.
                    </p>
                </div>
                
                <div>
                    <h4 class="font-bold text-lg mb-4">Fitur</h4>
                    <ul class="space-y-2 text-gray-300 text-sm">
                        <li><a href="#fitur-unggulan" class="hover:text-accent transition">Manajemen Jadwal</a></li>
                        <li><a href="#fitur-unggulan" class="hover:text-accent transition">Quiz & Assignment</a></li>
                        <li><a href="#fitur-unggulan" class="hover:text-accent transition">Pengingat Deadline</a></li>
                        <li><a href="#fitur-unggulan" class="hover:text-accent transition">Monitoring Progress</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-4">Tautan</h4>
                    <ul class="space-y-2 text-gray-300 text-sm">
                        <li><a href="{{ route('login') }}" class="hover:text-accent transition">Masuk</a></li>
                        <li><a href="{{ route('register') }}" class="hover:text-accent transition">Daftar</a></li>
                        <li><a href="#" class="hover:text-accent transition">Tentang Kami</a></li>
                    </ul>
                </div>

                <div>
                    <h4 class="font-bold text-lg mb-4">Follow Us</h4>
                    <div class="flex space-x-4">
                        <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-accent transition duration-300">
                            <i class="fab fa-github"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-accent transition duration-300">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="#" class="w-10 h-10 bg-white/10 rounded-full flex items-center justify-center hover:bg-accent transition duration-300">
                            <i class="fab fa-linkedin-in"></i>
                        </a>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-700 pt-8 text-center text-sm text-gray-400">
                <p>&copy; 2025 StudyFlow. All rights reserved. Created for IMPAL.</p>
            </div>
        </div>
    </footer>

</body>
</html>

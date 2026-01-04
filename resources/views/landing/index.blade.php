<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jaripos - Aplikasi Kasir UMKM Masakini</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- AOS Animation CSS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
    
    <!-- Lenis Smooth Scroll -->
    <link rel="stylesheet" href="https://unpkg.com/lenis@1.1.18/dist/lenis.css">

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#f0f9ff',
                            100: '#e0f2fe',
                            200: '#bae6fd',
                            300: '#7dd3fc',
                            400: '#38bdf8',
                            500: '#0ea5e9', // Ocean Blue Primary
                            600: '#0284c7',
                            700: '#0369a1',
                            800: '#075985',
                            900: '#0c4a6e',
                        },
                        secondary: {
                            900: '#0f172a', // Dark Slate
                        }
                    }
                }
            }
        }
    </script>

    <style>
        /* Custom Utilities */
        .glass-nav {
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        .text-gradient {
            background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .mockup-shadow {
            box-shadow: 0 25px 50px -12px rgba(14, 165, 233, 0.25);
        }
        
        /* 3D and Animations */
        .perspective-1000 {
            perspective: 1000px;
        }
        .transform-style-3d {
            transform-style: preserve-3d;
        }
        .backface-hidden {
            backface-visibility: hidden;
        }
        .rotate-y-12 {
            transform: rotateY(-12deg) rotateX(5deg);
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-20px); }
        }
        .animate-float {
            animation: float 6s ease-in-out infinite;
        }
        .animate-float-delayed {
            animation: float 6s ease-in-out 3s infinite;
        }
        
        @keyframes glow {
            0%, 100% { opacity: 0.5; transform: scale(1); }
            50% { opacity: 0.8; transform: scale(1.05); }
        }
        
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            opacity: 0.6;
        }
        .bg-grid-white {
            background-image: url("data:image/svg+xml,%3Csvg width='40' height='40' viewBox='0 0 40 40' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='%23ffffff' fill-opacity='0.1' fill-rule='evenodd'%3E%3Cpath d='M0 40L40 0H20L0 20M40 40V20L20 40'/%3E%3C/g%3E%3C/svg%3E");
        }
    </style>
</head>
<body class="font-sans text-slate-600 antialiased overflow-x-hidden bg-slate-900">

    <!-- Main Content Wrapper for Footer Reveal Effect -->
    <main class="relative z-10 bg-white shadow-2xl mb-[100vh]">

    <!-- Navbar -->
    <!-- Default state: Dark background (slate-900) matching Scalable Section, White Text -->
    <!-- Navbar -->
    <!-- Default state: Dark background (slate-900), White Text -->
    <nav id="navbar" class="fixed w-full z-50 transition-colors duration-500 ease-in-out bg-slate-900 border-b border-slate-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20">
                <!-- Logo -->
                <div class="flex-shrink-0 flex items-center gap-2 cursor-pointer transition-transform hover:scale-105" onclick="window.scrollTo(0,0)">
                    <div class="w-10 h-10 bg-gradient-to-br from-brand-500 to-brand-600 rounded-xl flex items-center justify-center text-white font-bold text-2xl shadow-lg shadow-brand-500/30">
                        J
                    </div>
                    <!-- Ensure text is white by default -->
                    <span id="logo-text" class="font-bold text-2xl text-white tracking-tight transition-colors duration-500">Jaripos</span>
                </div>

                <!-- Desktop Menu -->
                <div class="hidden md:flex space-x-8 items-center">
                    <a href="#features" class="nav-link text-slate-300 hover:text-white font-medium transition duration-300 hover:-translate-y-0.5">Fitur Unggulan</a>
                    <a href="#comparison" class="nav-link text-slate-300 hover:text-white font-medium transition duration-300 hover:-translate-y-0.5">Solusi</a>
                    <a href="#pricing" class="nav-link text-slate-300 hover:text-white font-medium transition duration-300 hover:-translate-y-0.5">Harga</a>
                    <a href="#cta" class="group relative inline-flex items-center justify-center overflow-hidden rounded-full px-6 py-2.5 font-semibold text-white bg-brand-600 duration-300 hover:bg-brand-700 shadow-lg shadow-brand-500/30">
                        <div class="absolute inset-0 flex h-full w-full justify-center [transform:skew(-12deg)_translateX(-100%)] group-hover:duration-1000 group-hover:[transform:skew(-12deg)_translateX(100%)]">
                            <div class="relative h-full w-8 bg-white/20"></div>
                        </div>
                        <span class="relative">Coba Gratis</span>
                    </a>
                </div>

                <!-- Mobile Menu Button -->
                <div class="md:hidden flex items-center">
                    <button id="mobile-menu-btn" class="text-slate-300 hover:text-white focus:outline-none p-2 transition-all duration-300">
                        <!-- Hamburger Icon -->
                        <svg id="hamburger-icon" class="h-8 w-8 transition-all duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                        </svg>
                        <!-- Close (X) Icon -->
                        <svg id="close-icon" class="h-8 w-8 hidden transition-all duration-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        
        <!-- Mobile Menu Panel -->
        <div id="mobile-menu" class="hidden md:hidden absolute w-full bg-slate-900 border-b border-slate-800 shadow-xl transition-colors duration-500">
            <div class="px-4 pt-2 pb-6 space-y-2">
                <a href="#features" class="mobile-nav-link block px-3 py-3 rounded-md text-base font-medium text-slate-300 hover:text-white hover:bg-slate-800">Fitur Unggulan</a>
                <a href="#comparison" class="mobile-nav-link block px-3 py-3 rounded-md text-base font-medium text-slate-300 hover:text-white hover:bg-slate-800">Solusi</a>
                <a href="#pricing" class="mobile-nav-link block px-3 py-3 rounded-md text-base font-medium text-slate-300 hover:text-white hover:bg-slate-800">Harga</a>
                <a href="#cta" class="block w-full text-center mt-4 px-6 py-3 rounded-lg text-white bg-brand-600 font-bold shadow-md">Coba Gratis Sekarang</a>
            </div>
        </div>
    </nav>
    
    <style>
        /* Navbar Theme Logic */
        /* Default is Dark (bg-slate-900) */
        
        /* Light Navbar State (Activated by JS when over dark sections) */
        nav.nav-light {
            background-color: rgba(255, 255, 255, 0.95) !important;
            border-bottom-color: rgba(226, 232, 240, 1) !important; /* slate-200 */
        }
        nav.nav-light #logo-text { color: #0f172a !important; /* slate-900 */ }
        nav.nav-light .nav-link { color: #475569 !important; /* slate-600 */ }
        nav.nav-light .nav-link:hover { color: #0ea5e9 !important; /* brand-600 */ }
        nav.nav-light #mobile-menu-btn { color: #475569 !important; }
        
        /* Mobile Menu Theme Sync for Light Mode */
        nav.nav-light #mobile-menu { background-color: white !important; border-bottom-color: #f1f5f9 !important; }
        nav.nav-light .mobile-nav-link { color: #334155 !important; }
        nav.nav-light .mobile-nav-link:hover { color: #0ea5e9 !important; background-color: #f0f9ff !important; }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const navbar = document.getElementById('navbar');
            
            // Explicitly selecting the Dark Sections by ID or Class
            // We want the navbar to be LIGHT when over these sections
            const darkSections = [
                document.getElementById('scalable'),
                document.getElementById('cta')
            ].filter(el => el !== null); // Filter out nulls just in case

            function updateNavbar() {
                let shouldBeLight = false;
                const navbarHeight = 80;
                
                darkSections.forEach(section => {
                     const rect = section.getBoundingClientRect();
                     // If the section overlaps with the top of the viewport (navbar area)
                     // Logic: Top of section is above navbar bottom, AND Bottom of section is below navbar top
                     if (rect.top <= navbarHeight && rect.bottom >= navbarHeight * 0.5) {
                         shouldBeLight = true;
                     }
                });

                if (shouldBeLight) {
                    navbar.classList.add('nav-light');
                } else {
                    navbar.classList.remove('nav-light');
                }
            }
            
            // Listen to scroll
            window.addEventListener('scroll', updateNavbar, { passive: true });
            
            // Initial check
            updateNavbar();
        });
    </script>

    <!-- Hero Section -->
    <section class="pt-32 pb-20 lg:pt-48 lg:pb-32 overflow-hidden bg-gradient-to-b from-brand-50 to-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative">
            
            <!-- Badge -->
            <div data-aos="fade-down" data-aos-delay="100" class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-white border border-brand-100 shadow-sm text-brand-600 text-sm font-semibold mb-8">
                <span class="flex h-2 w-2 rounded-full bg-brand-500 animate-pulse"></span>
                Solusi Terbaik untuk UMKM & Retail
            </div>

            <!-- Headline -->
            <h1 data-aos="fade-up" data-aos-delay="200" class="text-4xl md:text-7xl font-extrabold text-slate-900 leading-tight tracking-tight mb-6">
                Kelola Bisnis Jadi <br class="hidden md:block" />
                <span class="text-gradient">Lebih Menguntungkan</span>
            </h1>

            <!-- Subheadline -->
            <p data-aos="fade-up" data-aos-delay="300" class="text-md md:text-xl text-slate-500 mb-10 max-w-2xl mx-auto leading-relaxed">
                Tinggalkan cara lama yang rumit dan mahal. Jaripos memberikan kendali penuh atas stok, penjualan, dan keuntungan bisnis Anda dalam satu aplikasi.
            </p>

            <!-- CTA Buttons -->
            <div data-aos="fade-up" data-aos-delay="400" class="flex flex-col sm:flex-row justify-center gap-4 mb-16">
                <a href="#cta" class="group w-[75vw] md:w-auto mx-auto md:mx-0 relative inline-flex items-center justify-center overflow-hidden rounded-full px-8 py-4 font-bold text-white bg-brand-600 duration-300 hover:bg-brand-700 shadow-lg shadow-brand-500/40">
                    <div class="absolute inset-0 flex h-full w-full justify-center [transform:skew(-12deg)_translateX(-100%)] group-hover:duration-1000 group-hover:[transform:skew(-12deg)_translateX(100%)]">
                        <div class="relative h-full w-8 bg-white/20"></div>
                    </div>
                    <span class="mr-2 relative">Mulai Sekarang - Gratis</span>
                    <svg class="h-5 w-5 relative transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                </a>
                <a href="javascript:void(0)" id="video-modal-btn" class="w-[75vw] md:w-auto mx-auto md:mx-0 px-8 py-4 rounded-full text-slate-700 bg-white border border-slate-200 hover:border-brand-300 hover:bg-brand-50 transition font-semibold flex items-center justify-center gap-2 group">
                    <div class="w-8 h-8 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 group-hover:scale-110 transition">
                        <svg class="w-4 h-4 ml-0.5" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                    </div>
                    Tonton Demo
                </a>
            </div>

            <!-- 3D Mockup Container -->
            <div data-aos="fade-up" data-aos-delay="500" class="relative mx-auto max-w-6xl perspective-1000 mt-12">
                
                <!-- Floating Element 1 (Left) -->
                <div class="absolute -left-12 top-20 z-20 hidden lg:block animate-float">
                    <div class="bg-white p-4 rounded-2xl shadow-2xl border border-slate-100 w-64 transform -rotate-6 transition hover:scale-105 duration-300">
                        <div class="flex items-center gap-3 mb-3">
                            <div class="w-10 h-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-xs text-slate-400 font-bold uppercase">Profit Hari Ini</p>
                                <p class="text-lg font-bold text-slate-900">+Rp 2.500.000</p>
                            </div>
                        </div>
                        <div class="w-full bg-slate-100 h-1 rounded-full overflow-hidden">
                            <div class="bg-green-500 h-full w-[85%]"></div>
                        </div>
                    </div>
                </div>

                <!-- Floating Element 2 (Right) -->
                 <div class="absolute -right-8 bottom-32 z-20 hidden lg:block animate-float-delayed">
                    <div class="bg-white p-4 rounded-2xl shadow-2xl border border-slate-100 w-56 transform rotate-6 transition hover:scale-105 duration-300">
                        <div class="flex items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name=Budi+Santoso&background=0ea5e9&color=fff" class="w-10 h-10 rounded-full border-2 border-white shadow-sm" alt="User">
                            <div>
                                <p class="text-sm font-bold text-slate-900">Order Baru!</p>
                                <p class="text-xs text-slate-500">2x Kopi Susu, 1x Roti</p>
                            </div>
                        </div>
                         <div class="mt-3 flex gap-2">
                             <span class="text-[10px] bg-brand-50 text-brand-600 px-2 py-1 rounded font-bold">Meja 4</span>
                             <span class="text-[10px] bg-green-50 text-green-600 px-2 py-1 rounded font-bold">Lunas</span>
                         </div>
                    </div>
                </div>

                <!-- Main Mockup -->
                <div class="relative bg-slate-900 rounded-2xl p-2 md:p-3 shadow-2xl mockup-shadow border border-slate-800 transform-style-3d lg:rotate-y-12 transition-transform duration-500 hover:rotate-0">
                     <!-- Reflection/Glass Effect -->
                     <div class="absolute inset-0 bg-gradient-to-tr from-white/10 to-transparent pointer-events-none rounded-2xl z-10"></div>

                    <!-- Browser Chrome -->
                    <div class="h-8 flex items-center gap-2 px-4 mb-2">
                        <div class="w-3 h-3 rounded-full bg-red-500"></div>
                        <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                        <div class="w-3 h-3 rounded-full bg-green-500"></div>
                    </div>
                    <!-- App Content -->
                    <div class="bg-slate-50 rounded-xl overflow-hidden shadow-inner flex flex-col md:flex-row min-h-[400px]">
                        <!-- Sidebar -->
                        <div class="w-full md:w-20 bg-white border-r border-slate-200 hidden md:flex flex-col items-center py-6 gap-6">
                            <div class="w-10 h-10 bg-brand-100 text-brand-600 rounded-lg flex items-center justify-center"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg></div>
                            <div class="w-10 h-10 text-slate-400 hover:bg-slate-50 rounded-lg flex items-center justify-center"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg></div>
                            <div class="w-10 h-10 text-slate-400 hover:bg-slate-50 rounded-lg flex items-center justify-center"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg></div>
                        </div>
                        <!-- Main Area -->
                        <div class="flex-1 p-6 text-left">
                            <div class="flex justify-between items-center mb-8">
                                <div>
                                    <h3 class="text-xl font-bold text-slate-900">Dashboard Penjualan</h3>
                                    <p class="text-slate-400 text-sm">Selasa, 30 Des 2025</p>
                                </div>
                                <div class="bg-brand-50 text-brand-600 px-4 py-2 rounded-lg text-sm font-bold">+ Export Laporan</div>
                            </div>
                            <!-- Stats Grid -->
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
                                <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100">
                                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Total Omset</p>
                                    <p class="text-2xl font-bold text-slate-900 mt-1">Rp 12.500.000</p>
                                    <p class="text-green-500 text-xs mt-2 flex items-center gap-1 font-semibold">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                        +12% hari ini
                                    </p>
                                </div>
                                <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100">
                                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Transaksi</p>
                                    <p class="text-2xl font-bold text-slate-900 mt-1">142</p>
                                    <p class="text-green-500 text-xs mt-2 flex items-center gap-1 font-semibold">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                                        +5% hari ini
                                    </p>
                                </div>
                                <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100">
                                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">Produk Terlaris</p>
                                    <p class="text-xl font-bold text-slate-900 mt-1 truncate">Kopi Susu Gula Aren</p>
                                    <p class="text-brand-500 text-xs mt-2 font-semibold">54 terjual</p>
                                </div>
                            </div>
                            <!-- Chart Placeholder -->
                            <div class="bg-white p-4 rounded-xl shadow-sm border border-slate-100 h-32 flex items-end justify-between px-4 pb-2 items-end gap-2">
                                <div class="w-full bg-brand-50 rounded-t h-[40%]"></div>
                                <div class="w-full bg-brand-100 rounded-t h-[60%]"></div>
                                <div class="w-full bg-brand-200 rounded-t h-[30%]"></div>
                                <div class="w-full bg-brand-300 rounded-t h-[80%]"></div>
                                <div class="w-full bg-brand-400 rounded-t h-[50%]"></div>
                                <div class="w-full bg-brand-500 rounded-t h-[90%] relative group">
                                    <div class="absolute -top-8 left-1/2 -translate-x-1/2 bg-slate-900 text-white text-[10px] py-1 px-2 rounded opacity-0 group-hover:opacity-100 transition">Highest</div>
                                </div>
                                <div class="w-full bg-brand-200 rounded-t h-[70%]"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Decorative Blobs -->
                <div class="absolute -top-20 -right-20 w-72 h-72 bg-brand-400 rounded-full blur-3xl opacity-40 -z-10 animate-pulse"></div>
                <div class="absolute -bottom-20 -left-20 w-72 h-72 bg-purple-500 rounded-full blur-3xl opacity-40 -z-10 animate-pulse"></div>
            </div>
        </div>
    </section>

    <!-- Problem vs Solution Section -->
    <section id="comparison" class="py-20 bg-slate-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div  class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl font-bold text-slate-900 mb-4">Mengapa Pindah ke Jaripos?</h2>
                <p class="text-slate-500 max-w-2xl mx-auto">Kami mengerti frustrasi Anda dengan sistem lama. Inilah perbedaannya.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-5xl mx-auto">
                <!-- Bad Option -->
                <div data-aos="fade-right" class="bg-white p-8 rounded-2xl border border-slate-200 shadow-sm opacity-80 hover:opacity-100 transition">
                    <div class="flex items-center gap-4 mb-6 pb-6 border-b border-slate-100">
                        <div class="w-12 h-12 bg-slate-100 rounded-lg flex items-center justify-center text-slate-400">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-400">Sistem POS Lama</h3>
                    </div>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3 text-slate-500">
                            <svg class="w-5 h-5 text-red-400 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Biaya langganan mahal & ada biaya tersembunyi.</span>
                        </li>
                        <li class="flex items-start gap-3 text-slate-500">
                            <svg class="w-5 h-5 text-red-400 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Interface rumit, butuh training lama.</span>
                        </li>
                         <li class="flex items-start gap-3 text-slate-500">
                            <svg class="w-5 h-5 text-red-400 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Harus beli hardware khusus yang mahal.</span>
                        </li>
                        <li class="flex items-start gap-3 text-slate-500">
                            <svg class="w-5 h-5 text-red-400 mt-1 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span>Support lambat, hanya via email.</span>
                        </li>
                    </ul>
                </div>

                <!-- Good Option (Jaripos) -->
                <div data-aos="fade-left" class="bg-white p-8 rounded-2xl border-2 border-brand-500 shadow-2xl relative transform md:-translate-y-4 z-10">
                    <div class="absolute top-0 right-0 bg-brand-500 text-white text-xs font-bold px-3 py-1 rounded-bl-lg rounded-tr-lg">RECOMMENDED</div>
                    <div class="flex items-center gap-4 mb-6 pb-6 border-b border-slate-100">
                        <div class="w-12 h-12 bg-brand-600 rounded-lg flex items-center justify-center text-white shadow-lg shadow-brand-500/30">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        </div>
                        <h3 class="text-2xl font-bold text-slate-900">Jaripos Smart POS</h3>
                    </div>
                    <ul class="space-y-4">
                        <li class="flex items-start gap-3 text-slate-700">
                            <div class="mt-1 w-5 h-5 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 flex-shrink-0">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="font-medium">Sangat terjangkau, transparan, tanpa biaya hantu.</span>
                        </li>
                        <li class="flex items-start gap-3 text-slate-700">
                            <div class="mt-1 w-5 h-5 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 flex-shrink-0">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="font-medium">User-friendly, kasir baru bisa langsung pakai dalam 5 menit.</span>
                        </li>
                        <li class="flex items-start gap-3 text-slate-700">
                            <div class="mt-1 w-5 h-5 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 flex-shrink-0">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="font-medium">Bisa pakai HP/Tablet Android & Printer Bluetooth apa saja.</span>
                        </li>
                        <li class="flex items-start gap-3 text-slate-700">
                            <div class="mt-1 w-5 h-5 rounded-full bg-brand-100 flex items-center justify-center text-brand-600 flex-shrink-0">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <span class="font-medium">Support 24/7 via WhatsApp, respon cepat.</span>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section with Glassmorphism -->
    <section id="features" class="py-24 relative overflow-hidden">
        <!-- Background Elements -->
        <div class="absolute inset-0 "></div>
        <div class="absolute top-0 right-0 w-1/2 h-1/2 bg-gradient-to-br from-brand-100/40 to-transparent rounded-bl-full blur-3xl opacity-50 pointer-events-none"></div>
        <div class="absolute bottom-0 left-0 w-1/2 h-1/2 bg-gradient-to-tr from-purple-100/40 to-transparent rounded-tr-full blur-3xl opacity-50 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div  class="text-center mb-16" data-aos="fade-up">
                <span class="text-brand-600 font-bold tracking-wider uppercase text-sm bg-brand-50 px-3 py-1 rounded-full border border-brand-100">Fitur Lengkap</span>
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mt-4">Semua yang Anda Butuhkan</h2>
                <p class="text-slate-500 mt-4 max-w-2xl mx-auto">Kami menyederhanakan teknologi canggih menjadi fitur yang mudah digunakan oleh siapa saja.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Feature 1 -->
                <div data-aos="fade-up" data-aos-delay="100" class="p-8 bg-white/60 backdrop-blur-lg rounded-2xl border border-white/50 shadow-lg shadow-slate-200/50 hover:shadow-brand-500/10 hover:border-brand-200 hover:-translate-y-1 transition-all duration-300 group relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-brand-50 rounded-bl-full -mr-10 -mt-10 transition-transform group-hover:scale-110"></div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 bg-gradient-to-br from-brand-50 to-brand-100 rounded-xl flex items-center justify-center text-brand-600 mb-6 shadow-sm group-hover:scale-110 transition duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Kasir Tercepat</h3>
                        <p class="text-slate-500 text-sm leading-relaxed">Input order hitungan detik. Scan barcode, pilih varian, checkout. Pelanggan tak perlu antre.</p>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div data-aos="fade-up" data-aos-delay="200" class="p-8 bg-white/60 backdrop-blur-lg rounded-2xl border border-white/50 shadow-lg shadow-slate-200/50 hover:shadow-brand-500/10 hover:border-purple-200 hover:-translate-y-1 transition-all duration-300 group relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-purple-50 rounded-bl-full -mr-10 -mt-10 transition-transform group-hover:scale-110"></div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-50 to-purple-100 rounded-xl flex items-center justify-center text-purple-600 mb-6 shadow-sm group-hover:scale-110 transition duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Manajemen Stok</h3>
                        <p class="text-slate-500 text-sm leading-relaxed">Stok berkurang otomatis saat terjual. Peringatan otomatis saat stok menipis. Anti boncos.</p>
                    </div>
                </div>

                <!-- Feature 3 -->
                <div data-aos="fade-up" data-aos-delay="300" class="p-8 bg-white/60 backdrop-blur-lg rounded-2xl border border-white/50 shadow-lg shadow-slate-200/50 hover:shadow-brand-500/10 hover:border-green-200 hover:-translate-y-1 transition-all duration-300 group relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-green-50 rounded-bl-full -mr-10 -mt-10 transition-transform group-hover:scale-110"></div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 bg-gradient-to-br from-green-50 to-green-100 rounded-xl flex items-center justify-center text-green-600 mb-6 shadow-sm group-hover:scale-110 transition duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Laporan Otomatis</h3>
                        <p class="text-slate-500 text-sm leading-relaxed">Laba rugi, arus kas, dan laporan penjualan harian tersaji instan. Tak perlu rekap manual lagi.</p>
                    </div>
                </div>

                 <!-- Feature 4 -->
                 <div data-aos="fade-up" data-aos-delay="400" class="p-8 bg-white/60 backdrop-blur-lg rounded-2xl border border-white/50 shadow-lg shadow-slate-200/50 hover:shadow-brand-500/10 hover:border-orange-200 hover:-translate-y-1 transition-all duration-300 group relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-20 h-20 bg-orange-50 rounded-bl-full -mr-10 -mt-10 transition-transform group-hover:scale-110"></div>
                    <div class="relative z-10">
                        <div class="w-14 h-14 bg-gradient-to-br from-orange-50 to-orange-100 rounded-xl flex items-center justify-center text-orange-600 mb-6 shadow-sm group-hover:scale-110 transition duration-300">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold text-slate-900 mb-2">Multi Perangkat</h3>
                        <p class="text-slate-500 text-sm leading-relaxed">Pantau bisnis dari rumah, kantor, atau saat liburan. Data tersinkronisasi realtime di cloud.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Scalable Section -->
    <section id="scalable" class="dark-section min-h-screen flex items-center py-20 bg-slate-900 text-white overflow-hidden relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-20">
            <div class="flex flex-col lg:flex-row items-center gap-24">
                <div class="lg:w-1/2" data-aos="fade-right">
                    <h2 class="text-4xl md:text-6xl font-bold mb-8 leading-tight">Mulai dari Kecil, <br><span class="text-brand-400">Tumbuh Raksasa</span></h2>
                    <p class="text-slate-400 text-md md:text-xl mb-12 leading-relaxed max-w-2xl">
                        Jaripos didesain untuk skalabilitas. Mulai dari satu gerobak kopi, hingga ratusan cabang franchise, sistem kami siap menangani jutaan transaksi tanpa hambatan.
                    </p>
                    
                    <div class="space-y-8">
                        <div class="flex items-center gap-6 group hover:translate-x-2 transition duration-300">
                            <div class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center font-bold text-brand-400 group-hover:bg-brand-600 group-hover:text-white transition">1</div>
                            <div>
                                <h4 class="font-bold text-xl group-hover:text-brand-400 transition">UMKM Rintisan</h4>
                                <p class="text-slate-500 text-base">Fitur dasar lengkap, gratis selamanya.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-6 group hover:translate-x-2 transition duration-300">
                            <div class="w-12 h-12 rounded-full bg-slate-800 flex items-center justify-center font-bold text-brand-400 group-hover:bg-brand-600 group-hover:text-white transition">2</div>
                            <div>
                                <h4 class="font-bold text-xl group-hover:text-brand-400 transition">Berkembang (Growing)</h4>
                                <p class="text-slate-500 text-base">Manajemen karyawan, stok gudang, dan CRM.</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-6 group hover:translate-x-2 transition duration-300">
                            <div class="w-12 h-12 rounded-full bg-brand-600 flex items-center justify-center font-bold text-white shadow-lg shadow-brand-500/50 scale-110">3</div>
                            <div>
                                <h4 class="font-bold text-xl text-white">Enterprise Scale</h4>
                                <p class="text-slate-500 text-base">API access, custom integration, dedicated server.</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="lg:w-1/2 relative w-full" data-aos="fade-left">
                     <!-- Abstract Visualization of growth -->
                     <div class="grid grid-cols-2 gap-6">
                        <div class="space-y-6 pt-12">
                             <div class="bg-slate-800/50 backdrop-blur-sm p-4 md:p-8 rounded-3xl border border-slate-700 hover:border-brand-500 transition duration-500 hover:-translate-y-2">
                                 <div class="text-3xl md:text-5xl font-bold text-white mb-2">100+</div>
                                 <div class="text-sm md:text-base text-slate-400">Cabang Terintegrasi</div>
                             </div>
                             <div class="bg-slate-800/50 backdrop-blur-sm p-4 md:p-8 rounded-3xl border border-slate-700 hover:border-brand-500 transition duration-500 hover:-translate-y-2">
                                <div class="text-3xl md:text-5xl font-bold text-brand-400 mb-2">0%</div>
                                <div class="text-sm md:text-base text-slate-400">Data Hilang</div>
                            </div>
                        </div>
                        <div class="space-y-6">
                            <div class="bg-gradient-to-br from-brand-600 to-brand-800 p-4 md:p-8 rounded-3xl border border-brand-500 shadow-2xl shadow-brand-500/20 hover:-translate-y-2 transition duration-500">
                                <div class="text-3xl md:text-5xl font-bold text-white mb-2">99.9%</div>
                                <div class="text-sm md:text-base text-brand-100">Uptime SLA</div>
                            </div>
                            <div class="bg-slate-800/50 backdrop-blur-sm p-4 md:p-8 rounded-3xl border border-slate-700 hover:border-brand-500 transition duration-500 hover:-translate-y-2">
                                <div class="text-3xl md:text-5xl font-bold text-white mb-2">24/7</div>
                                <div class="text-sm md:text-base text-slate-400">Priority Support</div>
                            </div>
                        </div>
                     </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Pricing Section -->
    <section id="pricing" class="py-24 bg-white relative">
        <div class="absolute inset-0 bg-[radial-gradient(#e5e7eb_1px,transparent_1px)] [background-size:16px_16px] [mask-image:radial-gradient(ellipse_50%_50%_at_50%_50%,#000_70%,transparent_100%)] opacity-30"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center mb-16" data-aos="fade-up">
                <h2 class="text-3xl md:text-4xl font-bold text-slate-900 mb-4">Investasi Receh, Untung Gede</h2>
                <p class="text-slate-500 text-md md:text-lg max-w-2xl mx-auto">Pilih paket yang pas buat tokomu. Tidak ada biaya tersembunyi.</p>
                <!-- Toggle (Visual Only) -->
                <!-- Toggle (Functional) -->
                <div class="mt-8 flex justify-center items-center gap-3">
                    <span class="text-sm font-semibold text-slate-500 cursor-pointer" onclick="$('#pricing-toggle').click()">Bulanan</span>
                    <button id="pricing-toggle" class="w-14 h-8 bg-brand-600 rounded-full p-1 flex items-center transition-colors duration-300 focus:outline-none">
                        <div id="toggle-circle" class="w-6 h-6 bg-white rounded-full shadow-md transform translate-x-6 transition-transform duration-300"></div>
                    </button>
                    <span class="text-sm font-bold text-slate-900 cursor-pointer" onclick="$('#pricing-toggle').click()">Tahunan <span class="text-brand-500 text-xs ml-1">(Hemat 33%)</span></span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 max-w-6xl mx-auto items-center">
                
                <!-- Starter -->
                <div data-aos="fade-up" data-aos-delay="100" class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <h3 class="text-xl font-bold text-slate-900">Starter</h3>
                    <p class="text-slate-400 text-sm mt-2 mb-6">Untuk usaha rumahan baru.</p>
                    <div class="flex items-baseline mb-6">
                        <span class="text-4xl font-extrabold text-slate-900">Gratis</span>
                    </div>
                    <a href="#" class="block w-full py-3 px-4 bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold text-center rounded-xl transition duration-300 mb-8">Daftar Sekarang</a>
                    <ul class="space-y-4 text-sm text-slate-600">
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> 1 Outlet</li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> 100 Produk</li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Laporan Harian Sederhana</li>
                    </ul>
                </div>

                <!-- Business (Featured) -->
                <div data-aos="fade-up" data-aos-delay="200" class="relative bg-slate-900 rounded-3xl p-8 md:p-10 border border-slate-700 shadow-2xl transform md:scale-105 z-10 overflow-hidden group">
                    <!-- Glassmorphism BG Inner -->
                    <div class="absolute inset-0 bg-gradient-to-br from-brand-600/20 to-purple-600/20 backdrop-blur-sm pointer-events-none"></div>
                    <div class="absolute top-0 right-0 bg-gradient-to-l from-brand-500 to-purple-500 text-white text-xs font-bold px-4 py-1.5 rounded-bl-xl shadow-lg">PALING LARIS</div>
                    
                    <div class="relative z-10">
                        <h3 class="text-2xl font-bold text-white">Pro Business</h3>
                        <p class="text-slate-300 text-sm mt-2 mb-6">Wajib buat yang mau scale-up.</p>
                        <div class="flex items-baseline mb-6">
                            <span id="price-pro" class="text-5xl font-extrabold text-white">99k</span>
                            <span id="price-period" class="text-slate-400 font-small ml-2">/ bulan</span>
                        </div>
                        <a href="#" class="group relative inline-flex w-full items-center justify-center overflow-hidden rounded-xl bg-gradient-to-r from-brand-500 to-brand-600 py-4 px-6 font-bold text-white shadow-lg shadow-brand-500/30 duration-300 hover:from-brand-400 hover:to-brand-500">
                             <div class="absolute inset-0 flex h-full w-full justify-center [transform:skew(-12deg)_translateX(-100%)] group-hover:duration-1000 group-hover:[transform:skew(-12deg)_translateX(100%)]">
                                <div class="relative h-full w-8 bg-white/20"></div>
                            </div>
                            <span class="relative">Coba Gratis 14 Hari</span>
                        </a>
                        <ul class="space-y-4 text-sm text-slate-300 mt-8">
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Unlimited Outlet & Produk</li>
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Manajemen Stok Lengkap</li>
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Laporan Laba/Rugi Realtime</li>
                            <li class="flex items-center gap-3"><svg class="w-5 h-5 text-brand-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Manajemen Pelanggan (CRM)</li>
                        </ul>
                    </div>
                </div>

                <!-- Enterprise -->
                <div data-aos="fade-up" data-aos-delay="300" class="bg-white rounded-2xl p-8 border border-slate-200 shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
                    <h3 class="text-xl font-bold text-slate-900">Enterprise</h3>
                    <p class="text-slate-400 text-sm mt-2 mb-6">Untuk franchise & korporat.</p>
                    <div class="flex items-baseline mb-6">
                        <span class="text-4xl font-extrabold text-slate-900">Custom</span>
                    </div>
                    <a href="#" class="block w-full py-3 px-4 bg-white border-2 border-slate-200 hover:border-brand-300 hover:text-brand-600 text-slate-700 font-bold text-center rounded-xl transition duration-300 mb-8">Hubungi Sales</a>
                    <ul class="space-y-4 text-sm text-slate-600">
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Dedicated Account Manager</li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> Custom API Integration</li>
                        <li class="flex items-center gap-3"><svg class="w-5 h-5 text-brand-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg> White Label Option</li>
                    </ul>
                </div>

            </div>
        </div>
    </section>
    
    <!-- CTA Section -->
    <section id="cta" class="dark-section py-24 bg-brand-600 relative overflow-hidden">
        <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10"></div>
        <div class="max-w-4xl mx-auto px-4 text-center relative z-10">
            <h2 data-aos="zoom-in" class="text-4xl md:text-5xl font-bold text-white mb-8 leading-tight">
                Siap Mengubah Bisnis Anda?
            </h2>
            <p data-aos="fade-up" data-aos-delay="100" class="text-brand-100 text-md md:text-xl mb-12 max-w-2xl mx-auto">
                Bergabunglah dengan 5,000+ pemilik bisnis cerdas yang telah beralih ke Jaripos.
            </p>
            <div data-aos="fade-up" data-aos-delay="200" class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="/login" class="group w-[75vw] md:w-auto mx-auto md:mx-0 relative inline-flex items-center justify-center overflow-hidden rounded-full px-10 py-5 font-bold text-brand-600 bg-white border-2 border-white duration-300 hover:bg-transparent hover:text-white shadow-2xl">
                    <div class="absolute inset-0 flex h-full w-full justify-center [transform:skew(-12deg)_translateX(-100%)] group-hover:duration-1000 group-hover:[transform:skew(-12deg)_translateX(100%)]">
                        <div class="relative h-full w-8 bg-white/20"></div>
                    </div>
                    <span class="relative">Daftar Sekarang - Gratis</span>
                </a>
                <a href="https://wa.me/6281234567890" target="_blank" class="w-[75vw] md:w-auto mx-auto md:mx-0 px-10 py-5 bg-transparent border-2 border-white text-white font-bold text-lg rounded-full hover:bg-white/10 transition flex items-center justify-center gap-2">
                    <i class="fab fa-whatsapp text-2xl"></i>
                    Hubungi Sales
                </a>
            </div>
            <p class="text-brand-200 text-sm mt-8 opacity-80">
                Tanpa kartu kredit. Batalkan kapan saja.
            </p>
        </div>
    </section>

    </main>

    <!-- Footer Reveal (Fixed, Full Screen) -->
    <!-- Footer Reveal (Fixed, Height 75vh) -->
    <footer class="fixed bottom-0 left-0 w-full h-[100dvh] z-0 bg-slate-900 flex flex-col justify-end pb-12 items-center text-slate-400 overflow-hidden">
        
        <!-- Animated Mesh Gradient Background -->
        <div class="absolute inset-0 opacity-40">
            <div class="absolute top-[-50%] left-[-50%] w-[200%] h-[200%] bg-[radial-gradient(circle_farthest-corner_at_center,_var(--tw-gradient-stops))] from-indigo-900/40 via-slate-950 to-slate-950 animate-spin-slow"></div>
            <div class="absolute top-1/4 left-1/4 w-96 h-96 bg-brand-600/20 rounded-full blur-[100px] animate-pulse"></div>
            <div class="absolute bottom-1/4 right-1/4 w-[500px] h-[500px] bg-purple-600/20 rounded-full blur-[120px] animate-pulse" style="animation-duration: 4s;"></div>
        </div>

        <!-- Grid Pattern Overlay -->
        <div class="absolute inset-0 bg-[url('https://grainy-gradients.vercel.app/noise.svg')] opacity-20 mix-blend-soft-light"></div>
        <div class="absolute inset-0 bg-[linear-gradient(rgba(255,255,255,0.03)_1px,transparent_1px),linear-gradient(90deg,rgba(255,255,255,0.03)_1px,transparent_1px)] bg-[size:100px_100px] [mask-image:radial-gradient(ellipse_60%_60%_at_50%_50%,#000_70%,transparent_100%)]"></div>


        <div class="max-w-[90%] w-full relative z-10 flex flex-col h-full justify-between">
            
            <!-- Spacer with Title inside for perfect centering between top and bottom content -->
            <div class="flex-grow relative">
                <!-- Big Bold Brand (Absolute Center of Spacer) -->
                <div class="absolute md:pt-32 2xl:pt-56 inset-0 flex items-center justify-center pointer-events-none z-0" data-aos="zoom-in" data-aos-duration="1000">
                    <div class="relative w-full text-center">
                        <!-- Layer 1: Stroke/Border (Bottom Visibility) -->
                        <h1 class="text-[20vw] font-black leading-none tracking-tighter cursor-default select-none font-['Outfit'] absolute left-0 right-0 top-1/2 -translate-y-1/2 text-transparent" style="-webkit-text-stroke: 2px rgba(255,255,255,0.15);">
                            JARIPOS
                        </h1>
                        
                        <!-- Layer 2: Gradient Fill (Top Visibility) -->
                        <h1 class="text-[20vw] font-black leading-none tracking-tighter cursor-default select-none font-['Outfit'] absolute left-0 right-0 top-1/2 -translate-y-1/2 text-transparent bg-clip-text bg-gradient-to-b from-white via-white/50 to-transparent">
                            JARIPOS
                        </h1>
                        
                        <p class="text-2xl 2xl:text-4xl font-light text-brand-400 mt-[35vw] md:mt-[12vw] md:tracking-[1em] uppercase font-['Outfit'] opacity-80 bg-slate-950/30 backdrop-blur-sm inline-block px-4 py-2 rounded-lg">Satu Jari, Beres Semua</p>
                    </div>
                </div>
            </div>

            <!-- Footer Bottom Grid (Z-Index High to sit on top of Text) -->
            <div class="relative z-20 w-full">
                <!-- CTA Button in Footer -->
                <div class="mb-12 flex justify-center">
                     <a href="#" class="group relative inline-flex h-14 items-center justify-center overflow-hidden rounded-full bg-white px-10 font-bold text-slate-900 duration-300 hover:bg-brand-400 hover:text-white shadow-2xl shadow-brand-500/20">
                        <div class="absolute inset-0 flex h-full w-full justify-center [transform:skew(-12deg)_translateX(-100%)] group-hover:duration-1000 group-hover:[transform:skew(-12deg)_translateX(100%)]">
                          <div class="relative h-full w-8 bg-white/20"></div>
                        </div>
                        <span class="mr-2 text-base">Mulai Sekarang</span>
                        <svg class="h-5 w-5 transition-transform group-hover:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                      </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 text-center md:text-left border-t border-white/10 pt-8 bg-slate-950/20 backdrop-blur-sm rounded-t-3xl p-6">
                    <div class="flex flex-col items-center md:items-start space-y-3">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Office</span>
                        <p class="text-sm text-slate-300">Jl. Pemuda No. 123, Semarang</p>
                        <a href="mailto:hello@jaripos.id" class="text-sm text-brand-400 hover:text-white transition">hello@jaripos.id</a>
                    </div>

                    <div class="flex flex-col items-center justify-center space-y-3">
                         <div class="flex gap-6">
                             <a href="#" class="group flex flex-col items-center gap-1 text-slate-400 hover:text-white transition">
                                <span class="text-xl"><i class="fab fa-instagram"></i></span>
                             </a>
                             <a href="#" class="group flex flex-col items-center gap-1 text-slate-400 hover:text-white transition">
                                <span class="text-xl"><i class="fab fa-linkedin"></i></span>
                             </a>
                             <a href="#" class="group flex flex-col items-center gap-1 text-slate-400 hover:text-white transition">
                                <span class="text-xl"><i class="fab fa-twitter"></i></span>
                             </a>
                         </div>
                    </div>

                    <div class="flex flex-col items-center md:items-end space-y-3">
                        <span class="text-xs font-bold text-slate-500 uppercase tracking-widest">Legal</span>
                        <div class="flex gap-4 md:gap-2 md:flex-col">
                             <a href="#" class="text-sm text-end text-slate-400 hover:text-white transition">Privacy Policy</a>
                             <a href="#" class="text-sm text-end text-slate-400 hover:text-white transition">Terms of Service</a>
                        </div>
                    </div>
                </div>
                
                <div class="mt-4 text-center text-slate-600 text-xs pb-4">
                    &copy; 2025 Jaripos Inc.
                </div>
            </div>
        </div>
    </footer>

    <!-- Video Modal -->
    <div id="video-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 sm:p-6" role="dialog" aria-modal="true">
        <!-- Backdrop with transition -->
        <div class="fixed inset-0 bg-slate-950/90 backdrop-blur-sm transition-opacity duration-500 opacity-0" aria-hidden="true" id="modal-backdrop"></div>
        
        <!-- Modal Content with transition -->
        <div id="modal-content" class="relative w-full max-w-4xl bg-black rounded-3xl overflow-hidden shadow-2xl border border-slate-800 transform transition-all duration-500 scale-90 opacity-0">
            <!-- Close Button -->
            <button type="button" id="close-modal" class="absolute top-4 right-4 z-20 w-10 h-10 flex items-center justify-center rounded-full bg-black/50 text-white hover:bg-white hover:text-black transition duration-300 backdrop-blur-md border border-white/10">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
            
            <!-- Video Container -->
            <div class="relative aspect-video bg-slate-900">
                <iframe id="youtube-video" class="w-full h-full" src="" title="Video Demo Jaripos" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
            </div>
        </div>
    </div>

    <!-- Back to Top Button -->
    <button id="back-to-top" class="fixed bottom-8 right-8 z-50 w-12 h-12 bg-brand-600 hover:bg-brand-700 text-white rounded-full shadow-lg shadow-brand-500/30 flex items-center justify-center opacity-0 invisible translate-y-4 transition-all duration-300 hover:scale-110">
        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path>
        </svg>
    </button>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://unpkg.com/lenis@1.1.18/dist/lenis.min.js"></script>
    <script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
    <script>
        $(document).ready(function() {
            // Lenis Smooth Scroll
            const lenis = new Lenis({
                duration: 1.2,
                syncTouch: true,
                easing: (t) => Math.min(1, 1.001 - Math.pow(2, -10 * t)),
                smoothWheel: true,
                smoothTouch: true,
                touchMultiplier: 1.5,
            });

            function raf(time) {
                lenis.raf(time);
                requestAnimationFrame(raf);
            }
            requestAnimationFrame(raf);

            // Init AOS
            AOS.init({
                duration: 800,
                once: true,
                offset: 50
            });

            // Pricing Toggle Logic
            // Default: Yearly (99k) - Toggle ON
            let isYearly = true;
            
            $('#pricing-toggle').click(function() {
                isYearly = !isYearly;
                
                if(isYearly) {
                    // Switch to Yearly (Default)
                    $(this).removeClass('bg-slate-200').addClass('bg-brand-600');
                    $('#toggle-circle').addClass('translate-x-6');
                    
                    // Update Price -> 99k
                    $('#price-pro').text('99k');
                    $('#price-period').text('/ bulan (dibayar tahunan)');
                    
                    // Optional: Animate price change
                    $('#price-pro').hide().fadeIn(300);
                } else {
                    // Switch to Monthly
                    $(this).removeClass('bg-brand-600').addClass('bg-slate-200');
                    $('#toggle-circle').removeClass('translate-x-6');
                    
                    // Update Price -> 149k
                    $('#price-pro').text('149k');
                    $('#price-period').text('/ bulan');
                    
                    $('#price-pro').hide().fadeIn(300);
                }
            });

            // Mobile Menu Toggle
            let menuOpen = false;
            $('#mobile-menu-btn').click(function() {
                menuOpen = !menuOpen;
                $('#mobile-menu').slideToggle();
                
                if (menuOpen) {
                    $('#hamburger-icon').addClass('hidden');
                    $('#close-icon').removeClass('hidden');
                } else {
                    $('#hamburger-icon').removeClass('hidden');
                    $('#close-icon').addClass('hidden');
                }
            });
            
            // Close mobile menu on link click
            $('#mobile-menu a').click(function() {
                $('#mobile-menu').slideUp();
            });

            // Smooth Scroll for Anchor Links (Handled by CSS scroll-smooth, but backup just in case)
            // Custom Easing for Smoother Feel
            $.extend($.easing, {
                easeInOutExpo: function (x, t, b, c, d) {
                    if (t==0) return b;
                    if (t==d) return b+c;
                    if ((t/=d/2) < 1) return c/2 * Math.pow(2, 10 * (t - 1)) + b;
                    return c/2 * (-Math.pow(2, -10 * --t) + 2) + b;
                }
            });

            // Smooth Scroll for Anchor Links (Handled by CSS scroll-smooth, but backup just in case)
            $('a[href^="#"]').on('click', function(event) {
                var target = $(this.getAttribute('href'));
                if( target.length ) {
                    event.preventDefault();
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 80
                    }, 100, 'easeInOutExpo');
                }
            });


            // Video Modal Logic
            const videoSrc = "https://www.youtube.com/embed/dQw4w9WgXcQ?autoplay=1&rel=0"; // Placeholder Video
            
            
            $('#video-modal-btn').click(function(e) {
                e.preventDefault();
                const modal = $('#video-modal');
                const backdrop = $('#modal-backdrop');
                const content = $('#modal-content');

                modal.removeClass('hidden').addClass('flex');
                // $('body').addClass('overflow-hidden');
                
                // Allow browser to render 'flex' first, then animate opacity/scale
                requestAnimationFrame(() => {
                    backdrop.removeClass('opacity-0').addClass('opacity-100');
                    content.removeClass('scale-90 opacity-0').addClass('scale-100 opacity-100');
                });
                
                setTimeout(() => {
                    $('#youtube-video').attr('src', videoSrc);
                }, 300); // Wait for playing until almost visible
            });

            function closeModal() {
                const modal = $('#video-modal');
                const backdrop = $('#modal-backdrop');
                const content = $('#modal-content');

                // Start Exit Animation
                backdrop.removeClass('opacity-100').addClass('opacity-0');
                content.removeClass('scale-100 opacity-100').addClass('scale-90 opacity-0');

                // Wait for animation to finish before hiding
                setTimeout(() => {
                    modal.removeClass('flex').addClass('hidden');
                    $('#youtube-video').attr('src', '');
                    $('body').removeClass('overflow-hidden');
                }, 500); // Matches CSS duration-500
            }

            $('#close-modal, #modal-backdrop').click(function() {
                closeModal();
            });

            // Close on Escape key
            $(document).keydown(function(e) {
                if (e.key === "Escape") { 
                   closeModal();
                }
            });

            // Back to Top Button
            const backToTop = $('#back-to-top');
            
            $(window).scroll(function() {
                if ($(this).scrollTop() > 300) {
                    backToTop.removeClass('opacity-0 invisible translate-y-4').addClass('opacity-100 visible translate-y-0');
                } else {
                    backToTop.removeClass('opacity-100 visible translate-y-0').addClass('opacity-0 invisible translate-y-4');
                }
            });

            backToTop.click(function() {
                lenis.scrollTo(0, { 
                    duration: 2,
                    easing: (t) => t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2
                });
            });
        });
    </script>
</body>
</html>
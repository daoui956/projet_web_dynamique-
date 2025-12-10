<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduPlatform - @yield('title')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js (For Dropdowns & Toggles) -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col" x-data="{ mobileMenuOpen: false }">

    <!-- ================= NAVBAR ================= -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">

                <!-- Logo & Desktop Nav -->
                <div class="flex items-center gap-8">
                    <!-- Logo -->
                    <a href="{{ route('professor.dashboard') }}" class="flex items-center gap-2 group">
                        <div class="bg-blue-600 text-white p-2 rounded-lg group-hover:bg-blue-700 transition">
                            <i class="fas fa-graduation-cap text-lg"></i>
                        </div>
                        <span class="text-xl font-bold text-slate-800 tracking-tight">EduPlatform</span>
                    </a>

                    <!-- Desktop Links -->
                    <nav class="hidden md:flex gap-1">
                        <a href="{{ route('professor.dashboard') }}"
                           class="px-4 py-2 rounded-lg text-sm font-medium transition duration-150 ease-in-out
                           {{ request()->routeIs('professor.dashboard') ? 'bg-blue-50 text-blue-700' : 'text-slate-600 hover:text-slate-900 hover:bg-slate-50' }}">
                           Dashboard
                        </a>
                        <a href="{{ route('professor.dashboard') }}#my-courses"
                           class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition duration-150">
                           My Courses
                        </a>
                        <a href="{{ route('professor.dashboard') }}#my-classes"
                           class="px-4 py-2 rounded-lg text-sm font-medium text-slate-600 hover:text-slate-900 hover:bg-slate-50 transition duration-150">
                           My Classes
                        </a>
                    </nav>
                </div>

                <!-- Right Side Actions -->
                <div class="hidden md:flex items-center gap-4">

                    <!-- Notifications -->
                    <button class="relative p-2 text-slate-400 hover:text-blue-600 transition">
                        <i class="fas fa-bell text-xl"></i>
                        <span class="absolute top-1 right-1 h-2.5 w-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                    </button>

                    <!-- Profile Dropdown -->
                    <div class="relative" x-data="{ open: false }">
                        <button @click="open = !open" class="flex items-center gap-3 focus:outline-none group">
                            <div class="text-right hidden lg:block">
                                <p class="text-sm font-bold text-slate-700 group-hover:text-blue-700 transition">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-slate-500">Professor</p>
                            </div>
                            <div class="h-10 w-10 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-600 text-white flex items-center justify-center font-bold shadow ring-2 ring-white group-hover:ring-blue-100 transition">
                                {{ substr(Auth::user()->name, 0, 1) }}
                            </div>
                            <i class="fas fa-chevron-down text-xs text-slate-400"></i>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="open"
                             @click.away="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 py-1 z-50"
                             x-cloak>

                            <div class="px-4 py-3 border-b border-slate-50 lg:hidden">
                                <p class="text-sm font-bold text-slate-800">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-slate-500">Professor</p>
                            </div>

                            <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-blue-600">
                                <i class="fas fa-user-circle mr-2 w-4"></i> Profile
                            </a>
                            <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-blue-600">
                                <i class="fas fa-cog mr-2 w-4"></i> Settings
                            </a>

                            <div class="border-t border-slate-100 my-1"></div>

                            <form action="{{ route('logout') }}" method="POST">
                                @csrf
                                <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 flex items-center">
                                    <i class="fas fa-sign-out-alt mr-2 w-4"></i> Sign Out
                                </button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Mobile Menu Button -->
                <div class="flex items-center md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="p-2 text-slate-500 hover:text-blue-600 transition">
                        <i class="fas" :class="mobileMenuOpen ? 'fa-times' : 'fa-bars'"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu Panel -->
        <div x-show="mobileMenuOpen"
             x-transition.origin.top
             class="md:hidden bg-white border-b border-slate-200 shadow-lg" x-cloak>
            <div class="px-4 pt-2 pb-4 space-y-1">
                <a href="{{ route('professor.dashboard') }}" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:text-blue-600 hover:bg-slate-50">Dashboard</a>
                <a href="#my-courses" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:text-blue-600 hover:bg-slate-50">My Courses</a>
                <a href="#my-classes" class="block px-3 py-2 rounded-md text-base font-medium text-slate-700 hover:text-blue-600 hover:bg-slate-50">My Classes</a>
                <div class="border-t border-slate-100 my-2 pt-2">
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button class="w-full text-left px-3 py-2 text-base font-medium text-red-600 hover:bg-red-50 rounded-md">Sign Out</button>
                    </form>
                </div>
            </div>
        </div>
    </header>

    <!-- ================= CONTENT ================= -->
    <main class="flex-grow w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <!-- ================= FOOTER ================= -->
    <footer class="bg-white border-t border-slate-200 mt-auto">
        <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <p class="text-sm text-slate-500">
                    &copy; {{ date('Y') }} EduPlatform. All rights reserved.
                </p>
                <div class="flex space-x-6 mt-4 md:mt-0 text-slate-400">
                    <a href="#" class="hover:text-blue-600 transition"><i class="fab fa-twitter"></i></a>
                    <a href="#" class="hover:text-blue-600 transition"><i class="fab fa-linkedin"></i></a>
                    <a href="#" class="hover:text-blue-600 transition"><i class="fab fa-github"></i></a>
                </div>
            </div>
        </div>
    </footer>

    <!-- ================= NOTIFICATIONS (Toasts) ================= -->
    <div class="fixed bottom-4 right-4 z-50 space-y-2 pointer-events-none">
        <!-- Success Message -->
        @if(session('success'))
            <div x-data="{ show: true }"
                 x-show="show"
                 x-init="setTimeout(() => show = false, 5000)"
                 x-transition:enter="transform ease-out duration-300 transition"
                 x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
                 x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
                 x-transition:leave="transition ease-in duration-100"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-check-circle text-green-400 text-xl"></i>
                        </div>
                        <div class="ml-3 w-0 flex-1 pt-0.5">
                            <p class="text-sm font-medium text-gray-900">Success!</p>
                            <p class="mt-1 text-sm text-gray-500">{{ session('success') }}</p>
                        </div>
                        <div class="ml-4 flex flex-shrink-0">
                            <button @click="show = false" class="inline-flex rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none">
                                <span class="sr-only">Close</span>
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Error Message -->
        @if($errors->any())
            <div x-data="{ show: true }"
                 x-show="show"
                 class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg bg-white shadow-lg ring-1 ring-black ring-opacity-5 border-l-4 border-red-500">
                <div class="p-4">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-red-500 text-xl"></i>
                        </div>
                        <div class="ml-3 w-0 flex-1 pt-0.5">
                            <p class="text-sm font-medium text-gray-900">Action Failed</p>
                            <p class="mt-1 text-sm text-gray-500">Please check the form for errors.</p>
                        </div>
                        <button @click="show = false" class="ml-auto text-gray-400 hover:text-gray-500"><i class="fas fa-times"></i></button>
                    </div>
                </div>
            </div>
        @endif
    </div>

</body>
</html>

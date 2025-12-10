<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EduPlatform - @yield('title')</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Fonts & Icons -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Alpine.js (Lightweight JS for dropdowns/toggles) -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body { font-family: 'Inter', sans-serif; }
        [x-cloak] { display: none !important; }

        /* Custom Scrollbar */
        .sidebar-scroll::-webkit-scrollbar { width: 5px; }
        .sidebar-scroll::-webkit-scrollbar-track { background: transparent; }
        .sidebar-scroll::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 4px; }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased" x-data="{ sidebarOpen: false }">

    <!-- MOBILE OVERLAY -->
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity
         class="fixed inset-0 z-20 bg-black/50 lg:hidden"></div>

    <!-- ================= SIDEBAR ================= -->
    <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
           class="fixed inset-y-0 left-0 z-30 w-64 bg-white border-r border-slate-200 shadow-xl transition-transform duration-300 lg:translate-x-0 lg:shadow-none">

        <!-- Logo Area -->
        <div class="flex items-center justify-center h-16 border-b border-slate-100 bg-gradient-to-r from-blue-600 to-indigo-600">
            <h1 class="text-white font-bold text-xl tracking-wide">
                <i class="fas fa-graduation-cap mr-2"></i> Edu<span class="font-light">Admin</span>
            </h1>
        </div>

        <!-- Navigation Links -->
        <nav class="p-4 space-y-1 overflow-y-auto h-[calc(100vh-4rem)] sidebar-scroll">

            <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 mt-2">Main</p>

            <a href="{{ route('admin.dashboard') }}"
               class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 group
               {{ request()->routeIs('admin.dashboard') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <i class="fas fa-chart-pie w-5 h-5 mr-3 {{ request()->routeIs('admin.dashboard') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                <span class="font-medium">Dashboard</span>
            </a>

            <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 mt-6">Management</p>

            <a href="{{ route('admin.users') }}"
               class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 group
               {{ request()->routeIs('admin.users*') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <i class="fas fa-users w-5 h-5 mr-3 {{ request()->routeIs('admin.users*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                <span class="font-medium">Users</span>
            </a>

            <a href="{{ route('admin.classes') }}"
               class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 group
               {{ request()->routeIs('admin.classes*') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <i class="fas fa-chalkboard-teacher w-5 h-5 mr-3 {{ request()->routeIs('admin.classes*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                <span class="font-medium">Classes</span>
            </a>

            <a href="{{ route('admin.courses') }}"
               class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 group
               {{ request()->routeIs('admin.courses*') ? 'bg-blue-50 text-blue-600' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                <i class="fas fa-book w-5 h-5 mr-3 {{ request()->routeIs('admin.courses*') ? 'text-blue-600' : 'text-slate-400 group-hover:text-slate-600' }}"></i>
                <span class="font-medium">Courses</span>
            </a>

            <p class="px-4 text-xs font-bold text-slate-400 uppercase tracking-wider mb-2 mt-6">Requests</p>

            <a href="{{ route('admin.dashboard') }}#document-requests"
               class="flex items-center px-4 py-3 rounded-lg transition-colors duration-200 group text-slate-600 hover:bg-slate-50 hover:text-slate-900">
                <i class="fas fa-file-signature w-5 h-5 mr-3 text-slate-400 group-hover:text-slate-600"></i>
                <span class="font-medium">Documents</span>
                @php $pendingCount = \App\Models\DocumentRequest::where('status', 'pending')->count(); @endphp
                @if($pendingCount > 0)
                    <span class="ml-auto bg-red-500 text-white text-xs font-bold px-2 py-0.5 rounded-full">{{ $pendingCount }}</span>
                @endif
            </a>

            <!-- Logout Section -->
            <div class="mt-8 pt-6 border-t border-slate-100">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="w-full flex items-center px-4 py-3 text-red-600 hover:bg-red-50 rounded-lg transition-colors">
                        <i class="fas fa-sign-out-alt w-5 h-5 mr-3"></i>
                        <span class="font-medium">Sign Out</span>
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    <!-- ================= MAIN CONTENT WRAPPER ================= -->
    <div class="lg:ml-64 flex flex-col min-h-screen">

        <!-- Top Navbar -->
        <header class="bg-white border-b border-slate-200 h-16 flex items-center justify-between px-6 sticky top-0 z-10">

            <!-- Mobile Toggle & Page Title -->
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="lg:hidden text-slate-500 hover:text-blue-600">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <h2 class="text-xl font-bold text-slate-800 hidden sm:block">@yield('page-title')</h2>
            </div>

            <!-- Right Actions -->
            <div class="flex items-center gap-6">

                <!-- Notification Bell (Visual Only) -->
                <button class="relative text-slate-400 hover:text-blue-600 transition">
                    <i class="fas fa-bell text-xl"></i>
                    <span class="absolute -top-1 -right-1 h-2.5 w-2.5 bg-red-500 rounded-full border-2 border-white"></span>
                </button>

                <!-- Profile Dropdown -->
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" class="flex items-center gap-3 focus:outline-none">
                        <div class="text-right hidden md:block">
                            <p class="text-sm font-bold text-slate-700">{{ Auth::user()->name }}</p>
                            <p class="text-xs text-slate-500">Administrator</p>
                        </div>
                        <div class="h-10 w-10 rounded-full bg-gradient-to-tr from-blue-500 to-indigo-500 flex items-center justify-center text-white font-bold shadow-md ring-2 ring-white cursor-pointer">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </button>

                    <!-- Dropdown Menu -->
                    <div x-show="open" @click.away="open = false" x-cloak
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         class="absolute right-0 mt-3 w-48 bg-white rounded-lg shadow-lg border border-slate-100 py-2 z-50">
                        <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-blue-600">
                            <i class="fas fa-user-circle mr-2"></i> Profile
                        </a>
                        <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-50 hover:text-blue-600">
                            <i class="fas fa-cog mr-2"></i> Settings
                        </a>
                        <div class="border-t border-slate-100 my-1"></div>
                        <form action="{{ route('logout') }}" method="POST">
                            @csrf
                            <button class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                Logout
                            </button>
                        </form>
                    </div>
                </div>

            </div>
        </header>

        <!-- Page Content -->
        <main class="flex-1 p-6 overflow-y-auto">

            <!-- Flash Messages -->
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 4000)"
                     class="mb-6 bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg flex items-center justify-between shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-check-circle text-xl mr-3"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-green-500 hover:text-green-700"><i class="fas fa-times"></i></button>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show"
                     class="mb-6 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg flex items-center justify-between shadow-sm">
                    <div class="flex items-center">
                        <i class="fas fa-exclamation-circle text-xl mr-3"></i>
                        <span>{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-red-500 hover:text-red-700"><i class="fas fa-times"></i></button>
                </div>
            @endif

            <!-- Actual Content -->
            @yield('content')

        </main>

        <!-- Footer -->
        <footer class="bg-white border-t border-slate-200 py-4 px-6 text-center text-xs text-slate-400">
            &copy; {{ date('Y') }} EduPlatform Admin Panel. All rights reserved.
        </footer>

    </div>

</body>
</html>

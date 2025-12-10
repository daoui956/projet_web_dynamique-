<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Portal - @yield('title')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { font-family: 'Inter', sans-serif; }
        .sidebar { transition: all 0.3s ease; }
        .sidebar-link.active {
            background: linear-gradient(90deg, #3b82f6 0%, #1d4ed8 100%);
            color: white;
            box-shadow: 0 2px 10px rgba(59, 130, 246, 0.3);
        }
        .sidebar-link:hover:not(.active) {
            background-color: #f8fafc;
            transform: translateX(5px);
        }
        .badge {
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
    </style>
</head>
<body class="bg-gradient-to-br from-gray-50 to-blue-50 min-h-screen">

    <!-- Student Layout -->
    <div class="flex">
        <!-- Sidebar -->
        <aside class="sidebar w-64 bg-white border-r border-gray-200 min-h-screen fixed left-0 top-0 z-40 shadow-xl">
            <!-- Student Profile Header -->
            <div class="p-6 border-b border-gray-200">
                <div class="flex items-center">
                    <div class="h-14 w-14 bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white text-2xl font-bold shadow-lg">
                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                    </div>
                    <div class="ml-4">
                        <h1 class="text-lg font-bold text-gray-800">{{ Auth::user()->name }}</h1>
                        <div class="flex items-center mt-1">
                            <span class="px-3 py-1 bg-gradient-to-r from-green-100 to-emerald-100 text-green-800 text-xs font-semibold rounded-full">
                                <i class="fas fa-user-graduate mr-1"></i> Student
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation -->
            <nav class="mt-6 px-4">
                <!-- Dashboard -->
                <a href="{{ url('/student') }}"
                   class="sidebar-link flex items-center px-4 py-3 rounded-xl mb-2 text-gray-700 {{ request()->is('student') && !request()->is('student/*') ? 'active' : '' }}">
                    <i class="fas fa-tachometer-alt text-lg w-8"></i>
                    <span class="font-medium">Dashboard</span>
                </a>

                <!-- My Classes -->
                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-8">
                    Academic
                </div>
                <a href="#my-classes"
                   class="sidebar-link flex items-center px-4 py-3 rounded-xl mb-2 text-gray-700">
                    <i class="fas fa-chalkboard-teacher text-lg w-8"></i>
                    <span class="font-medium">My Classes</span>
                </a>
                <a href="#my-courses"
                   class="sidebar-link flex items-center px-4 py-3 rounded-xl mb-2 text-gray-700">
                    <i class="fas fa-book-open text-lg w-8"></i>
                    <span class="font-medium">My Courses</span>
                </a>

                <!-- Free Courses -->
                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-8">
                    Learning
                </div>
                <a href="#available-courses"
                   class="sidebar-link flex items-center px-4 py-3 rounded-xl mb-2 text-gray-700">
                    <i class="fas fa-search text-lg w-8"></i>
                    <span class="font-medium">Browse Courses</span>
                </a>
                <a href="{{ url('/student/search') }}"
                   class="sidebar-link flex items-center px-4 py-3 rounded-xl mb-2 text-gray-700 {{ request()->is('student/search') ? 'active' : '' }}">
                    <i class="fas fa-search-plus text-lg w-8"></i>
                    <span class="font-medium">Search</span>
                </a>

                <!-- Performance -->
                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-8">
                    Performance
                </div>
                <a href="#grades"
                   class="sidebar-link flex items-center px-4 py-3 rounded-xl mb-2 text-gray-700">
                    <i class="fas fa-chart-line text-lg w-8"></i>
                    <span class="font-medium">My Grades</span>
                    @php
                        $gradeCount = \App\Models\Grade::where('student_id', Auth::id())->count();
                    @endphp
                    @if($gradeCount > 0)
                        <span class="ml-auto bg-green-100 text-green-800 text-xs px-2 py-1 rounded-full">
                            {{ $gradeCount }}
                        </span>
                    @endif
                </a>

                <!-- Documents -->
                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-8">
                    Documents
                </div>
                <a href="#document-requests"
                   class="sidebar-link flex items-center px-4 py-3 rounded-xl mb-2 text-gray-700">
                    <i class="fas fa-file-alt text-lg w-8"></i>
                    <span class="font-medium">My Requests</span>
                    @php
                        $pendingRequests = \App\Models\DocumentRequest::where('user_id', Auth::id())
                            ->where('status', 'pending')
                            ->count();
                    @endphp
                    @if($pendingRequests > 0)
                        <span class="ml-auto bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded-full badge">
                            {{ $pendingRequests }} pending
                        </span>
                    @endif
                </a>

                <!-- Support -->
                <div class="px-4 py-2 text-xs font-semibold text-gray-500 uppercase tracking-wider mt-8">
                    Support
                </div>
                <a href="#request-document"
                   class="sidebar-link flex items-center px-4 py-3 rounded-xl mb-2 text-gray-700">
                    <i class="fas fa-question-circle text-lg w-8"></i>
                    <span class="font-medium">Request Help</span>
                </a>
            </nav>

            <!-- Quick Stats -->
            <div class="absolute bottom-24 left-0 right-0 px-6">
                <div class="bg-gradient-to-r from-blue-50 to-indigo-50 p-4 rounded-xl border border-blue-100">
                    <p class="text-xs text-gray-600 mb-2">Academic Progress</p>
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-lg font-bold text-gray-800">
                                @php
                                    $coursesCount = \App\Models\Enrollment::where('student_id', Auth::id())->count();
                                @endphp
                                {{ $coursesCount }}
                            </p>
                            <p class="text-xs text-gray-600">Courses</p>
                        </div>
                        <div class="h-8 w-px bg-blue-200"></div>
                        <div>
                            <p class="text-lg font-bold text-gray-800">
                                @php
                                    $classesCount = \App\Models\ClassRoom::whereHas('students', function($q) {
                                        $q->where('users.id', Auth::id());
                                    })->count();
                                @endphp
                                {{ $classesCount }}
                            </p>
                            <p class="text-xs text-gray-600">Classes</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Logout -->
            <div class="absolute bottom-0 w-full p-6 bg-gradient-to-t from-white to-transparent">
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button class="w-full flex items-center justify-center px-4 py-3 bg-gradient-to-r from-red-500 to-pink-600 hover:from-red-600 hover:to-pink-700 text-white rounded-xl transition shadow-lg hover:shadow-xl">
                        <i class="fas fa-sign-out-alt mr-3"></i> Logout
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 ml-64 p-6">
            <!-- Top Bar -->
            <header class="bg-white rounded-2xl shadow-sm p-4 mb-6 border border-gray-100">
                <div class="flex justify-between items-center">
                    <div>
                        <h2 class="text-2xl font-bold text-gray-800">@yield('page-title', 'Student Dashboard')</h2>
                        <p class="text-sm text-gray-600">@yield('page-subtitle', 'Welcome to your learning portal')</p>
                    </div>
                    <div class="flex items-center space-x-4">
                        <!-- Notifications -->
                        <button class="relative p-2 text-gray-600 hover:text-blue-600">
                            <i class="fas fa-bell text-xl"></i>
                            <span class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center animate-pulse">
                                2
                            </span>
                        </button>

                        <!-- Current Time -->
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-700">{{ now()->format('l, F j, Y') }}</p>
                            <p class="text-xs text-gray-500">{{ now()->format('h:i A') }}</p>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Flash Messages -->
            @if(session('success'))
                <div class="animate-slide-down bg-gradient-to-r from-green-500 to-emerald-600 text-white px-6 py-4 rounded-xl mb-6 shadow-lg flex items-center">
                    <i class="fas fa-check-circle text-2xl mr-3"></i>
                    <div>
                        <p class="font-semibold">{{ session('success') }}</p>
                        @if(session('success-detail'))
                            <p class="text-sm opacity-90 mt-1">{{ session('success-detail') }}</p>
                        @endif
                    </div>
                    <button class="ml-auto text-white hover:text-gray-200" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            @if(session('error'))
                <div class="animate-slide-down bg-gradient-to-r from-red-500 to-pink-600 text-white px-6 py-4 rounded-xl mb-6 shadow-lg flex items-center">
                    <i class="fas fa-exclamation-circle text-2xl mr-3"></i>
                    <div>
                        <p class="font-semibold">{{ session('error') }}</p>
                        @if(session('error-detail'))
                            <p class="text-sm opacity-90 mt-1">{{ session('error-detail') }}</p>
                        @endif
                    </div>
                    <button class="ml-auto text-white hover:text-gray-200" onclick="this.parentElement.remove()">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            @endif

            <!-- Page Content -->
            <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
                @yield('content')
            </div>

            <!-- Footer -->
            <footer class="mt-8 text-center text-gray-500 text-sm">
                <p>© {{ date('Y') }} Student Portal • <span class="text-blue-600">Last login: {{ Auth::user()->updated_at->diffForHumans() }}</span></p>
            </footer>
        </main>
    </div>

    <script>
        // Auto-hide flash messages
        setTimeout(() => {
            const flashMessages = document.querySelectorAll('.animate-slide-down');
            flashMessages.forEach(msg => {
                msg.style.opacity = '0';
                msg.style.transform = 'translateY(-20px)';
                setTimeout(() => msg.remove(), 300);
            });
        }, 5000);

        // Smooth scroll to sections
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const targetId = this.getAttribute('href').substring(1);
                const targetElement = document.getElementById(targetId);
                if (targetElement) {
                    window.scrollTo({
                        top: targetElement.offsetTop - 100,
                        behavior: 'smooth'
                    });
                }
            });
        });

        // Mobile sidebar toggle
        function toggleSidebar() {
            const sidebar = document.querySelector('.sidebar');
            const mainContent = document.querySelector('main');

            sidebar.classList.toggle('-translate-x-full');
            mainContent.classList.toggle('ml-0');
            mainContent.classList.toggle('ml-64');
        }

        // Update time every minute
        function updateTime() {
            const now = new Date();
            const timeElement = document.querySelector('.current-time');
            const dateElement = document.querySelector('.current-date');

            if (timeElement) {
                timeElement.textContent = now.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit'
                });
            }

            if (dateElement) {
                dateElement.textContent = now.toLocaleDateString('en-US', {
                    weekday: 'long',
                    year: 'numeric',
                    month: 'long',
                    day: 'numeric'
                });
            }
        }

        // Update time every minute
        setInterval(updateTime, 60000);

        // Initialize
        document.addEventListener('DOMContentLoaded', function() {
            updateTime();

            // Highlight active section based on scroll
            window.addEventListener('scroll', function() {
                const sections = document.querySelectorAll('section[id]');
                const scrollPosition = window.scrollY + 150;

                sections.forEach(section => {
                    const sectionTop = section.offsetTop;
                    const sectionHeight = section.clientHeight;
                    const sectionId = section.getAttribute('id');

                    if (scrollPosition >= sectionTop && scrollPosition < sectionTop + sectionHeight) {
                        document.querySelectorAll('.sidebar-link').forEach(link => {
                            link.classList.remove('active');
                            if (link.getAttribute('href') === `#${sectionId}`) {
                                link.classList.add('active');
                            }
                        });
                    }
                });
            });
        });
    </script>

    <style>
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-slide-down {
            animation: slideDown 0.3s ease-out;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(to bottom, #3b82f6, #1d4ed8);
            border-radius: 10px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(to bottom, #2563eb, #1e40af);
        }
    </style>
</body>
</html>

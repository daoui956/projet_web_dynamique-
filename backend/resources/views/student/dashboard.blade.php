@extends('layouts.student')
@section('title', 'Student Dashboard')
@section('page-title', 'Welcome, ' . Auth::user()->name)
@section('page-subtitle', 'Your learning journey at a glance')

@section('content')
<div class="space-y-10">

    <!-- Welcome Banner with Logout -->
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl p-8 text-white shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Welcome back, {{ Auth::user()->name }}!</h1>
                <p class="opacity-90">You have {{ $classes->count() }} active classes and {{ $enrolled->count() }} enrolled courses</p>
                <div class="flex items-center mt-4 space-x-4">
                    <span class="bg-white/20 px-4 py-2 rounded-lg">
                        <i class="fas fa-clock mr-2"></i> {{ now()->format('h:i A') }}
                    </span>
                    <span class="bg-white/20 px-4 py-2 rounded-lg">
                        <i class="fas fa-calendar mr-2"></i> {{ now()->format('M d, Y') }}
                    </span>
                    <form action="{{ route('logout') }}" method="POST">
                        @csrf
                        <button type="submit" class="bg-white/20 hover:bg-white/30 px-4 py-2 rounded-lg transition">
                            <i class="fas fa-sign-out-alt mr-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
            <div class="text-6xl">
                <i class="fas fa-graduation-cap"></i>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-gradient-to-r from-green-50 to-emerald-100 border border-green-200 rounded-xl p-6">
            <div class="flex items-center">
                <div class="bg-green-500 p-3 rounded-lg mr-4">
                    <i class="fas fa-book text-white text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Total Courses</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $enrolled->count() + $classes->sum('courses.count') }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-blue-50 to-indigo-100 border border-blue-200 rounded-xl p-6">
            <div class="flex items-center">
                <div class="bg-blue-500 p-3 rounded-lg mr-4">
                    <i class="fas fa-chalkboard-teacher text-white text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Active Classes</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $classes->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-50 to-violet-100 border border-purple-200 rounded-xl p-6">
            <div class="flex items-center">
                <div class="bg-purple-500 p-3 rounded-lg mr-4">
                    <i class="fas fa-file-alt text-white text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">Pending Requests</p>
                    <p class="text-3xl font-bold text-gray-800">{{ $requests->where('status', 'pending')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-orange-50 to-amber-100 border border-orange-200 rounded-xl p-6">
            <div class="flex items-center">
                <div class="bg-orange-500 p-3 rounded-lg mr-4">
                    <i class="fas fa-user-graduate text-white text-2xl"></i>
                </div>
                <div>
                    <p class="text-sm text-gray-600">My Profile</p>
                    <a href="#profile" class="text-3xl font-bold text-gray-800 hover:text-blue-600 transition">
                        View
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
        <a href="{{ route('student.classes') }}"
           class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition text-center">
            <div class="text-blue-600 mb-2">
                <i class="fas fa-chalkboard-teacher text-2xl"></i>
            </div>
            <p class="font-medium text-gray-800">My Classes</p>
            <p class="text-sm text-gray-600">{{ $classes->count() }} classes</p>
        </a>

        <a href="#my-courses"
           class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition text-center">
            <div class="text-green-600 mb-2">
                <i class="fas fa-book text-2xl"></i>
            </div>
            <p class="font-medium text-gray-800">My Courses</p>
            <p class="text-sm text-gray-600">{{ $enrolled->count() }} enrolled</p>
        </a>

        <a href="#grades"
           class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition text-center">
            <div class="text-purple-600 mb-2">
                <i class="fas fa-chart-line text-2xl"></i>
            </div>
            <p class="font-medium text-gray-800">My Grades</p>
            <p class="text-sm text-gray-600">{{ $grades->count() }} records</p>
        </a>

        <a href="#document-requests"
           class="bg-white border border-gray-200 rounded-xl p-4 shadow-sm hover:shadow-md transition text-center">
            <div class="text-red-600 mb-2">
                <i class="fas fa-file-alt text-2xl"></i>
            </div>
            <p class="font-medium text-gray-800">Requests</p>
            <p class="text-sm text-gray-600">{{ $requests->count() }} total</p>
        </a>
    </div>

    <!-- ==================== MY CLASSES ==================== -->
    <section id="my-classes" class="pt-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">My Classes</h2>
                <p class="text-gray-600">Click on any class to view details, courses, and chat</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="bg-blue-100 text-blue-800 text-sm px-4 py-2 rounded-full">
                    {{ $classes->count() }} classes
                </span>
                <a href="{{ route('student.classes') }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                    <i class="fas fa-eye mr-2"></i> View All
                </a>
            </div>
        </div>

        @if($classes->count() > 0)
        <div class="grid md:grid-cols-2 gap-6">
            @foreach($classes as $class)
            <a href="{{ route('student.class.show', $class->id) }}"
               class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition transform hover:-translate-y-1">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-xl font-bold text-gray-800 hover:text-blue-600 transition">{{ $class->name }}</h3>
                            <p class="text-gray-600 text-sm">{{ $class->level }} • Year {{ $class->year ?? date('Y') }}</p>
                        </div>
                        <span class="bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full">
                            {{ $class->courses->count() }} courses
                        </span>
                    </div>

                    <!-- Professors -->
                    @if($class->professors->count() > 0)
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">Professors:</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach($class->professors as $professor)
                                <span class="bg-blue-50 text-blue-700 text-xs px-3 py-1 rounded">
                                    <i class="fas fa-user-tie mr-1"></i> {{ $professor->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Course Preview -->
                    @if($class->courses->count() > 0)
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">Recent Courses:</p>
                        <div class="space-y-2">
                            @foreach($class->courses->take(2) as $course)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    @if($course->thumbnail)
                                    <div class="w-10 h-10 rounded overflow-hidden mr-3">
                                        <img src="{{ asset('storage/' . $course->thumbnail) }}"
                                             alt="{{ $course->title }}"
                                             class="w-full h-full object-cover">
                                    </div>
                                    @endif
                                    <span class="text-sm font-medium text-gray-700">{{ $course->title }}</span>
                                </div>
                                <span class="text-xs bg-gray-200 text-gray-800 px-2 py-1 rounded">
                                    {{ $course->files->count() }} files
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Quick Info -->
                    <div class="flex items-center justify-between mt-6 pt-4 border-t border-gray-100">
                        <div class="flex items-center text-sm text-gray-600">
                            <i class="fas fa-users mr-2"></i>
                            <span>{{ $class->students_count ?? '?' }} students</span>
                        </div>
                        <div class="text-blue-600 text-sm font-medium">
                            View Class <i class="fas fa-arrow-right ml-1"></i>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="text-center py-12 bg-gray-50 rounded-xl">
            <i class="fas fa-chalkboard-teacher text-5xl text-gray-400 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Classes Found</h3>
            <p class="text-gray-600 mb-6">You are not enrolled in any classes yet.</p>
            <div class="space-y-3 max-w-md mx-auto">
                <p class="text-sm text-gray-500">What you can do:</p>
                <ul class="text-sm text-gray-600 text-left space-y-2">
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        Contact your administrator to be assigned to classes
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        Browse available courses below
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        Check back later for updates
                    </li>
                </ul>
            </div>
        </div>
        @endif
    </section>

    <!-- ==================== MY COURSES ==================== -->
    <!-- ==================== MY COURSES ==================== -->
<section id="my-courses" class="pt-12">
    <div class="flex items-center justify-between mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">My Enrolled Courses</h2>
            <p class="text-gray-600">Courses you are currently enrolled in</p>
        </div>
        <span class="bg-green-100 text-green-800 text-sm px-4 py-2 rounded-full">
            {{ $enrolled->count() }} courses
        </span>
    </div>

    @if($enrolled->count() > 0)
    <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($enrolled as $course)
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition transform hover:-translate-y-1">
            <!-- Course Thumbnail -->
            @if($course->thumbnail)
            <div class="h-48 overflow-hidden">
                <img src="{{ asset('storage/' . $course->thumbnail) }}"
                     alt="{{ $course->title }}"
                     class="w-full h-full object-cover hover:scale-105 transition duration-300">
            </div>
            @else
            <div class="h-48 bg-gradient-to-r from-blue-100 to-indigo-100 flex items-center justify-center">
                <i class="fas fa-book-open text-5xl text-blue-400"></i>
            </div>
            @endif

            <!-- Course Details -->
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <h3 class="text-lg font-bold text-gray-800">{{ $course->title }}</h3>
                    @if($course->level)
                        <span class="bg-blue-100 text-blue-800 text-xs px-3 py-1 rounded">
                            {{ $course->level }}
                        </span>
                    @endif
                </div>

                <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                    {{ $course->description ?: 'No description available' }}
                </p>

                <!-- Course Info -->
                <div class="flex items-center justify-between text-sm text-gray-500 mb-6">
                    <div class="flex items-center">
                        <i class="fas fa-user-tie mr-2"></i>
                        <span>{{ $course->professor->name ?? 'Unknown Professor' }}</span>
                    </div>
                    <div class="flex items-center space-x-3">
                        <span class="flex items-center">
                            <i class="fas fa-file-alt mr-1"></i>
                            {{ $course->files->count() }}
                        </span>
                        <span class="flex items-center">
                            <i class="fas fa-comments mr-1"></i>
                            {{ $course->messages->count() }}
                        </span>
                    </div>
                </div>

                <!-- Quick Actions - FIXED ROUTES -->
                <div class="grid grid-cols-2 gap-3">
                    @if($course->class_id)
                        <!-- Course is in a class -->
                        <a href="{{ route('student.class.course.show', ['class' => $course->class_id, 'course' => $course->id]) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white text-center py-2 rounded-lg text-sm transition">
                            <i class="fas fa-eye mr-2"></i> View
                        </a>
                    @else
                        <!-- Standalone course -->
<a href="{{ route('student.course.show', $course->id) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white text-center py-2 rounded-lg text-sm transition">
                            <i class="fas fa-eye mr-2"></i> View
                        </a>
                    @endif

                    <button onclick="showCourseChat({{ $course->id }})"
                            class="bg-green-600 hover:bg-green-700 text-white py-2 rounded-lg text-sm">
                        <i class="fas fa-comment mr-2"></i> Chat
                    </button>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @else
    <div class="text-center py-12 bg-gray-50 rounded-xl">
        <i class="fas fa-book-open text-5xl text-gray-400 mb-4"></i>
        <p class="text-gray-600 text-lg">You haven't enrolled in any courses yet</p>
        <p class="text-gray-500 mb-6">Start your learning journey by enrolling in courses below</p>
        <a href="#available-courses"
           class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg transition shadow hover:shadow-lg">
            <i class="fas fa-arrow-down mr-2"></i> Browse Available Courses
        </a>
    </div>
    @endif
</section>

    <!-- ==================== AVAILABLE COURSES ==================== -->
    @if($available->count() > 0)
    <section id="available-courses" class="pt-12">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">Available Courses</h2>
                <p class="text-gray-600">Courses you can enroll in right now</p>
            </div>
            <span class="bg-yellow-100 text-yellow-800 text-sm px-4 py-2 rounded-full">
                {{ $available->count() }} courses available
            </span>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($available as $course)
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition transform hover:-translate-y-1">
                <!-- Course Thumbnail -->
                @if($course->thumbnail)
                <div class="h-48 overflow-hidden">
                    <img src="{{ asset('storage/' . $course->thumbnail) }}"
                         alt="{{ $course->title }}"
                         class="w-full h-full object-cover hover:scale-105 transition duration-300">
                </div>
                @else
                <div class="h-48 bg-gradient-to-r from-green-100 to-emerald-100 flex items-center justify-center">
                    <i class="fas fa-book text-5xl text-green-400"></i>
                </div>
                @endif

                <div class="p-6">
                    <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $course->title }}</h3>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                        {{ Str::limit($course->description, 100) ?: 'No description available' }}
                    </p>

                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center text-sm text-gray-500">
                            <i class="fas fa-user-tie mr-2"></i>
                            <span>{{ $course->professor->name ?? 'Unknown Professor' }}</span>
                        </div>
                        @if($course->duration)
                            <span class="text-xs bg-gray-100 text-gray-800 px-2 py-1 rounded">
                                {{ $course->duration }}
                            </span>
                        @endif
                    </div>

                    <form action="{{ route('student.enroll', $course->id) }}" method="POST">
                        @csrf
                        <button type="submit"
                                class="w-full bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white font-medium py-3 rounded-lg transition shadow hover:shadow-lg flex items-center justify-center">
                            <i class="fas fa-plus-circle mr-2"></i> Enroll Now
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- ==================== GRADES ==================== -->
    <section id="grades" class="pt-12">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">My Grades</h2>
                <p class="text-gray-600">Your academic performance overview</p>
            </div>
            @if($grades->count() > 0)
                @php
                    $average = $grades->avg('value');
                    $color = $average >= 10 ? 'text-green-600' : 'text-red-600';
                    $bgColor = $average >= 10 ? 'bg-green-100' : 'bg-red-100';
                @endphp
                <span class="{{ $bgColor }} {{ $color }} text-sm px-4 py-2 rounded-full font-bold">
                    Average: {{ number_format($average, 2) }}/20
                </span>
            @endif
        </div>

        @if($grades->count() > 0)
        <div class="bg-white border border-gray-200 rounded-xl overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Course</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Professor</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Grade</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Comment</th>
                            <th class="px-6 py-3 text-left text-sm font-medium text-gray-700">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($grades as $grade)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4">
                                <p class="font-medium text-gray-900">{{ $grade->course->title }}</p>
                            </td>
                            <td class="px-6 py-4 text-gray-700">
                                {{ $grade->professor->name ?? 'Unknown' }}
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                                    {{ $grade->value >= 10 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $grade->value }}/20
                                </span>
                            </td>
                            <td class="px-6 py-4 text-gray-700">
                                {{ $grade->comment ?: 'No comment' }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-500">
                                {{ $grade->created_at->format('M d, Y') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Grade Summary -->
        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-blue-50 border border-blue-200 rounded-xl p-4">
                <p class="text-sm text-blue-700 font-medium">Total Courses Graded</p>
                <p class="text-2xl font-bold text-blue-800">{{ $grades->count() }}</p>
            </div>
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <p class="text-sm text-green-700 font-medium">Highest Grade</p>
                <p class="text-2xl font-bold text-green-800">{{ $grades->max('value') ?? 'N/A' }}/20</p>
            </div>
            <div class="bg-orange-50 border border-orange-200 rounded-xl p-4">
                <p class="text-sm text-orange-700 font-medium">Lowest Grade</p>
                <p class="text-2xl font-bold text-orange-800">{{ $grades->min('value') ?? 'N/A' }}/20</p>
            </div>
        </div>
        @else
        <div class="text-center py-12 bg-gray-50 rounded-xl">
            <i class="fas fa-chart-line text-5xl text-gray-400 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Grades Available</h3>
            <p class="text-gray-600 mb-4">Your grades will appear here once professors evaluate your work</p>
            <p class="text-sm text-gray-500">Keep submitting assignments and participating in classes</p>
        </div>
        @endif
    </section>

    <!-- ==================== DOCUMENT REQUESTS ==================== -->
    <section id="document-requests" class="pt-12">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">My Document Requests</h2>
                <p class="text-gray-600">Track your document requests and admin responses</p>
            </div>
            <div class="flex items-center space-x-3">
                <span class="bg-red-100 text-red-800 text-sm px-4 py-2 rounded-full">
                    {{ $requests->where('status', 'pending')->count() }} pending
                </span>
                <button onclick="document.getElementById('requestModal').classList.remove('hidden')"
                        class="bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white px-6 py-3 rounded-lg font-medium transition shadow hover:shadow-lg flex items-center">
                    <i class="fas fa-plus mr-2"></i> New Request
                </button>
            </div>
        </div>

        @if($requests->count() > 0)
        <div class="space-y-6">
            @foreach($requests as $request)
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm hover:shadow-md transition">
                <div class="flex justify-between items-start mb-4">
                    <div>
                        <h3 class="text-lg font-bold text-gray-800">{{ $request->type }}</h3>
                        <p class="text-gray-600 text-sm">
                            <i class="fas fa-clock mr-1"></i>
                            Requested {{ $request->created_at->format('M d, Y \a\t h:i A') }}
                        </p>
                    </div>
                    <span class="px-4 py-2 rounded-full text-sm font-medium
                        {{ $request->status == 'pending' ? 'bg-yellow-100 text-yellow-800' :
                           ($request->status == 'approved' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800') }}">
                        {{ ucfirst($request->status) }}
                    </span>
                </div>

                <!-- Request Message -->
                <div class="bg-gray-50 p-4 rounded-lg mb-4">
                    <p class="text-gray-700"><strong>Your request:</strong> {{ $request->message }}</p>
                </div>

                <!-- Admin Reply -->
                @if($request->reply)
                <div class="mt-4 p-5 bg-gradient-to-r from-blue-50 to-indigo-50 border border-blue-200 rounded-lg">
                    <div class="flex items-center mb-3">
                        <div class="bg-blue-600 p-2 rounded-lg mr-3">
                            <i class="fas fa-reply text-white"></i>
                        </div>
                        <h4 class="text-lg font-semibold text-blue-800">Admin Response</h4>
                    </div>

                    <!-- Text Reply -->
                    @if($request->reply->reply)
                    <div class="mb-4">
                        <p class="text-gray-700">{{ $request->reply->reply }}</p>
                        <p class="text-xs text-gray-500 mt-2">
                            <i class="fas fa-clock mr-1"></i>
                            Replied on: {{ $request->reply->created_at->format('M d, Y \a\t h:i A') }}
                        </p>
                    </div>
                    @endif

                    <!-- File Attachment -->
                    @if($request->reply->file_path)
                    <div class="mt-4 p-4 bg-white border border-blue-100 rounded-lg">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <div class="mr-4 text-blue-600">
                                    @php
                                        $extension = pathinfo($request->reply->file_name, PATHINFO_EXTENSION);
                                        $icon = match(strtolower($extension)) {
                                            'pdf' => 'fas fa-file-pdf',
                                            'doc', 'docx' => 'fas fa-file-word',
                                            'jpg', 'jpeg', 'png', 'gif' => 'fas fa-file-image',
                                            default => 'fas fa-file'
                                        };
                                    @endphp
                                    <i class="{{ $icon }} text-2xl"></i>
                                </div>
                                <div>
                                    <a href="{{ asset('storage/' . $request->reply->file_path) }}"
                                       target="_blank"
                                       class="text-blue-600 hover:text-blue-800 font-medium">
                                        {{ $request->reply->file_name }}
                                    </a>
                                    @if($request->reply->file_size)
                                        <p class="text-sm text-gray-600 mt-1">
                                            {{ round($request->reply->file_size / 1024, 1) }} KB
                                        </p>
                                    @endif
                                </div>
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="{{ asset('storage/' . $request->reply->file_path) }}"
                                   target="_blank"
                                   class="text-blue-600 hover:text-blue-800 p-2 rounded hover:bg-blue-50">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ asset('storage/' . $request->reply->file_path) }}"
                                   download
                                   class="text-green-600 hover:text-green-800 p-2 rounded hover:bg-green-50">
                                    <i class="fas fa-download"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                @endif
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12 bg-gray-50 rounded-xl">
            <i class="fas fa-inbox text-5xl text-gray-400 mb-4"></i>
            <h3 class="text-xl font-semibold text-gray-700 mb-2">No Document Requests</h3>
            <p class="text-gray-600 mb-6">You haven't submitted any document requests yet</p>
            <button onclick="document.getElementById('requestModal').classList.remove('hidden')"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition">
                <i class="fas fa-plus mr-2"></i> Submit Your First Request
            </button>
        </div>
        @endif
    </section>

    <!-- ==================== REQUEST DOCUMENT MODAL ==================== -->
    <div id="requestModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
        <div class="bg-white rounded-2xl w-full max-w-md">
            <div class="p-6">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-xl font-bold text-gray-800">Request Document</h3>
                    <button onclick="document.getElementById('requestModal').classList.add('hidden')"
                            class="text-gray-500 hover:text-gray-700">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form action="{{ route('request.document') }}" method="POST">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Document Type *</label>
                            <select name="type" required
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <option value="">Select document type</option>
                                <option>Certificate</option>
                                <option>Transcript</option>
                                <option>Recommendation Letter</option>
                                <option>Other</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Message *</label>
                            <textarea name="message" rows="4" required
                                      placeholder="Please describe what you need and any specific details..."
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"></textarea>
                        </div>

                        <div class="flex space-x-3 pt-4">
                            <button type="button"
                                    onclick="document.getElementById('requestModal').classList.add('hidden')"
                                    class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 rounded-lg transition">
                                Cancel
                            </button>
                            <button type="submit"
                                    class="flex-1 bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white font-medium py-3 rounded-lg transition shadow hover:shadow-lg">
                                Submit Request
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Floating Logout Button (Mobile) -->
<div class="md:hidden fixed bottom-6 right-6 z-40">
    <form action="{{ route('logout') }}" method="POST">
        @csrf
        <button type="submit"
                class="bg-red-600 hover:bg-red-700 text-white p-4 rounded-full shadow-lg transition transform hover:scale-110">
            <i class="fas fa-sign-out-alt text-xl"></i>
        </button>
    </form>
</div>

<script>
// Course Chat Function
function showCourseChat(courseId) {
    // Implement AJAX chat or redirect to course page
    window.location.href = `/student/course/${courseId}#chat`;
}

// Scroll to section
function scrollToSection(sectionId) {
    document.getElementById(sectionId).scrollIntoView({
        behavior: 'smooth'
    });
}

// Close modal when clicking outside
document.getElementById('requestModal').addEventListener('click', function(e) {
    if (e.target.id === 'requestModal') {
        this.classList.add('hidden');
    }
});

// Close modal with Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById('requestModal').classList.add('hidden');
    }
});

// Add active class to current section in view
document.addEventListener('DOMContentLoaded', function() {
    const sections = document.querySelectorAll('section[id]');
    const navLinks = document.querySelectorAll('.quick-actions a');

    window.addEventListener('scroll', function() {
        let current = '';

        sections.forEach(section => {
            const sectionTop = section.offsetTop;
            const sectionHeight = section.clientHeight;

            if (scrollY >= (sectionTop - 200)) {
                current = section.getAttribute('id');
            }
        });

        navLinks.forEach(link => {
            link.classList.remove('active');
            if (link.getAttribute('href') === `#${current}`) {
                link.classList.add('active');
            }
        });
    });
});
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.active {
    background-color: #3b82f6;
    color: white;
}

.active:hover {
    background-color: #2563eb;
}

/* Smooth transitions */
.transition {
    transition: all 0.3s ease;
}

.transform {
    transition: transform 0.3s ease;
}

/* Custom scrollbar */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
@endsection

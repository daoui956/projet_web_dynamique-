@extends('layouts.student')
@section('title', 'My Classes')
@section('page-title', 'My Classes')
@section('page-subtitle', 'All classes you are enrolled in')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center mb-6">
        <div>
            <h2 class="text-2xl font-bold text-gray-800">My Classes</h2>
            <p class="text-gray-600">Total {{ $classes->total() }} classes</p>
        </div>
        <div class="flex space-x-3">
            <a href="{{ route('student.dashboard') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg flex items-center">
                <i class="fas fa-home mr-2"></i> Back to Dashboard
            </a>
        </div>
    </div>

    @if($classes->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($classes as $class)
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition duration-300">
                <!-- Class Header -->
                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 p-6 text-white">
                    <div class="flex justify-between items-start">
                        <div>
                            <h3 class="text-xl font-bold mb-1">{{ $class->name }}</h3>
                            <p class="text-blue-100 text-sm">{{ $class->level ?? 'All Levels' }}</p>
                        </div>
                        <span class="bg-white/20 px-3 py-1 rounded-full text-xs">
                            {{ $class->courses_count ?? $class->courses->count() }} courses
                        </span>
                    </div>
                </div>

                <!-- Class Details -->
                <div class="p-6">
                    <!-- Professor Info -->
                    @if($class->professors->count() > 0)
                    <div class="flex items-center mb-4 p-3 bg-gray-50 rounded-lg">
                        <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-user-tie text-blue-600"></i>
                        </div>
                        <div>
                            <p class="font-medium text-gray-800">{{ $class->professors->first()->name }}</p>
                            <p class="text-sm text-gray-600">Professor</p>
                        </div>
                    </div>
                    @endif

                    <!-- Course Preview -->
                    @if($class->courses->count() > 0)
                    <div class="mb-4">
                        <p class="text-sm font-medium text-gray-700 mb-2">Recent Courses:</p>
                        <div class="space-y-2">
                            @foreach($class->courses->take(2) as $course)
                            <div class="flex items-center justify-between p-2 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    @if($course->thumbnail)
                                    <div class="w-8 h-8 rounded overflow-hidden mr-2">
                                        <img src="{{ asset('storage/' . $course->thumbnail) }}"
                                             alt="{{ $course->title }}"
                                             class="w-full h-full object-cover">
                                    </div>
                                    @endif
                                    <span class="text-sm text-gray-700 truncate">{{ $course->title }}</span>
                                </div>
                                <span class="text-xs bg-gray-200 text-gray-800 px-2 py-1 rounded">
                                    {{ $course->files_count ?? $course->files->count() }} files
                                </span>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="grid grid-cols-2 gap-3">
                        <a href="{{ route('student.class.show', $class->id) }}"
                           class="bg-blue-600 hover:bg-blue-700 text-white text-center py-2 rounded-lg text-sm transition">
                            <i class="fas fa-eye mr-1"></i> View Class
                        </a>
                        <a href="{{ route('student.class.show', $class->id) }}#chat"
                           class="bg-green-600 hover:bg-green-700 text-white text-center py-2 rounded-lg text-sm">
                            <i class="fas fa-comments mr-1"></i> Chat
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($classes->hasPages())
        <div class="mt-8">
            {{ $classes->links() }}
        </div>
        @endif
    @else
        <div class="text-center py-16 bg-gray-50 rounded-2xl">
            <div class="mb-6">
                <i class="fas fa-chalkboard-teacher text-6xl text-gray-400"></i>
            </div>
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
                        Check back later for class assignments
                    </li>
                    <li class="flex items-center">
                        <i class="fas fa-check-circle text-green-500 mr-2"></i>
                        Browse available courses in the meantime
                    </li>
                </ul>
            </div>
        </div>
    @endif
</div>
@endsection

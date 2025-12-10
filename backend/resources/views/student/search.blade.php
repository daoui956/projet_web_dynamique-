@extends('layouts.student')
@section('title', 'Search')
@section('page-title', 'Search Courses & Classes')
@section('page-subtitle', 'Find learning materials and courses')

@section('content')
<div class="space-y-8">

    <!-- Search Bar -->
    <div class="bg-white rounded-2xl shadow-sm p-6 border border-gray-100">
        <form action="{{ url('/student/search') }}" method="GET" class="relative">
            <div class="flex">
                <input type="text" name="q" value="{{ $query ?? '' }}"
                       placeholder="Search for courses, classes, or professors..."
                       class="flex-1 px-6 py-4 border border-gray-300 rounded-l-xl focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 text-lg">
                <button type="submit"
                        class="bg-gradient-to-r from-blue-600 to-indigo-700 hover:from-blue-700 hover:to-indigo-800 text-white px-8 rounded-r-xl transition">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>

        @if(isset($query) && !empty($query))
            <p class="mt-4 text-gray-600">
                Search results for: <span class="font-semibold text-blue-600">"{{ $query }}"</span>
            </p>
        @endif
    </div>

    <!-- Search Results -->
    @if(isset($query) && !empty($query))
    <div class="space-y-8">

        <!-- Classes Results -->
        @if($classes->count() > 0)
        <div>
            <h3 class="text-xl font-bold text-gray-800 mb-4">Classes ({{ $classes->count() }})</h3>
            <div class="grid md:grid-cols-2 gap-6">
                @foreach($classes as $class)
                <div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-md transition">
                    <h4 class="text-lg font-bold text-gray-800 mb-2">{{ $class->name }}</h4>
                    <p class="text-gray-600 text-sm mb-4">{{ $class->level }}</p>
                    <div class="flex items-center text-sm text-gray-500">
                        <i class="fas fa-users mr-2"></i>
                        <span>{{ $class->students_count ?? 0 }} students</span>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Courses Results -->
        @if($courses->count() > 0)
        <div>
            <h3 class="text-xl font-bold text-gray-800 mb-4">Courses ({{ $courses->count() }})</h3>
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($courses as $course)
                <div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-md transition">
                    <h4 class="text-lg font-bold text-gray-800 mb-2">{{ $course->title }}</h4>
                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                        {{ $course->description ?: 'No description' }}
                    </p>
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-500">{{ $course->professor->name ?? 'Unknown' }}</span>
                        @if(!auth()->user()->enrollments->contains('course_id', $course->id))
                        <form action="{{ url('/student/enroll/' . $course->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="text-blue-600 hover:text-blue-800 text-sm">
                                <i class="fas fa-plus mr-1"></i> Enroll
                            </button>
                        </form>
                        @else
                        <span class="text-green-600 text-sm">
                            <i class="fas fa-check mr-1"></i> Enrolled
                        </span>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- Professors Results -->
        @if($professors->count() > 0)
        <div>
            <h3 class="text-xl font-bold text-gray-800 mb-4">Professors ({{ $professors->count() }})</h3>
            <div class="grid md:grid-cols-2 gap-6">
                @foreach($professors as $professor)
                <div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-md transition">
                    <div class="flex items-center">
                        <div class="h-12 w-12 bg-blue-100 text-blue-800 rounded-full flex items-center justify-center mr-4 text-lg font-bold">
                            {{ strtoupper(substr($professor->name, 0, 1)) }}
                        </div>
                        <div>
                            <h4 class="font-bold text-gray-800">{{ $professor->name }}</h4>
                            <p class="text-gray-600 text-sm">{{ $professor->email }}</p>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        <!-- No Results -->
        @if($classes->count() == 0 && $courses->count() == 0 && $professors->count() == 0)
        <div class="text-center py-12 bg-white rounded-2xl border border-gray-200">
            <i class="fas fa-search text-5xl text-gray-400 mb-4"></i>
            <p class="text-gray-600 text-lg">No results found for "{{ $query }}"</p>
            <p class="text-gray-500 mt-2">Try different keywords or browse available courses</p>
        </div>
        @endif
    </div>
    @else
    <!-- Search Prompt -->
    <div class="text-center py-12 bg-white rounded-2xl border border-gray-200">
        <i class="fas fa-search text-5xl text-gray-400 mb-4"></i>
        <p class="text-gray-600 text-lg">What would you like to learn today?</p>
        <p class="text-gray-500 mt-2">Search for courses, classes, or professors to get started</p>
    </div>
    @endif
</div>

<style>
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }
</style>
@endsection

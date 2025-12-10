@extends('layouts.app')
@section('title', 'Professor Dashboard')

@section('content')
<!-- MAIN CONTAINER -->
<div class="max-w-7xl mx-auto space-y-8 pb-12" x-data="{ activeTab: 'courses', showCreateModal: false }">

    <!-- 1. HEADER SECTION -->
    <div class="flex flex-col md:flex-row justify-between items-end gap-6 bg-white p-8 rounded-3xl border border-slate-100 shadow-sm relative overflow-hidden">
        <!-- Decorative Background -->
        <div class="absolute top-0 right-0 w-64 h-64 bg-blue-50 rounded-full mix-blend-multiply filter blur-3xl opacity-50 -translate-y-1/2 translate-x-1/2"></div>

        <div class="relative z-10">
            <h1 class="text-4xl font-extrabold text-slate-800 tracking-tight">Welcome, {{ Auth::user()->name }}</h1>
            <p class="text-lg text-slate-500 mt-2">Manage your curriculum and evaluate student performance.</p>

            <!-- Quick Stats Row -->
            <div class="flex gap-6 mt-6">
                <div class="flex items-center gap-2 text-sm font-semibold text-slate-600">
                    <span class="w-2 h-2 rounded-full bg-green-500"></span>
                    {{ $myCourses->count() }} Active Courses
                </div>
                <div class="flex items-center gap-2 text-sm font-semibold text-slate-600">
                    <span class="w-2 h-2 rounded-full bg-blue-500"></span>
                    {{ $classes->count() }} Assigned Classes
                </div>
            </div>
        </div>

        <div class="relative z-10 flex gap-3">
            <button @click="showCreateModal = true"
                    class="group bg-slate-900 hover:bg-slate-800 text-white font-bold py-3 px-6 rounded-xl shadow-lg transition transform hover:-translate-y-0.5 flex items-center gap-2">
                <i class="fas fa-plus-circle text-blue-400 group-hover:text-white transition"></i>
                Create Course
            </button>
        </div>
    </div>

    <!-- 2. NAVIGATION TABS -->
    <div class="flex items-center gap-8 border-b border-slate-200">
        <button @click="activeTab = 'courses'"
                :class="activeTab === 'courses' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
                class="pb-4 text-sm font-bold uppercase tracking-wide border-b-2 transition duration-300">
            <i class="fas fa-laptop-code mr-2"></i> Course Management
        </button>
        <button @click="activeTab = 'gradebook'"
                :class="activeTab === 'gradebook' ? 'border-blue-600 text-blue-600' : 'border-transparent text-slate-500 hover:text-slate-700'"
                class="pb-4 text-sm font-bold uppercase tracking-wide border-b-2 transition duration-300">
            <i class="fas fa-user-graduate mr-2"></i> Gradebook & Classes
        </button>
    </div>

    <!-- ALERT MESSAGES -->
    @if(session('success'))
    <div x-data="{ show: true }" x-show="show" x-transition.duration.500ms class="bg-emerald-50 text-emerald-700 px-4 py-3 rounded-xl border border-emerald-100 flex items-center justify-between shadow-sm">
        <div class="flex items-center gap-2"><i class="fas fa-check-circle"></i> <span>{{ session('success') }}</span></div>
        <button @click="show = false"><i class="fas fa-times"></i></button>
    </div>
    @endif

    <!-- ================= TAB 1: COURSE MANAGEMENT ================= -->
    <div x-show="activeTab === 'courses'" x-transition.opacity.duration.500ms>
        @if($myCourses->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($myCourses as $course)
            <div class="group bg-white rounded-2xl shadow-sm border border-slate-200 hover:shadow-xl hover:border-blue-200 transition duration-300 flex flex-col h-full overflow-hidden relative">
                <!-- Thumbnail -->
                <div class="relative h-48 overflow-hidden bg-slate-100">
                    <img src="{{ asset('storage/' . $course->thumbnail) }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-700">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition duration-300"></div>

                    <div class="absolute top-3 right-3">
                        <span class="px-3 py-1 rounded-lg text-xs font-bold bg-white/90 text-slate-800 shadow-sm backdrop-blur-md">
                            {{ $course->level ?? 'General' }}
                        </span>
                    </div>
                </div>

                <!-- Body -->
                <div class="p-6 flex-1 flex flex-col">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-lg font-bold text-slate-800 line-clamp-1 group-hover:text-blue-600 transition">{{ $course->title }}</h3>
                    </div>
                    <p class="text-slate-500 text-sm line-clamp-2 mb-6">{{ $course->description }}</p>

                    <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between text-xs text-slate-500 font-medium">
                        <span class="flex items-center gap-1"><i class="fas fa-chalkboard"></i> {{ $course->classRoom->name ?? 'Unassigned' }}</span>
                        <span class="flex items-center gap-1"><i class="fas fa-clock"></i> {{ $course->duration ?? 'N/A' }}</span>
                    </div>

                    <!-- Hover Actions -->
                    <div class="grid grid-cols-2 gap-2 mt-4">
                        <a href="{{ route('professor.course.edit', $course->id) }}" class="py-2 rounded-lg bg-slate-50 hover:bg-blue-50 text-slate-600 hover:text-blue-600 text-center text-sm font-bold transition">
                            Edit
                        </a>
                        <form action="{{ route('professor.course.delete', $course->id) }}" method="POST" onsubmit="return confirm('Delete this course?')">
                            @csrf @method('DELETE')
                            <button class="w-full py-2 rounded-lg bg-red-50 hover:bg-red-100 text-red-500 hover:text-red-700 text-sm font-bold transition">
                                Delete
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-24 bg-white rounded-3xl border-2 border-dashed border-slate-200">
            <div class="w-20 h-20 bg-blue-50 text-blue-500 rounded-full flex items-center justify-center mx-auto mb-4 text-3xl">
                <i class="fas fa-layer-group"></i>
            </div>
            <h3 class="text-xl font-bold text-slate-800">No Courses Yet</h3>
            <p class="text-slate-500 mb-6">Create your first course to get started.</p>
            <button @click="showCreateModal = true" class="text-blue-600 font-bold hover:underline">Create Course Now</button>
        </div>
        @endif
    </div>

    <!-- ================= TAB 2: GRADEBOOK (BY CLASS) ================= -->
    <div x-show="activeTab === 'gradebook'" x-transition.opacity.duration.500ms style="display: none;">

        @if($classes->count() > 0)
        <div class="grid lg:grid-cols-2 gap-8">
            @foreach($classes as $class)
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <!-- Class Header -->
                <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-lg text-slate-800">{{ $class->name }}</h3>
                        <p class="text-xs text-slate-500 font-semibold uppercase tracking-wider">{{ $class->students->count() }} Students • Level {{ $class->level }}</p>
                    </div>
                    <div class="h-10 w-10 bg-white rounded-full flex items-center justify-center text-slate-400 shadow-sm border border-slate-100">
                        <i class="fas fa-users"></i>
                    </div>
                </div>

                <!-- List of Courses Taught in this Class -->
                <div class="p-6">
                    <p class="text-xs font-bold text-slate-400 uppercase mb-4">Courses you teach this class:</p>

                    @if($class->my_courses->count() > 0)
                    <div class="space-y-3">
                        @foreach($class->my_courses as $course)
                        <div class="flex items-center justify-between p-4 rounded-xl border border-slate-100 hover:border-blue-200 hover:bg-blue-50/50 transition group">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div>
                                    <h4 class="font-bold text-slate-800 text-sm group-hover:text-blue-700 transition">{{ $course->title }}</h4>
                                    <p class="text-xs text-slate-500">{{ $course->grades->count() }} grades recorded</p>
                                </div>
                            </div>

                            <!-- OPEN GRADE SHEET BUTTON -->
                            <button onclick="toggleModal('gradeSheet-{{ $course->id }}')"
                                    class="px-4 py-2 bg-white border border-slate-200 text-slate-600 text-xs font-bold rounded-lg hover:bg-blue-600 hover:text-white hover:border-blue-600 transition shadow-sm">
                                Grade Students
                            </button>
                        </div>

                        <!-- ================= GRADE SHEET MODAL ================= -->
                        <div id="gradeSheet-{{ $course->id }}" class="hidden fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm transition-opacity" onclick="toggleModal('gradeSheet-{{ $course->id }}')"></div>

                            <div class="flex items-center justify-center min-h-screen p-4">
                                <div class="bg-white rounded-2xl shadow-2xl w-full max-w-3xl overflow-hidden relative transform transition-all">

                                    <!-- Modal Header -->
                                    <div class="bg-slate-800 px-6 py-5 flex justify-between items-center text-white">
                                        <div>
                                            <h3 class="font-bold text-xl">Grade Sheet</h3>
                                            <p class="text-slate-400 text-sm">Course: <span class="text-white">{{ $course->title }}</span> • Class: <span class="text-white">{{ $class->name }}</span></p>
                                        </div>
                                        <button onclick="toggleModal('gradeSheet-{{ $course->id }}')" class="text-slate-400 hover:text-white transition"><i class="fas fa-times text-xl"></i></button>
                                    </div>

                                    <!-- Student List -->
                                    <div class="p-0 max-h-[65vh] overflow-y-auto">
                                        <table class="w-full text-left">
                                            <thead class="bg-slate-50 text-xs uppercase font-bold text-slate-500 sticky top-0">
                                                <tr>
                                                    <th class="px-6 py-3">Student Name</th>
                                                    <th class="px-6 py-3 text-center">Current Grade</th>
                                                    <th class="px-6 py-3 text-right">Action</th>
                                                </tr>
                                            </thead>
                                            <tbody class="divide-y divide-slate-100">
                                                @foreach($class->students as $student)
                                                @php
                                                    $existingGrade = $course->grades->where('student_id', $student->id)->first();
                                                @endphp
                                                <tr class="hover:bg-slate-50">
                                                    <td class="px-6 py-4">
                                                        <div class="flex items-center gap-3">
                                                            <div class="h-8 w-8 rounded-full bg-slate-200 flex items-center justify-center text-xs font-bold text-slate-600">
                                                                {{ substr($student->name, 0, 1) }}
                                                            </div>
                                                            <div>
                                                                <p class="font-bold text-slate-800 text-sm">{{ $student->name }}</p>
                                                                <p class="text-xs text-slate-400">{{ $student->email }}</p>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4">
                                                        <form action="{{ route('professor.grade', ['courseId' => $course->id, 'studentId' => $student->id]) }}" method="POST" class="flex justify-center">
                                                            @csrf
                                                            <div class="relative">
                                                                <input type="number" name="grade" step="0.5" min="0" max="20" placeholder="-"
                                                                       value="{{ $existingGrade ? $existingGrade->value : '' }}"
                                                                       class="w-20 text-center font-bold border border-slate-300 rounded-lg py-1 px-2 focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                                                <span class="absolute right-[-25px] top-1.5 text-xs text-slate-400 font-bold">/20</span>
                                                            </div>
                                                            <button class="ml-8 text-blue-600 hover:text-blue-800 text-sm font-bold bg-blue-50 px-3 py-1 rounded hover:bg-blue-100 transition">Save</button>
                                                        </form>
                                                    </td>
                                                    <td class="px-6 py-4 text-right">
                                                        @if($existingGrade)
                                                            <span class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2 py-1 rounded"><i class="fas fa-check mr-1"></i> Graded</span>
                                                        @else
                                                            <span class="text-xs font-bold text-slate-400 bg-slate-100 px-2 py-1 rounded">Pending</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-200 text-right">
                                        <button onclick="toggleModal('gradeSheet-{{ $course->id }}')" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-bold rounded-lg transition">Close</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- END MODAL -->

                        @endforeach
                    </div>
                    @else
                    <div class="text-center py-8">
                        <p class="text-sm text-slate-400 italic">You haven't assigned any courses to this class yet.</p>
                        <button @click="activeTab = 'courses'; showCreateModal = true" class="text-blue-600 text-xs font-bold mt-2 hover:underline">Create a Course</button>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-20">
            <h3 class="text-lg font-bold text-slate-600">No Classes Assigned</h3>
            <p class="text-slate-400">Contact the administrator to get assigned to a class.</p>
        </div>
        @endif

    </div>

</div>

<!-- ================= CREATE COURSE SLIDE-OVER (DRAWER) ================= -->
<div x-show="showCreateModal" class="relative z-50" aria-labelledby="slide-over-title" role="dialog" aria-modal="true" style="display: none;">
    <div x-show="showCreateModal" x-transition.opacity class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm"></div>

    <div class="fixed inset-0 overflow-hidden">
        <div class="absolute inset-0 overflow-hidden">
            <div class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                <div x-show="showCreateModal"
                     x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700"
                     x-transition:enter-start="translate-x-full"
                     x-transition:enter-end="translate-x-0"
                     x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700"
                     x-transition:leave-start="translate-x-0"
                     x-transition:leave-end="translate-x-full"
                     class="pointer-events-auto w-screen max-w-md">

                    <form action="{{ route('professor.course.create') }}" method="POST" enctype="multipart/form-data" class="flex h-full flex-col overflow-y-scroll bg-white shadow-xl">
                        @csrf
                        <div class="bg-slate-900 px-4 py-6 sm:px-6">
                            <div class="flex items-center justify-between">
                                <h2 class="text-lg font-bold text-white" id="slide-over-title">New Course</h2>
                                <button type="button" @click="showCreateModal = false" class="text-slate-400 hover:text-white">
                                    <span class="sr-only">Close panel</span>
                                    <i class="fas fa-times text-xl"></i>
                                </button>
                            </div>
                            <div class="mt-1">
                                <p class="text-sm text-slate-400">Fill in the details to create a new learning module.</p>
                            </div>
                        </div>

                        <div class="relative flex-1 px-4 py-6 sm:px-6 space-y-6">

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Course Title</label>
                                <input type="text" name="title" required class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-3 px-4 bg-slate-50">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Description</label>
                                <textarea name="description" rows="4" required class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-3 px-4 bg-slate-50"></textarea>
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Assign Class</label>
                                    <select name="class_id" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-3 px-2 bg-slate-50">
                                        <option value="">None</option>
                                        @foreach($classes as $c)
                                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-1">Duration</label>
                                    <input type="text" name="duration" placeholder="e.g. 8 Weeks" class="block w-full rounded-lg border-slate-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm py-3 px-4 bg-slate-50">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1">Thumbnail</label>
                                <div class="mt-1 flex justify-center rounded-lg border-2 border-dashed border-slate-300 px-6 pt-5 pb-6 hover:bg-slate-50 transition">
                                    <div class="space-y-1 text-center">
                                        <i class="fas fa-image text-slate-400 text-3xl mb-2"></i>
                                        <div class="flex text-sm text-slate-600 justify-center">
                                            <label class="relative cursor-pointer rounded-md bg-white font-medium text-blue-600 focus-within:outline-none hover:text-blue-500">
                                                <span>Upload a file</span>
                                                <input name="thumbnail" type="file" class="sr-only" required accept="image/*">
                                            </label>
                                        </div>
                                        <p class="text-xs text-slate-500">PNG, JPG up to 5MB</p>
                                    </div>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Difficulty</label>
                                <div class="flex gap-4">
                                    <label class="flex items-center">
                                        <input type="radio" name="level" value="Beginner" class="text-blue-600 focus:ring-blue-500" checked>
                                        <span class="ml-2 text-sm text-slate-700">Beginner</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="radio" name="level" value="Intermediate" class="text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-slate-700">Intermediate</span>
                                    </label>
                                </div>
                            </div>

                        </div>

                        <div class="flex flex-shrink-0 justify-end px-4 py-4 bg-slate-50">
                            <button type="button" @click="showCreateModal = false" class="rounded-lg border border-slate-300 bg-white py-2 px-4 text-sm font-medium text-slate-700 shadow-sm hover:bg-slate-50 focus:outline-none">Cancel</button>
                            <button type="submit" class="ml-4 inline-flex justify-center rounded-lg border border-transparent bg-blue-600 py-2 px-4 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none">Save Course</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleModal(id) {
        document.getElementById(id).classList.toggle('hidden');
    }
</script>
@endsection

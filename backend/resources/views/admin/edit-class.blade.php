@extends('layouts.admin')
@section('title', 'Edit Class')
@section('page-title', 'Edit Class')
@section('page-subtitle', 'Update details for ' . $class->name)

@section('content')
<div class="max-w-5xl mx-auto">

    <!-- Header Actions -->
    <div class="mb-6 flex items-center justify-between">
        <a href="{{ route('admin.classes') }}" class="text-slate-500 hover:text-blue-600 transition flex items-center gap-2 text-sm font-medium">
            <i class="fas fa-arrow-left"></i> Back to All Classes
        </a>
        <div class="text-sm text-slate-400">
            Last updated: {{ $class->updated_at->diffForHumans() }}
        </div>
    </div>

    <form action="{{ route('admin.class.update', $class->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- LEFT COLUMN: Basic Details -->
            <div class="lg:col-span-1 space-y-6">
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <h3 class="font-bold text-slate-800 mb-4 border-b border-slate-100 pb-2">
                        <i class="fas fa-info-circle text-blue-500 mr-2"></i> Class Details
                    </h3>

                    <div class="space-y-4">
                        <!-- Name -->
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Class Name</label>
                            <input type="text" name="name" value="{{ old('name', $class->name) }}" required
                                   class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition font-medium text-slate-700">
                        </div>

                        <!-- Level -->
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Level</label>
                            <input type="text" name="level" value="{{ old('level', $class->level) }}" required
                                   class="w-full px-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                        </div>

                        <!-- Year -->
                        <div>
                            <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Academic Year</label>
                            <div class="relative">
                                <i class="fas fa-calendar-alt absolute left-3 top-2.5 text-slate-400"></i>
                                <input type="number" name="year" value="{{ old('year', $class->year) }}" required min="2020" max="2030"
                                       class="w-full pl-10 pr-4 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Save Button (Sticky on mobile, static on desktop) -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 p-6">
                    <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg shadow-md hover:shadow-lg transition transform hover:-translate-y-0.5 flex items-center justify-center">
                        <i class="fas fa-save mr-2"></i> Save Changes
                    </button>
                    <a href="{{ route('admin.classes') }}" class="block text-center text-slate-500 hover:text-slate-700 text-sm mt-3">Cancel</a>
                </div>
            </div>

            <!-- RIGHT COLUMN: Assignments -->
            <div class="lg:col-span-2 space-y-6">

                <!-- PROFESSORS ASSIGNMENT -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                        <h3 class="font-bold text-slate-800">
                            <i class="fas fa-chalkboard-teacher text-green-600 mr-2"></i> Assign Professors
                        </h3>
                        <div class="relative w-48">
                            <input type="text" id="searchProf" onkeyup="filterList('searchProf', 'profList')"
                                   placeholder="Search professors..."
                                   class="w-full px-3 py-1 text-sm border border-slate-300 rounded-full focus:outline-none focus:border-green-500">
                            <i class="fas fa-search absolute right-3 top-1.5 text-slate-400 text-xs"></i>
                        </div>
                    </div>

                    <div class="h-48 overflow-y-auto p-2" id="profList">
                        @foreach($allProfessors as $prof)
                        <label class="flex items-center p-2 rounded hover:bg-slate-50 cursor-pointer transition group">
                            <input type="checkbox" name="professors[]" value="{{ $prof->id }}"
                                   class="w-4 h-4 text-green-600 border-slate-300 rounded focus:ring-green-500"
                                   {{ $class->professors->contains($prof->id) ? 'checked' : '' }}>

                            <div class="ml-3 flex items-center">
                                <div class="h-8 w-8 rounded-full bg-green-100 text-green-600 flex items-center justify-center text-xs font-bold mr-2">
                                    {{ substr($prof->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-700 group-hover:text-green-700 search-text">{{ $prof->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $prof->email }}</p>
                                </div>
                            </div>

                            <!-- Checked Indicator -->
                            @if($class->professors->contains($prof->id))
                                <span class="ml-auto text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-bold">Assigned</span>
                            @endif
                        </label>
                        @endforeach
                    </div>
                </div>

                <!-- STUDENTS ASSIGNMENT -->
                <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                    <div class="px-6 py-4 bg-slate-50 border-b border-slate-200 flex justify-between items-center">
                        <h3 class="font-bold text-slate-800">
                            <i class="fas fa-user-graduate text-blue-600 mr-2"></i> Enrolled Students
                        </h3>
                        <div class="relative w-48">
                            <input type="text" id="searchStudent" onkeyup="filterList('searchStudent', 'studentList')"
                                   placeholder="Search students..."
                                   class="w-full px-3 py-1 text-sm border border-slate-300 rounded-full focus:outline-none focus:border-blue-500">
                            <i class="fas fa-search absolute right-3 top-1.5 text-slate-400 text-xs"></i>
                        </div>
                    </div>

                    <div class="h-64 overflow-y-auto p-2" id="studentList">
                        @foreach($allStudents as $student)
                        <label class="flex items-center p-2 rounded hover:bg-slate-50 cursor-pointer transition group">
                            <input type="checkbox" name="students[]" value="{{ $student->id }}"
                                   class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500"
                                   {{ $class->students->contains($student->id) ? 'checked' : '' }}>

                            <div class="ml-3 flex items-center">
                                <div class="h-8 w-8 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center text-xs font-bold mr-2">
                                    {{ substr($student->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-slate-700 group-hover:text-blue-700 search-text">{{ $student->name }}</p>
                                    <p class="text-xs text-slate-500">{{ $student->email }}</p>
                                </div>
                            </div>

                            <!-- Checked Indicator -->
                            @if($class->students->contains($student->id))
                                <span class="ml-auto text-xs bg-blue-100 text-blue-700 px-2 py-0.5 rounded-full font-bold">Enrolled</span>
                            @endif
                        </label>
                        @endforeach
                    </div>

                    <!-- Quick Count -->
                    <div class="px-4 py-2 bg-slate-50 border-t border-slate-200 text-xs text-slate-500 text-right">
                        {{ $allStudents->count() }} total students available
                    </div>
                </div>

            </div>
        </div>
    </form>
</div>

<!-- Simple Filter Script -->
<script>
    function filterList(inputId, listId) {
        // Get input value
        let input = document.getElementById(inputId);
        let filter = input.value.toUpperCase();

        // Get list container
        let list = document.getElementById(listId);
        // Get all labels (items)
        let items = list.getElementsByTagName('label');

        // Loop through all items and hide those who don't match the search
        for (let i = 0; i < items.length; i++) {
            let p = items[i].querySelector('.search-text');
            let txtValue = p.textContent || p.innerText;

            if (txtValue.toUpperCase().indexOf(filter) > -1) {
                items[i].style.display = "";
            } else {
                items[i].style.display = "none";
            }
        }
    }
</script>
@endsection

@extends('layouts.admin')
@section('title', 'Class Management')
@section('page-title', 'Class Management')
@section('page-subtitle', 'Create and manage classes')

@section('content')
<div class="space-y-6" x-data="{ showCreateModal: false }">

    <!-- Top Bar: Search & Create Button -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-white p-4 rounded-xl shadow-sm border border-gray-200">

        <!-- Search Input -->
        <div class="relative w-full md:w-96">
            <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <i class="fas fa-search text-gray-400"></i>
            </span>
            <input type="text" id="classSearch" onkeyup="filterClasses()"
                   placeholder="Search classes by name or level..."
                   class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
        </div>

        <!-- Create Button (Triggers Modal) -->
        <button onclick="toggleModal('createClassModal')"
                class="w-full md:w-auto bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2 rounded-lg transition shadow-md flex items-center justify-center">
            <i class="fas fa-plus-circle mr-2"></i> Create New Class
        </button>
    </div>

    <!-- CREATE CLASS MODAL (Hidden by default) -->
    <div id="createClassModal" class="fixed inset-0 z-50 hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <!-- Backdrop -->
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div onclick="toggleModal('createClassModal')" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" aria-hidden="true"></div>

            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

            <!-- Modal Panel -->
            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-white" id="modal-title">Create New Class</h3>
                    <button onclick="toggleModal('createClassModal')" class="text-white hover:text-gray-200">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>

                <form action="{{ route('admin.create.class') }}" method="POST" class="p-6">
                    @csrf
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Class Name *</label>
                            <input type="text" name="name" required placeholder="e.g. Computer Science A"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Level *</label>
                                <input type="text" name="level" required placeholder="e.g. L3"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Year *</label>
                                <input type="number" name="year" required min="2020" max="2030" value="{{ date('Y') }}"
                                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-blue-500">
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Select Students</label>
                                <select name="students[]" multiple class="w-full border border-gray-300 rounded-lg p-2 h-32 text-sm focus:ring-blue-500">
                                    @foreach($allStudents as $student)
                                        <option value="{{ $student->id }}">{{ $student->name }}</option>
                                    @endforeach
                                </select>
                                <p class="text-xs text-gray-500 mt-1">Ctrl+Click to select multiple</p>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">Select Professors</label>
                                <select name="professors[]" multiple class="w-full border border-gray-300 rounded-lg p-2 h-32 text-sm focus:ring-blue-500">
                                    @foreach($allProfessors as $professor)
                                        <option value="{{ $professor->id }}">{{ $professor->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end gap-3">
                        <button type="button" onclick="toggleModal('createClassModal')"
                                class="px-4 py-2 bg-gray-100 text-gray-700 rounded-lg hover:bg-gray-200 transition">Cancel</button>
                        <button type="submit"
                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow">Save Class</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- All Classes Grid -->
    <div>
        @if($classes->count() > 0)
        <div id="classesGrid" class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($classes as $class)
            <div class="class-card bg-white rounded-xl shadow-sm border border-gray-200 hover:shadow-md transition duration-300 group">
                <!-- Card Header -->
                <div class="p-6 border-b border-gray-100">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h4 class="text-lg font-bold text-gray-800 group-hover:text-blue-600 transition class-name">{{ $class->name }}</h4>
                            <span class="text-xs font-semibold px-2 py-1 bg-gray-100 text-gray-600 rounded mt-1 inline-block class-level">
                                {{ $class->level }}
                            </span>
                        </div>
                        <div class="text-right">
                             <span class="text-xs text-gray-400 font-mono block">{{ $class->year }}</span>
                        </div>
                    </div>
                </div>

                <!-- Card Stats -->
                <div class="grid grid-cols-2 divide-x divide-gray-100 bg-gray-50">
                    <div class="p-4 text-center">
                        <span class="block text-2xl font-bold text-blue-600">{{ $class->students_count }}</span>
                        <span class="text-xs text-gray-500 uppercase tracking-wide">Students</span>
                    </div>
                    <div class="p-4 text-center">
                        <span class="block text-2xl font-bold text-green-600">{{ $class->professors_count }}</span>
                        <span class="text-xs text-gray-500 uppercase tracking-wide">Profs</span>
                    </div>
                </div>

                <!-- Card Actions -->
                <div class="p-4 flex gap-2">
                    <a href="{{ route('admin.class.show', $class->id) }}"
                       class="flex-1 flex justify-center items-center py-2 px-3 bg-white border border-blue-200 text-blue-600 rounded-lg hover:bg-blue-50 text-sm transition font-medium">
                        <i class="fas fa-eye mr-2"></i> View
                    </a>
                    <a href="{{ route('admin.class.edit', $class->id) }}"
                       class="py-2 px-3 bg-white border border-yellow-200 text-yellow-600 rounded-lg hover:bg-yellow-50 transition" title="Edit">
                        <i class="fas fa-pen"></i>
                    </a>
                    <form action="{{ route('admin.delete.class', $class->id) }}" method="POST"
                          onsubmit="return confirm('Are you sure? All data for this class will be lost.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="h-full py-2 px-3 bg-white border border-red-200 text-red-600 rounded-lg hover:bg-red-50 transition" title="Delete">
                            <i class="fas fa-trash"></i>
                        </button>
                    </form>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-16 bg-white rounded-xl shadow border border-gray-200">
            <div class="bg-gray-50 w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-chalkboard text-3xl text-gray-400"></i>
            </div>
            <p class="text-gray-800 font-medium text-lg">No classes found</p>
            <p class="text-gray-500 mb-6">Get started by creating your first class.</p>
            <button onclick="toggleModal('createClassModal')" class="text-blue-600 font-medium hover:underline">Create Class Now</button>
        </div>
        @endif
    </div>
</div>

<!-- JavaScript for Modal and Search -->
<script>
    // Toggle Modal Function
    function toggleModal(modalID) {
        document.getElementById(modalID).classList.toggle("hidden");
        document.getElementById(modalID).classList.toggle("flex");
    }

    // Filter Classes Function
    function filterClasses() {
        let input = document.getElementById('classSearch');
        let filter = input.value.toUpperCase();
        let grid = document.getElementById('classesGrid');
        let cards = grid.getElementsByClassName('class-card');

        for (let i = 0; i < cards.length; i++) {
            let nameElement = cards[i].querySelector('.class-name');
            let levelElement = cards[i].querySelector('.class-level');

            let nameTxt = nameElement.textContent || nameElement.innerText;
            let levelTxt = levelElement.textContent || levelElement.innerText;

            if (nameTxt.toUpperCase().indexOf(filter) > -1 || levelTxt.toUpperCase().indexOf(filter) > -1) {
                cards[i].style.display = "";
            } else {
                cards[i].style.display = "none";
            }
        }
    }
</script>
@endsection

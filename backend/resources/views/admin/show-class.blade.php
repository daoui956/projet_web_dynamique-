@extends('layouts.admin')
@section('title', 'Class Details')
@section('page-title', $class->name)
@section('page-subtitle', 'Level ' . $class->level . ' • Year ' . $class->year)

@section('content')
<div class="space-y-8">

    <!-- Header Actions -->
    <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="flex items-center gap-2">
            <a href="{{ route('admin.classes') }}" class="text-gray-500 hover:text-blue-600 transition flex items-center gap-1 text-sm font-medium">
                <i class="fas fa-arrow-left"></i> Back to Classes
            </a>
        </div>
        <div class="flex gap-3">
            <!-- IMPORT BUTTON (Triggers JS Function now) -->
            <button onclick="toggleImportModal(true)" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg text-sm font-bold transition shadow-sm flex items-center">
                <i class="fas fa-file-excel mr-2"></i> Import Excel
            </button>

            <a href="{{ route('admin.class.edit', $class->id) }}" class="px-4 py-2 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 text-sm font-bold transition shadow-sm">
                <i class="fas fa-edit mr-2"></i> Edit Class
            </a>
        </div>
    </div>

    <!-- 1. Top Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Students Stat -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center">
            <div class="h-14 w-14 rounded-full bg-blue-50 flex items-center justify-center text-blue-600 mr-4">
                <i class="fas fa-user-graduate text-2xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Students</p>
                <p class="text-3xl font-bold text-gray-800">{{ $class->students_count }}</p>
            </div>
        </div>

        <!-- Professors Stat -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center">
            <div class="h-14 w-14 rounded-full bg-green-50 flex items-center justify-center text-green-600 mr-4">
                <i class="fas fa-chalkboard-teacher text-2xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Professors</p>
                <p class="text-3xl font-bold text-gray-800">{{ $class->professors_count }}</p>
            </div>
        </div>

        <!-- Courses Stat -->
        <div class="bg-white p-6 rounded-xl shadow-sm border border-gray-200 flex items-center">
            <div class="h-14 w-14 rounded-full bg-indigo-50 flex items-center justify-center text-indigo-600 mr-4">
                <i class="fas fa-book text-2xl"></i>
            </div>
            <div>
                <p class="text-sm text-gray-500 font-medium uppercase tracking-wide">Courses</p>
                <p class="text-3xl font-bold text-gray-800">{{ $class->courses_count }}</p>
            </div>
        </div>
    </div>

    <!-- 2. Manual Add User -->
    <div class="bg-gradient-to-r from-gray-50 to-white p-6 rounded-xl shadow-sm border border-gray-200">
        <div class="flex items-center mb-6">
            <div class="bg-blue-600 h-8 w-1 rounded mr-3"></div>
            <h3 class="text-lg font-bold text-gray-800">Add Member Manually</h3>
        </div>

        <form id="assignForm" action="{{ route('admin.assign.class') }}" method="POST" class="grid md:grid-cols-12 gap-4 items-end" onsubmit="return validateForm(event)">
            @csrf
            <input type="hidden" name="class_id" value="{{ $class->id }}">

            <!-- Role Selection -->
            <div class="md:col-span-3">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">Select Role</label>
                <div class="relative">
                    <i class="fas fa-user-tag absolute left-3 top-3.5 text-gray-400"></i>
                    <select name="role" id="roleSelect" onchange="toggleUserSelect()"
                            class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white cursor-pointer transition">
                        <option value="student">Student</option>
                        <option value="professor">Professor</option>
                    </select>
                </div>
            </div>

            <!-- User Selection -->
            <div class="md:col-span-7">
                <label class="block text-xs font-bold text-gray-500 uppercase mb-2 ml-1">Select User</label>

                <!-- Student Select List -->
                <div id="studentWrapper" class="relative">
                    <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
                    <select id="studentSelect" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white cursor-pointer transition">
                        <option value="" disabled selected>-- Choose a Student --</option>
                        @foreach($allStudents as $student)
                            @unless($class->students->contains($student->id))
                                <option value="{{ $student->id }}">{{ $student->name }} ({{ $student->email }})</option>
                            @endunless
                        @endforeach
                    </select>
                </div>

                <!-- Professor Select List (Hidden) -->
                <div id="professorWrapper" class="relative hidden">
                    <i class="fas fa-search absolute left-3 top-3.5 text-gray-400"></i>
                    <select id="professorSelect" class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white cursor-pointer transition">
                        <option value="" disabled selected>-- Choose a Professor --</option>
                        @foreach($allProfessors as $prof)
                            @unless($class->professors->contains($prof->id))
                                <option value="{{ $prof->id }}">{{ $prof->name }} ({{ $prof->email }})</option>
                            @endunless
                        @endforeach
                    </select>
                </div>
            </div>

            <input type="hidden" name="user_id" id="finalUserId">

            <div class="md:col-span-2">
                <button type="submit"
                        class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition font-bold shadow-md flex items-center justify-center transform active:scale-95">
                    <i class="fas fa-plus mr-2"></i> Assign
                </button>
            </div>
        </form>
    </div>

    <!-- 3. Lists Grid -->
    <div class="grid lg:grid-cols-2 gap-8">

        <!-- PROFESSORS LIST -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col h-[500px]">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <i class="fas fa-chalkboard-teacher text-green-600"></i>
                    <h3 class="font-bold text-gray-800">Professors</h3>
                </div>
                <span class="bg-green-100 text-green-700 text-xs font-bold px-2.5 py-1 rounded-full">{{ $class->professors_count }}</span>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar p-2 space-y-2">
                @forelse($class->professors as $prof)
                <div class="group flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 border border-transparent hover:border-gray-100 transition duration-200">
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-green-100 to-green-200 flex items-center justify-center text-green-700 font-bold shadow-sm mr-3">
                            {{ substr($prof->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $prof->name }}</p>
                            <p class="text-xs text-gray-500">{{ $prof->email }}</p>
                        </div>
                    </div>
                    <form action="{{ route('admin.class.remove.user', ['classId' => $class->id, 'userId' => $prof->id]) }}" method="POST"
                          onsubmit="return confirm('Remove {{ $prof->name }} from this class?');">
                        @csrf
                        @method('DELETE')
                        <button class="opacity-0 group-hover:opacity-100 p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-full transition duration-200">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                    </form>
                </div>
                @empty
                <div class="h-full flex flex-col items-center justify-center text-center p-6">
                    <div class="h-16 w-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-user-slash text-gray-300 text-2xl"></i>
                    </div>
                    <p class="text-gray-500 font-medium">No professors</p>
                </div>
                @endforelse
            </div>
        </div>

        <!-- STUDENTS LIST -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden flex flex-col h-[500px]">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 flex justify-between items-center">
                <div class="flex items-center gap-2">
                    <i class="fas fa-user-graduate text-blue-600"></i>
                    <h3 class="font-bold text-gray-800">Students</h3>
                </div>
                <span class="bg-blue-100 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-full">{{ $class->students_count }}</span>
            </div>

            <div class="flex-1 overflow-y-auto custom-scrollbar p-2 space-y-2">
                @forelse($class->students as $student)
                <div class="group flex items-center justify-between p-3 rounded-lg hover:bg-gray-50 border border-transparent hover:border-gray-100 transition duration-200">
                    <div class="flex items-center">
                        <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center text-blue-700 font-bold shadow-sm mr-3">
                            {{ substr($student->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-900">{{ $student->name }}</p>
                            <p class="text-xs text-gray-500">{{ $student->email }}</p>
                        </div>
                    </div>
                    <form action="{{ route('admin.class.remove.user', ['classId' => $class->id, 'userId' => $student->id]) }}" method="POST"
                        onsubmit="return confirm('Remove {{ $student->name }} from this class?');">
                      @csrf
                      @method('DELETE')
                      <button class="opacity-0 group-hover:opacity-100 p-2 text-gray-400 hover:text-red-600 hover:bg-red-50 rounded-full transition duration-200">
                          <i class="fas fa-trash-alt"></i>
                      </button>
                  </form>
                </div>
                @empty
                <div class="h-full flex flex-col items-center justify-center text-center p-6">
                    <div class="h-16 w-16 bg-gray-50 rounded-full flex items-center justify-center mb-4">
                        <i class="fas fa-user-slash text-gray-300 text-2xl"></i>
                    </div>
                    <p class="text-gray-500 font-medium">No students enrolled</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- ================= IMPORT EXCEL MODAL (Hidden by default) ================= -->
<div id="importModal" class="hidden fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">

    <!-- Backdrop -->
    <div class="fixed inset-0 bg-black bg-opacity-50 transition-opacity" onclick="toggleImportModal(false)"></div>

    <!-- Modal Panel -->
    <div class="relative min-h-screen flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full overflow-hidden transform transition-all">
            <div class="bg-green-600 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold text-lg"><i class="fas fa-file-excel mr-2"></i> Import Students</h3>
                <button onclick="toggleImportModal(false)" class="hover:text-green-200"><i class="fas fa-times"></i></button>
            </div>

            <form action="{{ route('admin.class.import', $class->id) }}" method="POST" enctype="multipart/form-data" class="p-6">
                @csrf
                <div class="mb-4">
                    <p class="text-sm text-gray-600 mb-4">
                        Upload an Excel/CSV file. The system will search for students by <strong>Name</strong> or <strong>Email</strong> (Column A).
                    </p>

                    <label class="block w-full border-2 border-dashed border-gray-300 rounded-lg p-8 text-center hover:border-green-500 hover:bg-green-50 transition cursor-pointer">
                        <i class="fas fa-cloud-upload-alt text-4xl text-gray-400 mb-2"></i>
                        <span class="block text-gray-700 font-medium">Click to select file</span>
                        <span class="block text-xs text-gray-500 mt-1">.xlsx, .xls, .csv</span>
                        <input type="file" name="file" class="hidden" required accept=".csv, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel">
                    </label>
                </div>

                <div class="flex justify-end gap-3">
                    <button type="button" onclick="toggleImportModal(false)" class="px-4 py-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">Cancel</button>
                    <button type="submit" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold shadow transition">
                        Start Import
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .custom-scrollbar::-webkit-scrollbar { width: 6px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .custom-scrollbar::-webkit-scrollbar-thumb { background-color: #e2e8f0; border-radius: 20px; }
</style>

<!-- Vanilla JS Scripts (Works without Alpine) -->
<script>
    function toggleImportModal(show) {
        const modal = document.getElementById('importModal');
        if (show) {
            modal.classList.remove('hidden');
        } else {
            modal.classList.add('hidden');
        }
    }

    function toggleUserSelect() {
        const role = document.getElementById('roleSelect').value;
        const studentWrapper = document.getElementById('studentWrapper');
        const professorWrapper = document.getElementById('professorWrapper');
        if (role === 'student') {
            studentWrapper.classList.remove('hidden');
            professorWrapper.classList.add('hidden');
            document.getElementById('professorSelect').value = "";
        } else {
            studentWrapper.classList.add('hidden');
            professorWrapper.classList.remove('hidden');
             document.getElementById('studentSelect').value = "";
        }
    }

    function validateForm(event) {
        const role = document.getElementById('roleSelect').value;
        const finalInput = document.getElementById('finalUserId');
        let selectedValue = role === 'student' ? document.getElementById('studentSelect').value : document.getElementById('professorSelect').value;
        if (!selectedValue) {
            event.preventDefault();
            alert("Please select a user from the list.");
            return false;
        }
        finalInput.value = selectedValue;
        return true;
    }
</script>
@endsection

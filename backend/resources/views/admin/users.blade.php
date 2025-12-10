@extends('layouts.admin')
@section('title', 'User Management')
@section('page-title', 'User Management')
@section('page-subtitle', 'Manage all users: Students, Professors, Admins')

@section('content')
<div class="space-y-6" x-data="{ showProfModal: false, showStudentModal: false }">

    <!-- TOP ACTION BAR -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-white p-4 rounded-xl shadow-sm border border-gray-200">

        <!-- Search & Filter -->
        <div class="flex flex-col md:flex-row gap-3 w-full md:w-auto flex-1">
            <div class="relative w-full md:w-80">
                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fas fa-search text-gray-400"></i>
                </span>
                <input type="text" id="userSearch" onkeyup="filterUsers()"
                       placeholder="Search by name or email..."
                       class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition">
            </div>

            <select id="roleFilter" onchange="filterUsers()"
                    class="w-full md:w-48 px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 cursor-pointer">
                <option value="all">All Roles</option>
                <option value="student">Students</option>
                <option value="professor">Professors</option>
                <option value="admin">Admins</option>
            </select>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-3 w-full md:w-auto">
            <button onclick="toggleModal('modalProfessor')"
                    class="flex-1 md:flex-none bg-blue-600 hover:bg-blue-700 text-white font-medium px-4 py-2 rounded-lg transition shadow flex items-center justify-center gap-2">
                <i class="fas fa-user-tie"></i> <span class="hidden sm:inline">Add Professor</span>
            </button>
            <button onclick="toggleModal('modalStudent')"
                    class="flex-1 md:flex-none bg-green-600 hover:bg-green-700 text-white font-medium px-4 py-2 rounded-lg transition shadow flex items-center justify-center gap-2">
                <i class="fas fa-user-graduate"></i> <span class="hidden sm:inline">Add Student</span>
            </button>
        </div>
    </div>

    <!-- USERS TABLE -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full" id="usersTable">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">User Profile</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Role</th>
                        <th class="px-6 py-4 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Enrolled Classes</th>
                        <th class="px-6 py-4 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @foreach($users as $user)
                    <tr class="user-row hover:bg-gray-50 transition duration-150" data-role="{{ $user->role }}">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="flex-shrink-0 h-10 w-10">
                                    <div class="h-10 w-10 rounded-full flex items-center justify-center text-sm font-bold shadow-sm
                                        {{ $user->role === 'admin' ? 'bg-red-100 text-red-600' :
                                           ($user->role === 'professor' ? 'bg-blue-100 text-blue-600' : 'bg-green-100 text-green-600') }}">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <div class="text-sm font-bold text-gray-900 user-name">{{ $user->name }}</div>
                                    <div class="text-xs text-gray-500 user-email">{{ $user->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-3 py-1 inline-flex text-xs leading-5 font-semibold rounded-full border
                                {{ $user->role === 'admin' ? 'bg-red-50 text-red-700 border-red-100' :
                                   ($user->role === 'professor' ? 'bg-blue-50 text-blue-700 border-blue-100' : 'bg-green-50 text-green-700 border-green-100') }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>
                        <td class="px-6 py-4">
                            @if($user->classes->count() > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($user->classes->take(2) as $class)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 border border-gray-200">
                                            {{ $class->name }}
                                        </span>
                                    @endforeach
                                    @if($user->classes->count() > 2)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-50 text-gray-500 border border-gray-200">
                                            +{{ $user->classes->count() - 2 }} more
                                        </span>
                                    @endif
                                </div>
                            @else
                                <span class="text-xs text-gray-400 italic">No active classes</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                            <div class="flex justify-end gap-2">
                                @if($user->role !== 'admin')
                                <form action="{{ route('admin.reset.password', $user->id) }}" method="POST"
                                      onsubmit="return confirm('Reset password to 123456 for {{ $user->name }}?');">
                                    @csrf
                                    <button type="submit" class="p-2 bg-yellow-50 text-yellow-600 hover:bg-yellow-100 rounded-lg transition" title="Reset Password to 123456">
                                        <i class="fas fa-key"></i>
                                    </button>
                                </form>
                                @endif

                                @if($user->id !== auth()->id() && $user->role !== 'admin')
                                <form action="{{ route('admin.delete.user', $user->id) }}" method="POST"
                                      onsubmit="return confirm('Are you sure you want to delete {{ $user->name }}? This action cannot be undone.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition" title="Delete User">
                                        <i class="fas fa-trash-alt"></i>
                                    </button>
                                </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <!-- Empty State (Hidden by default, shown via JS) -->
            <div id="noResults" class="hidden text-center py-12">
                <div class="bg-gray-50 rounded-full h-16 w-16 flex items-center justify-center mx-auto mb-4">
                    <i class="fas fa-search text-gray-400 text-2xl"></i>
                </div>
                <h3 class="text-gray-900 font-medium">No users found</h3>
                <p class="text-gray-500 text-sm mt-1">Try adjusting your search or filters</p>
            </div>
        </div>
    </div>
</div>

<!-- ================= MODALS ================= -->

<!-- Create Professor Modal -->
<div id="modalProfessor" class="fixed inset-0 z-50 hidden" role="dialog">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="toggleModal('modalProfessor')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all">
            <div class="bg-blue-600 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold">Create New Professor</h3>
                <button onclick="toggleModal('modalProfessor')" class="hover:text-blue-200"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('admin.create.professor') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Full Name</label>
                    <input name="name" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email Address</label>
                    <input name="email" type="email" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Password</label>
                    <input name="password" type="password" required minlength="6" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>
                <button class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow transition">Create Account</button>
            </form>
        </div>
    </div>
</div>

<!-- Create Student Modal -->
<div id="modalStudent" class="fixed inset-0 z-50 hidden" role="dialog">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="toggleModal('modalStudent')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all">
            <div class="bg-green-600 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold">Create New Student</h3>
                <button onclick="toggleModal('modalStudent')" class="hover:text-green-200"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('admin.create.student') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Full Name</label>
                    <input name="name" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email Address</label>
                    <input name="email" type="email" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Password</label>
                    <input name="password" type="password" required minlength="6" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500">
                </div>
                <button class="w-full py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold shadow transition">Create Account</button>
            </form>
        </div>
    </div>
</div>

<!-- JAVASCRIPT FOR FILTERING & MODALS -->
<script>
    // Modal Toggle
    function toggleModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal.classList.contains('hidden')) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        } else {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
        }
    }

    // Filter Users Table
    function filterUsers() {
        const searchInput = document.getElementById('userSearch').value.toLowerCase();
        const roleFilter = document.getElementById('roleFilter').value.toLowerCase();
        const rows = document.querySelectorAll('.user-row');
        let hasResults = false;

        rows.forEach(row => {
            const name = row.querySelector('.user-name').textContent.toLowerCase();
            const email = row.querySelector('.user-email').textContent.toLowerCase();
            const role = row.getAttribute('data-role').toLowerCase();

            const matchesSearch = name.includes(searchInput) || email.includes(searchInput);
            const matchesRole = roleFilter === 'all' || role === roleFilter;

            if (matchesSearch && matchesRole) {
                row.style.display = '';
                hasResults = true;
            } else {
                row.style.display = 'none';
            }
        });

        // Toggle Empty State
        const noResults = document.getElementById('noResults');
        if (hasResults) {
            noResults.classList.add('hidden');
        } else {
            noResults.classList.remove('hidden');
        }
    }
</script>
@endsection

@extends('layouts.admin')
@section('title', 'Admin Dashboard')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'System Overview & Quick Actions')

@section('content')
<div class="space-y-8">

    <!-- 1. STATS OVERVIEW -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Users Stat -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-xl p-6 shadow-lg relative overflow-hidden group">
            <div class="absolute right-0 top-0 opacity-10 transform translate-x-2 -translate-y-2 group-hover:scale-110 transition duration-300">
                <i class="fas fa-users text-8xl"></i>
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 bg-white/20 rounded-lg backdrop-blur-sm">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <p class="text-blue-100 font-medium">Total Users</p>
                </div>
                <h3 class="text-4xl font-bold mb-2">{{ $stats['total_users'] ?? 0 }}</h3>
                <div class="flex text-xs font-medium text-blue-100 gap-4">
                    <span><i class="fas fa-user-graduate mr-1"></i> {{ $stats['students'] ?? 0 }} Students</span>
                    <span><i class="fas fa-chalkboard-teacher mr-1"></i> {{ $stats['professors'] ?? 0 }} Profs</span>
                </div>
            </div>
        </div>

        <!-- Classes Stat -->
        <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 text-white rounded-xl p-6 shadow-lg relative overflow-hidden group">
            <div class="absolute right-0 top-0 opacity-10 transform translate-x-2 -translate-y-2 group-hover:scale-110 transition duration-300">
                <i class="fas fa-chalkboard text-8xl"></i>
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 bg-white/20 rounded-lg backdrop-blur-sm">
                        <i class="fas fa-chalkboard-teacher text-xl"></i>
                    </div>
                    <p class="text-emerald-100 font-medium">Active Classes</p>
                </div>
                <h3 class="text-4xl font-bold mb-2">{{ $stats['classes'] ?? 0 }}</h3>
                <div class="flex text-xs font-medium text-emerald-100 gap-4">
                    <span><i class="fas fa-book mr-1"></i> {{ $stats['courses'] ?? 0 }} Courses</span>
                </div>
            </div>
        </div>

        <!-- Requests Stat -->
        <div class="bg-gradient-to-br from-violet-500 to-violet-600 text-white rounded-xl p-6 shadow-lg relative overflow-hidden group">
            <div class="absolute right-0 top-0 opacity-10 transform translate-x-2 -translate-y-2 group-hover:scale-110 transition duration-300">
                <i class="fas fa-file-alt text-8xl"></i>
            </div>
            <div class="relative z-10">
                <div class="flex items-center gap-4 mb-4">
                    <div class="p-3 bg-white/20 rounded-lg backdrop-blur-sm">
                        <i class="fas fa-file-signature text-xl"></i>
                    </div>
                    <p class="text-violet-100 font-medium">Pending Requests</p>
                </div>
                <h3 class="text-4xl font-bold mb-2">{{ $stats['pending_requests'] ?? 0 }}</h3>
                <a href="#document-requests" class="text-xs bg-white/20 hover:bg-white/30 px-3 py-1 rounded-full transition inline-flex items-center">
                    Review Pending <i class="fas fa-arrow-right ml-1"></i>
                </a>
            </div>
        </div>
    </div>

    <!-- 2. QUICK ACTIONS BAR -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
        <h3 class="text-gray-800 font-bold mb-4 flex items-center">
            <i class="fas fa-bolt text-yellow-500 mr-2"></i> Quick Actions
        </h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <!-- Button: Create Professor -->
            <button onclick="toggleModal('modalProfessor')" class="flex items-center p-4 border border-gray-200 rounded-xl hover:border-blue-500 hover:bg-blue-50 transition group text-left">
                <div class="h-10 w-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mr-4 group-hover:bg-blue-600 group-hover:text-white transition">
                    <i class="fas fa-user-tie"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">Add Professor</h4>
                    <p class="text-xs text-gray-500">Create new faculty account</p>
                </div>
            </button>

            <!-- Button: Create Student -->
            <button onclick="toggleModal('modalStudent')" class="flex items-center p-4 border border-gray-200 rounded-xl hover:border-green-500 hover:bg-green-50 transition group text-left">
                <div class="h-10 w-10 rounded-full bg-green-100 text-green-600 flex items-center justify-center mr-4 group-hover:bg-green-600 group-hover:text-white transition">
                    <i class="fas fa-user-graduate"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">Add Student</h4>
                    <p class="text-xs text-gray-500">Register new student</p>
                </div>
            </button>

            <!-- Button: Create Class -->
            <button onclick="toggleModal('modalClass')" class="flex items-center p-4 border border-gray-200 rounded-xl hover:border-indigo-500 hover:bg-indigo-50 transition group text-left">
                <div class="h-10 w-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mr-4 group-hover:bg-indigo-600 group-hover:text-white transition">
                    <i class="fas fa-chalkboard"></i>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800">Create Class</h4>
                    <p class="text-xs text-gray-500">Setup new classroom</p>
                </div>
            </button>
        </div>
    </div>

    <!-- 3. MAIN GRID (Requests & Recent Classes) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

        <!-- COLUMN LEFT: Document Requests (Priority) -->
        <div id="document-requests" class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col h-full">
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-xl">
                <h3 class="font-bold text-gray-800">Document Requests</h3>
                @if($stats['pending_requests'] > 0)
                <span class="bg-red-100 text-red-600 text-xs font-bold px-2 py-1 rounded-full animate-pulse">
                    {{ $stats['pending_requests'] }} Pending
                </span>
                @endif
            </div>

            <div class="flex-1 overflow-y-auto max-h-[600px] p-4 space-y-4">
                @forelse($recentRequests as $req)
                <div class="border border-gray-100 rounded-lg p-4 hover:shadow-md transition bg-white">
                    <!-- Request Header -->
                    <div class="flex justify-between items-start mb-3">
                        <div class="flex items-center">
                            <div class="h-9 w-9 rounded-full bg-gray-100 flex items-center justify-center text-gray-600 font-bold text-sm mr-3">
                                {{ strtoupper(substr($req->user->name, 0, 1)) }}
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $req->user->name }}</p>
                                <p class="text-xs text-gray-500">{{ $req->type }} • {{ $req->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <span class="text-xs px-2 py-1 rounded border {{ $req->status == 'pending' ? 'bg-orange-50 text-orange-600 border-orange-100' : 'bg-green-50 text-green-600 border-green-100' }}">
                            {{ ucfirst($req->status) }}
                        </span>
                    </div>

                    <!-- Message Body -->
                    <div class="bg-gray-50 p-3 rounded text-sm text-gray-700 italic mb-3">
                        "{{ $req->message }}"
                    </div>

                    <!-- Actions Area -->
                    @if($req->status == 'pending')
                    <div>
                        <!-- Toggle Button -->
                        <button onclick="toggleReply('reply-form-{{ $req->id }}')"
                                class="text-blue-600 text-xs font-medium hover:underline flex items-center">
                            <i class="fas fa-reply mr-1"></i> Reply to request
                        </button>

                        <!-- Hidden Reply Form -->
                        <div id="reply-form-{{ $req->id }}" class="hidden mt-3 border-t border-gray-100 pt-3">
                            <form action="{{ route('admin.reply.request', $req->id) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <textarea name="reply" rows="2" placeholder="Write message..."
                                          class="w-full text-sm border-gray-300 rounded focus:ring-blue-500 mb-2 p-2 bg-white"></textarea>

                                <div class="flex justify-between items-center">
                                    <label class="cursor-pointer text-xs text-gray-500 hover:text-blue-600 flex items-center">
                                        <i class="fas fa-paperclip mr-1"></i> Attach File
                                        <input type="file" name="file" class="hidden">
                                    </label>
                                    <button class="bg-blue-600 text-white text-xs px-3 py-1.5 rounded hover:bg-blue-700">Send</button>
                                </div>
                            </form>
                        </div>
                    </div>
                    @else
                        <!-- Existing Reply Display -->
                        @if($req->reply)
                        <div class="mt-2 text-xs text-green-700 bg-green-50 p-2 rounded border border-green-100 flex items-center">
                            <i class="fas fa-check mr-2"></i>
                            <span>Replied: {{ Str::limit($req->reply->reply, 40) }}</span>
                            @if($req->reply->file_name)
                                <a href="{{ asset('storage/'.$req->reply->file_path) }}" class="ml-auto underline">File</a>
                            @endif
                        </div>
                        @endif
                    @endif
                </div>
                @empty
                <div class="text-center py-10">
                    <div class="bg-gray-50 rounded-full h-12 w-12 flex items-center justify-center mx-auto mb-3">
                        <i class="fas fa-check text-green-500"></i>
                    </div>
                    <p class="text-gray-500 text-sm">All caught up!</p>
                </div>
                @endforelse
            </div>
            <div class="p-3 border-t border-gray-100 text-center bg-gray-50 rounded-b-xl">
                 <a href="{{ route('admin.document.requests') }}" class="text-xs text-blue-600 font-bold hover:underline">View All History</a>
            </div>
        </div>

        <!-- COLUMN RIGHT: Recent Classes -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 flex flex-col h-full">
            <div class="px-6 py-4 border-b border-gray-100 bg-gray-50 rounded-t-xl">
                <h3 class="font-bold text-gray-800">Recently Created Classes</h3>
            </div>

            <div class="divide-y divide-gray-100">
                @forelse($recentClasses as $class)
                <div class="px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition group">
                    <div>
                        <h4 class="font-bold text-gray-800 group-hover:text-blue-600 transition">{{ $class->name }}</h4>
                        <div class="text-xs text-gray-500 mt-1 flex gap-2">
                            <span class="bg-gray-100 px-1.5 rounded">{{ $class->level }}</span>
                            <span>{{ $class->year }}</span>
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="text-right hidden sm:block">
                            <div class="text-xs text-gray-500" title="Students">
                                <i class="fas fa-user-graduate mr-1"></i> {{ $class->students_count }}
                            </div>
                            <div class="text-xs text-gray-500" title="Professors">
                                <i class="fas fa-chalkboard-teacher mr-1"></i> {{ $class->professors_count }}
                            </div>
                        </div>
                        <a href="{{ route('admin.class.show', $class->id) }}" class="p-2 text-gray-400 hover:text-blue-600 hover:bg-blue-50 rounded-full transition">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </div>
                </div>
                @empty
                <div class="p-8 text-center text-gray-500">No classes found.</div>
                @endforelse
            </div>

            <div class="p-3 border-t border-gray-100 text-center bg-gray-50 rounded-b-xl mt-auto">
                <a href="{{ route('admin.classes') }}" class="text-xs text-blue-600 font-bold hover:underline">Manage All Classes</a>
            </div>
        </div>
    </div>

</div>

<!-- ================= MODALS ================= -->

<!-- 1. Modal: Create Professor -->
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
                    <input name="name" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email Address</label>
                    <input name="email" type="email" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Password</label>
                    <input name="password" type="password" required minlength="6" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>
                <button class="w-full py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-bold shadow transition">Create Account</button>
            </form>
        </div>
    </div>
</div>

<!-- 2. Modal: Create Student -->
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
                    <input name="name" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Email Address</label>
                    <input name="email" type="email" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Password</label>
                    <input name="password" type="password" required minlength="6" class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                </div>
                <button class="w-full py-3 bg-green-600 hover:bg-green-700 text-white rounded-lg font-bold shadow transition">Create Account</button>
            </form>
        </div>
    </div>
</div>

<!-- 3. Modal: Create Class -->
<div id="modalClass" class="fixed inset-0 z-50 hidden" role="dialog">
    <div class="fixed inset-0 bg-black/50 backdrop-blur-sm transition-opacity" onclick="toggleModal('modalClass')"></div>
    <div class="fixed inset-0 flex items-center justify-center p-4">
        <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all">
            <div class="bg-indigo-600 px-6 py-4 flex justify-between items-center text-white">
                <h3 class="font-bold">Create New Class</h3>
                <button onclick="toggleModal('modalClass')" class="hover:text-indigo-200"><i class="fas fa-times"></i></button>
            </div>
            <form action="{{ route('admin.create.class') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Class Name</label>
                    <input name="name" placeholder="e.g. Computer Science A" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Level</label>
                        <input name="level" placeholder="e.g. L3" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-gray-700 uppercase mb-1">Year</label>
                        <input name="year" type="number" min="2020" max="2030" value="{{ date('Y') }}" required class="w-full px-4 py-2 border rounded-lg focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>
                <button class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold shadow transition">Create Class</button>
            </form>
        </div>
    </div>
</div>

<!-- Scripts -->
<script>
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

    function toggleReply(id) {
        const el = document.getElementById(id);
        el.classList.toggle('hidden');
    }
</script>
@endsection

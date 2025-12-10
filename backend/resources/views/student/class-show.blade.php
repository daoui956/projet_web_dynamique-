@extends('layouts.student')
@section('title', $class->name)
@section('page-title', $class->name)
@section('page-subtitle', 'Class Dashboard')

@section('content')
<div class="space-y-8">
    <!-- Class Header -->
    <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl p-8 text-white shadow-xl">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center">
            <div>
                <h1 class="text-3xl font-bold mb-2">{{ $class->name }}</h1>
                <p class="text-blue-100 mb-4">
                    Level: {{ $class->level ?? 'All Levels' }} •
                    Year: {{ $class->year ?? '2024' }}
                </p>
                <div class="flex flex-wrap gap-4">
                    <div class="bg-white/20 px-4 py-2 rounded-lg">
                        <i class="fas fa-book mr-2"></i>
                        {{ $class->courses->count() }} Courses
                    </div>
                    <div class="bg-white/20 px-4 py-2 rounded-lg">
                        <i class="fas fa-users mr-2"></i>
                        {{ $class->students->count() }} Students
                    </div>
                    @if($class->professors->count() > 0)
                    <div class="bg-white/20 px-4 py-2 rounded-lg">
                        <i class="fas fa-user-tie mr-2"></i>
                        {{ $class->professors->count() }} Professors
                    </div>
                    @endif
                </div>
            </div>
            <div class="mt-4 md:mt-0">
                <a href="{{ route('student.classes') }}"
                   class="bg-white text-blue-600 hover:bg-blue-50 px-6 py-3 rounded-lg font-semibold transition">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Classes
                </a>
            </div>
        </div>
    </div>

    <!-- Professors -->
    @if($class->professors->count() > 0)
    <section>
        <h2 class="text-2xl font-bold text-gray-800 mb-4">Professors</h2>
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($class->professors as $professor)
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <div class="flex items-center">
                    <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                        <i class="fas fa-user-tie text-blue-600 text-xl"></i>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800">{{ $professor->name }}</h3>
                        <p class="text-gray-600 text-sm">{{ $professor->email }}</p>
                        <p class="text-blue-600 text-sm font-medium">Professor</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif

    <!-- Courses Section -->
    <section>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Class Courses</h2>
            <span class="bg-blue-100 text-blue-800 text-sm px-4 py-2 rounded-full">
                {{ $class->courses->count() }} courses
            </span>
        </div>

        @if($class->courses->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($class->courses as $course)
            <a href="{{ route('student.class.course.show', ['class' => $class->id, 'course' => $course->id]) }}"
               class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden hover:shadow-lg transition duration-300">
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
                    <h3 class="text-lg font-bold text-gray-800 mb-2">{{ $course->title }}</h3>

                    <p class="text-gray-600 text-sm mb-4 line-clamp-2">
                        {{ $course->description ?: 'No description available' }}
                    </p>

                    <!-- Course Info -->
                    <div class="flex items-center justify-between text-sm text-gray-500 mb-4">
                        <div class="flex items-center">
                            <i class="fas fa-user-tie mr-2"></i>
                            <span>{{ $course->professor->name ?? 'Unknown' }}</span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-file-alt mr-2"></i>
                            <span>{{ $course->files->count() }} files</span>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="flex items-center justify-between">
                        <span class="text-sm px-3 py-1 bg-blue-100 text-blue-800 rounded-full">
                            {{ $course->level ?? 'All Levels' }}
                        </span>
                        <span class="text-sm text-blue-600 font-medium">
                            View Course <i class="fas fa-arrow-right ml-1"></i>
                        </span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        @else
        <div class="text-center py-12 bg-gray-50 rounded-xl">
            <i class="fas fa-book-open text-5xl text-gray-400 mb-4"></i>
            <p class="text-gray-600 text-lg">No courses available in this class yet</p>
            <p class="text-gray-500">Check back later or contact your professor</p>
        </div>
        @endif
    </section>

    <!-- Class Chat Section -->
    <section id="chat">
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <!-- Chat Header -->
            <div class="bg-gray-50 border-b border-gray-200 p-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-comments mr-2"></i> Class Chat
                    </h2>
                    <span class="text-sm text-gray-600">
                        {{ $class->messages->count() }} messages
                    </span>
                </div>
            </div>

            <!-- Chat Messages -->
            <div class="h-96 overflow-y-auto p-4 space-y-4" id="chatMessages">
                @foreach($class->messages->reverse() as $message)
                <div class="flex {{ $message->user_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs lg:max-w-md rounded-lg p-4
                              {{ $message->user_id === Auth::id()
                                 ? 'bg-blue-600 text-white rounded-br-none'
                                 : 'bg-gray-100 text-gray-800 rounded-bl-none' }}">
                        <div class="flex items-center mb-2">
                            @if($message->user_id !== Auth::id())
                            <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                                <i class="fas fa-user text-blue-600 text-sm"></i>
                            </div>
                            @endif
                            <span class="font-semibold {{ $message->user_id === Auth::id() ? 'text-blue-100' : 'text-gray-700' }}">
                                {{ $message->user->name }}
                            </span>
                            <span class="text-xs ml-2 opacity-75">
                                {{ $message->created_at->format('h:i A') }}
                            </span>
                        </div>
                        <p class="text-sm">{{ $message->message }}</p>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Chat Input -->
            <div class="border-t border-gray-200 p-4">
                <form id="chatForm" class="flex space-x-3">
                    @csrf
                    <input type="hidden" name="class_id" value="{{ $class->id }}">

                    <div class="flex-1">
                        <input type="text"
                               name="message"
                               id="chatInput"
                               placeholder="Type your message here..."
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    </div>

                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition">
                        <i class="fas fa-paper-plane mr-2"></i> Send
                    </button>
                </form>
            </div>
        </div>
    </section>
</div>

<!-- JavaScript for Chat -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const chatMessages = document.getElementById('chatMessages');

    // Scroll to bottom of chat
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // Handle form submission
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const message = chatInput.value.trim();
        if (!message) return;

        // Disable input while sending
        chatInput.disabled = true;

        // Send message via AJAX
        fetch('{{ route("student.class.chat", $class->id) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Clear input
                chatInput.value = '';

                // Add new message to chat
                const message = data.message;
                const isCurrentUser = message.user_id === {{ Auth::id() }};

                const messageHtml = `
                    <div class="flex ${isCurrentUser ? 'justify-end' : 'justify-start'}">
                        <div class="max-w-xs lg:max-w-md rounded-lg p-4
                                  ${isCurrentUser
                                     ? 'bg-blue-600 text-white rounded-br-none'
                                     : 'bg-gray-100 text-gray-800 rounded-bl-none'}">
                            <div class="flex items-center mb-2">
                                ${!isCurrentUser ? `
                                <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center mr-2">
                                    <i class="fas fa-user text-blue-600 text-sm"></i>
                                </div>
                                ` : ''}
                                <span class="font-semibold ${isCurrentUser ? 'text-blue-100' : 'text-gray-700'}">
                                    ${message.user.name}
                                </span>
                                <span class="text-xs ml-2 opacity-75">
                                    Just now
                                </span>
                            </div>
                            <p class="text-sm">${message.message}</p>
                        </div>
                    </div>
                `;

                chatMessages.insertAdjacentHTML('beforeend', messageHtml);
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to send message');
        })
        .finally(() => {
            chatInput.disabled = false;
            chatInput.focus();
        });
    });

    // Auto-focus chat input
    chatInput.focus();
});
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

#chatMessages::-webkit-scrollbar {
    width: 6px;
}

#chatMessages::-webkit-scrollbar-track {
    background: #f1f1f1;
}

#chatMessages::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

#chatMessages::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
@endsection

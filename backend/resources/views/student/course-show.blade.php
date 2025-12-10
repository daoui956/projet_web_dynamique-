@extends('layouts.student')
@section('title', $course->title)
@section('page-title', $course->title)
@section('page-subtitle', 'in ' . $class->name)

@section('content')
<div class="space-y-8">
    <!-- Breadcrumb -->
    <nav class="flex" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
            <li class="inline-flex items-center">
                <a href="{{ route('student.dashboard') }}" class="text-gray-700 hover:text-blue-600">
                    <i class="fas fa-home mr-2"></i> Dashboard
                </a>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="{{ route('student.classes') }}" class="text-gray-700 hover:text-blue-600">
                        My Classes
                    </a>
                </div>
            </li>
            <li>
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <a href="{{ route('student.class.show', $class->id) }}" class="text-gray-700 hover:text-blue-600">
                        {{ $class->name }}
                    </a>
                </div>
            </li>
            <li aria-current="page">
                <div class="flex items-center">
                    <i class="fas fa-chevron-right text-gray-400 mx-2"></i>
                    <span class="text-gray-500">{{ $course->title }}</span>
                </div>
            </li>
        </ol>
    </nav>

    <!-- Course Header -->
    <div class="bg-white border border-gray-200 rounded-2xl shadow-sm overflow-hidden">
        <!-- Course Thumbnail -->
        @if($course->thumbnail)
        <div class="h-64 md:h-80 overflow-hidden">
            <img src="{{ asset('storage/' . $course->thumbnail) }}"
                 alt="{{ $course->title }}"
                 class="w-full h-full object-cover">
        </div>
        @else
        <div class="h-64 md:h-80 bg-gradient-to-r from-blue-100 to-indigo-100 flex items-center justify-center">
            <i class="fas fa-book-open text-8xl text-blue-300"></i>
        </div>
        @endif

        <!-- Course Details -->
        <div class="p-8">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-6">
                <div class="mb-4 lg:mb-0">
                    <h1 class="text-3xl font-bold text-gray-800 mb-2">{{ $course->title }}</h1>
                    <div class="flex items-center space-x-4 text-gray-600">
                        <span class="flex items-center">
                            <i class="fas fa-user-tie mr-2"></i>
                            {{ $course->professor->name ?? 'Unknown Professor' }}
                        </span>
                        @if($course->level)
                        <span class="flex items-center">
                            <i class="fas fa-chart-line mr-2"></i>
                            {{ $course->level }}
                        </span>
                        @endif
                        @if($course->duration)
                        <span class="flex items-center">
                            <i class="fas fa-clock mr-2"></i>
                            {{ $course->duration }}
                        </span>
                        @endif
                    </div>
                </div>
                <a href="{{ route('student.class.show', $class->id) }}"
                   class="bg-gray-100 hover:bg-gray-200 text-gray-800 px-6 py-3 rounded-lg font-medium transition">
                    <i class="fas fa-arrow-left mr-2"></i> Back to Class
                </a>
            </div>

            <!-- Course Description -->
            @if($course->description)
            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-800 mb-3">Course Description</h2>
                <div class="prose max-w-none text-gray-700">
                    {!! nl2br(e($course->description)) !!}
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Files Section -->
    <section>
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold text-gray-800">Course Files</h2>
            <span class="bg-blue-100 text-blue-800 text-sm px-4 py-2 rounded-full">
                {{ $course->files->count() }} files
            </span>
        </div>

        @if($course->files->count() > 0)
        <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($course->files as $file)
            <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden hover:shadow-md transition">
                <div class="p-6">
                    <!-- File Icon -->
                    <div class="flex items-center mb-4">
                        @php
                            $extension = pathinfo($file->file_name, PATHINFO_EXTENSION);
                            $icon = match(strtolower($extension)) {
                                'pdf' => 'fas fa-file-pdf text-red-500',
                                'doc', 'docx' => 'fas fa-file-word text-blue-500',
                                'xls', 'xlsx' => 'fas fa-file-excel text-green-500',
                                'ppt', 'pptx' => 'fas fa-file-powerpoint text-orange-500',
                                'jpg', 'jpeg', 'png', 'gif', 'svg' => 'fas fa-file-image text-purple-500',
                                'zip', 'rar', '7z' => 'fas fa-file-archive text-yellow-500',
                                default => 'fas fa-file text-gray-500'
                            };
                        @endphp
                        <div class="mr-4">
                            <i class="{{ $icon }} text-4xl"></i>
                        </div>
                        <div class="flex-1">
                            <h3 class="font-bold text-gray-800 truncate">{{ $file->file_name }}</h3>
                            <p class="text-sm text-gray-600">
                                @if($file->file_size)
                                    {{ round($file->file_size / 1024, 1) }} KB
                                @endif
                                • {{ strtoupper($extension) }}
                            </p>
                        </div>
                    </div>

                    <!-- File Actions -->
                    <div class="flex space-x-3">
                        <a href="{{ route('student.file.download', $file->id) }}"
                           class="flex-1 bg-blue-600 hover:bg-blue-700 text-white text-center py-2 rounded-lg text-sm transition">
                            <i class="fas fa-download mr-2"></i> Download
                        </a>
                        <button onclick="previewFile('{{ asset('storage/' . $file->file_path) }}', '{{ $file->file_name }}')"
                                class="flex-1 bg-gray-200 hover:bg-gray-300 text-gray-800 py-2 rounded-lg text-sm">
                            <i class="fas fa-eye mr-2"></i> Preview
                        </button>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div class="text-center py-12 bg-gray-50 rounded-xl">
            <i class="fas fa-folder-open text-5xl text-gray-400 mb-4"></i>
            <p class="text-gray-600 text-lg">No files available for this course yet</p>
            <p class="text-gray-500">Check back later for uploaded materials</p>
        </div>
        @endif
    </section>

    <!-- Course Chat Section -->
    <section>
        <div class="bg-white border border-gray-200 rounded-xl shadow-sm overflow-hidden">
            <!-- Chat Header -->
            <div class="bg-gray-50 border-b border-gray-200 p-4">
                <div class="flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-800">
                        <i class="fas fa-comments mr-2"></i> Course Discussion
                    </h2>
                    <span class="text-sm text-gray-600">
                        {{ $course->messages->count() }} messages
                    </span>
                </div>
            </div>

            <!-- Chat Messages -->
            <div class="h-96 overflow-y-auto p-4 space-y-4" id="courseChatMessages">
                @foreach($course->messages->reverse() as $message)
                <div class="flex {{ $message->user_id === Auth::id() ? 'justify-end' : 'justify-start' }}">
                    <div class="max-w-xs lg:max-w-md rounded-lg p-4
                              {{ $message->user_id === Auth::id()
                                 ? 'bg-green-600 text-white rounded-br-none'
                                 : 'bg-gray-100 text-gray-800 rounded-bl-none' }}">
                        <div class="flex items-center mb-2">
                            @if($message->user_id !== Auth::id())
                            <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-2">
                                <i class="fas fa-user text-green-600 text-sm"></i>
                            </div>
                            @endif
                            <span class="font-semibold {{ $message->user_id === Auth::id() ? 'text-green-100' : 'text-gray-700' }}">
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
                <form id="courseChatForm" class="flex space-x-3">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->id }}">

                    <div class="flex-1">
                        <input type="text"
                               name="message"
                               id="courseChatInput"
                               placeholder="Ask a question or share your thoughts..."
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500">
                    </div>

                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition">
                        <i class="fas fa-paper-plane mr-2"></i> Send
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Comments Section -->
    @if($course->comments->count() > 0)
    <section>
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Comments</h2>
        <div class="space-y-6">
            @foreach($course->comments as $comment)
            <div class="bg-white border border-gray-200 rounded-xl p-6">
                <div class="flex items-start mb-4">
                    <div class="w-10 h-10 bg-blue-100 rounded-full flex items-center justify-center mr-3">
                        <span class="text-blue-600 font-semibold">
                            {{ strtoupper(substr($comment->user->name, 0, 1)) }}
                        </span>
                    </div>
                    <div class="flex-1">
                        <div class="flex justify-between items-start">
                            <div>
                                <h4 class="font-bold text-gray-800">{{ $comment->user->name }}</h4>
                                <p class="text-sm text-gray-600">
                                    {{ $comment->created_at->diffForHumans() }}
                                </p>
                            </div>
                        </div>
                        <p class="text-gray-700 mt-3">{{ $comment->content }}</p>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </section>
    @endif
</div>

<!-- File Preview Modal -->
<div id="filePreviewModal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl w-full max-w-4xl max-h-[90vh] overflow-hidden">
        <div class="flex justify-between items-center p-6 border-b">
            <h3 class="text-xl font-bold text-gray-800" id="previewFileName"></h3>
            <button onclick="closePreview()"
                    class="text-gray-500 hover:text-gray-700">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        <div class="p-6 overflow-auto max-h-[70vh]" id="previewContent">
            <!-- Content will be loaded here -->
        </div>
    </div>
</div>

<!-- JavaScript -->
<script>
// File Preview
function previewFile(url, fileName) {
    const modal = document.getElementById('filePreviewModal');
    const fileNameElement = document.getElementById('previewFileName');
    const previewContent = document.getElementById('previewContent');

    fileNameElement.textContent = fileName;

    // Check file type
    const extension = fileName.split('.').pop().toLowerCase();

    if (['jpg', 'jpeg', 'png', 'gif', 'svg'].includes(extension)) {
        previewContent.innerHTML = `
            <div class="flex justify-center">
                <img src="${url}" alt="${fileName}" class="max-w-full h-auto rounded-lg">
            </div>
        `;
    } else if (extension === 'pdf') {
        previewContent.innerHTML = `
            <iframe src="${url}" class="w-full h-[600px]" frameborder="0"></iframe>
        `;
    } else {
        previewContent.innerHTML = `
            <div class="text-center py-12">
                <i class="fas fa-file text-6xl text-gray-400 mb-4"></i>
                <p class="text-gray-600">Preview not available for this file type</p>
                <p class="text-gray-500 text-sm mt-2">Please download the file to view it</p>
                <a href="${url}" download
                   class="inline-block mt-4 bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg">
                    <i class="fas fa-download mr-2"></i> Download File
                </a>
            </div>
        `;
    }

    modal.classList.remove('hidden');
}

function closePreview() {
    document.getElementById('filePreviewModal').classList.add('hidden');
}

// Course Chat
document.addEventListener('DOMContentLoaded', function() {
    const chatForm = document.getElementById('courseChatForm');
    const chatInput = document.getElementById('courseChatInput');
    const chatMessages = document.getElementById('courseChatMessages');

    // Scroll to bottom
    chatMessages.scrollTop = chatMessages.scrollHeight;

    // Handle form submission
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();

        const message = chatInput.value.trim();
        if (!message) return;

        chatInput.disabled = true;

        fetch('{{ route("student.course.chat", $course->id) }}', {
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
                chatInput.value = '';

                const message = data.message;
                const isCurrentUser = message.user_id === {{ Auth::id() }};

                const messageHtml = `
                    <div class="flex ${isCurrentUser ? 'justify-end' : 'justify-start'}">
                        <div class="max-w-xs lg:max-w-md rounded-lg p-4
                                  ${isCurrentUser
                                     ? 'bg-green-600 text-white rounded-br-none'
                                     : 'bg-gray-100 text-gray-800 rounded-bl-none'}">
                            <div class="flex items-center mb-2">
                                ${!isCurrentUser ? `
                                <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center mr-2">
                                    <i class="fas fa-user text-green-600 text-sm"></i>
                                </div>
                                ` : ''}
                                <span class="font-semibold ${isCurrentUser ? 'text-green-100' : 'text-gray-700'}">
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

    // Close preview modal with Escape
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closePreview();
        }
    });

    // Close preview when clicking outside
    document.getElementById('filePreviewModal').addEventListener('click', function(e) {
        if (e.target.id === 'filePreviewModal') {
            closePreview();
        }
    });

    chatInput.focus();
});
</script>

<style>
.prose {
    line-height: 1.75;
}

.prose p {
    margin-bottom: 1rem;
}

#courseChatMessages::-webkit-scrollbar {
    width: 6px;
}

#courseChatMessages::-webkit-scrollbar-track {
    background: #f1f1f1;
}

#courseChatMessages::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 3px;
}

#courseChatMessages::-webkit-scrollbar-thumb:hover {
    background: #555;
}
</style>
@endsection

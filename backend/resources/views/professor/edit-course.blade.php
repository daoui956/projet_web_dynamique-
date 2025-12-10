@extends('layouts.app')
@section('title', 'Edit Course')

@section('content')
<div class="max-w-4xl mx-auto py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-2">Edit Course</h1>
        <p class="text-gray-600">Update course details and manage files</p>
    </div>

    @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-xl shadow-lg border border-gray-200 overflow-hidden">
        <!-- Tabs -->
        <div class="border-b border-gray-200">
            <nav class="flex">
                <button id="details-tab" class="tab-button active px-6 py-4 text-lg font-medium text-gray-800 border-b-2 border-blue-500">
                    <i class="fas fa-edit mr-2"></i> Course Details
                </button>
                <button id="files-tab" class="tab-button px-6 py-4 text-lg font-medium text-gray-600 hover:text-gray-800">
                    <i class="fas fa-file-upload mr-2"></i> Files ({{ $course->files->count() }})
                </button>
            </nav>
        </div>

        <!-- Course Details Tab -->
        <div id="details-content" class="tab-content p-8">
            <form action="{{ route('professor.course.update', $course->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="grid md:grid-cols-2 gap-8">
                    <!-- Left Column -->
                    <div class="space-y-6">
                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-2">Course Title *</label>
                            <input type="text" name="title" value="{{ old('title', $course->title) }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        </div>

                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-2">Description *</label>
                            <textarea name="description" rows="6"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500" required>{{ old('description', $course->description) }}</textarea>
                        </div>

                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-2">Select Class</label>
                            <select name="class_id" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">-- No Class --</option>
                                @foreach($classes as $class)
                                    <option value="{{ $class->id }}" {{ $course->class_id == $class->id ? 'selected' : '' }}>
                                        {{ $class->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="space-y-6">
                        <!-- Current Thumbnail -->
                        <div>
                            <label class="block text-lg font-semibold text-gray-700 mb-2">Current Thumbnail</label>
                            @if($course->thumbnail)
                                <div class="mb-4">
                                    <img src="{{ asset('storage/' . $course->thumbnail) }}"
                                         alt="{{ $course->title }}"
                                         class="w-full h-48 object-cover rounded-lg">
                                </div>
                            @else
                                <div class="mb-4 p-8 bg-gray-100 rounded-lg text-center">
                                    <i class="fas fa-image text-4xl text-gray-400 mb-2"></i>
                                    <p class="text-gray-600">No thumbnail set</p>
                                </div>
                            @endif

                            <label class="block text-sm font-medium text-gray-700 mb-2">Upload New Thumbnail (Optional)</label>
                            <input type="file" name="thumbnail" accept="image/*"
                                   class="w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Duration</label>
                                <input type="text" name="duration" value="{{ old('duration', $course->duration) }}"
                                       placeholder="e.g. 12 weeks"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Level</label>
                                <select name="level" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">Select Level</option>
                                    <option value="Beginner" {{ $course->level == 'Beginner' ? 'selected' : '' }}>Beginner</option>
                                    <option value="Intermediate" {{ $course->level == 'Intermediate' ? 'selected' : '' }}>Intermediate</option>
                                    <option value="Advanced" {{ $course->level == 'Advanced' ? 'selected' : '' }}>Advanced</option>
                                    <option value="Expert" {{ $course->level == 'Expert' ? 'selected' : '' }}>Expert</option>
                                </select>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="pt-6 space-y-4">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-medium py-3 rounded-lg transition">
                                <i class="fas fa-save mr-2"></i> Update Course
                            </button>

                            <a href="{{ route('professor.dashboard') }}"
                               class="block w-full text-center bg-gray-200 hover:bg-gray-300 text-gray-800 font-medium py-3 rounded-lg transition">
                                <i class="fas fa-arrow-left mr-2"></i> Back to Dashboard
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Files Management Tab -->
        <div id="files-content" class="tab-content hidden p-8">
            <!-- Upload New File Form -->
            <div class="mb-8 p-6 bg-blue-50 rounded-xl">
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Upload New File</h3>
                <form action="{{ route('professor.course.upload', $course->id) }}" method="POST" enctype="multipart/form-data" class="flex gap-4">
                    @csrf
                    <input type="file" name="file" required
                           class="flex-1 px-4 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium">
                        <i class="fas fa-upload mr-2"></i> Upload
                    </button>
                </form>
                <p class="text-sm text-gray-600 mt-2">Max file size: 10MB. Supported: PDF, DOC, PPT, ZIP, Images, Videos</p>
            </div>

            <!-- Files List -->
            <div>
                <h3 class="text-xl font-semibold text-gray-800 mb-4">Course Files ({{ $course->files->count() }})</h3>

                @if($course->files->count() > 0)
                    <div class="space-y-4">
                        @foreach($course->files as $file)
                            <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg hover:bg-gray-100 transition">
                                <div class="flex items-center">
                                    <div class="mr-4 text-blue-600">
                                        @if(in_array($file->file_type, ['pdf']))
                                            <i class="fas fa-file-pdf text-2xl"></i>
                                        @elseif(in_array($file->file_type, ['doc', 'docx']))
                                            <i class="fas fa-file-word text-2xl"></i>
                                        @elseif(in_array($file->file_type, ['ppt', 'pptx']))
                                            <i class="fas fa-file-powerpoint text-2xl"></i>
                                        @elseif(in_array($file->file_type, ['jpg', 'jpeg', 'png', 'gif']))
                                            <i class="fas fa-file-image text-2xl"></i>
                                        @elseif(in_array($file->file_type, ['mp4', 'avi', 'mov']))
                                            <i class="fas fa-file-video text-2xl"></i>
                                        @else
                                            <i class="fas fa-file text-2xl"></i>
                                        @endif
                                    </div>
                                    <div>
                                        <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                           class="text-gray-800 font-medium hover:text-blue-600">
                                            {{ $file->file_name }}
                                        </a>
                                        <p class="text-sm text-gray-600">
                                            {{ $file->file_size ? round($file->file_size / 1024, 1) . ' KB' : 'Size unknown' }} •
                                            Uploaded {{ $file->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-2">
                                    <a href="{{ asset('storage/' . $file->file_path) }}" target="_blank"
                                       class="text-blue-600 hover:text-blue-800 p-2" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>

                                    <form action="{{ route('professor.file.delete', $file->id) }}" method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this file?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-600 hover:text-red-800 p-2" title="Delete">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12 bg-gray-50 rounded-lg">
                        <i class="fas fa-file text-5xl text-gray-400 mb-4"></i>
                        <p class="text-gray-600 text-lg">No files uploaded yet</p>
                        <p class="text-gray-500">Upload your first file using the form above</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Delete Course Section -->
    <div class="mt-8 p-6 bg-red-50 border border-red-200 rounded-xl">
        <h3 class="text-lg font-semibold text-red-800 mb-3">Danger Zone</h3>
        <p class="text-red-700 mb-4">Once you delete a course, there is no going back. Please be certain.</p>

        <form action="{{ route('professor.course.delete', $course->id) }}" method="POST"
              onsubmit="return confirm('Are you absolutely sure? This will delete the course and all its files.');">
            @csrf
            @method('DELETE')
            <button type="submit" class="bg-red-600 hover:bg-red-700 text-white font-medium px-6 py-3 rounded-lg transition">
                <i class="fas fa-trash mr-2"></i> Delete This Course Permanently
            </button>
        </form>
    </div>
</div>

<script>
// Tab switching functionality
document.addEventListener('DOMContentLoaded', function() {
    const detailsTab = document.getElementById('details-tab');
    const filesTab = document.getElementById('files-tab');
    const detailsContent = document.getElementById('details-content');
    const filesContent = document.getElementById('files-content');

    detailsTab.addEventListener('click', function() {
        // Activate details tab
        detailsTab.classList.add('active', 'text-gray-800', 'border-blue-500');
        detailsTab.classList.remove('text-gray-600');
        filesTab.classList.remove('active', 'text-gray-800', 'border-blue-500');
        filesTab.classList.add('text-gray-600');

        // Show details content
        detailsContent.classList.remove('hidden');
        filesContent.classList.add('hidden');
    });

    filesTab.addEventListener('click', function() {
        // Activate files tab
        filesTab.classList.add('active', 'text-gray-800', 'border-blue-500');
        filesTab.classList.remove('text-gray-600');
        detailsTab.classList.remove('active', 'text-gray-800', 'border-blue-500');
        detailsTab.classList.add('text-gray-600');

        // Show files content
        filesContent.classList.remove('hidden');
        detailsContent.classList.add('hidden');
    });
});
</script>

<style>
.tab-button.active {
    border-bottom-width: 2px;
    border-color: #3b82f6;
    color: #1f2937;
}

.tab-content {
    transition: all 0.3s ease;
}
</style>
@endsection

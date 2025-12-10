<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseFile;
use App\Models\Grade;
use App\Models\Message;
use App\Models\DocumentRequest;
use App\Models\ClassRoom;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfessorController extends Controller
{
    // ================= DASHBOARD (Optimized) =================
    public function dashboard()
    {
        $professor = Auth::user();

        // 1. Get All Courses (For the "My Courses" Tab - General View)
        $myCourses = Course::where('professor_id', $professor->id)
            ->with(['classRoom', 'files'])
            ->orderBy('created_at', 'desc')
            ->get();

        // 2. Get Classes (For the "Gradebook" Tab - Hierarchical View)
        // We fetch classes assigned to the professor, then attach ONLY the courses
        // that THIS professor teaches in those classes.
        $classes = $professor->classes()
            ->with(['students']) // Load students so we can list them for grading
            ->withCount('students') // Safety count
            ->get()
            ->map(function ($class) use ($professor) {
                // Find courses specifically for this class & professor
                $courses = Course::where('class_id', $class->id)
                    ->where('professor_id', $professor->id)
                    ->with('grades') // Load grades to show current scores
                    ->get();

                // CRITICAL FIX: Attach this collection to the class model
                $class->setRelation('my_courses', $courses);

                return $class;
            });

        return view('professor.dashboard', compact('myCourses', 'classes'));
    }

    // ================= GRADING SYSTEM =================
    public function giveGrade(Request $request, $courseId, $studentId)
    {
        $request->validate([
            'grade' => 'nullable|numeric|min:0|max:20',
            'comment' => 'nullable|string|max:255'
        ]);

        // Use updateOrCreate to handle both new grades and updates
        Grade::updateOrCreate(
            [
                'course_id' => $courseId,
                'student_id' => $studentId,
            ],
            [
                'professor_id' => Auth::id(),
                'value' => $request->grade, // If null, it saves null (clears grade)
                'comment' => $request->comment
            ]
        );

        return back()->with('success', 'Grade saved successfully!');
    }

    // ================= COURSE MANAGEMENT =================
    public function createCourse(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'thumbnail' => 'required|image|mimes:jpeg,png,jpg,webp|max:5120', // 5MB Max
            'duration' => 'nullable|string|max:100',
            'level' => 'nullable|string',
            'class_id' => 'nullable|exists:classes,id'
        ]);

        try {
            $thumbnailPath = $request->file('thumbnail')->store('course-thumbnails', 'public');

            Course::create([
                'title' => $request->title,
                'description' => $request->description,
                'professor_id' => Auth::id(),
                'thumbnail' => $thumbnailPath,
                'duration' => $request->duration,
                'level' => $request->level,
                'class_id' => $request->class_id,
                'is_published' => true
            ]);

            return back()->with('success', 'Course created successfully!');

        } catch (\Exception $e) {
            return back()->with('error', 'Error creating course: ' . $e->getMessage());
        }
    }

    public function editCourse($id)
    {
        $course = Course::where('professor_id', Auth::id())
            ->with(['files', 'classRoom'])
            ->findOrFail($id);

        $classes = Auth::user()->classes; // Get classes for the dropdown

        return view('professor.edit-course', compact('course', 'classes'));
    }

    public function updateCourse(Request $request, $id)
    {
        $course = Course::where('professor_id', Auth::id())->findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:5120',
            'class_id' => 'nullable|exists:classes,id'
        ]);

        $data = $request->only(['title', 'description', 'duration', 'level', 'class_id']);

        if ($request->hasFile('thumbnail')) {
            // Delete old file
            if ($course->thumbnail && Storage::exists('public/' . $course->thumbnail)) {
                Storage::delete('public/' . $course->thumbnail);
            }
            $data['thumbnail'] = $request->file('thumbnail')->store('course-thumbnails', 'public');
        }

        $course->update($data);

        return redirect()->route('professor.dashboard')->with('success', 'Course updated successfully!');
    }

    public function deleteCourse($id)
    {
        $course = Course::where('professor_id', Auth::id())->findOrFail($id);

        if ($course->thumbnail && Storage::exists('public/' . $course->thumbnail)) {
            Storage::delete('public/' . $course->thumbnail);
        }

        $course->delete();

        return back()->with('success', 'Course deleted.');
    }

    // ================= FILE MANAGEMENT =================
    public function uploadFile(Request $request, $id)
    {
        $request->validate(['file' => 'required|file|max:10240']); // 10MB

        // Ensure course belongs to professor
        $course = Course::where('professor_id', Auth::id())->findOrFail($id);

        $file = $request->file('file');
        $path = $file->store('course-files', 'public');

        CourseFile::create([
            'course_id' => $id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $path,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'uploaded_by' => Auth::id()
        ]);

        return back()->with('success', 'File uploaded!');
    }

    public function deleteFile($id)
    {
        $file = CourseFile::where('uploaded_by', Auth::id())->findOrFail($id);

        if (Storage::exists('public/' . $file->file_path)) {
            Storage::delete('public/' . $file->file_path);
        }

        $file->delete();
        return back()->with('success', 'File deleted.');
    }
}



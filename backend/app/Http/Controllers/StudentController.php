<?php

namespace App\Http\Controllers;

use App\Models\{
    Course, Enrollment, Comment, Grade, Message,
    DocumentRequest, User, CourseFile, ClassRoom
};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{
    // MAIN DASHBOARD
    public function dashboard()
    {
        $student = Auth::user();

        // 1. YOUR CLASSES
        $classes = $student->classes()->with([
            'courses' => function($query) {
                $query->with('files')->take(3);
            },
            'professors:id,name'
        ])->take(3)->get();

        // 2. ENROLLED COURSES
        $enrolled = Course::whereHas('enrollments', function($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->whereNull('class_id')
            ->with(['files', 'professor'])
            ->take(6)
            ->get();

        $available = Course::whereDoesntHave('enrollments', function($q) use ($student) {
                $q->where('student_id', $student->id);
            })
            ->whereNull('class_id')
            ->take(6)
            ->get();

        // 3. GRADES
        $grades = Grade::where('student_id', $student->id)
            ->with('course')
            ->take(5)
            ->get();

        // 4. DOCUMENT REQUESTS
        $requests = DocumentRequest::where('user_id', $student->id)
            ->with(['reply'])
            ->latest()
            ->take(5)
            ->get();

        return view('student.dashboard', compact('classes', 'enrolled', 'available', 'grades', 'requests'));
    }

    // VIEW ALL CLASSES
    public function myClasses()
    {
        $student = Auth::user();

        $classes = $student->classes()
            ->with([
                'courses' => function($query) {
                    $query->withCount('files');
                },
                'professors:id,name'
            ])
            ->paginate(9);

        return view('student.classes', compact('classes'));
    }

    // VIEW SINGLE CLASS WITH COURSES AND CHAT
    public function showClass($classId)
    {
        $student = Auth::user();

        // Check if student is enrolled in this class
        $class = $student->classes()
            ->with([
                'courses' => function($query) {
                    $query->with(['files', 'professor'])->orderBy('created_at', 'desc');
                },
                'messages.user' => function($query) {
                    $query->orderBy('created_at', 'desc')->take(20);
                },
                'professors:id,name,email',
                'students:id,name'
            ])
            ->findOrFail($classId);

        return view('student.class-show', compact('class'));
    }

    // VIEW SPECIFIC COURSE IN A CLASS
    public function showClassCourse($classId, $courseId)
    {
        $student = Auth::user();

        // Verify student is enrolled in the class
        $class = $student->classes()->findOrFail($classId);

        // Get the course with all details
        $course = Course::where('class_id', $classId)
            ->where('id', $courseId)
            ->with([
                'files',
                'professor',
                'messages.user' => function($query) {
                    $query->orderBy('created_at', 'desc')->take(20);
                },
                'comments.user'
            ])
            ->firstOrFail();

        return view('student.course-show', compact('class', 'course'));
    }

    // SEND MESSAGE IN CLASS CHAT
    public function sendClassChat(Request $request, $classId)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $student = Auth::user();

        // Check if student is in class
        if (!$student->classes()->where('classes.id', $classId)->exists()) {
            return response()->json(['error' => 'Not enrolled in this class'], 403);
        }

        $message = Message::create([
            'class_id' => $classId,
            'user_id'  => $student->id,
            'message'  => $request->message
        ]);

        // Load user relationship for response
        $message->load('user:id,name');

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    // SEND MESSAGE IN COURSE CHAT
    public function sendCourseChat(Request $request, $courseId)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $student = Auth::user();

        // Check if student is enrolled in the course
        $isEnrolled = Enrollment::where('course_id', $courseId)
            ->where('student_id', $student->id)
            ->exists();

        if (!$isEnrolled) {
            return response()->json(['error' => 'Not enrolled in this course'], 403);
        }

        $message = Message::create([
            'course_id' => $courseId,
            'user_id'   => $student->id,
            'message'   => $request->message
        ]);

        $message->load('user:id,name');

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    // DOWNLOAD FILE
    public function downloadFile($fileId)
    {
        $student = Auth::user();
        $file = CourseFile::findOrFail($fileId);

        // Check access permissions
        if ($file->course_id) {
            // Check if enrolled in course
            $isEnrolled = Enrollment::where('course_id', $file->course_id)
                ->where('student_id', $student->id)
                ->exists();

            if (!$isEnrolled) {
                abort(403, 'Access denied');
            }
        } elseif ($file->class_id) {
            // Check if in class
            $isInClass = $student->classes()->where('classes.id', $file->class_id)->exists();
            if (!$isInClass) {
                abort(403, 'Access denied');
            }
        }

        $path = storage_path('app/public/' . $file->file_path);

        if (!file_exists($path)) {
            abort(404, 'File not found');
        }

        return response()->download($path, $file->file_name);
    }

    // EXISTING METHODS (keep them as they are)
    public function search(Request $request)
    {
        $query = trim($request->input('q'));

        if (empty($query)) {
            return back()->with('error', 'Please enter a search term.');
        }

        $classes = Auth::user()->classes()
            ->where(function($q) use ($query) {
                $q->where('name', 'LIKE', "%{$query}%")
                  ->orWhere('level', 'LIKE', "%{$query}%");
            })
            ->get();

        $courses = Course::whereNull('class_id')
            ->where(function($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhere('description', 'LIKE', "%{$query}%");
            })
            ->get();

        $professors = User::where('role', 'professor')
            ->where('name', 'LIKE', "%{$query}%")
            ->get();

        return view('student.search', compact('classes', 'courses', 'professors', 'query'));
    }

    public function enroll($courseId)
    {
        $course = Course::whereNull('class_id')->findOrFail($courseId);

        Enrollment::firstOrCreate([
            'course_id' => $courseId,
            'student_id' => Auth::id()
        ]);

        return back()->with('success', "Enrolled in '{$course->title}' successfully!");
    }

    public function comment(Request $request, $courseId)
    {
        $request->validate([
            'content' => 'required|string|max:1000'
        ]);

        $course = Course::whereNull('class_id')->findOrFail($courseId);

        Comment::create([
            'course_id' => $courseId,
            'user_id'   => Auth::id(),
            'content'   => $request->content
        ]);

        return back()->with('success', 'Comment posted!');
    }

    public function sendMessageClass(Request $request, $classId)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $isInClass = Auth::user()->classes()->where('classes.id', $classId)->exists();

        if (!$isInClass) {
            return back()->with('error', 'You are not enrolled in this class.');
        }

        Message::create([
            'class_id' => $classId,
            'user_id'  => Auth::id(),
            'message'  => $request->message
        ]);

        return back()->with('success', 'Message sent!');
    }

    public function sendMessageCourse(Request $request, $courseId)
    {
        $request->validate([
            'message' => 'required|string|max:1000'
        ]);

        $course = Course::whereNull('class_id')->findOrFail($courseId);

        $isEnrolled = Enrollment::where('course_id', $courseId)
            ->where('student_id', Auth::id())
            ->exists();

        if (!$isEnrolled) {
            return back()->with('error', 'You are not enrolled in this course.');
        }

        Message::create([
            'course_id' => $courseId,
            'user_id'   => Auth::id(),
            'message'   => $request->message
        ]);

        return back()->with('success', 'Message sent!');
    }

    public function requestDocument(Request $request)
    {
        $request->validate([
            'type' => 'required|string|in:Certificate,Transcript,Recommendation Letter,Other',
            'message' => 'required|string|max:2000'
        ]);

        DocumentRequest::create([
            'user_id' => Auth::id(),
            'type'    => $request->type,
            'message' => $request->message,
            'status'  => 'pending'
        ]);

        return back()->with('success', 'Document request sent to Admin!');
    }

    public function showCourse($id)
    {
        $course = Course::with(['files', 'messages.user', 'professor', 'comments.user'])
            ->findOrFail($id);

        $isEnrolled = Enrollment::where('course_id', $id)
            ->where('student_id', Auth::id())
            ->exists();

        if (!$isEnrolled && $course->class_id === null) {
            return back()->with('error', 'You are not enrolled in this course.');
        }

        return view('student.course-show', compact('course'));
    }

    public function grades()
    {
        $grades = Grade::where('student_id', Auth::id())
            ->with(['course', 'professor'])
            ->orderBy('created_at', 'desc')
            ->get();

        $average = $grades->avg('value');
        $totalCourses = $grades->count();

        return view('student.grades', compact('grades', 'average', 'totalCourses'));
    }

    public function unenroll($courseId)
    {
        $enrollment = Enrollment::where('course_id', $courseId)
            ->where('student_id', Auth::id())
            ->firstOrFail();

        $enrollment->delete();

        return back()->with('success', 'Successfully unenrolled from the course.');
    }
}

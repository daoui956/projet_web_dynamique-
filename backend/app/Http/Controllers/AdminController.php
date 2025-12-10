<?php

namespace App\Http\Controllers;

use App\Models\{User, DocumentRequest, AdminReply, ClassRoom, Course};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel; // Correct


class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'students' => User::where('role', 'student')->count(),
            'professors' => User::where('role', 'professor')->count(),
            'classes' => ClassRoom::count(),
            'courses' => Course::count(),
            'pending_requests' => DocumentRequest::where('status', 'pending')->count(),
        ];

        $recentRequests = DocumentRequest::with(['user', 'reply'])->latest()->take(10)->get();
        $recentClasses = ClassRoom::withCount('students', 'professors')->latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentRequests', 'recentClasses'));
    }

    // CREATE PROFESSOR (IMPROVED + validation + success message)
    public function createProfessor(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'professor'
        ]);

        return back()->with('success', 'Professor created successfully!');
    }

    // CREATE STUDENT
    public function createStudent(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'student'
        ]);

        return back()->with('success', 'Student created successfully!');
    }

    // REPLY TO DOCUMENT REQUEST (IMPROVED + file name + size)
   public function replyRequest(Request $request, $id)
{
    $request->validate([
        'reply' => 'nullable|string',
        'file' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120' // 5MB max
    ]);

    $req = DocumentRequest::findOrFail($id);

    // Update request status
    $req->update(['status' => 'approved']);

    $filePath = null;
    $fileName = null;
    $fileSize = null;

    if ($request->hasFile('file')) {
        $file = $request->file('file');
        $fileName = $file->getClientOriginalName();
        $fileSize = $file->getSize();

        // Store in a folder specific to the document request
        $filePath = $file->store("admin-documents/request-{$id}", 'public');
    }

    // Create or update the admin reply
    AdminReply::updateOrCreate(
        ['document_request_id' => $id],
        [
            'reply' => $request->reply,
            'file_path' => $filePath,
            'file_name' => $fileName,
            'file_size' => $fileSize,
            'created_at' => now(),
            'updated_at' => now()
        ]
    );

    return back()->with('success', 'Reply sent successfully with ' . ($fileName ? 'file attachment' : 'text'));
}

    // RESET ANY USER PASSWORD
    public function resetPassword(User $user)
    {
        $newPassword = '123456'; // You can generate random: Str::random(10)
        $user->update(['password' => Hash::make($newPassword)]);

        return back()->with('success', "Password reset to: <strong>$newPassword</strong>");
    }

    // CREATE CLASS + ASSIGN USERS (IMPROVED + validation)
    public function createClass(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|string|max:50',
            'year' => 'required|digits:4|integer|min:2020|max:2030',
            'students' => 'nullable|array',
            'students.*' => 'exists:users,id,role,student',
            'professors' => 'nullable|array',
            'professors.*' => 'exists:users,id,role,professor'
        ]);

        $class = ClassRoom::create([
            'name' => $request->name,
            'level' => $request->level,
            'year' => $request->year
        ]);

        // Attach students
        if ($request->filled('students')) {
            $class->students()->attach($request->students);
        }

        // Attach professors
        if ($request->filled('professors')) {
            $class->professors()->attach($request->professors);
        }

        return back()->with('success', "Class '{$class->name}' created successfully!");
    }

    // ASSIGN USERS TO EXISTING CLASS (NEW FEATURE)
    public function assignToClass(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'user_id' => 'required|exists:users,id',
            'role' => 'required|in:student,professor'
        ]);

        $class = ClassRoom::findOrFail($request->class_id);
        $user = User::findOrFail($request->user_id);

        if ($request->role === 'student') {
            $class->students()->syncWithoutDetaching($user->id);
        } else {
            $class->professors()->syncWithoutDetaching($user->id);
        }

        return back()->with('success', "{$user->name} assigned as {$request->role}!");
    }

    // DELETE USER (NEW + SAFE)
    public function deleteUser(User $user)
    {
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete yourself!');
        }
        if ($user->role === 'admin') {
            return back()->with('error', 'Cannot delete admin accounts!');
        }

        $user->delete();
        return back()->with('success', 'User deleted permanently.');
    }

    // VIEW ALL CLASSES (NEW) - UPDATED
    public function classes()
    {
        $classes = ClassRoom::with(['students', 'professors'])
            ->withCount(['students', 'professors', 'courses'])
            ->latest()
            ->get();

        $allStudents = User::where('role', 'student')->get();
        $allProfessors = User::where('role', 'professor')->get();

        return view('admin.classes', compact('classes', 'allStudents', 'allProfessors'));
    }

    // DELETE CLASS (NEW + SAFE)
    public function deleteClass(ClassRoom $class)
    {
        $class->delete();
        return back()->with('success', 'Class deleted with all data.');
    }

    // EDIT CLASS FORM
    public function editClass($id)
    {
        $class = ClassRoom::with(['students', 'professors'])->findOrFail($id);
        $allStudents = User::where('role', 'student')->get();
        $allProfessors = User::where('role', 'professor')->get();

        return view('admin.edit-class', compact('class', 'allStudents', 'allProfessors'));
    }

    // UPDATE CLASS
    public function updateClass(Request $request, $id)
    {
        $class = ClassRoom::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|string|max:50',
            'year' => 'required|digits:4|integer|min:2020|max:2030',
            'students' => 'nullable|array',
            'students.*' => 'exists:users,id,role,student',
            'professors' => 'nullable|array',
            'professors.*' => 'exists:users,id,role,professor'
        ]);

        $class->update([
            'name' => $request->name,
            'level' => $request->level,
            'year' => $request->year
        ]);

        // Sync students
        $class->students()->sync($request->students ?? []);

        // Sync professors
        $class->professors()->sync($request->professors ?? []);

        return redirect()->route('admin.classes')
            ->with('success', "Class '{$class->name}' updated successfully!");
    }

    // VIEW ALL USERS
    public function users()
    {
        $users = User::with(['classes'])
            ->withCount(['courses', 'enrollments'])
            ->latest()
            ->get();

        return view('admin.users', compact('users'));
    }

    // VIEW CLASS DETAILS
   // VIEW CLASS DETAILS
    public function showClass($id)
    {
        // 1. Get the Class details
        $class = ClassRoom::with(['students', 'professors', 'courses'])
            ->withCount(['students', 'professors', 'courses'])
            ->findOrFail($id);

        // 2. GET STUDENTS AND PROFESSORS (You were missing this part!)
        $allStudents = User::where('role', 'student')->get();
        $allProfessors = User::where('role', 'professor')->get();

        // 3. Send everything to the view
        return view('admin.show-class', compact('class', 'allStudents', 'allProfessors'));
    }
public function importStudentsToClass(Request $request, $id)
{
    $request->validate([
        'file' => 'required|mimes:xlsx,xls,csv|max:2048',
    ]);

    $class = ClassRoom::findOrFail($id);

    // Convert Excel to Array
    $data = Excel::toArray([], $request->file('file'));

    // We assume the data is in the first sheet [0]
    $rows = $data[0] ?? [];

    $importedCount = 0;
    $notFoundCount = 0;

    foreach ($rows as $row) {
        // Assume Column A (index 0) contains the Name or Email
        $searchValue = trim($row[0]);

        if (empty($searchValue)) continue;

        // Try to find user by Email first (more accurate), then by Name
        $student = User::where('role', 'student')
            ->where(function($query) use ($searchValue) {
                $query->where('email', $searchValue)
                      ->orWhere('name', 'LIKE', $searchValue);
            })->first();

        if ($student) {
            // Assign to class (syncWithoutDetaching prevents duplicates)
            $class->students()->syncWithoutDetaching($student->id);
            $importedCount++;
        } else {
            $notFoundCount++;
        }
    }

    $message = "Process complete! Assigned $importedCount students.";
    if ($notFoundCount > 0) {
        $message .= " ($notFoundCount names not found in database)";
    }

    return back()->with('success', $message);
}
    // REMOVE USER FROM CLASS
    public function removeFromClass(Request $request, $classId, $userId)
    {
        $class = ClassRoom::findOrFail($classId);
        $user = User::findOrFail($userId);

        if ($user->role === 'student') {
            $class->students()->detach($userId);
        } else {
            $class->professors()->detach($userId);
        }

        return back()->with('success', "{$user->name} removed from class!");
    }
}

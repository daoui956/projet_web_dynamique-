<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\ProfessorController;
use App\Http\Controllers\AdminController;

// GUEST ROUTES
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register/student', [AuthController::class, 'showStudentRegister'])->name('register.student');
Route::post('/register/student', [AuthController::class, 'registerStudent']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
Route::get('/', function() {
    return redirect('/login');
});

// STUDENT ROUTES
Route::middleware(['auth'])->prefix('student')->group(function () {
    // Dashboard & Navigation
    Route::get('/', [StudentController::class, 'dashboard'])->name('student.dashboard');
    Route::get('/search', [StudentController::class, 'search'])->name('student.search');

    // Classes
    Route::get('/classes', [StudentController::class, 'myClasses'])->name('student.classes');
    Route::get('/class/{class}', [StudentController::class, 'showClass'])->name('student.class.show');

    // Courses
    Route::get('/course/{course}', [StudentController::class, 'showCourse'])->name('student.course.show');
    Route::get('/class/{class}/course/{course}', [StudentController::class, 'showClassCourse'])->name('student.class.course.show');

    // Enrollment & Actions
    Route::post('/enroll/{course}', [StudentController::class, 'enroll'])->name('student.enroll');
    Route::post('/unenroll/{course}', [StudentController::class, 'unenroll'])->name('student.unenroll');
    Route::post('/comment/{course}', [StudentController::class, 'comment'])->name('student.comment');

    // Messaging
    Route::post('/message/course/{course}', [StudentController::class, 'sendMessageCourse'])->name('student.message.course');
    Route::post('/message/class/{class}', [StudentController::class, 'sendMessageClass'])->name('student.message.class');
    Route::post('/class/{class}/chat', [StudentController::class, 'sendClassChat'])->name('student.class.chat');
    Route::post('/course/{course}/chat', [StudentController::class, 'sendCourseChat'])->name('student.course.chat');

    // Documents & Files
    Route::post('/request-document', [StudentController::class, 'requestDocument'])->name('student.request.document');
    Route::get('/file/{file}/download', [StudentController::class, 'downloadFile'])->name('student.file.download');

    // Grades
    Route::get('/grades', [StudentController::class, 'grades'])->name('student.grades');
});

// PROFESSOR ROUTES
Route::middleware(['auth'])->prefix('professor')->group(function () {
    // Dashboard
    Route::get('/', [ProfessorController::class, 'dashboard'])->name('professor.dashboard');
Route::post('/course/{courseId}/student/{studentId}/grade', [ProfessorController::class, 'giveGrade'])
    ->name('professor.grade');
    // Course Management
    Route::post('/course/create', [ProfessorController::class, 'createCourse'])->name('professor.course.create');
    Route::get('/course/{course}/edit', [ProfessorController::class, 'editCourse'])->name('professor.course.edit');
    Route::put('/course/{course}/update', [ProfessorController::class, 'updateCourse'])->name('professor.course.update');
    Route::delete('/course/{course}/delete', [ProfessorController::class, 'deleteCourse'])->name('professor.course.delete');

    // File Management
    Route::post('/upload-old/{course}', [ProfessorController::class, 'uploadFileOld'])->name('professor.upload.old');
    Route::post('/course/{course}/upload-file', [ProfessorController::class, 'uploadFile'])->name('professor.course.upload');
    Route::delete('/file/{file}/delete', [ProfessorController::class, 'deleteFile'])->name('professor.file.delete');

    // Document Requests
    Route::post('/request-document', [ProfessorController::class, 'requestDocument'])->name('professor.request.document');
});

// ADMIN ROUTES - UPDATED
Route::middleware(['auth'])->prefix('admin')->group(function () {
    // Dashboard
    Route::get('/', [AdminController::class, 'dashboard'])->name('admin.dashboard');

    // User Management
    Route::get('/users', [AdminController::class, 'users'])->name('admin.users');
    Route::get('/users/create', [AdminController::class, 'createUserForm'])->name('admin.users.create.form');
    Route::post('/users/store', [AdminController::class, 'storeUser'])->name('admin.user.store');
    Route::get('/users/{user}', [AdminController::class, 'showUser'])->name('admin.user.show');
    Route::get('/users/{user}/edit', [AdminController::class, 'editUser'])->name('admin.user.edit');
    Route::put('/users/{user}/update', [AdminController::class, 'updateUser'])->name('admin.user.update');
Route::post('/classes/{id}/import', [AdminController::class, 'importStudentsToClass'])->name('admin.class.import');
    // Quick User Creation
    Route::post('/create-professor', [AdminController::class, 'createProfessor'])->name('admin.create.professor');
    Route::post('/create-student', [AdminController::class, 'createStudent'])->name('admin.create.student');

    // User Actions
    Route::post('/reset-password/{user}', [AdminController::class, 'resetPassword'])->name('admin.reset.password');
    Route::delete('/delete-user/{user}', [AdminController::class, 'deleteUser'])->name('admin.delete.user');

    // ----------------------------------------------------
    // CLASS MANAGEMENT (Updated for new Views)
    // ----------------------------------------------------
    Route::get('/classes', [AdminController::class, 'classes'])->name('admin.classes');
    Route::post('/create-class', [AdminController::class, 'createClass'])->name('admin.create.class');

    // View Class Details
    Route::get('/classes/{class}', [AdminController::class, 'showClass'])->name('admin.class.show');

    // Edit Class
    Route::get('/classes/{class}/edit', [AdminController::class, 'editClass'])->name('admin.class.edit');
    Route::put('/classes/{class}/update', [AdminController::class, 'updateClass'])->name('admin.class.update');

    // Delete Class
    Route::delete('/delete-class/{class}', [AdminController::class, 'deleteClass'])->name('admin.delete.class');

    // [NEW] Assign User to Class (Needed for Show Class page)
    Route::post('/classes/assign', [AdminController::class, 'assignToClass'])->name('admin.assign.class');

    // [NEW] Remove User from Class (Needed for Show Class page)
    Route::delete('/classes/{classId}/remove/{userId}', [AdminController::class, 'removeFromClass'])->name('admin.class.remove.user');

    // ----------------------------------------------------

    // Course Management
    Route::get('/courses', [AdminController::class, 'courses'])->name('admin.courses');
    Route::get('/courses/{course}', [AdminController::class, 'showCourse'])->name('admin.course.show');
    Route::get('/courses/{course}/edit', [AdminController::class, 'editCourse'])->name('admin.course.edit');
    Route::put('/courses/{course}/update', [AdminController::class, 'updateCourse'])->name('admin.course.update');
    Route::delete('/courses/{course}/delete', [AdminController::class, 'deleteCourse'])->name('admin.course.delete');

    // Document Requests
    Route::get('/document-requests', [AdminController::class, 'documentRequests'])->name('admin.document.requests');
    Route::get('/document-requests/{id}', [AdminController::class, 'showDocumentRequest'])->name('admin.document.request.show');
    Route::post('/reply/{id}', [AdminController::class, 'replyRequest'])->name('admin.reply.request');
    Route::delete('/document-request/{id}/delete', [AdminController::class, 'deleteDocumentRequest'])->name('admin.document.request.delete');

    // Reports
    Route::get('/reports', [AdminController::class, 'reports'])->name('admin.reports');
});

// SHARED ROUTES
Route::middleware('auth')->group(function () {
    Route::post('/request-document', function (Request $request) {
        \App\Models\DocumentRequest::create([
            'user_id' => Auth::id(),
            'type' => $request->type,
            'message' => $request->message,
            'status' => 'pending'
        ]);

        return back()->with('success', 'Document request submitted successfully!');
    })->name('request.document');
});

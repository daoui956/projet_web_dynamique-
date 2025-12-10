<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable;

    protected $fillable = ['name', 'email', 'password', 'role'];
    protected $hidden = ['password', 'remember_token'];

    // OLD RELATIONSHIPS (keep them)
    public function courses() { return $this->hasMany(Course::class, 'professor_id'); }
    public function enrollments() { return $this->hasMany(Enrollment::class, 'student_id'); }
    public function grades() { return $this->hasMany(Grade::class, 'student_id'); }
    public function comments() { return $this->hasMany(Comment::class); }
    public function messages() { return $this->hasMany(Message::class); }
    public function documentRequests() { return $this->hasMany(DocumentRequest::class); }

    // BULLETPROOF CLASS METHODS — NO ERRORS EVER
    public function myClasses()
    {
        if ($this->role === 'student') {
            return DB::table('classes')
                ->join('class_student', 'classes.id', '=', 'class_student.class_id')
                ->where('class_student.student_id', $this->id)
                ->select('classes.*');
        }

        if ($this->role === 'professor') {
            return DB::table('classes')
                ->join('class_professor', 'classes.id', '=', 'class_professor.class_id')
                ->where('class_professor.professor_id', $this->id)
                ->select('classes.*');
        }

        return collect();
    }

    public function myStudents($classId)
    {
        return DB::table('users')
            ->join('class_student', 'users.id', '=', 'class_student.student_id')
            ->where('class_student.class_id', $classId)
            ->select('users.*');
    }
    public function classes()
    {
        if ($this->role === 'student') {
            return $this->belongsToMany(ClassRoom::class, 'class_student', 'student_id', 'class_id')
                        ->withTimestamps()
                        ->withPivot('enrolled_at');
        } else {
            return $this->belongsToMany(ClassRoom::class, 'class_professor', 'professor_id', 'class_id');
        }
    }

    // For professors who teach classes
    public function teachingClasses()
    {
        return $this->belongsToMany(ClassRoom::class, 'class_professor', 'professor_id', 'class_id');
    }
}


<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    protected $table = 'classes';

    protected $fillable = ['name', 'level', 'year'];

    // Relationship with students
    public function students()
    {
        return $this->belongsToMany(User::class, 'class_student', 'class_id', 'student_id')
                    ->where('role', 'student')
                    ->withPivot('enrolled_at');
    }

    // Relationship with professors
    public function professors()
    {
        return $this->belongsToMany(User::class, 'class_professor', 'class_id', 'professor_id')
                    ->where('role', 'professor');
    }

    // Relationship with courses
    public function courses()
    {
        return $this->hasMany(Course::class, 'class_id');
    }

    // ADD THIS: Relationship with messages
    public function messages()
    {
        return $this->hasMany(Message::class, 'class_id');
    }

    // ADD THIS: Relationship with files
    public function files()
    {
        return $this->hasMany(CourseFile::class, 'class_id');
    }
}

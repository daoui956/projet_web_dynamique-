<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    protected $fillable = [
        'title',
        'description',
        'professor_id',
        'thumbnail',
        'cover_image',
        'duration',
        'level',
        'category',
        'is_published',
        'class_id'
    ];

    protected $appends = ['thumbnail_url', 'students_count'];

    public function professor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'professor_id');
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }

    public function grades(): HasMany
    {
        return $this->hasMany(Grade::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(CourseFile::class);
    }

    public function classRoom(): BelongsTo
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function students()
    {
        return $this->hasManyThrough(
            User::class,
            Enrollment::class,
            'course_id',
            'id',
            'id',
            'student_id'
        )->where('users.role', 'student');
    }

    // Accessor for thumbnail URL - FIXED
    public function getThumbnailUrlAttribute()
    {
        if (!$this->thumbnail) {
            return asset('images/default-course-thumbnail.jpg');
        }

        // Check if it's already a full URL
        if (filter_var($this->thumbnail, FILTER_VALIDATE_URL)) {
            return $this->thumbnail;
        }

        // Check if it starts with storage path
        if (str_starts_with($this->thumbnail, 'storage/')) {
            return asset($this->thumbnail);
        }

        // Check if it's just a filename in course-thumbnails folder
        if (str_starts_with($this->thumbnail, 'course-thumbnails/')) {
            return asset('storage/' . $this->thumbnail);
        }

        // For backward compatibility - assume it's in course-thumbnails folder
        return asset('storage/course-thumbnails/' . $this->thumbnail);
    }

    // Accessor for students count
    public function getStudentsCountAttribute()
    {
        return $this->enrollments()->count();
    }

    // Get enrolled students for this course
    public function enrolledStudents()
    {
        return $this->belongsToMany(User::class, 'enrollments', 'course_id', 'student_id')
                    ->where('role', 'student')
                    ->withTimestamps();
    }

    // Scope for professor's courses
    public function scopeByProfessor($query, $professorId)
    {
        return $query->where('professor_id', $professorId);
    }
}

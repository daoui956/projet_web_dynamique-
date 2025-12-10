<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Classes extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'name',
        'level',
        'year'
    ];

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_student', 'class_id', 'student_id')
                    ->where('role', 'student')
                    ->withTimestamps()
                    ->withPivot('enrolled_at');
    }

    public function professors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'class_professor', 'class_id', 'professor_id')
                    ->where('role', 'professor')
                    ->withTimestamps();
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class, 'class_id');
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'class_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(CourseFile::class, 'class_id');
    }
}

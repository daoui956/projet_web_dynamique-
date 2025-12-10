<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class CourseFile extends Model
{
    protected $fillable = ['course_id', 'file_name', 'file_path', 'file_type', 'uploaded_by'];
    public function course() { return $this->belongsTo(Course::class); }
    public function uploader() { return $this->belongsTo(User::class, 'uploaded_by'); }
}

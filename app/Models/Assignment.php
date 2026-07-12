<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = ['course_id', 'title', 'description', 'deadline', 'max_score', 'attachment_path'];

    public function course() { return $this->belongsTo(Course::class); }
    public function submissions() { return $this->hasMany(AssignmentSubmission::class); }

    //
}

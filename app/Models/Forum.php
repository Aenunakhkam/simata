<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{
    protected $fillable = ['course_id', 'title', 'description', 'created_by'];

    public function course() { return $this->belongsTo(Course::class); }
    public function creator() { return $this->belongsTo(User::class, 'created_by'); }
    public function replies() { return $this->hasMany(ForumReply::class); }

    //
}

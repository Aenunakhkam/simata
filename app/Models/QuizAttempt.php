<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $fillable = ['quiz_id', 'student_id', 'start_time', 'end_time', 'score', 'status'];

    protected $casts = ['start_time' => 'datetime', 'end_time' => 'datetime'];
    public function quiz() { return $this->belongsTo(Quiz::class); }
    public function student() { return $this->belongsTo(User::class, 'student_id'); }
    public function answers() { return $this->hasMany(QuizAnswer::class, 'attempt_id'); }

    //
}

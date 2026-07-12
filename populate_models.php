<?php

$models = [
    'AcademicYear' => [
        'fillable' => "['name', 'semester', 'is_active']",
        'methods' => ""
    ],
    'Assignment' => [
        'fillable' => "['course_id', 'title', 'description', 'deadline', 'max_score', 'attachment_path']",
        'methods' => "
    public function course() { return \$this->belongsTo(Course::class); }
    public function submissions() { return \$this->hasMany(AssignmentSubmission::class); }"
    ],
    'AssignmentSubmission' => [
        'fillable' => "['assignment_id', 'student_id', 'file_path', 'notes', 'score', 'feedback']",
        'methods' => "
    public function assignment() { return \$this->belongsTo(Assignment::class); }
    public function student() { return \$this->belongsTo(User::class, 'student_id'); }"
    ],
    'Quiz' => [
        'fillable' => "['course_id', 'title', 'description', 'time_limit_minutes', 'max_attempts', 'is_active', 'shuffle_questions']",
        'methods' => "
    public function course() { return \$this->belongsTo(Course::class); }
    public function questions() { return \$this->hasMany(QuizQuestion::class); }
    public function attempts() { return \$this->hasMany(QuizAttempt::class); }"
    ],
    'QuizQuestion' => [
        'fillable' => "['quiz_id', 'type', 'question', 'options', 'correct_answer', 'score_weight']",
        'methods' => "
    protected \$casts = ['options' => 'array'];
    public function quiz() { return \$this->belongsTo(Quiz::class); }"
    ],
    'QuizAttempt' => [
        'fillable' => "['quiz_id', 'student_id', 'start_time', 'end_time', 'score', 'status']",
        'methods' => "
    protected \$casts = ['start_time' => 'datetime', 'end_time' => 'datetime'];
    public function quiz() { return \$this->belongsTo(Quiz::class); }
    public function student() { return \$this->belongsTo(User::class, 'student_id'); }
    public function answers() { return \$this->hasMany(QuizAnswer::class, 'attempt_id'); }"
    ],
    'QuizAnswer' => [
        'fillable' => "['attempt_id', 'question_id', 'answer', 'is_correct', 'score']",
        'methods' => "
    public function attempt() { return \$this->belongsTo(QuizAttempt::class, 'attempt_id'); }
    public function question() { return \$this->belongsTo(QuizQuestion::class); }"
    ],
    'Forum' => [
        'fillable' => "['course_id', 'title', 'description', 'created_by']",
        'methods' => "
    public function course() { return \$this->belongsTo(Course::class); }
    public function creator() { return \$this->belongsTo(User::class, 'created_by'); }
    public function replies() { return \$this->hasMany(ForumReply::class); }"
    ],
    'ForumReply' => [
        'fillable' => "['forum_id', 'user_id', 'reply']",
        'methods' => "
    public function forum() { return \$this->belongsTo(Forum::class); }
    public function user() { return \$this->belongsTo(User::class); }"
    ],
    'Announcement' => [
        'fillable' => "['title', 'content', 'type', 'created_by']",
        'methods' => "
    public function creator() { return \$this->belongsTo(User::class, 'created_by'); }"
    ],
    'SystemSetting' => [
        'fillable' => "['key', 'value']",
        'methods' => ""
    ]
];

foreach ($models as $name => $data) {
    $path = "d:/program/simata/dataweb/app/Models/{$name}.php";
    $content = file_get_contents($path);
    $replacement = "    protected \$fillable = {$data['fillable']};\n{$data['methods']}\n";
    
    // Find the first opening brace of the class
    $content = preg_replace('/class\s+' . $name . '\s+extends\s+Model\s*\{/i', "class $name extends Model\n{\n" . $replacement, $content, 1);
    
    file_put_contents($path, $content);
    echo "Updated {$name}\n";
}
echo "Done!\n";

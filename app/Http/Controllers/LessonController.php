<?php

namespace App\Http\Controllers;

use App\Models\Lesson;
use Illuminate\Http\Request;
use Inertia\Inertia;

class LessonController extends Controller
{
    public function index()
    {
        $lessons = Lesson::all();
        return Inertia::render('Lessons/Index', ['lessons' => $lessons]);
    }

    public function show($id)
    {
        $lesson = Lesson::with('course.lessons')->findOrFail($id);
        $userId = auth()->id();
        
        $enrollment = Enrollment::where('user_id', $userId)
            ->where('course_id', $lesson->course_id)
            ->first();
            
        if (!$enrollment) {
            return redirect()->route('courses.show', $lesson->course_id)
                ->with('error', 'Silakan daftar kelas ini terlebih dahulu untuk mengakses materi.');
        }

        return Inertia::render('Lessons/Show', [
            'lesson' => $lesson,
            'course' => $lesson->course,
            'enrollment' => $enrollment,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;

class CourseController extends Controller
{
    public function index()
    {
        $courses = Course::withCount('lessons')->get();
        
        $enrolledCourseIds = [];
        if (auth()->check()) {
            $enrolledCourseIds = Enrollment::where('user_id', auth()->id())
                ->pluck('course_id')
                ->toArray();
        }

        return Inertia::render('Courses/Index', [
            'courses' => $courses,
            'enrolledCourseIds' => $enrolledCourseIds,
        ]);
    }

    public function show($id)
    {
        $course = Course::with('lessons')->findOrFail($id);
        
        $enrollment = null;
        if (auth()->check()) {
            $enrollment = Enrollment::where('user_id', auth()->id())
                ->where('course_id', $course->id)
                ->first();
        }

        return Inertia::render('Courses/Show', [
            'course' => $course,
            'isEnrolled' => !is_null($enrollment),
            'enrollment' => $enrollment,
        ]);
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        
        if ($user->role === 'admin') {
            $stats = [
                'total_teachers' => Teacher::count(),
                'total_students' => Student::count(),
                'total_classrooms' => Classroom::count(),
                'total_subjects' => Subject::count(),
                'total_users' => User::count(),
            ];

            return Inertia::render('Dashboard', [
                'role' => 'admin',
                'stats' => $stats,
            ]);
        }

        if ($user->role === 'guru') {
            $teacher = Teacher::where('user_id', $user->id)->first();
            $stats = [
                'total_students' => Student::where('status', 'active')->count(),
                'total_classrooms' => Classroom::count(),
                'total_subjects' => Subject::count(),
                'nip' => $teacher ? $teacher->nip : '-',
                'phone' => $teacher ? $teacher->phone : '-',
            ];

            return Inertia::render('Dashboard', [
                'role' => 'guru',
                'stats' => $stats,
            ]);
        }

        // Student or other roles (Default fallback)
        $userId = $user->id;
        $enrollments = Enrollment::where('user_id', $userId)
            ->with(['course' => function($q) {
                $q->withCount('lessons');
            }])
            ->get();
            
        $enrolledCourseIds = $enrollments->pluck('course_id')->toArray();
        
        $stats = [
            'total_enrolled' => $enrollments->count(),
            'completed' => $enrollments->where('progress', 100)->count(),
            'average_progress' => round($enrollments->avg('progress') ?? 0),
        ];

        $recommendations = Course::whereNotIn('id', $enrolledCourseIds)
            ->withCount('lessons')
            ->limit(3)
            ->get();

        return Inertia::render('Dashboard', [
            'role' => $user->role,
            'enrollments' => $enrollments,
            'stats' => $stats,
            'recommendations' => $recommendations,
        ]);
    }
}

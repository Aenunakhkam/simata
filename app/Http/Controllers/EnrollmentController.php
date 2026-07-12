<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Illuminate\Http\Request;
use Inertia\Inertia;

class EnrollmentController extends Controller
{
    public function index()
    {
        $userId = auth()->id();
        $enrollments = Enrollment::where('user_id', $userId)
            ->with(['course' => function($q) {
                $q->withCount('lessons');
            }])
            ->get();
            
        return Inertia::render('Enrollments/Index', [
            'enrollments' => $enrollments
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id'
        ]);

        $userId = auth()->id();
        
        $enrollment = Enrollment::firstOrCreate([
            'user_id' => $userId,
            'course_id' => $request->course_id,
        ], [
            'enrolled_at' => now(),
            'progress' => 0
        ]);

        return redirect()->route('courses.show', $request->course_id)
            ->with('success', 'Selamat! Anda berhasil mendaftar di kelas ini.');
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'progress' => 'required|integer|min:0|max:100'
        ]);

        $enrollment = Enrollment::where('user_id', auth()->id())->findOrFail($id);
        
        if ($request->progress > $enrollment->progress) {
            $enrollment->update([
                'progress' => $request->progress
            ]);
        }

        return redirect()->back()->with('success', 'Progress belajar berhasil diperbarui!');
    }
}

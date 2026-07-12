<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Subject;
use App\Models\Classroom;
use App\Models\Teacher;
use App\Models\AcademicYear;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $query = Course::with(['subject', 'classroom', 'teacher', 'academicYear'])
            ->whereNotNull('subject_id')
            ->whereNotNull('classroom_id');

        if ($request->search) {
            $query->whereHas('subject', function($q) use ($request) {
                $q->where('name', 'ilike', "%{$request->search}%");
            })->orWhereHas('teacher', function($q) use ($request) {
                $q->where('name', 'ilike', "%{$request->search}%");
            })->orWhereHas('classroom', function($q) use ($request) {
                $q->where('name', 'ilike', "%{$request->search}%");
            });
        }

        if ($request->academic_year_id) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        $schedules = $query->latest()->paginate($request->per_page ?? 10)->withQueryString();

        $subjects = Subject::orderBy('name')->get();
        $classrooms = Classroom::orderBy('name')->get();
        $teachers = Teacher::orderBy('name')->get();
        $academicYears = AcademicYear::orderByDesc('id')->get();
        $activeYear = AcademicYear::where('is_active', true)->first();

        return Inertia::render('Schedules/Index', [
            'schedules' => $schedules,
            'subjects' => $subjects,
            'classrooms' => $classrooms,
            'teachers' => $teachers,
            'academicYears' => $academicYears,
            'activeYear' => $activeYear,
            'filters' => $request->only(['search', 'per_page', 'academic_year_id']),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'teacher_id' => 'required|exists:teachers,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);
        
        $subject = Subject::find($validated['subject_id']);
        $classroom = Classroom::find($validated['classroom_id']);
        $teacher = Teacher::find($validated['teacher_id']);

        $validated['title'] = $subject->name . ' - ' . $classroom->name;
        $validated['description'] = 'Mata pelajaran ' . $subject->name . ' untuk kelas ' . $classroom->name . ' diajarkan oleh ' . $teacher->name . '.';
        $validated['category'] = 'Akademik';
        $validated['instructor_name'] = $teacher->name;
        $validated['level'] = 'Kelas ' . $classroom->level;

        Course::create($validated);

        return redirect()->back()->with('message', 'Jadwal / Penempatan guru berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $course = Course::findOrFail($id);

        $validated = $request->validate([
            'subject_id' => 'required|exists:subjects,id',
            'classroom_id' => 'required|exists:classrooms,id',
            'teacher_id' => 'required|exists:teachers,id',
            'academic_year_id' => 'required|exists:academic_years,id',
        ]);

        $subject = Subject::find($validated['subject_id']);
        $classroom = Classroom::find($validated['classroom_id']);
        $teacher = Teacher::find($validated['teacher_id']);

        $validated['title'] = $subject->name . ' - ' . $classroom->name;
        $validated['instructor_name'] = $teacher->name;

        $course->update($validated);

        return redirect()->back()->with('message', 'Jadwal berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $course = Course::findOrFail($id);
        $course->delete();
        return redirect()->back()->with('message', 'Jadwal berhasil dihapus.');
    }
}

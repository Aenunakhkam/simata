<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Shuchkin\SimpleXLSXGen;

class GradeController extends Controller
{
    public function index(Request $request)
    {
        $classroomId = $request->input('classroom_id');
        $subjectId = $request->input('subject_id');
        $type = $request->input('type'); // Tugas, UH, UTS, UAS

        $students = [];
        $existingGrades = [];

        if ($classroomId && $subjectId) {
            $students = Student::where('classroom_id', $classroomId)
                ->where('status', 'active')
                ->orderBy('name')
                ->get();

            $existingGrades = Grade::where('classroom_id', $classroomId)
                ->where('subject_id', $subjectId)
                ->when($type, function($q) use ($type) {
                    $q->where('type', $type);
                })
                ->get()
                ->groupBy('student_id');
        }

        return Inertia::render('Grades/Index', [
            'classrooms' => Classroom::orderBy('level')->orderBy('name')->get(),
            'subjects' => Subject::orderBy('name')->get(),
            'types' => ['Tugas', 'UH', 'UTS', 'UAS'],
            'students' => $students,
            'existingGrades' => $existingGrades,
            'filters' => $request->only(['classroom_id', 'subject_id', 'type'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'type' => 'required|string|in:Tugas,UH,UTS,UAS',
            'scores' => 'required|array',
            'scores.*.student_id' => 'required|exists:students,id',
            'scores.*.score' => 'required|integer|min:0|max:100',
            'scores.*.notes' => 'nullable|string|max:255',
        ]);

        $teacher = Teacher::where('user_id', auth()->id())->first();
        if (!$teacher) {
            // If admin or other role, fallback to first teacher or create a dummy teacher
            $teacher = Teacher::first();
            if (!$teacher) {
                return redirect()->back()->with('error', 'Harap daftarkan guru terlebih dahulu untuk menginput nilai.');
            }
        }

        foreach ($validated['scores'] as $scoreData) {
            Grade::updateOrCreate(
                [
                    'student_id' => $scoreData['student_id'],
                    'classroom_id' => $validated['classroom_id'],
                    'subject_id' => $validated['subject_id'],
                    'type' => $validated['type'],
                ],
                [
                    'teacher_id' => $teacher->id,
                    'score' => $scoreData['score'],
                    'notes' => $scoreData['notes'] ?? null,
                ]
            );
        }

        return redirect()->back()->with('success', 'Nilai berhasil disimpan.');
    }

    public function export(Request $request)
    {
        $classroomId = $request->input('classroom_id');
        $subjectId = $request->input('subject_id');
        $type = $request->input('type');

        if (!$classroomId || !$subjectId) {
            return redirect()->back()->with('error', 'Pilih Kelas dan Mata Pelajaran terlebih dahulu.');
        }

        $classroom = Classroom::findOrFail($classroomId);
        $subject = Subject::findOrFail($subjectId);

        $students = Student::where('classroom_id', $classroomId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        if ($type) {
            // Export specific type
            $grades = Grade::where('classroom_id', $classroomId)
                ->where('subject_id', $subjectId)
                ->where('type', $type)
                ->get()
                ->keyBy('student_id');

            $data = [
                ['Laporan Nilai ' . $type . ' - ' . $classroom->name],
                ['Mata Pelajaran: ' . $subject->name],
                ['Tanggal Cetak: ' . date('d-m-Y H:i')],
                [],
                ['No', 'NISN', 'NIS', 'Nama Siswa', 'Nilai', 'Catatan']
            ];

            foreach ($students as $index => $s) {
                $grade = $grades->get($s->id);
                $data[] = [
                    $index + 1,
                    $s->nisn,
                    $s->nis ?? '-',
                    $s->name,
                    $grade ? $grade->score : 0,
                    $grade ? ($grade->notes ?? '-') : '-'
                ];
            }

            $filename = 'Nilai_' . str_replace(' ', '_', $type) . '_' . str_replace(' ', '_', $classroom->name) . '_' . date('Ymd_His') . '.xlsx';
        } else {
            // Export Summary Matrix (Tugas, UH, UTS, UAS, Rata-rata)
            $grades = Grade::where('classroom_id', $classroomId)
                ->where('subject_id', $subjectId)
                ->get()
                ->groupBy('student_id');

            $data = [
                ['Laporan Rekapitulasi Nilai - ' . $classroom->name],
                ['Mata Pelajaran: ' . $subject->name],
                ['Tanggal Cetak: ' . date('d-m-Y H:i')],
                [],
                ['No', 'NISN', 'NIS', 'Nama Siswa', 'Tugas', 'UH', 'UTS', 'UAS', 'Rata-Rata']
            ];

            foreach ($students as $index => $s) {
                $studentGrades = $grades->get($s->id) ?? collect();
                
                $tugas = $studentGrades->where('type', 'Tugas')->first()?->score ?? 0;
                $uh = $studentGrades->where('type', 'UH')->first()?->score ?? 0;
                $uts = $studentGrades->where('type', 'UTS')->first()?->score ?? 0;
                $uas = $studentGrades->where('type', 'UAS')->first()?->score ?? 0;
                
                $average = ($tugas + $uh + $uts + $uas) / 4;

                $data[] = [
                    $index + 1,
                    $s->nisn,
                    $s->nis ?? '-',
                    $s->name,
                    $tugas,
                    $uh,
                    $uts,
                    $uas,
                    round($average, 2)
                ];
            }

            $filename = 'Rekap_Nilai_' . str_replace(' ', '_', $classroom->name) . '_' . date('Ymd_His') . '.xlsx';
        }

        return SimpleXLSXGen::fromArray($data)->downloadAs($filename);
    }
}

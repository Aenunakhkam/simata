<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Student;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Shuchkin\SimpleXLSXGen;

class AttendanceController extends Controller
{
    public function index(Request $request)
    {
        $classroomId = $request->input('classroom_id');
        $subjectId = $request->input('subject_id');
        $date = $request->input('date', date('Y-m-d'));

        $students = [];
        $existingAttendances = [];

        if ($classroomId && $subjectId) {
            $students = Student::where('classroom_id', $classroomId)
                ->where('status', 'active')
                ->orderBy('name')
                ->get();

            $existingAttendances = Attendance::where('classroom_id', $classroomId)
                ->where('subject_id', $subjectId)
                ->where('date', $date)
                ->get()
                ->keyBy('student_id');
        }

        return Inertia::render('Attendances/Index', [
            'classrooms' => Classroom::orderBy('level')->orderBy('name')->get(),
            'subjects' => Subject::orderBy('name')->get(),
            'students' => $students,
            'existingAttendances' => $existingAttendances,
            'filters' => [
                'classroom_id' => $classroomId,
                'subject_id' => $subjectId,
                'date' => $date
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'classroom_id' => 'required|exists:classrooms,id',
            'subject_id' => 'required|exists:subjects,id',
            'date' => 'required|date',
            'attendances' => 'required|array',
            'attendances.*.student_id' => 'required|exists:students,id',
            'attendances.*.status' => 'required|string|in:H,S,I,A',
            'attendances.*.notes' => 'nullable|string|max:255',
        ]);

        $teacher = Teacher::where('user_id', auth()->id())->first();
        if (!$teacher) {
            $teacher = Teacher::first();
            if (!$teacher) {
                return redirect()->back()->with('error', 'Harap daftarkan guru terlebih dahulu untuk mengabsen.');
            }
        }

        foreach ($validated['attendances'] as $attData) {
            Attendance::updateOrCreate(
                [
                    'student_id' => $attData['student_id'],
                    'classroom_id' => $validated['classroom_id'],
                    'subject_id' => $validated['subject_id'],
                    'date' => $validated['date'],
                ],
                [
                    'teacher_id' => $teacher->id,
                    'status' => $attData['status'],
                    'notes' => $attData['notes'] ?? null,
                ]
            );
        }

        return redirect()->back()->with('success', 'Presensi berhasil disimpan.');
    }

    public function export(Request $request)
    {
        $classroomId = $request->input('classroom_id');
        $subjectId = $request->input('subject_id');

        if (!$classroomId || !$subjectId) {
            return redirect()->back()->with('error', 'Pilih Kelas dan Mata Pelajaran terlebih dahulu.');
        }

        $classroom = Classroom::findOrFail($classroomId);
        $subject = Subject::findOrFail($subjectId);

        $students = Student::where('classroom_id', $classroomId)
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        // Get all attendances for summary
        $attendances = Attendance::where('classroom_id', $classroomId)
            ->where('subject_id', $subjectId)
            ->get()
            ->groupBy('student_id');

        $data = [
            ['Laporan Rekapitulasi Presensi - ' . $classroom->name],
            ['Mata Pelajaran: ' . $subject->name],
            ['Tanggal Cetak: ' . date('d-m-Y H:i')],
            [],
            ['No', 'NISN', 'NIS', 'Nama Siswa', 'Hadir (H)', 'Sakit (S)', 'Izin (I)', 'Alpa (A)', 'Persentase Kehadiran']
        ];

        foreach ($students as $index => $s) {
            $studentAtts = $attendances->get($s->id) ?? collect();
            
            $h = $studentAtts->where('status', 'H')->count();
            $skt = $studentAtts->where('status', 'S')->count();
            $i = $studentAtts->where('status', 'I')->count();
            $a = $studentAtts->where('status', 'A')->count();
            
            $total = $h + $skt + $i + $a;
            $percentage = $total > 0 ? ($h / $total) * 100 : 0;

            $data[] = [
                $index + 1,
                $s->nisn,
                $s->nis ?? '-',
                $s->name,
                $h,
                $skt,
                $i,
                $a,
                round($percentage, 1) . '%'
            ];
        }

        $filename = 'Rekap_Presensi_' . str_replace(' ', '_', $classroom->name) . '_' . date('Ymd_His') . '.xlsx';

        return SimpleXLSXGen::fromArray($data)->downloadAs($filename);
    }
}

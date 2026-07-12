<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Classroom;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Shuchkin\SimpleXLSX;
use Shuchkin\SimpleXLSXGen;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $classroomId = $request->input('classroom_id');

        $query = Student::with('classroom.major')->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('nisn', 'ilike', "%{$search}%")
                  ->orWhere('nis', 'ilike', "%{$search}%");
            });
        }

        if ($classroomId) {
            $query->where('classroom_id', $classroomId);
        }

        $students = ($perPage === 'all')
            ? $query->paginate($query->count() > 0 ? $query->count() : 1)
            : $query->paginate($perPage);

        return Inertia::render('Students/Index', [
            'students' => $students->withQueryString(),
            'classrooms' => Classroom::with('major')->orderBy('level')->orderBy('name')->get(),
            'filters' => $request->only(['search', 'per_page', 'classroom_id'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nisn' => 'required|string|max:20|unique:students,nisn',
            'nis' => 'nullable|string|max:20|unique:students,nis',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'status' => 'required|in:active,graduated,dropped_out',
        ]);

        Student::create(array_merge($validated, ['nasabah_type' => 'Siswa']));

        return redirect()->back()->with('success', 'Data siswa berhasil ditambahkan.');
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'nisn' => 'required|string|max:20|unique:students,nisn,' . $student->id,
            'nis' => 'nullable|string|max:20|unique:students,nis,' . $student->id,
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'status' => 'required|in:active,graduated,dropped_out',
        ]);

        $student->update($validated);

        return redirect()->back()->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->back()->with('success', 'Data siswa berhasil dihapus.');
    }

    public function template()
    {
        $data = [
            ['NISN', 'NIS', 'Nama Lengkap', 'Jenis Kelamin (L/P)', 'Nama Kelas', 'Status (Aktif/Lulus/Keluar)'],
            ['0012345678', '1001', 'Ahmad Dani', 'L', '10 RPL 1', 'Aktif'],
            ['0012345679', '1002', 'Siti Rahma', 'P', '11 TKJ 1', 'Aktif'],
        ];

        return SimpleXLSXGen::fromArray($data)->downloadAs('Template_Impor_Siswa.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        if ($xlsx = SimpleXLSX::parse($request->file('file')->path())) {
            $rows = $xlsx->rows();
            
            // Skip the header row
            array_shift($rows);

            $importedCount = 0;

            foreach ($rows as $row) {
                $nisn = isset($row[0]) ? trim($row[0]) : '';
                $nis = isset($row[1]) ? trim($row[1]) : '';
                $name = isset($row[2]) ? trim($row[2]) : '';
                $genderRaw = isset($row[3]) ? strtoupper(trim($row[3])) : 'L';
                $gender = in_array($genderRaw, ['L', 'P']) ? $genderRaw : 'L';
                $className = isset($row[4]) ? trim($row[4]) : '';
                
                $statusRaw = isset($row[5]) && trim($row[5]) !== '' ? strtolower(trim($row[5])) : 'aktif';
                $statusMap = [
                    'aktif' => 'active',
                    'lulus' => 'graduated',
                    'keluar' => 'dropped_out',
                    'pindah' => 'dropped_out'
                ];
                $status = isset($statusMap[$statusRaw]) ? $statusMap[$statusRaw] : 'active';

                if (empty($nisn) || empty($name)) continue;

                $classroomId = null;
                if (!empty($className)) {
                    $classroom = Classroom::whereRaw('LOWER(name) = ?', [strtolower($className)])->first();
                    if ($classroom) {
                        $classroomId = $classroom->id;
                    }
                }

                // Create or Update student based on NISN
                Student::updateOrCreate(
                    ['nisn' => $nisn],
                    [
                        'nis' => $nis === '' ? null : $nis,
                        'name' => $name,
                        'gender' => $gender,
                        'classroom_id' => $classroomId,
                        'status' => $status,
                        'nasabah_type' => 'Siswa'
                    ]
                );

                $importedCount++;
            }

            return redirect()->back()->with('success', "Berhasil mengimpor $importedCount data siswa.");
        } else {
            return redirect()->back()->with('error', 'Gagal membaca file Excel: ' . SimpleXLSX::parseError());
        }
    }

    public function export(Request $request)
    {
        $classroomId = $request->input('classroom_id');
        $query = Student::with('classroom.major')->orderBy('name');

        if ($classroomId) {
            $query->where('classroom_id', $classroomId);
        }

        $students = $query->get();

        $data = [
            ['No', 'NISN', 'NIS', 'Nama Lengkap', 'Jenis Kelamin', 'Kelas', 'Status']
        ];

        foreach ($students as $index => $s) {
            $statusLabel = 'Aktif';
            if ($s->status === 'graduated') $statusLabel = 'Lulus';
            if ($s->status === 'dropped_out') $statusLabel = 'Keluar';

            $data[] = [
                $index + 1,
                $s->nisn,
                $s->nis ?? '-',
                $s->name,
                $s->gender === 'L' ? 'Laki-laki' : 'Perempuan',
                $s->classroom->name ?? '-',
                $statusLabel
            ];
        }

        $filename = 'Data_Siswa_';
        if ($classroomId && $students->count() > 0) {
            $filename .= str_replace(' ', '_', $students->first()->classroom->name) . '_';
        }
        $filename .= date('Ymd_His') . '.xlsx';

        return SimpleXLSXGen::fromArray($data)->downloadAs($filename);
    }
}

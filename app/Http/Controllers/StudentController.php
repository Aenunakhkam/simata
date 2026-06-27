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

        if ($perPage === 'all') {
            $students = $query->paginate($query->count() > 0 ? $query->count() : 1);
        } else {
            $students = $query->paginate($perPage);
        }

        return Inertia::render('Students/Index', [
            'students' => $students->withQueryString(),
            'classrooms' => Classroom::with('major')->orderBy('level')->orderBy('name')->get(),
            'filters' => $request->only(['search', 'per_page', 'classroom_id'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nisn' => 'required|string|max:20|unique:students',
            'nis' => 'nullable|string|max:20|unique:students',
            'name' => 'required|string|max:255',
            'nasabah_type' => 'required|in:Siswa,Guru,Staf,Lainnya',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'status' => 'required|in:active,graduated,dropped_out',
        ]);

        Student::create($validated);

        return redirect()->back()->with('message', 'Data nasabah berhasil ditambahkan.');
    }

    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'nisn' => 'required|string|max:20|unique:students,nisn,' . $student->id,
            'nis' => 'nullable|string|max:20|unique:students,nis,' . $student->id,
            'name' => 'required|string|max:255',
            'nasabah_type' => 'required|in:Siswa,Guru,Staf,Lainnya',
            'classroom_id' => 'nullable|exists:classrooms,id',
            'status' => 'required|in:active,graduated,dropped_out',
        ]);

        $student->update($validated);

        return redirect()->back()->with('message', 'Data nasabah berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('students.index')->with('success', 'Siswa berhasil dihapus.');
    }

    public function template()
    {
        $data = [
            ['Nomor Rekening / NISN', 'NIP / NIS', 'Nama Lengkap', 'Kategori Nasabah', 'Nama Kelas (Khusus Siswa)', 'Status'],
            ['0012345678', '1001', 'Ahmad Dani', 'Siswa', '10 RPL 1', 'Aktif'],
            ['88812345', '19800101', 'Budi Santoso', 'Guru', '', 'Aktif'],
        ];

        return SimpleXLSXGen::fromArray($data)->downloadAs('Template_Data_Nasabah.xlsx');
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
                $nisn = isset($row[0]) ? trim($row[0]) : ''; // Nomor Rekening
                $nis = isset($row[1]) ? trim($row[1]) : '';  // NIP / NIS
                $name = isset($row[2]) ? trim($row[2]) : ''; // Nama Lengkap
                
                // Kategori Nasabah
                $rawType = isset($row[3]) && trim($row[3]) !== '' ? ucwords(strtolower(trim($row[3]))) : 'Siswa';
                $validTypes = ['Siswa', 'Guru', 'Staf', 'Lainnya'];
                $nasabahType = in_array($rawType, $validTypes) ? $rawType : 'Siswa';

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

                $nis = $nis === '' ? null : $nis;

                $classroomId = null;
                // Hanya cari kelas jika kategorinya Siswa
                if ($nasabahType === 'Siswa' && !empty($className)) {
                    $classroom = Classroom::whereRaw('LOWER(name) = ?', [strtolower($className)])->first();
                    if ($classroom) {
                        $classroomId = $classroom->id;
                    }
                }

                // Create or Update student based on NISN (No Rekening)
                Student::updateOrCreate(
                    ['nisn' => $nisn],
                    [
                        'nis' => $nis,
                        'name' => $name,
                        'nasabah_type' => $nasabahType,
                        'classroom_id' => $classroomId,
                        'status' => $status
                    ]
                );

                $importedCount++;
            }

            return redirect()->route('students.index')->with('success', "Berhasil mengimpor $importedCount data nasabah.");
        } else {
            return redirect()->route('students.index')->with('error', 'Gagal membaca file Excel. ' . SimpleXLSX::parseError());
        }
    }
    public function bulkUpdateClass(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'classroom_id' => 'required|exists:classrooms,id',
        ]);

        Student::whereIn('id', $request->student_ids)->update([
            'classroom_id' => $request->classroom_id
        ]);

        return redirect()->back()->with('success', count($request->student_ids) . ' siswa berhasil dipindahkan kelasnya.');
    }
}

<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Major;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Shuchkin\SimpleXLSX;
use Shuchkin\SimpleXLSXGen;

class ClassroomController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = Classroom::with(['major', 'students' => function ($q) {
            $q->orderBy('name', 'asc');
        }])
            ->when($search, function ($query, $search) {
                $query->where('name', 'ilike', "%{$search}%")
                      ->orWhereHas('major', function ($q) use ($search) {
                        $q->where('name', 'ilike', "%{$search}%")
                            ->orWhere('code', 'ilike', "%{$search}%");
                      });
            })
            ->orderBy('level')
            ->orderBy('name');

        if ($perPage === 'all') {
            $classrooms = $query->paginate($query->count() > 0 ? $query->count() : 1);
        } else {
            $classrooms = $query->paginate($perPage);
        }

        return Inertia::render('Classrooms/Index', [
            'classrooms' => $classrooms->withQueryString(),
            'majors' => Major::orderBy('name')->get(),
            'filters' => $request->only(['search', 'per_page'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'major_id' => 'required|exists:majors,id',
            'level' => 'required|integer|min:1|max:15',
            'name' => 'required|string|max:255',
        ]);

        Classroom::create($validated);

        return redirect()->back()->with('message', 'Kelas berhasil ditambahkan.');
    }

    public function update(Request $request, Classroom $classroom)
    {
        $validated = $request->validate([
            'major_id' => 'required|exists:majors,id',
            'level' => 'required|integer|min:1|max:15',
            'name' => 'required|string|max:255',
        ]);

        $classroom->update($validated);

        return redirect()->back()->with('message', 'Kelas berhasil diperbarui.');
    }

    public function destroy(Classroom $classroom)
    {
        $classroom->delete();

        return redirect()->back()->with('message', 'Kelas berhasil dihapus.');
    }

    public function template()
    {
        $data = [
            ['Tingkat (Angka)', 'Nama Kelas', 'Kode Jurusan'],
            ['10', '10 RPL 1', 'RPL'],
            ['11', '11 TKJ 2', 'TKJ'],
        ];

        return SimpleXLSXGen::fromArray($data)->downloadAs('Template_Data_Kelas.xlsx');
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
                $level = isset($row[0]) ? trim($row[0]) : '';
                $name = isset($row[1]) ? trim($row[1]) : '';
                $majorCode = isset($row[2]) ? trim($row[2]) : '';

                if (empty($level) || empty($name) || empty($majorCode)) continue;

                $major = Major::whereRaw('LOWER(code) = ?', [strtolower($majorCode)])->first();
                
                if (!$major) continue;

                Classroom::updateOrCreate(
                    ['name' => $name],
                    [
                        'level' => $level,
                        'major_id' => $major->id
                    ]
                );

                $importedCount++;
            }

            return redirect()->back()->with('success', "Berhasil mengimpor $importedCount data kelas.");
        } else {
            return redirect()->back()->with('error', 'Gagal membaca file Excel. ' . SimpleXLSX::parseError());
        }
    }
}

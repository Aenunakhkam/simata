<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Shuchkin\SimpleXLSX;
use Shuchkin\SimpleXLSXGen;

class SubjectController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = Subject::latest();

        if ($search) {
            $query->where('name', 'ilike', "%{$search}%")
                  ->orWhere('code', 'ilike', "%{$search}%");
        }

        $subjects = ($perPage === 'all')
            ? $query->paginate($query->count() > 0 ? $query->count() : 1)
            : $query->paginate($perPage);

        return Inertia::render('Subjects/Index', [
            'subjects' => $subjects->withQueryString(),
            'filters' => $request->only(['search', 'per_page'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:subjects,code',
            'name' => 'required|string|max:255',
        ]);

        Subject::create($validated);

        return redirect()->back()->with('success', 'Mata pelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, Subject $subject)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:subjects,code,' . $subject->id,
            'name' => 'required|string|max:255',
        ]);

        $subject->update($validated);

        return redirect()->back()->with('success', 'Mata pelajaran berhasil diperbarui.');
    }

    public function destroy(Subject $subject)
    {
        $subject->delete();

        return redirect()->back()->with('success', 'Mata pelajaran berhasil dihapus.');
    }

    public function template()
    {
        $data = [
            ['Kode Mapel', 'Nama Mata Pelajaran'],
            ['MAT', 'Matematika'],
            ['WEB', 'Pemrograman Web'],
            ['IND', 'Bahasa Indonesia'],
        ];

        return SimpleXLSXGen::fromArray($data)->downloadAs('Template_Impor_Mata_Pelajaran.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls'
        ]);

        if ($xlsx = SimpleXLSX::parse($request->file('file')->path())) {
            $rows = $xlsx->rows();
            array_shift($rows); // Skip header

            $importedCount = 0;

            foreach ($rows as $row) {
                $code = isset($row[0]) ? trim($row[0]) : '';
                $name = isset($row[1]) ? trim($row[1]) : '';

                if (empty($code) || empty($name)) continue;

                Subject::updateOrCreate(
                    ['code' => $code],
                    ['name' => $name]
                );

                $importedCount++;
            }

            return redirect()->back()->with('success', "Berhasil mengimpor $importedCount mata pelajaran.");
        } else {
            return redirect()->back()->with('error', 'Gagal membaca file Excel: ' . SimpleXLSX::parseError());
        }
    }
}

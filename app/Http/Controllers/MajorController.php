<?php

namespace App\Http\Controllers;

use App\Models\Major;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Shuchkin\SimpleXLSX;
use Shuchkin\SimpleXLSXGen;

class MajorController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = Major::latest();

        if ($search) {
            $query->where('name', 'ilike', "%{$search}%")
                  ->orWhere('code', 'ilike', "%{$search}%");
        }

        if ($perPage === 'all') {
            $majors = $query->paginate($query->count() > 0 ? $query->count() : 1);
        } else {
            $majors = $query->paginate($perPage);
        }

        return Inertia::render('Majors/Index', [
            'majors' => $majors->withQueryString(),
            'filters' => $request->only(['search', 'per_page'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:majors',
            'name' => 'required|string|max:255',
        ]);

        Major::create($validated);

        return redirect()->back()->with('message', 'Jurusan berhasil ditambahkan.');
    }

    public function update(Request $request, Major $major)
    {
        $validated = $request->validate([
            'code' => 'required|string|max:50|unique:majors,code,' . $major->id,
            'name' => 'required|string|max:255',
        ]);

        $major->update($validated);

        return redirect()->back()->with('message', 'Jurusan berhasil diperbarui.');
    }

    public function destroy(Major $major)
    {
        $major->delete();

        return redirect()->back()->with('message', 'Jurusan berhasil dihapus.');
    }

    public function template()
    {
        $data = [
            ['Kode Jurusan', 'Nama Jurusan'],
            ['RPL', 'Rekayasa Perangkat Lunak'],
            ['TKJ', 'Teknik Komputer dan Jaringan'],
        ];

        return SimpleXLSXGen::fromArray($data)->downloadAs('Template_Data_Jurusan.xlsx');
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
                $code = isset($row[0]) ? trim($row[0]) : '';
                $name = isset($row[1]) ? trim($row[1]) : '';

                if (empty($code) || empty($name)) continue;

                Major::updateOrCreate(
                    ['code' => $code],
                    ['name' => $name]
                );

                $importedCount++;
            }

            return redirect()->back()->with('success', "Berhasil mengimpor $importedCount data jurusan.");
        } else {
            return redirect()->back()->with('error', 'Gagal membaca file Excel. ' . SimpleXLSX::parseError());
        }
    }
}

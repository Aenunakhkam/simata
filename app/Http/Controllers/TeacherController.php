<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;
use Shuchkin\SimpleXLSX;
use Shuchkin\SimpleXLSXGen;

class TeacherController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');

        $query = Teacher::with('user')->latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('nip', 'ilike', "%{$search}%")
                  ->orWhereHas('user', function($qu) use ($search) {
                      $qu->where('email', 'ilike', "%{$search}%");
                  });
            });
        }

        $teachers = ($perPage === 'all') 
            ? $query->paginate($query->count() > 0 ? $query->count() : 1)
            : $query->paginate($perPage);

        return Inertia::render('Teachers/Index', [
            'teachers' => $teachers->withQueryString(),
            'filters' => $request->only(['search', 'per_page'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nip' => 'nullable|string|max:50|unique:teachers,nip',
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'guru',
        ]);

        Teacher::create([
            'user_id' => $user->id,
            'nip' => $validated['nip'],
            'name' => $validated['name'],
            'gender' => $validated['gender'],
            'phone' => $validated['phone'],
        ]);

        return redirect()->back()->with('success', 'Data guru berhasil ditambahkan.');
    }

    public function update(Request $request, Teacher $teacher)
    {
        $validated = $request->validate([
            'nip' => 'nullable|string|max:50|unique:teachers,nip,' . $teacher->id,
            'name' => 'required|string|max:255',
            'gender' => 'required|in:L,P',
            'phone' => 'nullable|string|max:20',
            'email' => 'required|email|unique:users,email,' . ($teacher->user_id ?? 0),
            'password' => 'nullable|string|min:6',
        ]);

        if ($teacher->user) {
            $userUpdate = [
                'name' => $validated['name'],
                'email' => $validated['email'],
            ];
            if (!empty($validated['password'])) {
                $userUpdate['password'] = Hash::make($validated['password']);
            }
            $teacher->user->update($userUpdate);
        }

        $teacher->update([
            'nip' => $validated['nip'],
            'name' => $validated['name'],
            'gender' => $validated['gender'],
            'phone' => $validated['phone'],
        ]);

        return redirect()->back()->with('success', 'Data guru berhasil diperbarui.');
    }

    public function destroy(Teacher $teacher)
    {
        if ($teacher->user) {
            $teacher->user->delete();
        } else {
            $teacher->delete();
        }

        return redirect()->back()->with('success', 'Data guru berhasil dihapus.');
    }

    public function template()
    {
        $data = [
            ['NIP', 'Nama Lengkap', 'Jenis Kelamin (L/P)', 'Nomor Telepon', 'Email Login', 'Kata Sandi'],
            ['198501102010011002', 'Budi Raharjo, S.Kom', 'L', '081234567890', 'budi@sekolah.com', 'password123'],
            ['199004122015032001', 'Amanda Wijaya, S.Pd', 'P', '082345678901', 'amanda@sekolah.com', 'password123'],
        ];

        return SimpleXLSXGen::fromArray($data)->downloadAs('Template_Impor_Guru.xlsx');
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
                $nip = isset($row[0]) ? trim($row[0]) : '';
                $name = isset($row[1]) ? trim($row[1]) : '';
                $genderRaw = isset($row[2]) ? strtoupper(trim($row[2])) : 'L';
                $gender = in_array($genderRaw, ['L', 'P']) ? $genderRaw : 'L';
                $phone = isset($row[3]) ? trim($row[3]) : '';
                $email = isset($row[4]) ? trim($row[4]) : '';
                $passwordRaw = isset($row[5]) && trim($row[5]) !== '' ? trim($row[5]) : 'password';

                if (empty($name) || empty($email)) continue;

                // Create or update user
                $user = User::updateOrCreate(
                    ['email' => $email],
                    [
                        'name' => $name,
                        'password' => Hash::make($passwordRaw),
                        'role' => 'guru'
                    ]
                );

                // Create or update teacher record
                Teacher::updateOrCreate(
                    ['user_id' => $user->id],
                    [
                        'nip' => $nip === '' ? null : $nip,
                        'name' => $name,
                        'gender' => $gender,
                        'phone' => $phone === '' ? null : $phone,
                    ]
                );

                $importedCount++;
            }

            return redirect()->back()->with('success', "Berhasil mengimpor $importedCount data guru.");
        } else {
            return redirect()->back()->with('error', 'Gagal membaca file Excel: ' . SimpleXLSX::parseError());
        }
    }

    public function export()
    {
        $teachers = Teacher::with('user')->orderBy('name')->get();

        $data = [
            ['No', 'NIP', 'Nama Lengkap', 'Jenis Kelamin', 'Telepon', 'Email']
        ];

        foreach ($teachers as $index => $t) {
            $data[] = [
                $index + 1,
                $t->nip ?? '-',
                $t->name,
                $t->gender === 'L' ? 'Laki-laki' : 'Perempuan',
                $t->phone ?? '-',
                $t->user->email ?? '-'
            ];
        }

        return SimpleXLSXGen::fromArray($data)->downloadAs('Data_Guru_' . date('Ymd_His') . '.xlsx');
    }
}

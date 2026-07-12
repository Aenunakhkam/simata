<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Inertia\Inertia;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $perPage = $request->input('per_page', 10);
        $search = $request->input('search');
        $role = $request->input('role');

        $query = User::latest();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'ilike', "%{$search}%")
                  ->orWhere('email', 'ilike', "%{$search}%")
                  ->orWhere('npsn', 'ilike', "%{$search}%");
            });
        }

        if ($role) {
            $query->where('role', $role);
        }

        $users = ($perPage === 'all')
            ? $query->paginate($query->count() > 0 ? $query->count() : 1)
            : $query->paginate($perPage);

        return Inertia::render('Users/Index', [
            'users' => $users->withQueryString(),
            'roles' => ['admin', 'guru', 'siswa', 'bendahara', 'kepsek'],
            'filters' => $request->only(['search', 'per_page', 'role'])
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'role' => 'required|string|in:admin,guru,siswa,bendahara,kepsek',
            'npsn' => 'nullable|string|max:50',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'npsn' => $validated['npsn'] ?? null,
        ]);

        return redirect()->back()->with('success', 'Akun pengguna berhasil ditambahkan.');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:6',
            'role' => 'required|string|in:admin,guru,siswa,bendahara,kepsek',
            'npsn' => 'nullable|string|max:50',
        ]);

        $updateData = [
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role' => $validated['role'],
            'npsn' => $validated['npsn'] ?? null,
        ];

        if (!empty($validated['password'])) {
            $updateData['password'] = Hash::make($validated['password']);
        }

        $user->update($updateData);

        return redirect()->back()->with('success', 'Akun pengguna berhasil diperbarui.');
    }

    public function destroy(User $user)
    {
        // Don't allow self-deletion
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        $user->delete();

        return redirect()->back()->with('success', 'Akun pengguna berhasil dihapus.');
    }
    public function resetPassword(User $user)
    {
        // Don't allow resetting own password
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'Anda tidak dapat mereset kata sandi Anda sendiri dari sini.');
        }

        $user->update([
            'password' => Hash::make('Belajar123*')
        ]);

        return redirect()->back()->with('success', 'Kata sandi berhasil direset menjadi: Belajar123*');
    }
}

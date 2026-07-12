@echo off
color 0A
title Reset Password Admin SIMATA

echo.
echo ========================================================
echo    SIMATA - SISTEM INFORMASI MANAJEMEN TATA AKADEMIK
echo ========================================================
echo.
echo Sedang memproses reset kata sandi Administrator...
echo.

cd /d %~dp0

php artisan tinker --execute="$user = App\Models\User::where('role', 'admin')->first(); if($user) { $user->password = Hash::make('Belajar123*'); $user->save(); echo \"[SUKSES] Kata sandi Admin berhasil direset menjadi: Belajar123*\n\"; } else { echo \"[ERROR] Akun Admin tidak ditemukan!\n\"; }"

echo.
echo ========================================================
echo Proses selesai! Silakan login kembali dengan sandi baru.
echo ========================================================
echo.
pause

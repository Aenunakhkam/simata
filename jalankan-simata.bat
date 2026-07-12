@echo off
color 1F
echo ===================================================
echo               MENYIAPKAN SIMATA
echo ===================================================
echo.
echo Mohon tunggu sebentar, sistem sedang dihidupkan...

:: Menjalankan server PHP di background pada port 2705
start /B php artisan serve --port=2705 > NUL 2>&1

:: Menunggu 3 detik agar server siap
timeout /t 3 /nobreak > NUL

:: Otomatis membuka browser ke alamat tersebut
start http://localhost:2705

echo.
echo SIMATA berhasil dijalankan!
echo Browser Anda akan otomatis terbuka.
echo.
echo PENTING: JANGAN TUTUP JENDELA HITAM INI.
echo Tutup jendela ini hanya jika Anda sudah selesai menggunakan SIMATA.
echo ===================================================

:: Menjaga jendela tetap terbuka
cmd /k

<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Kosongkan seeder agar instalasi SIMATA benar-benar bersih 
        // tanpa data dummy. Pengguna pertama yang mendaftar (Register) 
        // akan otomatis menjadi Administrator.
    }
}

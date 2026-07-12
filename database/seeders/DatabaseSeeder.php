<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Enrollment;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Administrator',
            'npsn' => '10002000',
            'email' => 'admin@simata.com',
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        User::create([
            'name' => 'Bendahara Sekolah',
            'npsn' => '10002001',
            'email' => 'bendahara@simata.com',
            'password' => Hash::make('password'),
            'role' => 'bendahara',
        ]);

        User::create([
            'name' => 'Kepala Sekolah',
            'npsn' => '10002002',
            'email' => 'kepsek@simata.com',
            'password' => Hash::make('password'),
            'role' => 'kepsek',
        ]);

        // E-Learning Courses Data
        $c1 = Course::create([
            'title' => 'HTML & CSS Dasar untuk Pemula',
            'description' => 'Mulai perjalanan Anda sebagai web developer dengan mempelajari dasar-dasar HTML5 untuk membuat struktur web, dan CSS3 untuk menghias halaman agar terlihat modern dan responsif.',
            'category' => 'Web Development',
            'instructor_name' => 'Budi Raharjo',
            'level' => 'Pemula',
            'duration_hours' => 12,
        ]);

        $c2 = Course::create([
            'title' => 'Kuasai Pemrograman JavaScript Modern',
            'description' => 'Pelajari logika pemrograman JavaScript dari dasar variabel, fungsi, objek, manipulasi DOM, hingga konsep modern ES6+ dan asynchronous programming dengan Fetch API.',
            'category' => 'Programming',
            'instructor_name' => 'Amanda Wijaya',
            'level' => 'Menengah',
            'duration_hours' => 24,
        ]);

        $c3 = Course::create([
            'title' => 'Desain UI/UX Modern dengan Figma',
            'description' => 'Belajar prinsip dasar desain antarmuka (UI) dan pengalaman pengguna (UX). Mulai dari pembuatan wireframe, pemilihan teori warna, komponen reusable, hingga membuat prototype yang interaktif.',
            'category' => 'UI/UX Design',
            'instructor_name' => 'Jessica Natalia',
            'level' => 'Pemula',
            'duration_hours' => 18,
        ]);

        $c4 = Course::create([
            'title' => 'Membangun Aplikasi Web dengan Laravel & Vue.js 3',
            'description' => 'Kelas tingkat lanjut untuk mempelajari integrasi Laravel sebagai backend API/MVC dengan Vue.js 3 sebagai frontend interaktif melalui perantara Inertia.js.',
            'category' => 'Web Development',
            'instructor_name' => 'Rian Pratama',
            'level' => 'Lanjutan',
            'duration_hours' => 36,
        ]);

        // Seed Lessons for Course 1
        Lesson::create([
            'course_id' => $c1->id,
            'title' => 'Pengenalan HTML dan Struktur Web',
            'content' => 'HTML (HyperText Markup Language) adalah bahasa standar yang digunakan untuk membuat dokumen web. Di bab ini, kita akan mempelajari sejarah singkat HTML dan struktur dasar tag pembuka/penutup.',
            'video_url' => 'https://www.youtube.com/embed/g8nQ9m9aVCE',
            'duration_minutes' => 15,
            'order' => 1,
        ]);
        Lesson::create([
            'course_id' => $c1->id,
            'title' => 'Bekerja dengan Text, List, dan Link',
            'content' => 'Dalam bab ini kita akan belajar bagaimana memformat teks dengan tag headings, paragraph, membuat daftar (ordered/unordered list), serta membuat hyperlink antar halaman web.',
            'video_url' => 'https://www.youtube.com/embed/X1b55G3Ggco',
            'duration_minutes' => 20,
            'order' => 2,
        ]);
        Lesson::create([
            'course_id' => $c1->id,
            'title' => 'Dasar Styling dengan CSS3',
            'content' => 'CSS (Cascading Style Sheets) digunakan untuk mengatur gaya visual halaman web. Kita akan belajar cara menautkan file CSS, menggunakan selector element/class/id, dan mengganti warna font.',
            'video_url' => 'https://www.youtube.com/embed/zH85S00Pq0M',
            'duration_minutes' => 25,
            'order' => 3,
        ]);
        Lesson::create([
            'course_id' => $c1->id,
            'title' => 'Membuat Layout Responsif dengan Flexbox',
            'content' => 'Flexbox adalah modul tata letak satu dimensi untuk meletakkan item dalam baris atau kolom. Pelajari `display: flex`, `justify-content`, dan `align-items` untuk membuat layout modern.',
            'video_url' => 'https://www.youtube.com/embed/u044iM9Xgfc',
            'duration_minutes' => 30,
            'order' => 4,
        ]);

        // Seed Lessons for Course 2
        Lesson::create([
            'course_id' => $c2->id,
            'title' => 'Pengenalan JavaScript & Tipe Data',
            'content' => 'JavaScript adalah bahasa pemrograman dinamis untuk web. Di sini kita belajar sintaks dasar, cara menulis kode js, serta tipe data String, Number, Boolean, Array, dan Object.',
            'video_url' => 'https://www.youtube.com/embed/R8K-_iK159c',
            'duration_minutes' => 15,
            'order' => 1,
        ]);
        Lesson::create([
            'course_id' => $c2->id,
            'title' => 'Logika Kondisional & Pengulangan',
            'content' => 'Belajar mengontrol alur program dengan pernyataan `if`, `else if`, `else` dan perulangan `for` serta `while` untuk mengeksekusi blok kode berulang kali secara efisien.',
            'video_url' => 'https://www.youtube.com/embed/2Xg0yK0qj3M',
            'duration_minutes' => 20,
            'order' => 2,
        ]);
        Lesson::create([
            'course_id' => $c2->id,
            'title' => 'Manipulasi DOM (Document Object Model)',
            'content' => 'DOM mewakili struktur halaman web sebagai pohon objek. Pelajari cara memilih elemen dengan `document.querySelector` dan mengubah kontennya menggunakan event listeners.',
            'video_url' => 'https://www.youtube.com/embed/H01-E8e0d4E',
            'duration_minutes' => 25,
            'order' => 3,
        ]);

        // Seed Lessons for Course 3
        Lesson::create([
            'course_id' => $c3->id,
            'title' => 'Pengenalan UI/UX dan Workspace Figma',
            'content' => 'Figma adalah tool desain kolaboratif berbasis cloud. Di bab ini kita akan menjelajahi antarmuka Figma, membuat frame, memanipulasi bentuk dasar (shape), dan menyusun layout.',
            'video_url' => 'https://www.youtube.com/embed/jP7QZ0y2m3c',
            'duration_minutes' => 18,
            'order' => 1,
        ]);
        Lesson::create([
            'course_id' => $c3->id,
            'title' => 'Teori Warna, Tipografi, dan Spasi',
            'content' => 'Pelajari prinsip penting dalam desain visual: kontras warna, hierarki teks dengan tipografi yang tepat, serta manajemen whitespace agar desain terlihat rapi dan tidak padat.',
            'video_url' => 'https://www.youtube.com/embed/2K1M7l0tLzY',
            'duration_minutes' => 22,
            'order' => 2,
        ]);

        // Seed Lessons for Course 4
        Lesson::create([
            'course_id' => $c4->id,
            'title' => 'Mengenal Framework Laravel & Struktur MVC',
            'content' => 'Laravel adalah framework PHP yang elegan. Di sini kita belajar konsep routing, controller, view, dan model (ORM Eloquent) untuk menghubungkan backend ke database PostgreSQL.',
            'video_url' => 'https://www.youtube.com/embed/x4QZ7wHkXv4',
            'duration_minutes' => 25,
            'order' => 1,
        ]);
        Lesson::create([
            'course_id' => $c4->id,
            'title' => 'Koneksi Frontend & Backend via Inertia.js',
            'content' => 'Inertia.js bertindak sebagai perekat yang memungkinkan kita membuat Single Page Application (SPA) menggunakan Laravel dan Vue tanpa perlu membangun API RESTful yang rumit.',
            'video_url' => 'https://www.youtube.com/embed/yF0M4cM42h8',
            'duration_minutes' => 30,
            'order' => 2,
        ]);

        // Seed Enrollments for Administrator
        Enrollment::create([
            'user_id' => $admin->id,
            'course_id' => $c1->id,
            'enrolled_at' => now()->subDays(5),
            'progress' => 75, // 3 dari 4 materi selesai
        ]);

        Enrollment::create([
            'user_id' => $admin->id,
            'course_id' => $c4->id,
            'enrolled_at' => now()->subDays(2),
            'progress' => 50, // 1 dari 2 materi selesai
        ]);

        $this->call(AcademicSeeder::class);
    }
}

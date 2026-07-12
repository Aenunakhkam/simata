<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Major;
use App\Models\Classroom;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\Grade;
use App\Models\Attendance;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AcademicSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Majors
        $rpl = Major::create(['code' => 'RPL', 'name' => 'Rekayasa Perangkat Lunak']);
        $tkj = Major::create(['code' => 'TKJ', 'name' => 'Teknik Komputer dan Jaringan']);
        $mm = Major::create(['code' => 'MM', 'name' => 'Multimedia']);

        // 2. Seed Classrooms
        $c10rpl = Classroom::create(['major_id' => $rpl->id, 'level' => 10, 'name' => '10 RPL 1']);
        $c11rpl = Classroom::create(['major_id' => $rpl->id, 'level' => 11, 'name' => '11 RPL 1']);
        $c10tkj = Classroom::create(['major_id' => $tkj->id, 'level' => 10, 'name' => '10 TKJ 1']);
        $c11tkj = Classroom::create(['major_id' => $tkj->id, 'level' => 11, 'name' => '11 TKJ 1']);

        // 3. Seed Subjects
        $math = Subject::create(['code' => 'MAT', 'name' => 'Matematika']);
        $ind = Subject::create(['code' => 'IND', 'name' => 'Bahasa Indonesia']);
        $web = Subject::create(['code' => 'WEB', 'name' => 'Pemrograman Web']);
        $net = Subject::create(['code' => 'NET', 'name' => 'Administrasi Sistem Jaringan']);

        // 4. Seed Teachers (with User Accounts)
        $teacherUsersData = [
            [
                'name' => 'Budi Raharjo, S.Kom',
                'email' => 'budi.guru@simata.com',
                'password' => Hash::make('password'),
                'role' => 'guru',
                'nip' => '198501102010011002',
                'gender' => 'L',
                'phone' => '081234567890'
            ],
            [
                'name' => 'Amanda Wijaya, S.Pd',
                'email' => 'amanda.guru@simata.com',
                'password' => Hash::make('password'),
                'role' => 'guru',
                'nip' => '199004122015032001',
                'gender' => 'P',
                'phone' => '082345678901'
            ]
        ];

        $teachers = [];
        foreach ($teacherUsersData as $tData) {
            $user = User::create([
                'name' => $tData['name'],
                'email' => $tData['email'],
                'password' => $tData['password'],
                'role' => $tData['role']
            ]);

            $teachers[] = Teacher::create([
                'user_id' => $user->id,
                'nip' => $tData['nip'],
                'name' => $tData['name'],
                'gender' => $tData['gender'],
                'phone' => $tData['phone']
            ]);
        }

        // 5. Seed Students
        $studentsData = [
            // Class 10 RPL 1
            ['nisn' => '00100101', 'nis' => '1001', 'name' => 'Adi Wijaya', 'classroom_id' => $c10rpl->id, 'gender' => 'L'],
            ['nisn' => '00100102', 'nis' => '1002', 'name' => 'Budi Santoso', 'classroom_id' => $c10rpl->id, 'gender' => 'L'],
            ['nisn' => '00100103', 'nis' => '1003', 'name' => 'Cici Paramida', 'classroom_id' => $c10rpl->id, 'gender' => 'P'],
            ['nisn' => '00100104', 'nis' => '1004', 'name' => 'Dedi Setiadi', 'classroom_id' => $c10rpl->id, 'gender' => 'L'],
            ['nisn' => '00100105', 'nis' => '1005', 'name' => 'Evi Masamba', 'classroom_id' => $c10rpl->id, 'gender' => 'P'],

            // Class 11 RPL 1
            ['nisn' => '00110101', 'nis' => '2001', 'name' => 'Farhan Majid', 'classroom_id' => $c11rpl->id, 'gender' => 'L'],
            ['nisn' => '00110102', 'nis' => '2002', 'name' => 'Gina Selvina', 'classroom_id' => $c11rpl->id, 'gender' => 'P'],
            ['nisn' => '00110103', 'nis' => '2003', 'name' => 'Haris Munandar', 'classroom_id' => $c11rpl->id, 'gender' => 'L'],
            ['nisn' => '00110104', 'nis' => '2004', 'name' => 'Indah Lestari', 'classroom_id' => $c11rpl->id, 'gender' => 'P'],
            ['nisn' => '00110105', 'nis' => '2005', 'name' => 'Joko Widodo', 'classroom_id' => $c11rpl->id, 'gender' => 'L'],
        ];

        $students = [];
        foreach ($studentsData as $sData) {
            $students[] = Student::create([
                'nisn' => $sData['nisn'],
                'nis' => $sData['nis'],
                'name' => $sData['name'],
                'classroom_id' => $sData['classroom_id'],
                'gender' => $sData['gender'],
                'status' => 'active',
                'nasabah_type' => 'Siswa'
            ]);
        }

        // 6. Seed Sample Grades (Nilai)
        // Let's add some grades for Budi Santoso (10 RPL 1)
        Grade::create([
            'student_id' => $students[1]->id, // Budi Santoso
            'classroom_id' => $c10rpl->id,
            'subject_id' => $web->id,
            'teacher_id' => $teachers[0]->id, // Budi Raharjo
            'type' => 'Tugas',
            'score' => 85,
            'notes' => 'Tugas HTML Dasar'
        ]);

        Grade::create([
            'student_id' => $students[1]->id,
            'classroom_id' => $c10rpl->id,
            'subject_id' => $web->id,
            'teacher_id' => $teachers[0]->id,
            'type' => 'UTS',
            'score' => 78,
            'notes' => 'Ujian Tengah Semester'
        ]);

        // 7. Seed Sample Attendances (Presensi)
        $dates = [now()->subDays(2)->format('Y-m-d'), now()->subDays(1)->format('Y-m-d'), now()->format('Y-m-d')];
        
        // Seed attendance for 10 RPL 1 class for Matematika subject
        foreach ($dates as $date) {
            foreach ($students as $student) {
                if ($student->classroom_id === $c10rpl->id) {
                    Attendance::create([
                        'student_id' => $student->id,
                        'classroom_id' => $c10rpl->id,
                        'subject_id' => $math->id,
                        'teacher_id' => $teachers[1]->id, // Amanda Wijaya
                        'date' => $date,
                        'status' => 'H', // Hadir
                        'notes' => 'Hadir tepat waktu'
                    ]);
                }
            }
        }
    }
}

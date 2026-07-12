<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // E-Learning Routes
    Route::resource('courses', \App\Http\Controllers\CourseController::class);
    Route::resource('lessons', \App\Http\Controllers\LessonController::class);
    Route::resource('enrollments', \App\Http\Controllers\EnrollmentController::class);

    // Academic & Management Routes (Admin & Teacher specific)
    Route::middleware('role:admin')->group(function () {
        Route::resource('majors', \App\Http\Controllers\MajorController::class);
        Route::post('majors/import', [\App\Http\Controllers\MajorController::class, 'import'])->name('majors.import');
        Route::get('majors-template', [\App\Http\Controllers\MajorController::class, 'template'])->name('majors.template');

        Route::resource('classrooms', \App\Http\Controllers\ClassroomController::class);
        Route::post('classrooms/import', [\App\Http\Controllers\ClassroomController::class, 'import'])->name('classrooms.import');
        Route::get('classrooms-template', [\App\Http\Controllers\ClassroomController::class, 'template'])->name('classrooms.template');

        Route::resource('subjects', \App\Http\Controllers\SubjectController::class);
        Route::post('subjects/import', [\App\Http\Controllers\SubjectController::class, 'import'])->name('subjects.import');
        Route::get('subjects-template', [\App\Http\Controllers\SubjectController::class, 'template'])->name('subjects.template');

        Route::resource('teachers', \App\Http\Controllers\TeacherController::class);
        Route::post('teachers/import', [\App\Http\Controllers\TeacherController::class, 'import'])->name('teachers.import');
        Route::get('teachers-template', [\App\Http\Controllers\TeacherController::class, 'template'])->name('teachers.template');
        Route::get('teachers-export', [\App\Http\Controllers\TeacherController::class, 'export'])->name('teachers.export');

        Route::resource('students', \App\Http\Controllers\StudentController::class);
        Route::post('students/import', [\App\Http\Controllers\StudentController::class, 'import'])->name('students.import');
        Route::get('students-template', [\App\Http\Controllers\StudentController::class, 'template'])->name('students.template');
        Route::get('students-export', [\App\Http\Controllers\StudentController::class, 'export'])->name('students.export');

        Route::resource('users', \App\Http\Controllers\UserController::class);
        Route::post('users/{user}/reset-password', [\App\Http\Controllers\UserController::class, 'resetPassword'])->name('users.reset-password');

        Route::resource('academic-years', \App\Http\Controllers\AcademicYearController::class);
        Route::post('academic-years/{academic_year}/set-active', [\App\Http\Controllers\AcademicYearController::class, 'setActive'])->name('academic-years.set-active');

        Route::resource('schedules', \App\Http\Controllers\ScheduleController::class);

        Route::get('system-settings', [\App\Http\Controllers\SystemSettingController::class, 'index'])->name('system-settings.index');
        Route::post('system-settings', [\App\Http\Controllers\SystemSettingController::class, 'update'])->name('system-settings.update');

        Route::get('backups', [\App\Http\Controllers\BackupController::class, 'index'])->name('backups.index');
        Route::post('backups', [\App\Http\Controllers\BackupController::class, 'create'])->name('backups.create');
        Route::get('backups/{file}/download', [\App\Http\Controllers\BackupController::class, 'download'])->name('backups.download');
        Route::delete('backups/{file}', [\App\Http\Controllers\BackupController::class, 'delete'])->name('backups.delete');
        Route::post('backups/restore', [\App\Http\Controllers\BackupController::class, 'restore'])->name('backups.restore');

        Route::get('updates', [\App\Http\Controllers\UpdateController::class, 'index'])->name('updates.index');
        Route::post('updates/pull', [\App\Http\Controllers\UpdateController::class, 'pull'])->name('updates.pull');
    });

    Route::middleware('role:admin,guru')->group(function () {
        Route::resource('grades', \App\Http\Controllers\GradeController::class)->only(['index', 'store']);
        Route::get('grades-export', [\App\Http\Controllers\GradeController::class, 'export'])->name('grades.export');

        Route::resource('attendances', \App\Http\Controllers\AttendanceController::class)->only(['index', 'store']);
        Route::get('attendances-export', [\App\Http\Controllers\AttendanceController::class, 'export'])->name('attendances.export');
    });
});

require __DIR__.'/auth.php';

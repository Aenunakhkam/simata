<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

const props = defineProps({
    role: {
        type: String,
        default: 'siswa',
    },
    stats: {
        type: Object,
        default: () => ({}),
    },
    enrollments: {
        type: Array,
        default: () => [],
    },
    recommendations: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <Head title="Dashboard SIMATA" />

    <AuthenticatedLayout>
        <template #header>
            Dashboard {{ role === 'admin' ? 'Administrator' : (role === 'guru' ? 'Guru' : 'Belajar') }}
        </template>

        <div class="space-y-8 max-w-7xl mx-auto">
            
            <!-- Welcome Banner Section (All Roles) -->
            <div class="relative overflow-hidden bg-gradient-to-r from-slate-900 via-[#5A0D0E] to-[#7B1113] rounded-3xl p-6 sm:p-8 shadow-xl shadow-[#7B1113]/20 border border-slate-800/40">
                <!-- Abstract Glow Overlay -->
                <div class="absolute -right-16 -top-16 w-64 h-64 bg-[#D4AF37]/10 rounded-full blur-3xl pointer-events-none"></div>
                <div class="absolute right-1/4 bottom-[-30%] w-48 h-48 bg-[#7B1113]/20 rounded-full blur-3xl pointer-events-none"></div>

                <div class="relative z-10 max-w-2xl">
                    <p class="text-xs font-bold text-[#D4AF37] uppercase tracking-widest mb-1.5">Sistem Informasi Akademik & E-Learning</p>
                    <h2 class="text-2xl sm:text-3xl font-black text-white tracking-tight font-montserrat mb-2">
                        Selamat Datang, {{ $page.props.auth.user.name }}! 👋
                    </h2>
                    <p class="text-sm text-slate-300 font-medium leading-relaxed mb-5">
                        Anda masuk sebagai <span class="font-bold text-[#D4AF37] uppercase">{{ role }}</span>. 
                        {{ role === 'admin' ? 'Gunakan menu admin di sebelah kiri untuk mengelola data master sekolah, pengguna, dan rekapitulasi akademik.' : 
                           (role === 'guru' ? 'Anda dapat mulai melakukan pencatatan nilai siswa serta absensi harian kelas melalui shortcut di bawah atau bilah menu.' : 
                           'Silakan lanjutkan progres belajar Anda pada materi e-learning yang tersedia di bawah.') }}
                    </p>
                    <div class="flex flex-wrap gap-3">
                        <template v-if="role === 'admin'">
                            <Link :href="route('teachers.index')" class="inline-flex items-center justify-center px-4 py-2.5 bg-[#7B1113] hover:bg-[#5A0D0E] active:scale-95 text-xs font-bold text-white rounded-xl shadow-lg shadow-[#7B1113]/10 transition-all duration-200">
                                Kelola Guru
                            </Link>
                            <Link :href="route('students.index')" class="inline-flex items-center justify-center px-4 py-2.5 bg-slate-800 hover:bg-slate-700 active:scale-95 text-xs font-bold text-slate-200 rounded-xl transition-all duration-200 border border-slate-700/50">
                                Kelola Siswa
                            </Link>
                        </template>
                        <template v-else-if="role === 'guru'">
                            <Link :href="route('grades.index')" class="inline-flex items-center justify-center px-4 py-2.5 bg-[#7B1113] hover:bg-[#5A0D0E] active:scale-95 text-xs font-bold text-white rounded-xl shadow-lg shadow-[#7B1113]/10 transition-all duration-200">
                                Input Nilai Kelas
                            </Link>
                            <Link :href="route('attendances.index')" class="inline-flex items-center justify-center px-4 py-2.5 bg-[#D4AF37] hover:bg-[#B5952F] active:scale-95 text-xs font-bold text-white rounded-xl shadow-lg shadow-[#D4AF37]/10 transition-all duration-200">
                                Input Absensi Kelas
                            </Link>
                        </template>
                        <template v-else>
                            <Link :href="route('courses.index')" class="inline-flex items-center justify-center px-4 py-2.5 bg-[#7B1113] hover:bg-[#5A0D0E] active:scale-95 text-xs font-bold text-white rounded-xl shadow-lg shadow-[#7B1113]/10 transition-all duration-200">
                                Mulai Belajar
                            </Link>
                        </template>
                    </div>
                </div>
            </div>

            <!-- ================= DASHBOARD ADMIN ================= -->
            <div v-if="role === 'admin'" class="space-y-8">
                <!-- Stats Counters -->
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                    <!-- Total Guru -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition-all duration-200 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-[#7B1113]/10 text-[#7B1113] flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Total Guru</p>
                            <h3 class="text-xl sm:text-2xl font-black text-slate-800 font-montserrat">{{ stats.total_teachers }}</h3>
                        </div>
                    </div>

                    <!-- Total Siswa -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition-all duration-200 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-[#D4AF37]/10 text-[#D4AF37] flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Total Siswa</p>
                            <h3 class="text-xl sm:text-2xl font-black text-slate-800 font-montserrat">{{ stats.total_students }}</h3>
                        </div>
                    </div>

                    <!-- Total Kelas -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition-all duration-200 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Total Kelas</p>
                            <h3 class="text-xl sm:text-2xl font-black text-slate-800 font-montserrat">{{ stats.total_classrooms }}</h3>
                        </div>
                    </div>

                    <!-- Total Pengguna -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition-all duration-200 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-[#7B1113]/10 text-[#7B1113] flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Pengguna</p>
                            <h3 class="text-xl sm:text-2xl font-black text-slate-800 font-montserrat">{{ stats.total_users }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Admin Action shortcuts -->
                <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-sm">
                    <h4 class="text-base font-extrabold text-slate-800 font-montserrat mb-4">Akses Cepat Pengelolaan</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <Link :href="route('majors.index')" class="flex items-center gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-slate-100/50 hover:border-slate-200 transition-all group">
                            <div class="w-10 h-10 rounded-xl bg-[#7B1113]/10 text-[#7B1113] flex items-center justify-center shrink-0 group-hover:scale-105 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2H9z"></path></svg>
                            </div>
                            <div>
                                <h5 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Data Jurusan</h5>
                                <p class="text-[11px] text-slate-400">Atur program keahlian sekolah</p>
                            </div>
                        </Link>
                        <Link :href="route('classrooms.index')" class="flex items-center gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-slate-100/50 hover:border-slate-200 transition-all group">
                            <div class="w-10 h-10 rounded-xl bg-[#7B1113]/10 text-[#7B1113] flex items-center justify-center shrink-0 group-hover:scale-105 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"></path></svg>
                            </div>
                            <div>
                                <h5 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Data Kelas</h5>
                                <p class="text-[11px] text-slate-400">Atur tingkat dan ruang kelas siswa</p>
                            </div>
                        </Link>
                        <Link :href="route('subjects.index')" class="flex items-center gap-3 p-4 rounded-2xl bg-slate-50 border border-slate-100 hover:bg-slate-100/50 hover:border-slate-200 transition-all group">
                            <div class="w-10 h-10 rounded-xl bg-[#7B1113]/10 text-[#7B1113] flex items-center justify-center shrink-0 group-hover:scale-105 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13"></path></svg>
                            </div>
                            <div>
                                <h5 class="text-xs font-bold text-slate-800 uppercase tracking-wider">Mata Pelajaran</h5>
                                <p class="text-[11px] text-slate-400">Kelola mata pelajaran akademik</p>
                            </div>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- ================= DASHBOARD GURU ================= -->
            <div v-else-if="role === 'guru'" class="space-y-8">
                <!-- Stats Counters -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Total Siswa -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition-all duration-200 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-[#D4AF37]/10 text-[#D4AF37] flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Siswa Aktif</p>
                            <h3 class="text-2xl font-black text-slate-800 font-montserrat">{{ stats.total_students }}</h3>
                        </div>
                    </div>

                    <!-- Total Kelas -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition-all duration-200 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Total Kelas Sekolah</p>
                            <h3 class="text-2xl font-black text-slate-800 font-montserrat">{{ stats.total_classrooms }}</h3>
                        </div>
                    </div>

                    <!-- Total Mapel -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md transition-all duration-200 flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-[#7B1113]/10 text-[#7B1113] flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Total Mata Pelajaran</p>
                            <h3 class="text-2xl font-black text-slate-800 font-montserrat">{{ stats.total_subjects }}</h3>
                        </div>
                    </div>
                </div>

                <!-- Teacher Bio Card -->
                <div class="bg-white rounded-3xl border border-slate-100 p-6 shadow-sm">
                    <h4 class="text-base font-extrabold text-slate-800 font-montserrat mb-4">Profil Pendidik</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#7B1113]/10 text-[#7B1113] flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nomor Induk Pegawai (NIP)</p>
                                <p class="text-sm font-bold text-slate-700">{{ stats.nip }}</p>
                            </div>
                        </div>
                        <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-[#7B1113]/10 text-[#7B1113] flex items-center justify-center shrink-0">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.21.986L7.54 8.69a11.038 11.038 0 004.772 4.772l1.378-1.378a1 1 0 01.986-.21l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            </div>
                            <div>
                                <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Nomor Telepon</p>
                                <p class="text-sm font-bold text-slate-700">{{ stats.phone }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ================= DASHBOARD SISWA / FALLBACK ================= -->
            <div v-else class="space-y-8">
                <!-- Stats Counters -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Total Enrolled -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-[#7B1113]/10 text-[#7B1113] flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Kelas Terdaftar</p>
                            <h3 class="text-2xl font-black text-slate-800 font-montserrat">{{ stats.total_enrolled }}</h3>
                        </div>
                    </div>

                    <!-- Completed Courses -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-600 flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Kelas Selesai</p>
                            <h3 class="text-2xl font-black text-slate-800 font-montserrat">{{ stats.completed }}</h3>
                        </div>
                    </div>

                    <!-- Avg Progress -->
                    <div class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm flex items-center gap-4">
                        <div class="w-12 h-12 rounded-xl bg-[#D4AF37]/10 text-[#D4AF37] flex items-center justify-center shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-0.5">Rata-Rata Progres</p>
                            <h3 class="text-2xl font-black text-slate-800 font-montserrat">{{ stats.average_progress }}%</h3>
                        </div>
                    </div>
                </div>

                <!-- Learning Grid Section -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Left 2 Cols: Active Enrollments -->
                    <div class="lg:col-span-2 space-y-5">
                        <h4 class="text-base font-extrabold text-slate-800 font-montserrat tracking-tight flex items-center gap-2">
                            <span>Lanjutkan Belajar</span>
                            <span class="px-2 py-0.5 bg-slate-200 text-slate-700 text-[10px] font-bold rounded-full">
                                {{ enrollments.length }} Aktif
                            </span>
                        </h4>

                        <div class="space-y-4" v-if="enrollments.length > 0">
                            <div
                                v-for="enroll in enrollments"
                                :key="enroll.id"
                                class="bg-white rounded-2xl border border-slate-100 p-5 shadow-sm hover:shadow-md hover:border-slate-200 transition-all duration-300"
                            >
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <div class="space-y-2 max-w-md">
                                        <div class="flex items-center gap-2">
                                            <span class="px-2 py-0.5 bg-[#7B1113]/10 border border-[#7B1113]/20 text-[#7B1113] text-[10px] font-extrabold rounded-md uppercase tracking-wider">
                                                {{ enroll.course.category }}
                                            </span>
                                            <span class="text-slate-400 text-xs font-semibold">• {{ enroll.course.level }}</span>
                                        </div>
                                        <h5 class="text-base font-bold text-slate-800 leading-tight">
                                            {{ enroll.course.title }}
                                        </h5>
                                        <p class="text-xs text-slate-400 font-medium">
                                            Pengajar: <span class="font-bold text-slate-600">{{ enroll.course.instructor_name }}</span>
                                        </p>
                                    </div>
                                    <Link
                                        :href="route('courses.show', enroll.course.id)"
                                        class="inline-flex items-center justify-center py-2 px-4 bg-[#7B1113]/10 hover:bg-[#7B1113]/20 active:scale-95 text-[#7B1113] text-xs font-bold rounded-xl transition-all duration-200 self-start sm:self-center shrink-0"
                                    >
                                        Masuk Kelas
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"></path></svg>
                                    </Link>
                                </div>

                                <!-- Progress Bar -->
                                <div class="mt-5 pt-4 border-t border-slate-50">
                                    <div class="flex justify-between text-xs font-bold text-slate-500 mb-1.5">
                                        <span>Progres Belajar</span>
                                        <span class="text-[#7B1113]">{{ enroll.progress }}%</span>
                                    </div>
                                    <div class="w-full bg-slate-100 rounded-full h-2">
                                        <div
                                            class="bg-[#7B1113] h-2 rounded-full transition-all duration-500"
                                            :style="{ width: enroll.progress + '%' }"
                                        ></div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- In case no active courses -->
                        <div class="bg-white rounded-3xl border border-dashed border-slate-200/80 p-8 text-center" v-else>
                            <div class="w-14 h-14 bg-[#7B1113]/10 text-[#7B1113] rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13"></path></svg>
                            </div>
                            <h5 class="text-slate-800 font-bold text-sm mb-1">Belum Terdaftar di Kelas</h5>
                            <p class="text-xs text-slate-400 font-medium max-w-xs mx-auto mb-5 leading-normal">
                                Anda belum memulai kelas apa pun. Daftar sekarang di kelas impian Anda dan pelajari hal-hal hebat.
                            </p>
                            <Link
                                :href="route('courses.index')"
                                class="inline-flex items-center justify-center px-4 py-2 bg-[#7B1113] hover:bg-[#5A0D0E] active:scale-95 text-white text-xs font-bold rounded-xl shadow-sm transition-all duration-200"
                            >
                                Mulai Belajar Sekarang
                            </Link>
                        </div>
                    </div>

                    <!-- Right 1 Col: Course Recommendations -->
                    <div class="space-y-5">
                        <h4 class="text-base font-extrabold text-slate-800 font-montserrat tracking-tight">Rekomendasi Kelas</h4>
                        
                        <div class="space-y-4" v-if="recommendations.length > 0">
                            <div
                                v-for="rec in recommendations"
                                :key="rec.id"
                                class="bg-white rounded-2xl border border-slate-100 p-4.5 shadow-sm hover:shadow-md transition-all duration-300 flex flex-col justify-between h-full"
                            >
                                <div class="space-y-2">
                                    <div class="flex items-center justify-between gap-2">
                                        <span class="px-2 py-0.5 bg-[#D4AF37]/10 border border-[#D4AF37]/20 text-[#D4AF37] text-[9px] font-extrabold rounded-md uppercase tracking-wider">
                                            {{ rec.category }}
                                        </span>
                                        <span class="text-slate-400 text-[10px] font-semibold">{{ rec.level }}</span>
                                    </div>
                                    <h5 class="text-sm font-bold text-slate-800 leading-snug line-clamp-2">
                                        {{ rec.title }}
                                    </h5>
                                    <p class="text-[11px] text-slate-400 leading-normal line-clamp-2 font-medium">
                                        {{ rec.description }}
                                    </p>
                                </div>
                                
                                <div class="mt-4 pt-3.5 border-t border-slate-50 flex items-center justify-between">
                                    <span class="text-[10px] font-extrabold text-slate-500 flex items-center gap-1 shrink-0">
                                        <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        {{ rec.duration_hours }} Jam
                                    </span>
                                    <Link
                                        :href="route('courses.show', rec.id)"
                                        class="inline-flex items-center justify-center py-1.5 px-3 border border-[#7B1113]/30 bg-white hover:bg-[#7B1113]/5 text-[#7B1113] hover:text-[#5A0D0E] text-[11px] font-bold rounded-lg transition-all duration-200"
                                    >
                                        Lihat Detail
                                    </Link>
                                </div>
                            </div>
                        </div>

                        <!-- In case no recommendations -->
                        <div class="bg-white rounded-2xl border border-slate-100 p-6 text-center shadow-sm" v-else>
                            <p class="text-xs text-slate-400 font-medium leading-relaxed">
                                Hebat! Anda telah terdaftar di semua kelas yang kami sediakan saat ini. Tetap pertahankan progres Anda!
                            </p>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

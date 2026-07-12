<script setup>
import { ref, watch, onMounted } from 'vue';
import { usePage, Link } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import SidebarLink from '@/Components/SidebarLink.vue';

const page = usePage();
const showingSidebar = ref(false);
const desktopSidebarCollapsed = ref(false);

const toggleSidebar = () => {
    if (window.innerWidth >= 1024) {
        desktopSidebarCollapsed.value = !desktopSidebarCollapsed.value;
    } else {
        showingSidebar.value = !showingSidebar.value;
    }
};

// Icons as SVG strings
const icons = {
    dashboard: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>`,
    courses: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>`,
    enrollments: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>`,
    profile: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>`,
    teachers: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>`,
    students: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>`,
    classrooms: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>`,
    majors: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>`,
    subjects: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>`,
    users: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>`,
    grades: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`,
    attendances: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>`,
    academicYears: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>`, // Calendar
    schedules: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>`, // Clock
    settings: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>`,
    backups: `<svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4m0 5c0 2.21-3.582 4-8 4s-8-1.79-8-4"></path></svg>`,
};

const showNotification = () => {
    const flash = page.props.flash;
    if (flash?.success) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: flash.success,
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
        });
    }
    if (flash?.error) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'error',
            title: flash.error,
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
        });
    }
};

onMounted(() => {
    showNotification();
});

watch(() => page.props.flash, () => {
    showNotification();
}, { deep: true });
</script>

<template>
    <div class="flex h-screen bg-slate-50 text-slate-800 font-sans overflow-hidden">
        
        <!-- Mobile Sidebar Overlay -->
        <div v-show="showingSidebar" class="fixed inset-0 z-20 bg-slate-900/60 backdrop-blur-sm transition-opacity lg:hidden" @click="toggleSidebar"></div>

        <!-- Sidebar (Obsidian Slate Theme) -->
        <aside :class="[
            'fixed inset-y-0 left-0 z-30 w-64 bg-[#7B1113] text-white flex flex-col border-r border-white/10 transition-all duration-300',
            'lg:static lg:h-screen lg:shrink-0',
            showingSidebar ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
            desktopSidebarCollapsed ? 'lg:-ml-64' : 'lg:ml-0'
        ]">
            
            <!-- Sidebar Header / Logo -->
            <div class="h-16 flex items-center px-6 border-b border-white/10 shrink-0 gap-3">
                <Link :href="route('dashboard')" class="flex items-center gap-3 group">
                    <div class="w-9 h-9 rounded-xl bg-white flex items-center justify-center shadow-md shadow-black/10 group-hover:scale-105 transition-transform duration-300">
                        <svg class="w-5 h-5 text-[#7B1113]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <span class="text-xl font-black text-white tracking-tight font-montserrat">SIMATA</span>
                </Link>
            </div>

            <!-- Sidebar Navigation -->
            <nav class="flex-1 overflow-y-auto py-6 space-y-6 px-3">
                <!-- MENU ADMIN -->
                <div v-if="$page.props.auth.user.role === 'admin'" class="space-y-6">
                    <div>
                        <div class="px-5 text-[10px] font-bold text-white/50 uppercase tracking-widest mb-2.5">Menu Utama</div>
                        <SidebarLink :href="route('dashboard')" :active="route().current('dashboard')" :icon="icons.dashboard">
                            Dashboard
                        </SidebarLink>
                    </div>

                    <div>
                        <div class="px-5 text-[10px] font-bold text-white/50 uppercase tracking-widest mb-2.5">Akademik & Penempatan</div>
                        <SidebarLink :href="route('academic-years.index')" :active="route().current('academic-years.*')" :icon="icons.academicYears">
                            Tahun Ajaran
                        </SidebarLink>
                        <SidebarLink :href="route('schedules.index')" :active="route().current('schedules.*')" :icon="icons.schedules">
                            Jadwal & Penempatan
                        </SidebarLink>
                    </div>

                    <div>
                        <div class="px-5 text-[10px] font-bold text-white/50 uppercase tracking-widest mb-2.5">Data Master</div>
                        <SidebarLink :href="route('teachers.index')" :active="route().current('teachers.*')" :icon="icons.teachers">
                            Data Guru
                        </SidebarLink>
                        <SidebarLink :href="route('students.index')" :active="route().current('students.*')" :icon="icons.students">
                            Data Siswa
                        </SidebarLink>
                        <SidebarLink :href="route('classrooms.index')" :active="route().current('classrooms.*')" :icon="icons.classrooms">
                            Data Kelas
                        </SidebarLink>
                        <SidebarLink :href="route('majors.index')" :active="route().current('majors.*')" :icon="icons.majors">
                            Data Jurusan
                        </SidebarLink>
                        <SidebarLink :href="route('subjects.index')" :active="route().current('subjects.*')" :icon="icons.subjects">
                            Mata Pelajaran
                        </SidebarLink>
                    </div>

                    <div>
                        <div class="px-5 text-[10px] font-bold text-white/50 uppercase tracking-widest mb-2.5">Rekapitulasi</div>
                        <SidebarLink :href="route('grades.index')" :active="route().current('grades.*')" :icon="icons.grades">
                            Nilai Siswa
                        </SidebarLink>
                        <SidebarLink :href="route('attendances.index')" :active="route().current('attendances.*')" :icon="icons.attendances">
                            Presensi Siswa
                        </SidebarLink>
                    </div>

                    <div>
                        <div class="px-5 text-[10px] font-bold text-white/50 uppercase tracking-widest mb-2.5">Keamanan</div>
                        <SidebarLink :href="route('users.index')" :active="route().current('users.*')" :icon="icons.users">
                            Data Pengguna
                        </SidebarLink>
                    </div>

                    <div>
                        <div class="px-5 text-[10px] font-bold text-white/50 uppercase tracking-widest mb-2.5">Konfigurasi</div>
                        <SidebarLink :href="route('system-settings.index')" :active="route().current('system-settings.*')" :icon="icons.settings">
                            Pengaturan Sistem
                        </SidebarLink>
                        <SidebarLink :href="route('backups.index')" :active="route().current('backups.*')" :icon="icons.backups">
                            Backup & Restore
                        </SidebarLink>
                        <SidebarLink :href="route('updates.index')" :active="route().current('updates.*')" :icon="`<svg fill='none' stroke='currentColor' viewBox='0 0 24 24'><path stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15'></path></svg>`">
                            Cek Pembaruan
                        </SidebarLink>
                    </div>
                </div>

                <!-- MENU GURU -->
                <div v-else-if="$page.props.auth.user.role === 'guru'" class="space-y-6">
                    <div>
                        <div class="px-5 text-[10px] font-bold text-white/50 uppercase tracking-widest mb-2.5">Menu Utama</div>
                        <SidebarLink :href="route('dashboard')" :active="route().current('dashboard')" :icon="icons.dashboard">
                            Dashboard
                        </SidebarLink>
                    </div>

                    <div>
                        <div class="px-5 text-[10px] font-bold text-white/50 uppercase tracking-widest mb-2.5">Akademik</div>
                        <SidebarLink :href="route('grades.index')" :active="route().current('grades.*')" :icon="icons.grades">
                            Input Nilai
                        </SidebarLink>
                        <SidebarLink :href="route('attendances.index')" :active="route().current('attendances.*')" :icon="icons.attendances">
                            Input Presensi
                        </SidebarLink>
                    </div>
                </div>

                <!-- MENU SISWA / FALLBACK -->
                <div v-else class="space-y-6">
                    <div>
                        <div class="px-5 text-[10px] font-bold text-white/50 uppercase tracking-widest mb-2.5">Menu Utama</div>
                        <SidebarLink :href="route('dashboard')" :active="route().current('dashboard')" :icon="icons.dashboard">
                            Dashboard
                        </SidebarLink>
                        <SidebarLink :href="route('courses.index')" :active="route().current('courses.*')" :icon="icons.courses">
                            Daftar Kelas
                        </SidebarLink>
                        <SidebarLink :href="route('enrollments.index')" :active="route().current('enrollments.*')" :icon="icons.enrollments">
                            Kelas Saya
                        </SidebarLink>
                    </div>
                </div>

                <!-- PENGATURAN AKUN (SEMUA ROLE) -->
                <div>
                    <div class="px-5 text-[10px] font-bold text-white/50 uppercase tracking-widest mb-2.5">Akun Saya</div>
                    <SidebarLink :href="route('profile.edit')" :active="route().current('profile.edit')" :icon="icons.profile">
                        Profil Saya
                    </SidebarLink>
                </div>
            </nav>

            <!-- Sidebar Footer -->
            <div class="p-4 border-t border-white/10 text-[10px] font-semibold text-white/50 text-center uppercase tracking-wider">
                &copy; {{ new Date().getFullYear() }} SIMATA STUDY
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden">
            
            <!-- Top Navbar -->
            <header class="bg-white/80 backdrop-blur-md shadow-[0_1px_3px_rgba(0,0,0,0.02)] h-16 flex items-center justify-between px-6 z-10 shrink-0 border-b border-slate-100">
                
                <!-- Left: Hamburger & Header Title -->
                <div class="flex items-center gap-4 min-w-0">
                    <button @click="toggleSidebar" class="p-2 -ml-2 text-slate-400 hover:text-[#7B1113] hover:bg-rose-50 rounded-xl transition-all duration-200 focus:outline-none">
                        <svg class="w-5.5 h-5.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>
                    <div class="truncate text-lg font-bold text-slate-800 font-montserrat" v-if="$slots.header">
                        <slot name="header" />
                    </div>
                </div>

                <!-- Right: Profile Dropdown -->
                <div class="flex items-center shrink-0 ml-4">
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button type="button" class="flex items-center gap-2.5 bg-slate-50 hover:bg-slate-100/80 border border-slate-200/60 pl-2.5 pr-3.5 py-1.5 rounded-2xl transition-all duration-200 focus:outline-none group">
                                <div class="w-8 h-8 rounded-xl bg-gradient-to-tr from-[#7B1113] to-[#A51C30] flex items-center justify-center text-[#D4AF37] shadow-sm font-bold text-sm tracking-wide font-montserrat">
                                    {{ $page.props.auth.user.name ? $page.props.auth.user.name.charAt(0).toUpperCase() : 'U' }}
                                </div>
                                <div class="text-left hidden sm:block">
                                    <p class="text-xs font-bold text-slate-700 leading-none mb-0.5 group-hover:text-slate-900 transition-colors">{{ $page.props.auth.user.name }}</p>
                                    <p class="text-[9px] font-bold text-[#7B1113] leading-none uppercase tracking-wider">{{ $page.props.auth.user.role }}</p>
                                </div>
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-slate-600 transition-all duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>
                        </template>

                        <template #content>
                            <div class="px-4 py-3 border-b border-slate-100">
                                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider mb-0.5">Siswa Aktif</p>
                                <p class="text-xs font-bold text-slate-800 truncate">{{ $page.props.auth.user.email }}</p>
                            </div>
                            <DropdownLink :href="route('profile.edit')" class="font-semibold text-xs text-slate-600 hover:text-[#7B1113] hover:bg-rose-50">
                                Pengaturan Profil
                            </DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button" class="font-bold text-xs text-rose-600 hover:text-rose-700 hover:bg-rose-50 border-t border-slate-50 w-full text-left">
                                Keluar Sistem
                            </DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-y-auto p-6 lg:p-8">
                <slot />
            </main>
        </div>
    </div>
</template>

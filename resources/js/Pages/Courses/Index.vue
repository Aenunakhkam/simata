<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

const props = defineProps({
    courses: {
        type: Array,
        default: () => [],
    },
    enrolledCourseIds: {
        type: Array,
        default: () => [],
    },
});

const isEnrolled = (courseId) => {
    return props.enrolledCourseIds.includes(courseId);
};
</script>

<template>
    <Head title="Katalog Kelas Belajar SIMATA" />

    <AuthenticatedLayout>
        <template #header>
            Daftar Kelas Pembelajaran
        </template>

        <div class="space-y-6 max-w-7xl mx-auto">
            <!-- Header Text -->
            <div class="space-y-2">
                <h3 class="text-xl font-extrabold text-slate-800 font-montserrat tracking-tight">Semua Kelas Yang Tersedia</h3>
                <p class="text-xs sm:text-sm text-slate-400 font-medium leading-relaxed max-w-2xl">
                    Pilih kelas belajar sesuai dengan bidang minat Anda. Klik detail kelas untuk melihat kurikulum materi pelajaran atau langsung mendaftar belajar.
                </p>
            </div>

            <!-- Courses Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" v-if="courses.length > 0">
                <div
                    v-for="course in courses"
                    :key="course.id"
                    class="bg-white rounded-3xl border border-slate-100 p-5 shadow-sm hover:shadow-[0_15px_30px_rgba(99,102,241,0.04)] hover:border-slate-200 hover:-translate-y-1 transition-all duration-300 flex flex-col justify-between"
                >
                    <div class="space-y-3.5">
                        <!-- Badges -->
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 bg-[#7B1113]/10 border border-indigo-100 text-[#5A0D0E] text-[10px] font-extrabold rounded-md uppercase tracking-wider">
                                {{ course.category }}
                            </span>
                            <span class="text-slate-400 text-xs font-bold">{{ course.level }}</span>
                        </div>

                        <!-- Title & Description -->
                        <div class="space-y-1.5">
                            <h4 class="text-base font-bold text-slate-800 leading-snug font-montserrat hover:text-[#7B1113] transition-colors">
                                <Link :href="route('courses.show', course.id)">{{ course.title }}</Link>
                            </h4>
                            <p class="text-xs text-slate-400 font-medium leading-relaxed line-clamp-3">
                                {{ course.description }}
                            </p>
                        </div>
                    </div>

                    <!-- Details & Footer -->
                    <div class="mt-6 pt-4 border-t border-slate-50 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <span class="text-[10px] font-extrabold text-slate-500 flex items-center gap-1">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                {{ course.lessons_count }} Bab
                            </span>
                            <span class="text-[10px] font-extrabold text-slate-500 flex items-center gap-1">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ course.duration_hours }} Jam
                            </span>
                        </div>

                        <!-- Button -->
                        <Link
                            v-if="isEnrolled(course.id)"
                            :href="route('courses.show', course.id)"
                            class="inline-flex items-center justify-center py-2 px-4 bg-[#7B1113]/10 hover:bg-indigo-100 text-[#5A0D0E] text-xs font-bold rounded-xl transition-all duration-200"
                        >
                            Masuk Kelas
                        </Link>
                        <Link
                            v-else
                            :href="route('courses.show', course.id)"
                            class="inline-flex items-center justify-center py-2 px-4 border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl transition-all duration-200"
                        >
                            Detail Kelas
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div class="bg-white rounded-3xl border border-dashed border-slate-200/80 p-12 text-center" v-else>
                <div class="w-16 h-16 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <h4 class="text-slate-800 font-bold text-base mb-1.5">Tidak Ada Kelas Tersedia</h4>
                <p class="text-xs text-slate-400 font-medium max-w-sm mx-auto">
                    Mohon maaf, saat ini katalog kelas masih kosong. Silakan hubungi admin pembelajaran atau periksa kembali nanti.
                </p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

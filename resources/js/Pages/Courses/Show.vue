<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";

const props = defineProps({
    course: {
        type: Object,
        required: true,
    },
    isEnrolled: {
        type: Boolean,
        default: false,
    },
    enrollment: {
        type: Object,
        default: null,
    },
});

const enroll = () => {
    router.post(route('enrollments.store'), {
        course_id: props.course.id,
    });
};
</script>

<template>
    <Head :title="course.title + ' - Detail Kelas'" />

    <AuthenticatedLayout>
        <template #header>
            Detail Kelas
        </template>

        <div class="space-y-8 max-w-7xl mx-auto">
            
            <!-- Back button -->
            <div>
                <Link
                    :href="route('courses.index')"
                    class="inline-flex items-center text-xs font-bold text-slate-400 hover:text-[#7B1113] transition-colors gap-1.5"
                >
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    Kembali ke Katalog
                </Link>
            </div>

            <!-- Course Header Card -->
            <div class="bg-white rounded-3xl border border-slate-100 p-6 sm:p-8 shadow-sm relative overflow-hidden">
                <div class="flex flex-col lg:flex-row justify-between gap-8 items-start relative z-10">
                    <div class="space-y-4 max-w-3xl">
                        <!-- Badges -->
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 bg-[#7B1113]/10 border border-indigo-100 text-[#5A0D0E] text-[10px] font-extrabold rounded-md uppercase tracking-wider">
                                {{ course.category }}
                            </span>
                            <span class="px-2 py-0.5 bg-slate-100 text-slate-500 text-[10px] font-bold rounded-md">
                                {{ course.level }}
                            </span>
                        </div>

                        <!-- Title and Description -->
                        <div class="space-y-3">
                            <h3 class="text-2xl sm:text-3xl font-extrabold text-slate-800 tracking-tight font-montserrat leading-tight">
                                {{ course.title }}
                            </h3>
                            <p class="text-sm text-slate-500 font-medium leading-relaxed">
                                {{ course.description }}
                            </p>
                        </div>

                        <!-- Metadata -->
                        <div class="flex flex-wrap items-center gap-x-6 gap-y-2 pt-2 text-xs font-semibold text-slate-400">
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                Instruktur: <strong class="text-slate-600 font-bold">{{ course.instructor_name }}</strong>
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                {{ course.lessons.length }} Materi Pelajaran
                            </span>
                            <span class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                Estimasi: <strong class="text-slate-600 font-bold">{{ course.duration_hours }} Jam Belajar</strong>
                            </span>
                        </div>
                    </div>

                    <!-- Enrollment/Status Action box -->
                    <div class="w-full lg:w-auto shrink-0 bg-slate-50 border border-slate-100 p-5 rounded-2xl flex flex-col items-center justify-center text-center lg:min-w-[240px]">
                        <div class="w-full space-y-4" v-if="!isEnrolled">
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider leading-none">Status Belajar</p>
                            <p class="text-sm font-extrabold text-slate-800">Belum Terdaftar</p>
                            <button
                                @click="enroll"
                                class="w-full flex justify-center items-center py-2.5 px-4 rounded-xl shadow-md shadow-indigo-100 text-xs font-bold text-white bg-[#7B1113] hover:bg-[#5A0D0E] active:scale-95 transition-all duration-200"
                            >
                                Daftar Kelas Ini
                            </button>
                        </div>
                        <div class="w-full space-y-4" v-else>
                            <p class="text-xs font-bold text-slate-400 uppercase tracking-wider leading-none">Progres Belajar</p>
                            <div>
                                <p class="text-2xl font-black text-[#7B1113] tracking-tight font-montserrat">{{ enrollment.progress }}%</p>
                                <p class="text-[10px] font-bold text-slate-400 uppercase mt-0.5">Selesai</p>
                            </div>
                            <div class="w-full bg-slate-200 rounded-full h-1.5">
                                <div class="bg-[#7B1113] h-1.5 rounded-full" :style="{ width: enrollment.progress + '%' }"></div>
                            </div>
                            
                            <Link
                                v-if="course.lessons.length > 0"
                                :href="route('lessons.show', course.lessons[0].id)"
                                class="w-full inline-flex justify-center items-center py-2.5 px-4 rounded-xl text-xs font-bold text-white bg-[#7B1113] hover:bg-[#5A0D0E] active:scale-95 transition-all duration-200"
                            >
                                Mulai Belajar
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Curriculum Section -->
            <div class="space-y-4">
                <h4 class="text-lg font-extrabold text-slate-800 font-montserrat tracking-tight">Silabus & Kurikulum Kelas</h4>
                
                <div class="bg-white rounded-3xl border border-slate-100 overflow-hidden shadow-sm">
                    <div class="divide-y divide-slate-100" v-if="course.lessons.length > 0">
                        <!-- Lesson row -->
                        <div
                            v-for="(lesson, index) in course.lessons"
                            :key="lesson.id"
                            class="flex items-center justify-between p-5 hover:bg-slate-50/50 transition-colors group"
                        >
                            <div class="flex items-center gap-4 min-w-0 pr-4">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 font-extrabold text-xs flex items-center justify-center shrink-0 group-hover:bg-[#7B1113]/10 group-hover:text-[#7B1113] transition-colors">
                                    {{ index + 1 }}
                                </div>
                                <div class="min-w-0">
                                    <h5 class="text-sm font-bold text-slate-700 truncate leading-snug group-hover:text-[#7B1113] transition-colors">
                                        {{ lesson.title }}
                                    </h5>
                                    <p class="text-xs text-slate-400 font-medium tracking-wide">
                                        Durasi: {{ lesson.duration_minutes }} Menit
                                    </p>
                                </div>
                            </div>

                            <!-- Lock/Unlock status icon and link -->
                            <div class="shrink-0">
                                <Link
                                    v-if="isEnrolled"
                                    :href="route('lessons.show', lesson.id)"
                                    class="inline-flex items-center justify-center w-8 h-8 rounded-xl bg-[#7B1113]/10 text-[#7B1113] hover:bg-[#7B1113] hover:text-white transition-all duration-200"
                                >
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </Link>
                                <div class="w-8 h-8 rounded-xl bg-slate-50 text-slate-300 flex items-center justify-center select-none" v-else title="Daftar kelas untuk membuka materi ini">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- No Lessons Empty State -->
                    <div class="p-8 text-center" v-else>
                        <p class="text-xs text-slate-400 font-medium leading-relaxed">
                            Materi pelajaran sedang dalam tahap penyusunan oleh instruktur. Silakan periksa kembali beberapa saat lagi.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

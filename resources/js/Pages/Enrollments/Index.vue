<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link } from "@inertiajs/vue3";

const props = defineProps({
    enrollments: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <Head title="Kelas Saya - Belajar SIMATA" />

    <AuthenticatedLayout>
        <template #header>
            Kelas Saya
        </template>

        <div class="space-y-6 max-w-7xl mx-auto">
            <!-- Header Text -->
            <div class="space-y-2">
                <h3 class="text-xl font-extrabold text-slate-800 font-montserrat tracking-tight">Perkembangan Belajar Anda</h3>
                <p class="text-xs sm:text-sm text-slate-400 font-medium leading-relaxed max-w-2xl">
                    Berikut adalah daftar semua kelas aktif yang sedang Anda ikuti. Lanjutkan belajar untuk mengasah dan menyelesaikan keahlian baru Anda.
                </p>
            </div>

            <!-- Enrollments Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6" v-if="enrollments.length > 0">
                <div
                    v-for="enroll in enrollments"
                    :key="enroll.id"
                    class="bg-white rounded-3xl border border-slate-100 p-5 shadow-sm hover:shadow-[0_15px_30px_rgba(99,102,241,0.04)] hover:border-slate-200 transition-all duration-300 flex flex-col justify-between"
                >
                    <div class="space-y-4">
                        <!-- Badges -->
                        <div class="flex items-center justify-between">
                            <span class="px-2.5 py-0.5 bg-[#7B1113]/10 border border-indigo-100 text-[#5A0D0E] text-[10px] font-extrabold rounded-md uppercase tracking-wider">
                                {{ enroll.course.category }}
                            </span>
                            <span class="text-slate-400 text-xs font-bold">{{ enroll.course.level }}</span>
                        </div>

                        <!-- Title and Instructor -->
                        <div class="space-y-1.5">
                            <h4 class="text-base font-bold text-slate-800 leading-snug font-montserrat hover:text-[#7B1113] transition-colors">
                                <Link :href="route('courses.show', enroll.course.id)">{{ enroll.course.title }}</Link>
                            </h4>
                            <p class="text-xs text-slate-400 font-medium">
                                Instruktur: <span class="font-bold text-slate-600">{{ enroll.course.instructor_name }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Progress Bar & Actions -->
                    <div class="mt-6 pt-4 border-t border-slate-50 space-y-4">
                        <div>
                            <div class="flex justify-between text-xs font-bold text-slate-500 mb-1.5">
                                <span>Progres</span>
                                <span class="text-[#7B1113] font-extrabold">{{ enroll.progress }}%</span>
                            </div>
                            <div class="w-full bg-slate-100 rounded-full h-1.5">
                                <div
                                    class="bg-[#7B1113] h-1.5 rounded-full transition-all duration-500"
                                    :style="{ width: enroll.progress + '%' }"
                                ></div>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-2">
                            <span class="text-[10px] font-extrabold text-slate-400">
                                {{ enroll.course.lessons_count }} Pelajaran
                            </span>
                            
                            <Link
                                :href="route('courses.show', enroll.course.id)"
                                class="inline-flex items-center justify-center py-2 px-4 bg-[#7B1113] hover:bg-[#5A0D0E] active:scale-95 text-white text-xs font-bold rounded-xl transition-all duration-200"
                            >
                                Lanjutkan Belajar
                            </Link>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div class="bg-white rounded-3xl border border-dashed border-slate-200/80 p-12 text-center" v-else>
                <div class="w-16 h-16 bg-slate-50 text-slate-400 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <h4 class="text-slate-800 font-bold text-base mb-1.5">Anda Belum Terdaftar di Kelas Manapun</h4>
                <p class="text-xs text-slate-400 font-medium max-w-sm mx-auto mb-5 leading-normal">
                    Silakan buka katalog kelas kami untuk melihat daftar kelas yang tersedia dan mulailah belajar keahlian baru!
                </p>
                <Link
                    :href="route('courses.index')"
                    class="inline-flex items-center justify-center px-4 py-2 bg-[#7B1113] hover:bg-[#5A0D0E] active:scale-95 text-white text-xs font-bold rounded-xl shadow-sm transition-all duration-200"
                >
                    Lihat Katalog Kelas
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

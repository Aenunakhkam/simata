<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, Link, router } from "@inertiajs/vue3";
import { computed } from "vue";

const props = defineProps({
    lesson: {
        type: Object,
        required: true,
    },
    course: {
        type: Object,
        required: true,
    },
    enrollment: {
        type: Object,
        required: true,
    },
});

// Sort lessons by order
const sortedLessons = computed(() => {
    return [...props.course.lessons].sort((a, b) => a.order - b.order);
});

// Find current index of lesson
const currentLessonIndex = computed(() => {
    return sortedLessons.value.findIndex(l => l.id === props.lesson.id);
});

// Calculate next lesson
const nextLesson = computed(() => {
    if (currentLessonIndex.value < sortedLessons.value.length - 1) {
        return sortedLessons.value[currentLessonIndex.value + 1];
    }
    return null;
});

// Calculate previous lesson
const prevLesson = computed(() => {
    if (currentLessonIndex.value > 0) {
        return sortedLessons.value[currentLessonIndex.value - 1];
    }
    return null;
});

// Check if current lesson order exceeds progress percent
const completeLesson = () => {
    const total = sortedLessons.value.length;
    const current = currentLessonIndex.value + 1;
    const progressPercent = Math.round((current / total) * 100);

    router.put(route('enrollments.update', props.enrollment.id), {
        progress: progressPercent
    }, {
        onSuccess: () => {
            if (nextLesson.value) {
                router.visit(route('lessons.show', nextLesson.value.id));
            } else {
                Swal.fire({
                    icon: 'success',
                    title: 'Selamat!',
                    text: 'Anda telah menyelesaikan seluruh materi di kelas ini.',
                    confirmButtonText: 'Kembali ke Dashboard',
                    confirmButtonColor: '#4f46e5',
                }).then(() => {
                    router.visit(route('dashboard'));
                });
            }
        }
    });
};
</script>

<template>
    <Head :title="lesson.title + ' - Belajar SIMATA'" />

    <AuthenticatedLayout>
        <template #header>
            Ruang Belajar: {{ course.title }}
        </template>

        <div class="max-w-7xl mx-auto flex flex-col lg:flex-row gap-8 items-stretch h-full">
            
            <!-- Left Panel: Video and Reading Content -->
            <div class="w-full lg:w-[68%] space-y-6">
                <!-- Video Player Container -->
                <div class="bg-black aspect-video rounded-3xl overflow-hidden shadow-md relative group border border-slate-900" v-if="lesson.video_url">
                    <iframe
                        class="w-full h-full border-0"
                        :src="lesson.video_url"
                        title="Video Player"
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                        allowfullscreen
                    ></iframe>
                </div>

                <!-- Reading Content Card -->
                <div class="bg-white rounded-3xl border border-slate-100 p-6 sm:p-8 shadow-sm space-y-5">
                    <div class="space-y-2">
                        <div class="flex items-center justify-between gap-4">
                            <span class="px-2 py-0.5 bg-[#7B1113]/10 border border-indigo-100 text-[#5A0D0E] text-[10px] font-bold rounded-md">
                                Bab {{ currentLessonIndex + 1 }} dari {{ sortedLessons.length }}
                            </span>
                            <span class="text-xs text-slate-400 font-semibold flex items-center gap-1">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                {{ lesson.duration_minutes }} Menit
                            </span>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-extrabold text-slate-800 tracking-tight font-montserrat">
                            {{ lesson.title }}
                        </h3>
                    </div>

                    <!-- Article / HTML content of the lesson -->
                    <article class="prose prose-slate max-w-none text-sm sm:text-base text-slate-600 font-medium leading-relaxed pt-2 border-t border-slate-50 space-y-4">
                        <p class="whitespace-pre-line">{{ lesson.content }}</p>
                    </article>

                    <!-- Navigation Footer controls -->
                    <div class="flex items-center justify-between pt-6 border-t border-slate-50">
                        <Link
                            v-if="prevLesson"
                            :href="route('lessons.show', prevLesson.id)"
                            class="inline-flex items-center justify-center py-2 px-4 border border-slate-200 hover:bg-slate-50 text-slate-700 text-xs font-bold rounded-xl transition-all duration-200 gap-1"
                        >
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M15 19l-7-7 7-7"></path></svg>
                            Materi Sebelumnya
                        </Link>
                        <div v-else></div>

                        <button
                            @click="completeLesson"
                            class="inline-flex items-center justify-center py-2.5 px-5 rounded-xl shadow-md shadow-indigo-100 text-xs font-bold text-white bg-[#7B1113] hover:bg-[#5A0D0E] active:scale-95 transition-all duration-200 gap-1.5"
                        >
                            {{ nextLesson ? 'Selesai & Lanjut' : 'Selesai Kelas' }}
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Right Panel: Lessons Playlist Sidebar -->
            <div class="w-full lg:w-[32%] shrink-0">
                <div class="bg-white rounded-3xl border border-slate-100 p-5 shadow-sm space-y-4 lg:sticky lg:top-24">
                    <h4 class="text-sm font-extrabold text-slate-800 font-montserrat uppercase tracking-wider mb-2">Materi Kelas</h4>
                    
                    <div class="space-y-2">
                        <Link
                            v-for="(item, index) in sortedLessons"
                            :key="item.id"
                            :href="route('lessons.show', item.id)"
                            :class="[
                                'flex items-center gap-3 p-3.5 rounded-2xl transition-all duration-200 text-left w-full group border',
                                item.id === lesson.id
                                    ? 'bg-[#7B1113]/5 border-indigo-100 text-[#5A0D0E] font-bold'
                                    : 'bg-white border-transparent text-slate-600 hover:bg-slate-50 hover:text-slate-800'
                            ]"
                        >
                            <div :class="[
                                'w-7 h-7 rounded-lg text-xs font-bold flex items-center justify-center shrink-0 transition-colors',
                                item.id === lesson.id
                                    ? 'bg-[#7B1113] text-white shadow-sm'
                                    : 'bg-slate-50 text-slate-400 group-hover:bg-[#7B1113]/10 group-hover:text-[#7B1113]'
                            ]">
                                {{ index + 1 }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p :class="[
                                    'text-xs truncate leading-snug',
                                    item.id === lesson.id ? 'font-bold text-[#5A0D0E]' : 'font-semibold text-slate-700'
                                ]">
                                    {{ item.title }}
                                </p>
                                <p class="text-[10px] text-slate-400 font-semibold tracking-wide mt-0.5">
                                    {{ item.duration_minutes }} Menit
                                </p>
                            </div>
                        </Link>
                    </div>

                    <div class="pt-2 border-t border-slate-50">
                        <Link
                            :href="route('courses.show', course.id)"
                            class="w-full inline-flex items-center justify-center py-2 bg-slate-50 hover:bg-slate-100 text-slate-600 text-xs font-bold rounded-xl transition-all"
                        >
                            Kembali ke Detail Kelas
                        </Link>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

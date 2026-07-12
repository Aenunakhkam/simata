<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import Swal from "sweetalert2";

const props = defineProps({
    classrooms: Array,
    subjects: Array,
    types: Array,
    students: Array,
    existingGrades: Object, // Grouped by student_id
    filters: Object,
});

const classroomId = ref(props.filters.classroom_id || "");
const subjectId = ref(props.filters.subject_id || "");
const gradeType = ref(props.filters.type || "Tugas");

const scoresForm = useForm({
    classroom_id: "",
    subject_id: "",
    type: "",
    scores: [], // Array of { student_id, score, notes }
});

const loadGrades = () => {
    if (classroomId.value && subjectId.value) {
        router.get(
            route("grades.index"),
            { 
                classroom_id: classroomId.value, 
                subject_id: subjectId.value,
                type: gradeType.value
            },
            { 
                preserveState: true, 
                replace: true,
                onSuccess: () => {
                    initializeForm();
                }
            }
        );
    }
};

const initializeForm = () => {
    scoresForm.classroom_id = classroomId.value;
    scoresForm.subject_id = subjectId.value;
    scoresForm.type = gradeType.value;
    
    scoresForm.scores = props.students.map(student => {
        // Find existing score
        const studentGrades = props.existingGrades[student.id] || [];
        const match = studentGrades.find(g => g.type === gradeType.value);

        return {
            student_id: student.id,
            name: student.name,
            nisn: student.nisn,
            score: match ? match.score : "",
            notes: match ? (match.notes ?? "") : "",
        };
    });
};

// Initialize if students are loaded
if (props.students.length > 0) {
    initializeForm();
}

const submitScores = () => {
    if (scoresForm.scores.length === 0) return;

    // Validate scores
    let hasInvalid = false;
    scoresForm.scores.forEach(s => {
        if (s.score === "" || isNaN(s.score) || s.score < 0 || s.score > 100) {
            hasInvalid = true;
        }
    });

    if (hasInvalid) {
        Swal.fire("Peringatan", "Harap isi semua nilai siswa dengan angka antara 0-100", "warning");
        return;
    }

    scoresForm.post(route("grades.store"), {
        onSuccess: () => {
            Swal.fire("Berhasil", "Nilai siswa berhasil disimpan", "success");
        }
    });
};

const triggerExport = (specificType = true) => {
    if (!classroomId.value || !subjectId.value) {
        Swal.fire("Peringatan", "Pilih Kelas dan Mata Pelajaran terlebih dahulu", "warning");
        return;
    }

    const params = {
        classroom_id: classroomId.value,
        subject_id: subjectId.value
    };

    if (specificType) {
        params.type = gradeType.value;
    }

    window.open(route("grades.export", params), "_blank");
};
</script>

<template>
    <Head title="Pencatatan Nilai Siswa" />

    <AuthenticatedLayout>
        <template #header> Pencatatan Nilai Siswa </template>

        <div class="space-y-6 max-w-7xl mx-auto">
            <!-- Filter Selector Header -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
                <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest">Filter Pencarian</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Classroom Select -->
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Pilih Kelas</label>
                        <select v-model="classroomId" @change="loadGrades" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold text-slate-700 transition-all">
                            <option value="">-- Pilih Ruang Kelas --</option>
                            <option v-for="c in classrooms" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                    </div>

                    <!-- Subject Select -->
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Mata Pelajaran</label>
                        <select v-model="subjectId" @change="loadGrades" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold text-slate-700 transition-all">
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            <option v-for="s in subjects" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>
                    </div>

                    <!-- Grade Type Select -->
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Jenis Nilai</label>
                        <select v-model="gradeType" @change="loadGrades" :disabled="!classroomId || !subjectId" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold text-slate-700 transition-all disabled:opacity-50">
                            <option value="Tugas">Tugas Harian</option>
                            <option value="UH">Ulangan Harian (UH)</option>
                            <option value="UTS">Ujian Tengah Semester (UTS)</option>
                            <option value="UAS">Ujian Akhir Semester (UAS)</option>
                        </select>
                    </div>
                </div>

                <!-- Export Options -->
                <div class="pt-3 border-t border-slate-100 flex flex-wrap gap-2 justify-end" v-if="classroomId && subjectId">
                    <button @click="triggerExport(true)" class="inline-flex items-center px-4 py-2 bg-slate-100 hover:bg-slate-200/80 active:scale-95 text-xs font-bold text-slate-700 rounded-xl transition-all border border-slate-200/40">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Ekspor Nilai ({{ gradeType }})
                    </button>
                    <button @click="triggerExport(false)" class="inline-flex items-center px-4 py-2 bg-[#D4AF37] hover:bg-[#B5952F] active:scale-95 text-xs font-bold text-white rounded-xl shadow-md shadow-emerald-600/10 transition-all">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Ekspor Rekapitulasi (Semua Nilai)
                    </button>
                </div>
            </div>

            <!-- Grading Table Form -->
            <div v-if="classroomId && subjectId && students.length > 0" class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <form @submit.prevent="submitScores">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                                    <th class="py-4 px-6 w-20 text-center">No</th>
                                    <th class="py-4 px-6 w-40">NISN</th>
                                    <th class="py-4 px-6">Nama Lengkap Siswa</th>
                                    <th class="py-4 px-6 w-36 text-center">Nilai (0 - 100)</th>
                                    <th class="py-4 px-6">Catatan / Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-slate-700 text-sm">
                                <tr v-for="(row, index) in scoresForm.scores" :key="row.student_id" class="hover:bg-slate-50/50 transition-colors">
                                    <td class="py-4 px-6 text-center font-semibold text-slate-400">
                                        {{ index + 1 }}
                                    </td>
                                    <td class="py-4 px-6 font-bold text-slate-800">{{ row.nisn }}</td>
                                    <td class="py-4 px-6 font-semibold">{{ row.name }}</td>
                                    <td class="py-4 px-6 text-center">
                                        <input
                                            type="number"
                                            v-model.number="row.score"
                                            min="0"
                                            max="100"
                                            placeholder="0"
                                            required
                                            class="w-24 text-center px-3 py-1.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-xl text-sm font-bold transition-all"
                                        />
                                    </td>
                                    <td class="py-4 px-6">
                                        <input
                                            type="text"
                                            v-model="row.notes"
                                            placeholder="Tulis catatan jika ada..."
                                            class="w-full px-4 py-1.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-xl text-sm transition-all"
                                        />
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Submit section -->
                    <div class="bg-slate-50/50 border-t border-slate-100 px-6 py-4 flex items-center justify-end">
                        <button
                            type="submit"
                            :disabled="scoresForm.processing"
                            class="inline-flex items-center justify-center px-6 py-2.5 bg-[#7B1113] hover:bg-[#5A0D0E] active:scale-95 text-xs font-bold text-white rounded-2xl shadow-lg shadow-[#7B1113]/15 transition-all disabled:opacity-50"
                        >
                            <svg v-if="scoresForm.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Simpan Seluruh Nilai
                        </button>
                    </div>
                </form>
            </div>

            <!-- Helper Prompt when not loaded -->
            <div v-else-if="classroomId && subjectId && students.length === 0" class="bg-white rounded-3xl p-8 border border-slate-100 text-center shadow-sm">
                <p class="text-sm font-semibold text-slate-400">Tidak ada siswa aktif ditemukan di kelas ini.</p>
            </div>

            <div v-else class="bg-white rounded-3xl p-12 border border-slate-100 text-center shadow-sm space-y-3">
                <div class="w-16 h-16 rounded-full bg-[#7B1113]/10 text-[#7B1113] flex items-center justify-center mx-auto">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13"></path></svg>
                </div>
                <h5 class="text-slate-800 font-extrabold text-sm font-montserrat uppercase tracking-wider">Silakan Pilih Kelas & Mapel</h5>
                <p class="text-xs text-slate-400 max-w-xs mx-auto leading-relaxed">Pilih kelas dan mata pelajaran pada filter pencarian di atas untuk memulai pencatatan nilai siswa.</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, router } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import Swal from "sweetalert2";

const props = defineProps({
    classrooms: Array,
    subjects: Array,
    students: Array,
    existingAttendances: Object, // Keyed by student_id
    filters: Object,
});

const classroomId = ref(props.filters.classroom_id || "");
const subjectId = ref(props.filters.subject_id || "");
const attDate = ref(props.filters.date || new Date().toISOString().substr(0, 10));

const attendanceForm = useForm({
    classroom_id: "",
    subject_id: "",
    date: "",
    attendances: [], // Array of { student_id, status (H/S/I/A), notes }
});

const loadAttendances = () => {
    if (classroomId.value && subjectId.value) {
        router.get(
            route("attendances.index"),
            { 
                classroom_id: classroomId.value, 
                subject_id: subjectId.value,
                date: attDate.value
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
    attendanceForm.classroom_id = classroomId.value;
    attendanceForm.subject_id = subjectId.value;
    attendanceForm.date = attDate.value;
    
    attendanceForm.attendances = props.students.map(student => {
        const match = props.existingAttendances[student.id];

        return {
            student_id: student.id,
            name: student.name,
            nisn: student.nisn,
            status: match ? match.status : "H", // Default to Hadir
            notes: match ? (match.notes ?? "") : "",
        };
    });
};

if (props.students.length > 0) {
    initializeForm();
}

const submitAttendance = () => {
    if (attendanceForm.attendances.length === 0) return;

    attendanceForm.post(route("attendances.store"), {
        onSuccess: () => {
            Swal.fire("Berhasil", "Absensi hari ini berhasil disimpan", "success");
        }
    });
};

const triggerExport = () => {
    if (!classroomId.value || !subjectId.value) {
        Swal.fire("Peringatan", "Pilih Kelas dan Mata Pelajaran terlebih dahulu", "warning");
        return;
    }

    const params = {
        classroom_id: classroomId.value,
        subject_id: subjectId.value
    };

    window.open(route("attendances.export", params), "_blank");
};
</script>

<template>
    <Head title="Presensi / Absensi Siswa" />

    <AuthenticatedLayout>
        <template #header> Presensi / Absensi Siswa </template>

        <div class="space-y-6 max-w-7xl mx-auto">
            <!-- Filter Selector Header -->
            <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm space-y-4">
                <h4 class="text-xs font-black text-slate-400 uppercase tracking-widest">Filter Pencarian</h4>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Classroom Select -->
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Pilih Kelas</label>
                        <select v-model="classroomId" @change="loadAttendances" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold text-slate-700 transition-all">
                            <option value="">-- Pilih Ruang Kelas --</option>
                            <option v-for="c in classrooms" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                    </div>

                    <!-- Subject Select -->
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Mata Pelajaran</label>
                        <select v-model="subjectId" @change="loadAttendances" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold text-slate-700 transition-all">
                            <option value="">-- Pilih Mata Pelajaran --</option>
                            <option v-for="s in subjects" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>
                    </div>

                    <!-- Date Picker -->
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Tanggal Presensi</label>
                        <input
                            type="date"
                            v-model="attDate"
                            @change="loadAttendances"
                            :disabled="!classroomId || !subjectId"
                            class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold text-slate-700 transition-all disabled:opacity-50"
                        />
                    </div>
                </div>

                <!-- Export Options -->
                <div class="pt-3 border-t border-slate-100 flex flex-wrap gap-2 justify-end" v-if="classroomId && subjectId">
                    <button @click="triggerExport" class="inline-flex items-center px-4 py-2 bg-[#D4AF37] hover:bg-[#B5952F] active:scale-95 text-xs font-bold text-white rounded-xl shadow-md shadow-emerald-600/10 transition-all">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Ekspor Laporan Bulanan (Excel)
                    </button>
                </div>
            </div>

            <!-- Attendance Table Form -->
            <div v-if="classroomId && subjectId && students.length > 0" class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <form @submit.prevent="submitAttendance">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                                    <th class="py-4 px-6 w-20 text-center">No</th>
                                    <th class="py-4 px-6 w-40">NISN</th>
                                    <th class="py-4 px-6">Nama Lengkap Siswa</th>
                                    <th class="py-4 px-6 w-72 text-center">Status Kehadiran</th>
                                    <th class="py-4 px-6">Catatan / Keterangan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 text-slate-700 text-sm">
                                <tr v-for="(row, index) in attendanceForm.attendances" :key="row.student_id" class="hover:bg-slate-50/50 transition-colors">
                                    <td class="py-4 px-6 text-center font-semibold text-slate-400">
                                        {{ index + 1 }}
                                    </td>
                                    <td class="py-4 px-6 font-bold text-slate-800">{{ row.nisn }}</td>
                                    <td class="py-4 px-6 font-semibold">{{ row.name }}</td>
                                    <td class="py-4 px-6">
                                        <!-- Custom Button group for status -->
                                        <div class="flex items-center justify-center gap-1">
                                            <!-- Hadir -->
                                            <button
                                                type="button"
                                                @click="row.status = 'H'"
                                                :class="[
                                                    'px-3 py-1.5 text-xs font-black rounded-xl border transition-all duration-200',
                                                    row.status === 'H' 
                                                        ? 'bg-[#D4AF37]/10 border-emerald-500 text-[#967C26] font-extrabold shadow-sm' 
                                                        : 'bg-white border-slate-200 text-slate-400 hover:text-slate-600 hover:bg-slate-50'
                                                ]"
                                            >
                                                Hadir
                                            </button>
                                            
                                            <!-- Sakit -->
                                            <button
                                                type="button"
                                                @click="row.status = 'S'"
                                                :class="[
                                                    'px-3 py-1.5 text-xs font-black rounded-xl border transition-all duration-200',
                                                    row.status === 'S' 
                                                        ? 'bg-blue-50 border-blue-500 text-blue-700 font-extrabold shadow-sm' 
                                                        : 'bg-white border-slate-200 text-slate-400 hover:text-slate-600 hover:bg-slate-50'
                                                ]"
                                            >
                                                Sakit
                                            </button>

                                            <!-- Izin -->
                                            <button
                                                type="button"
                                                @click="row.status = 'I'"
                                                :class="[
                                                    'px-3 py-1.5 text-xs font-black rounded-xl border transition-all duration-200',
                                                    row.status === 'I' 
                                                        ? 'bg-amber-50 border-amber-500 text-amber-700 font-extrabold shadow-sm' 
                                                        : 'bg-white border-slate-200 text-slate-400 hover:text-slate-600 hover:bg-slate-50'
                                                ]"
                                            >
                                                Izin
                                            </button>

                                            <!-- Alpa -->
                                            <button
                                                type="button"
                                                @click="row.status = 'A'"
                                                :class="[
                                                    'px-3 py-1.5 text-xs font-black rounded-xl border transition-all duration-200',
                                                    row.status === 'A' 
                                                        ? 'bg-rose-50 border-rose-500 text-rose-700 font-extrabold shadow-sm' 
                                                        : 'bg-white border-slate-200 text-slate-400 hover:text-slate-600 hover:bg-slate-50'
                                                ]"
                                            >
                                                Alpa
                                            </button>
                                        </div>
                                    </td>
                                    <td class="py-4 px-6">
                                        <input
                                            type="text"
                                            v-model="row.notes"
                                            placeholder="Catatan absensi..."
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
                            :disabled="attendanceForm.processing"
                            class="inline-flex items-center justify-center px-6 py-2.5 bg-[#7B1113] hover:bg-[#5A0D0E] active:scale-95 text-xs font-bold text-white rounded-2xl shadow-lg shadow-[#7B1113]/15 transition-all disabled:opacity-50"
                        >
                            <svg v-if="attendanceForm.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Simpan Seluruh Absensi
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
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                </div>
                <h5 class="text-slate-800 font-extrabold text-sm font-montserrat uppercase tracking-wider">Silakan Pilih Kelas & Mapel</h5>
                <p class="text-xs text-slate-400 max-w-xs mx-auto leading-relaxed">Pilih kelas dan mata pelajaran pada filter pencarian di atas untuk memulai pencatatan absensi siswa.</p>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

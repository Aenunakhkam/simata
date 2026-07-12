<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, router, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import Swal from "sweetalert2";

const props = defineProps({
    schedules: Object,
    subjects: Array,
    classrooms: Array,
    teachers: Array,
    academicYears: Array,
    activeYear: Object,
    filters: Object,
});

const search = ref(props.filters.search || "");
const perPage = ref(props.filters.per_page || "10");
const filterAcademicYear = ref(props.filters.academic_year_id || (props.activeYear ? props.activeYear.id : ""));

const showAddModal = ref(false);
const showEditModal = ref(false);
const activeSchedule = ref(null);

const addForm = useForm({
    subject_id: "",
    classroom_id: "",
    teacher_id: "",
    academic_year_id: props.activeYear ? props.activeYear.id : "",
});

const editForm = useForm({
    subject_id: "",
    classroom_id: "",
    teacher_id: "",
    academic_year_id: "",
});

const handleSearch = () => {
    router.get(
        route("schedules.index"),
        { search: search.value, per_page: perPage.value, academic_year_id: filterAcademicYear.value },
        { preserveState: true, replace: true }
    );
};

const openAddModal = () => {
    addForm.reset();
    if (props.activeYear) {
        addForm.academic_year_id = props.activeYear.id;
    }
    showAddModal.value = true;
};

const submitAdd = () => {
    addForm.post(route("schedules.store"), {
        onSuccess: () => {
            showAddModal.value = false;
            addForm.reset();
        },
    });
};

const openEditModal = (schedule) => {
    activeSchedule.value = schedule;
    editForm.subject_id = schedule.subject_id;
    editForm.classroom_id = schedule.classroom_id;
    editForm.teacher_id = schedule.teacher_id;
    editForm.academic_year_id = schedule.academic_year_id;
    showEditModal.value = true;
};

const submitEdit = () => {
    editForm.put(route("schedules.update", activeSchedule.value.id), {
        onSuccess: () => {
            showEditModal.value = false;
            activeSchedule.value = null;
        },
    });
};

const deleteSchedule = (schedule) => {
    Swal.fire({
        title: "Apakah Anda yakin?",
        text: `Jadwal "${schedule.title}" akan dihapus permanen!`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#6366f1",
        cancelButtonColor: "#f43f5e",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("schedules.destroy", schedule.id));
        }
    });
};
</script>

<template>
    <Head title="Jadwal Pelajaran" />

    <AuthenticatedLayout>
        <template #header> Penempatan Jadwal Mengajar </template>

        <div class="space-y-6 max-w-7xl mx-auto">
            <!-- Table Action Header -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <!-- Filters -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full lg:max-w-2xl">
                    <!-- Search bar -->
                    <div class="relative flex-1">
                        <input
                            type="text"
                            v-model="search"
                            @keyup.enter="handleSearch"
                            placeholder="Cari kelas, mapel, atau guru..."
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-medium placeholder-slate-400 transition-all duration-200"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>
                    <!-- Academic Year Filter -->
                    <div class="w-full sm:w-56 shrink-0">
                        <select v-model="filterAcademicYear" @change="handleSearch" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold text-slate-700 transition-all">
                            <option value="">Semua Tahun Ajaran</option>
                            <option v-for="ay in academicYears" :key="ay.id" :value="ay.id">{{ ay.name }} - {{ ay.semester }}</option>
                        </select>
                    </div>
                </div>

                <!-- Add action -->
                <div>
                    <button @click="openAddModal" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-[#7B1113] hover:bg-[#5A0D0E] active:scale-95 text-xs font-bold text-white rounded-2xl shadow-lg shadow-[#7B1113]/15 transition-all duration-200">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Penempatan
                    </button>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                                <th class="py-4 px-6 w-16 text-center">No</th>
                                <th class="py-4 px-6">Tahun Ajaran</th>
                                <th class="py-4 px-6">Kelas</th>
                                <th class="py-4 px-6">Mata Pelajaran</th>
                                <th class="py-4 px-6">Guru Pengampu</th>
                                <th class="py-4 px-6 w-32 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-slate-700 text-sm">
                            <tr v-for="(schedule, index) in schedules.data" :key="schedule.id" class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-4 px-6 text-center font-semibold text-slate-400">
                                    {{ (schedules.current_page - 1) * schedules.per_page + index + 1 }}
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-800">
                                    {{ schedule.academic_year?.name ?? '-' }} <span class="text-xs text-slate-400 font-medium">({{ schedule.academic_year?.semester ?? '-' }})</span>
                                </td>
                                <td class="py-4 px-6 font-semibold text-[#7B1113]">{{ schedule.classroom?.name ?? '-' }}</td>
                                <td class="py-4 px-6 font-bold text-slate-700">{{ schedule.subject?.name ?? '-' }}</td>
                                <td class="py-4 px-6 font-semibold">{{ schedule.teacher?.name ?? '-' }}</td>
                                <td class="py-4 px-6 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button @click="openEditModal(schedule)" class="p-1.5 text-[#7B1113] hover:bg-[#7B1113]/10 rounded-lg transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button @click="deleteSchedule(schedule)" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="schedules.data.length === 0">
                                <td colspan="6" class="py-12 px-6 text-center text-slate-400 font-medium">
                                    Tidak ada data jadwal pelajaran ditemukan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination footer -->
                <div class="bg-slate-50/50 border-t border-slate-100 px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-400">Tampilkan</span>
                        <select v-model="perPage" @change="handleSearch" class="bg-white border border-slate-200 rounded-xl px-2 py-1 text-xs font-bold focus:outline-none focus:border-[#7B1113] text-slate-700">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="all">Semua</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-1.5" v-if="schedules.links && schedules.links.length > 3">
                        <Link v-for="(link, i) in schedules.links" :key="i" :href="link.url || '#'" :class="[
                            'px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-200',
                            link.active ? 'bg-[#7B1113] text-white shadow-sm' : 'text-slate-500 hover:bg-slate-150',
                            !link.url ? 'opacity-40 cursor-not-allowed' : ''
                        ]" v-html="link.label"></Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- ================= ADD MODAL ================= -->
        <div v-if="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white rounded-3xl shadow-xl max-w-lg w-full border border-slate-100 overflow-hidden transform transition-all">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-extrabold text-slate-800 font-montserrat text-lg">Tambah Penempatan</h3>
                    <button @click="showAddModal = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-50"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <form @submit.prevent="submitAdd" class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Tahun Ajaran</label>
                            <select v-model="addForm.academic_year_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold transition-all">
                                <option value="" disabled>Pilih Tahun Ajaran</option>
                                <option v-for="ay in academicYears" :key="ay.id" :value="ay.id">{{ ay.name }} ({{ ay.semester }})</option>
                            </select>
                            <span v-if="addForm.errors.academic_year_id" class="text-rose-500 text-xs font-semibold mt-1 block">{{ addForm.errors.academic_year_id }}</span>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Kelas</label>
                            <select v-model="addForm.classroom_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold transition-all">
                                <option value="" disabled>Pilih Kelas</option>
                                <option v-for="c in classrooms" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                            <span v-if="addForm.errors.classroom_id" class="text-rose-500 text-xs font-semibold mt-1 block">{{ addForm.errors.classroom_id }}</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Mata Pelajaran</label>
                        <select v-model="addForm.subject_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold transition-all">
                            <option value="" disabled>Pilih Mata Pelajaran</option>
                            <option v-for="s in subjects" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>
                        <span v-if="addForm.errors.subject_id" class="text-rose-500 text-xs font-semibold mt-1 block">{{ addForm.errors.subject_id }}</span>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Guru Pengampu</label>
                        <select v-model="addForm.teacher_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold transition-all">
                            <option value="" disabled>Pilih Guru</option>
                            <option v-for="t in teachers" :key="t.id" :value="t.id">{{ t.name }}</option>
                        </select>
                        <span v-if="addForm.errors.teacher_id" class="text-rose-500 text-xs font-semibold mt-1 block">{{ addForm.errors.teacher_id }}</span>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2.5 text-xs font-bold text-slate-500 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 rounded-2xl transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2.5 text-xs font-bold text-white bg-[#7B1113] hover:bg-[#5A0D0E] rounded-2xl transition-colors shadow-lg shadow-[#7B1113]/10">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================= EDIT MODAL ================= -->
        <div v-if="showEditModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white rounded-3xl shadow-xl max-w-lg w-full border border-slate-100 overflow-hidden transform transition-all">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-extrabold text-slate-800 font-montserrat text-lg">Ubah Penempatan</h3>
                    <button @click="showEditModal = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-50"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <form @submit.prevent="submitEdit" class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Tahun Ajaran</label>
                            <select v-model="editForm.academic_year_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold transition-all">
                                <option value="" disabled>Pilih Tahun Ajaran</option>
                                <option v-for="ay in academicYears" :key="ay.id" :value="ay.id">{{ ay.name }} ({{ ay.semester }})</option>
                            </select>
                            <span v-if="editForm.errors.academic_year_id" class="text-rose-500 text-xs font-semibold mt-1 block">{{ editForm.errors.academic_year_id }}</span>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Kelas</label>
                            <select v-model="editForm.classroom_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold transition-all">
                                <option value="" disabled>Pilih Kelas</option>
                                <option v-for="c in classrooms" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                            <span v-if="editForm.errors.classroom_id" class="text-rose-500 text-xs font-semibold mt-1 block">{{ editForm.errors.classroom_id }}</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Mata Pelajaran</label>
                        <select v-model="editForm.subject_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold transition-all">
                            <option value="" disabled>Pilih Mata Pelajaran</option>
                            <option v-for="s in subjects" :key="s.id" :value="s.id">{{ s.name }}</option>
                        </select>
                        <span v-if="editForm.errors.subject_id" class="text-rose-500 text-xs font-semibold mt-1 block">{{ editForm.errors.subject_id }}</span>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Guru Pengampu</label>
                        <select v-model="editForm.teacher_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold transition-all">
                            <option value="" disabled>Pilih Guru</option>
                            <option v-for="t in teachers" :key="t.id" :value="t.id">{{ t.name }}</option>
                        </select>
                        <span v-if="editForm.errors.teacher_id" class="text-rose-500 text-xs font-semibold mt-1 block">{{ editForm.errors.teacher_id }}</span>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2.5 text-xs font-bold text-slate-500 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 rounded-2xl transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2.5 text-xs font-bold text-white bg-[#7B1113] hover:bg-[#5A0D0E] rounded-2xl transition-colors shadow-lg shadow-[#7B1113]/10">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

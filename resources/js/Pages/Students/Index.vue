<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, router, Link } from "@inertiajs/vue3";
import { ref, watch } from "vue";
import Swal from "sweetalert2";

const props = defineProps({
    students: Object,
    classrooms: Array,
    filters: Object,
});

const search = ref(props.filters.search || "");
const perPage = ref(props.filters.per_page || "10");
const classroomId = ref(props.filters.classroom_id || "");

const showAddModal = ref(false);
const showEditModal = ref(false);
const showImportModal = ref(false);
const activeStudent = ref(null);

const addForm = useForm({
    nisn: "",
    nis: "",
    name: "",
    gender: "L",
    classroom_id: "",
    status: "active",
});

const editForm = useForm({
    nisn: "",
    nis: "",
    name: "",
    gender: "",
    classroom_id: "",
    status: "",
});

const importForm = useForm({
    file: null,
});

const handleSearch = () => {
    router.get(
        route("students.index"),
        { 
            search: search.value, 
            per_page: perPage.value,
            classroom_id: classroomId.value
        },
        { preserveState: true, replace: true }
    );
};

const handlePerPageChange = () => {
    handleSearch();
};

const handleClassFilterChange = () => {
    handleSearch();
};

const openAddModal = () => {
    addForm.reset();
    addForm.gender = "L";
    addForm.status = "active";
    if (props.classrooms.length > 0) {
        addForm.classroom_id = classroomId.value || props.classrooms[0].id;
    }
    showAddModal.value = true;
};

const submitAdd = () => {
    addForm.post(route("students.store"), {
        onSuccess: () => {
            showAddModal.value = false;
            addForm.reset();
        },
    });
};

const openEditModal = (student) => {
    activeStudent.value = student;
    editForm.nisn = student.nisn;
    editForm.nis = student.nis ?? "";
    editForm.name = student.name;
    editForm.gender = student.gender ?? "L";
    editForm.classroom_id = student.classroom_id ?? "";
    editForm.status = student.status;
    showEditModal.value = true;
};

const submitEdit = () => {
    editForm.put(route("students.update", activeStudent.value.id), {
        onSuccess: () => {
            showEditModal.value = false;
            activeStudent.value = null;
        },
    });
};

const deleteStudent = (student) => {
    Swal.fire({
        title: "Apakah Anda yakin?",
        text: `Data siswa "${student.name}" akan dihapus permanen!`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#6366f1",
        cancelButtonColor: "#f43f5e",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("students.destroy", student.id));
        }
    });
};

const handleFileSelect = (event) => {
    importForm.file = event.target.files[0];
};

const submitImport = () => {
    if (!importForm.file) {
        Swal.fire("Peringatan", "Harap pilih file terlebih dahulu", "warning");
        return;
    }

    importForm.post(route("students.import"), {
        onSuccess: () => {
            showImportModal.value = false;
            importForm.reset();
        },
    });
};

const getExportUrl = () => {
    return route('students.export', { classroom_id: classroomId.value });
};
</script>

<template>
    <Head title="Data Siswa" />

    <AuthenticatedLayout>
        <template #header> Data Siswa </template>

        <div class="space-y-6 max-w-7xl mx-auto">
            <!-- Table Action Header -->
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4 bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <!-- Search & Filters -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 w-full lg:max-w-2xl">
                    <!-- Search bar -->
                    <div class="relative flex-1">
                        <input
                            type="text"
                            v-model="search"
                            @keyup.enter="handleSearch"
                            placeholder="Cari NISN, NIS, atau nama..."
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-medium placeholder-slate-400 transition-all duration-200"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>
                    <!-- Class filter -->
                    <div class="w-full sm:w-56 shrink-0">
                        <select v-model="classroomId" @change="handleClassFilterChange" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold text-slate-700 transition-all">
                            <option value="">Semua Kelas</option>
                            <option v-for="c in classrooms" :key="c.id" :value="c.id">{{ c.name }}</option>
                        </select>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex flex-wrap items-center gap-2">
                    <button @click="openAddModal" class="inline-flex items-center justify-center px-4 py-2.5 bg-[#7B1113] hover:bg-[#5A0D0E] active:scale-95 text-xs font-bold text-white rounded-2xl shadow-lg shadow-[#7B1113]/15 transition-all duration-200">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Siswa
                    </button>
                    <button @click="showImportModal = true" class="inline-flex items-center justify-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200/80 active:scale-95 text-xs font-bold text-slate-700 rounded-2xl transition-all duration-200 border border-slate-200/40">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Import Excel
                    </button>
                    <a :href="getExportUrl()" class="inline-flex items-center justify-center px-4 py-2.5 bg-[#D4AF37] hover:bg-[#B5952F] active:scale-95 text-xs font-bold text-white rounded-2xl shadow-lg shadow-emerald-600/15 transition-all duration-200">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export Excel
                    </a>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                                <th class="py-4 px-6 w-20 text-center">No</th>
                                <th class="py-4 px-6 w-36">NISN</th>
                                <th class="py-4 px-6 w-32">NIS</th>
                                <th class="py-4 px-6">Nama Lengkap</th>
                                <th class="py-4 px-6 w-24 text-center">L/P</th>
                                <th class="py-4 px-6">Kelas</th>
                                <th class="py-4 px-6 w-32 text-center">Status</th>
                                <th class="py-4 px-6 w-36 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-slate-700 text-sm">
                            <tr v-for="(student, index) in students.data" :key="student.id" class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-4 px-6 text-center font-semibold text-slate-400">
                                    {{ (students.current_page - 1) * students.per_page + index + 1 }}
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-800">{{ student.nisn }}</td>
                                <td class="py-4 px-6 font-semibold text-slate-500">{{ student.nis ?? '-' }}</td>
                                <td class="py-4 px-6 font-bold text-slate-800">{{ student.name }}</td>
                                <td class="py-4 px-6 text-center">
                                    <span :class="[
                                        'px-2.5 py-0.5 rounded-full text-[11px] font-black',
                                        student.gender === 'L' ? 'bg-blue-50 text-blue-600' : 'bg-pink-50 text-pink-600'
                                    ]">{{ student.gender ?? 'L' }}</span>
                                </td>
                                <td class="py-4 px-6 font-semibold text-[#7B1113]">
                                    {{ student.classroom?.name ?? '-' }}
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span :class="[
                                        'px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider',
                                        student.status === 'active' ? 'bg-[#D4AF37]/10 text-[#967C26]' : (student.status === 'graduated' ? 'bg-[#7B1113]/10 text-[#5A0D0E]' : 'bg-slate-100 text-slate-600')
                                    ]">
                                        {{ student.status === 'active' ? 'Aktif' : (student.status === 'graduated' ? 'Lulus' : 'Keluar') }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button @click="openEditModal(student)" class="p-1.5 text-[#7B1113] hover:bg-[#7B1113]/10 rounded-lg transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button @click="deleteStudent(student)" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="students.data.length === 0">
                                <td colspan="8" class="py-12 px-6 text-center text-slate-400 font-medium">
                                    Tidak ada data siswa ditemukan.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Pagination footer -->
                <div class="bg-slate-50/50 border-t border-slate-100 px-6 py-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-slate-400">Tampilkan</span>
                        <select v-model="perPage" @change="handlePerPageChange" class="bg-white border border-slate-200 rounded-xl px-2 py-1 text-xs font-bold focus:outline-none focus:border-[#7B1113] text-slate-700">
                            <option value="10">10</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                            <option value="all">Semua</option>
                        </select>
                    </div>
                    <div class="flex items-center gap-1.5" v-if="students.links && students.links.length > 3">
                        <Link v-for="(link, i) in students.links" :key="i" :href="link.url || '#'" :class="[
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
                    <h3 class="font-extrabold text-slate-800 font-montserrat text-lg">Tambah Siswa Baru</h3>
                    <button @click="showAddModal = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-50"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <form @submit.prevent="submitAdd" class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">NISN</label>
                            <input type="text" v-model="addForm.nisn" placeholder="Nomor NISN" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-medium transition-all" />
                            <span v-if="addForm.errors.nisn" class="text-rose-500 text-xs font-semibold mt-1 block">{{ addForm.errors.nisn }}</span>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">NIS</label>
                            <input type="text" v-model="addForm.nis" placeholder="Nomor NIS sekolah" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-medium transition-all" />
                            <span v-if="addForm.errors.nis" class="text-rose-500 text-xs font-semibold mt-1 block">{{ addForm.errors.nis }}</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                        <input type="text" v-model="addForm.name" placeholder="Masukkan nama lengkap" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-medium transition-all" />
                        <span v-if="addForm.errors.name" class="text-rose-500 text-xs font-semibold mt-1 block">{{ addForm.errors.name }}</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Jenis Kelamin</label>
                            <select v-model="addForm.gender" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold transition-all">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            <span v-if="addForm.errors.gender" class="text-rose-500 text-xs font-semibold mt-1 block">{{ addForm.errors.gender }}</span>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Ruang Kelas</label>
                            <select v-model="addForm.classroom_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold transition-all">
                                <option value="">Tanpa Kelas</option>
                                <option v-for="c in classrooms" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                            <span v-if="addForm.errors.classroom_id" class="text-rose-500 text-xs font-semibold mt-1 block">{{ addForm.errors.classroom_id }}</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Status Siswa</label>
                        <select v-model="addForm.status" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold transition-all">
                            <option value="active">Aktif</option>
                            <option value="graduated">Lulus</option>
                            <option value="dropped_out">Keluar</option>
                        </select>
                        <span v-if="addForm.errors.status" class="text-rose-500 text-xs font-semibold mt-1 block">{{ addForm.errors.status }}</span>
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
                    <h3 class="font-extrabold text-slate-800 font-montserrat text-lg">Ubah Data Siswa</h3>
                    <button @click="showEditModal = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-50"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <form @submit.prevent="submitEdit" class="p-6 space-y-4">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">NISN</label>
                            <input type="text" v-model="editForm.nisn" placeholder="Nomor NISN" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-medium transition-all" />
                            <span v-if="editForm.errors.nisn" class="text-rose-500 text-xs font-semibold mt-1 block">{{ editForm.errors.nisn }}</span>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">NIS</label>
                            <input type="text" v-model="editForm.nis" placeholder="Nomor NIS sekolah" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-medium transition-all" />
                            <span v-if="editForm.errors.nis" class="text-rose-500 text-xs font-semibold mt-1 block">{{ editForm.errors.nis }}</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                        <input type="text" v-model="editForm.name" placeholder="Masukkan nama lengkap" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-medium transition-all" />
                        <span v-if="editForm.errors.name" class="text-rose-500 text-xs font-semibold mt-1 block">{{ editForm.errors.name }}</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Jenis Kelamin</label>
                            <select v-model="editForm.gender" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold transition-all">
                                <option value="L">Laki-laki</option>
                                <option value="P">Perempuan</option>
                            </select>
                            <span v-if="editForm.errors.gender" class="text-rose-500 text-xs font-semibold mt-1 block">{{ editForm.errors.gender }}</span>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Ruang Kelas</label>
                            <select v-model="editForm.classroom_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold transition-all">
                                <option value="">Tanpa Kelas</option>
                                <option v-for="c in classrooms" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                            <span v-if="editForm.errors.classroom_id" class="text-rose-500 text-xs font-semibold mt-1 block">{{ editForm.errors.classroom_id }}</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Status Siswa</label>
                        <select v-model="editForm.status" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold transition-all">
                            <option value="active">Aktif</option>
                            <option value="graduated">Lulus</option>
                            <option value="dropped_out">Keluar</option>
                        </select>
                        <span v-if="editForm.errors.status" class="text-rose-500 text-xs font-semibold mt-1 block">{{ editForm.errors.status }}</span>
                    </div>

                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2.5 text-xs font-bold text-slate-500 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 rounded-2xl transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2.5 text-xs font-bold text-white bg-[#7B1113] hover:bg-[#5A0D0E] rounded-2xl transition-colors shadow-lg shadow-[#7B1113]/10">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- ================= IMPORT EXCEL MODAL ================= -->
        <div v-if="showImportModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
            <div class="bg-white rounded-3xl shadow-xl max-w-md w-full border border-slate-100 overflow-hidden transform transition-all">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-extrabold text-slate-800 font-montserrat text-lg">Impor via Excel</h3>
                    <button @click="showImportModal = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-50"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <form @submit.prevent="submitImport" class="p-6 space-y-4">
                    <div class="p-4 bg-[#7B1113]/10 border border-indigo-100 rounded-2xl">
                        <p class="text-xs text-[#5A0D0E] font-medium leading-relaxed">
                            Unduh template Excel resmi di bawah ini agar kolom data sesuai dengan sistem sebelum mengimpor.
                        </p>
                        <a :href="route('students.template')" target="_blank" class="inline-flex items-center text-xs font-bold text-[#7B1113] hover:text-indigo-800 transition-colors mt-2.5">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Unduh Template Excel
                        </a>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">File Excel (.xlsx/.xls)</label>
                        <input
                            type="file"
                            accept=".xlsx, .xls"
                            @change="handleFileSelect"
                            class="w-full text-sm file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-[#7B1113]/10 file:text-[#5A0D0E] hover:file:bg-indigo-100 border border-slate-200 rounded-2xl p-2.5 bg-slate-50 cursor-pointer"
                        />
                    </div>
                    <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2">
                        <button type="button" @click="showImportModal = false" class="px-4 py-2.5 text-xs font-bold text-slate-500 hover:text-slate-700 bg-slate-50 hover:bg-slate-100 rounded-2xl transition-colors">Batal</button>
                        <button type="submit" class="px-4 py-2.5 text-xs font-bold text-white bg-[#7B1113] hover:bg-[#5A0D0E] rounded-2xl transition-colors shadow-lg shadow-[#7B1113]/10">Mulai Impor</button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

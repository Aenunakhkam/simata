<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, router, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import Swal from "sweetalert2";

const props = defineProps({
    classrooms: Object,
    majors: Array,
    filters: Object,
});

const search = ref(props.filters.search || "");
const perPage = ref(props.filters.per_page || "10");

const showAddModal = ref(false);
const showEditModal = ref(false);
const showImportModal = ref(false);
const activeClassroom = ref(null);

const addForm = useForm({
    major_id: "",
    level: "",
    name: "",
});

const editForm = useForm({
    major_id: "",
    level: "",
    name: "",
});

const importForm = useForm({
    file: null,
});

const handleSearch = () => {
    router.get(
        route("classrooms.index"),
        { search: search.value, per_page: perPage.value },
        { preserveState: true, replace: true }
    );
};

const handlePerPageChange = () => {
    handleSearch();
};

const openAddModal = () => {
    addForm.reset();
    if (props.majors.length > 0) {
        addForm.major_id = props.majors[0].id;
    }
    addForm.level = 10;
    showAddModal.value = true;
};

const submitAdd = () => {
    addForm.post(route("classrooms.store"), {
        onSuccess: () => {
            showAddModal.value = false;
            addForm.reset();
        },
    });
};

const openEditModal = (classroom) => {
    activeClassroom.value = classroom;
    editForm.major_id = classroom.major_id;
    editForm.level = classroom.level;
    editForm.name = classroom.name;
    showEditModal.value = true;
};

const submitEdit = () => {
    editForm.put(route("classrooms.update", activeClassroom.value.id), {
        onSuccess: () => {
            showEditModal.value = false;
            activeClassroom.value = null;
        },
    });
};

const deleteClassroom = (classroom) => {
    Swal.fire({
        title: "Apakah Anda yakin?",
        text: `Data kelas "${classroom.name}" akan dihapus permanen!`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#6366f1",
        cancelButtonColor: "#f43f5e",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("classrooms.destroy", classroom.id));
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

    importForm.post(route("classrooms.import"), {
        onSuccess: () => {
            showImportModal.value = false;
            importForm.reset();
        },
    });
};
</script>

<template>
    <Head title="Data Kelas" />

    <AuthenticatedLayout>
        <template #header> Data Kelas </template>

        <div class="space-y-6 max-w-7xl mx-auto">
            <!-- Table Action Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <!-- Search bar -->
                <div class="relative max-w-md w-full">
                    <input
                        type="text"
                        v-model="search"
                        @keyup.enter="handleSearch"
                        placeholder="Cari kelas..."
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-medium placeholder-slate-400 transition-all duration-200"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2">
                    <button @click="openAddModal" class="inline-flex items-center justify-center px-4 py-2.5 bg-[#7B1113] hover:bg-[#5A0D0E] active:scale-95 text-xs font-bold text-white rounded-2xl shadow-lg shadow-[#7B1113]/15 transition-all duration-200">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Kelas
                    </button>
                    <button @click="showImportModal = true" class="inline-flex items-center justify-center px-4 py-2.5 bg-slate-100 hover:bg-slate-200/80 active:scale-95 text-xs font-bold text-slate-700 rounded-2xl transition-all duration-200 border border-slate-200/40">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        Import Excel
                    </button>
                </div>
            </div>

            <!-- Table Card -->
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-slate-400 text-[10px] font-bold uppercase tracking-wider">
                                <th class="py-4 px-6 w-20 text-center">No</th>
                                <th class="py-4 px-6 w-32 text-center">Tingkat</th>
                                <th class="py-4 px-6">Nama Kelas</th>
                                <th class="py-4 px-6">Jurusan</th>
                                <th class="py-4 px-6 w-32 text-center">Jumlah Siswa</th>
                                <th class="py-4 px-6 w-36 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-slate-700 text-sm">
                            <tr v-for="(classroom, index) in classrooms.data" :key="classroom.id" class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-4 px-6 text-center font-semibold text-slate-400">
                                    {{ (classrooms.current_page - 1) * classrooms.per_page + index + 1 }}
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <span class="px-2.5 py-1 bg-slate-100 rounded-lg text-slate-700 text-xs font-black">{{ classroom.level }}</span>
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-800">{{ classroom.name }}</td>
                                <td class="py-4 px-6 font-semibold">
                                    <span class="text-[#7B1113] font-bold">{{ classroom.major?.code }}</span> - {{ classroom.major?.name }}
                                </td>
                                <td class="py-4 px-6 text-center font-bold text-slate-600">
                                    {{ classroom.students ? classroom.students.length : 0 }}
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button @click="openEditModal(classroom)" class="p-1.5 text-[#7B1113] hover:bg-[#7B1113]/10 rounded-lg transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button @click="deleteClassroom(classroom)" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="classrooms.data.length === 0">
                                <td colspan="6" class="py-12 px-6 text-center text-slate-400 font-medium">
                                    Tidak ada data kelas ditemukan.
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
                    <div class="flex items-center gap-1.5" v-if="classrooms.links && classrooms.links.length > 3">
                        <Link v-for="(link, i) in classrooms.links" :key="i" :href="link.url || '#'" :class="[
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
            <div class="bg-white rounded-3xl shadow-xl max-w-md w-full border border-slate-100 overflow-hidden transform transition-all">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-extrabold text-slate-800 font-montserrat text-lg">Tambah Kelas Baru</h3>
                    <button @click="showAddModal = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-50"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <form @submit.prevent="submitAdd" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Tingkat / Level</label>
                        <select v-model="addForm.level" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold transition-all">
                            <option value="10">Tingkat 10</option>
                            <option value="11">Tingkat 11</option>
                            <option value="12">Tingkat 12</option>
                        </select>
                        <span v-if="addForm.errors.level" class="text-rose-500 text-xs font-semibold mt-1 block">{{ addForm.errors.level }}</span>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Nama Kelas</label>
                        <input
                            type="text"
                            v-model="addForm.name"
                            placeholder="Contoh: 10 RPL 1"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-medium transition-all placeholder-slate-400"
                        />
                        <span v-if="addForm.errors.name" class="text-rose-500 text-xs font-semibold mt-1 block">{{ addForm.errors.name }}</span>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Jurusan</label>
                        <select v-model="addForm.major_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold transition-all">
                            <option v-for="m in majors" :key="m.id" :value="m.id">{{ m.code }} - {{ m.name }}</option>
                        </select>
                        <span v-if="addForm.errors.major_id" class="text-rose-500 text-xs font-semibold mt-1 block">{{ addForm.errors.major_id }}</span>
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
            <div class="bg-white rounded-3xl shadow-xl max-w-md w-full border border-slate-100 overflow-hidden transform transition-all">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-extrabold text-slate-800 font-montserrat text-lg">Ubah Data Kelas</h3>
                    <button @click="showEditModal = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-50"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <form @submit.prevent="submitEdit" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Tingkat / Level</label>
                        <select v-model="editForm.level" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold transition-all">
                            <option value="10">Tingkat 10</option>
                            <option value="11">Tingkat 11</option>
                            <option value="12">Tingkat 12</option>
                        </select>
                        <span v-if="editForm.errors.level" class="text-rose-500 text-xs font-semibold mt-1 block">{{ editForm.errors.level }}</span>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Nama Kelas</label>
                        <input
                            type="text"
                            v-model="editForm.name"
                            placeholder="Contoh: 10 RPL 1"
                            class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-medium transition-all placeholder-slate-400"
                        />
                        <span v-if="editForm.errors.name" class="text-rose-500 text-xs font-semibold mt-1 block">{{ editForm.errors.name }}</span>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Jurusan</label>
                        <select v-model="editForm.major_id" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold transition-all">
                            <option v-for="m in majors" :key="m.id" :value="m.id">{{ m.code }} - {{ m.name }}</option>
                        </select>
                        <span v-if="editForm.errors.major_id" class="text-rose-500 text-xs font-semibold mt-1 block">{{ editForm.errors.major_id }}</span>
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
                        <a :href="route('classrooms.template')" target="_blank" class="inline-flex items-center text-xs font-bold text-[#7B1113] hover:text-indigo-800 transition-colors mt-2.5">
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

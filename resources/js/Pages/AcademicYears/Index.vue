<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, router, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import Swal from "sweetalert2";

const props = defineProps({
    academicYears: Object,
    filters: Object,
});

const search = ref(props.filters.search || "");
const perPage = ref(props.filters.per_page || "10");

const showAddModal = ref(false);
const showEditModal = ref(false);
const activeAcademicYear = ref(null);

const addForm = useForm({
    name: "",
    semester: "Ganjil",
    is_active: false,
});

const editForm = useForm({
    name: "",
    semester: "",
    is_active: false,
});

const handleSearch = () => {
    router.get(
        route("academic-years.index"),
        { search: search.value, per_page: perPage.value },
        { preserveState: true, replace: true }
    );
};

const handlePerPageChange = () => {
    handleSearch();
};

const openAddModal = () => {
    addForm.reset();
    showAddModal.value = true;
};

const submitAdd = () => {
    addForm.post(route("academic-years.store"), {
        onSuccess: () => {
            showAddModal.value = false;
            addForm.reset();
        },
    });
};

const openEditModal = (year) => {
    activeAcademicYear.value = year;
    editForm.name = year.name;
    editForm.semester = year.semester;
    editForm.is_active = year.is_active;
    showEditModal.value = true;
};

const submitEdit = () => {
    editForm.put(route("academic-years.update", activeAcademicYear.value.id), {
        onSuccess: () => {
            showEditModal.value = false;
            activeAcademicYear.value = null;
        },
    });
};

const setActive = (year) => {
    Swal.fire({
        title: "Aktifkan Tahun Ajaran?",
        text: `Tahun ajaran ${year.name} - ${year.semester} akan diaktifkan secara global untuk sistem.`,
        icon: "info",
        showCancelButton: true,
        confirmButtonColor: "#6366f1",
        cancelButtonColor: "#94a3b8",
        confirmButtonText: "Ya, Aktifkan",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route("academic-years.set-active", year.id));
        }
    });
};

const deleteYear = (year) => {
    Swal.fire({
        title: "Apakah Anda yakin?",
        text: `Data tahun ajaran "${year.name} - ${year.semester}" akan dihapus permanen!`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#6366f1",
        cancelButtonColor: "#f43f5e",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("academic-years.destroy", year.id));
        }
    });
};
</script>

<template>
    <Head title="Tahun Ajaran" />

    <AuthenticatedLayout>
        <template #header> Tahun Ajaran & Semester </template>

        <div class="space-y-6 max-w-7xl mx-auto">
            <!-- Table Action Header -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-white p-5 rounded-3xl border border-slate-100 shadow-sm">
                <!-- Search bar -->
                <div class="relative max-w-md w-full">
                    <input
                        type="text"
                        v-model="search"
                        @keyup.enter="handleSearch"
                        placeholder="Cari tahun ajaran (misal: 2026)..."
                        class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-medium placeholder-slate-400 transition-all duration-200"
                    />
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </div>
                </div>

                <!-- Add action -->
                <div>
                    <button @click="openAddModal" class="inline-flex items-center justify-center px-4 py-2.5 bg-[#7B1113] hover:bg-[#5A0D0E] active:scale-95 text-xs font-bold text-white rounded-2xl shadow-lg shadow-[#7B1113]/15 transition-all duration-200">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Tahun Ajaran
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
                                <th class="py-4 px-6">Tahun Ajaran</th>
                                <th class="py-4 px-6">Semester</th>
                                <th class="py-4 px-6 w-32 text-center">Status Aktif</th>
                                <th class="py-4 px-6 w-48 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-slate-700 text-sm">
                            <tr v-for="(year, index) in academicYears.data" :key="year.id" class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-4 px-6 text-center font-semibold text-slate-400">
                                    {{ (academicYears.current_page - 1) * academicYears.per_page + index + 1 }}
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-800">{{ year.name }}</td>
                                <td class="py-4 px-6 font-semibold text-slate-600">{{ year.semester }}</td>
                                <td class="py-4 px-6 text-center">
                                    <span v-if="year.is_active" class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-[#D4AF37]/10 text-[#B5952F] border border-[#D4AF37]/20">Aktif</span>
                                    <span v-else class="px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-slate-100 text-slate-400 border border-slate-200">Tidak Aktif</span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        <button v-if="!year.is_active" @click="setActive(year)" class="px-2.5 py-1 bg-[#D4AF37]/10 text-[#B5952F] hover:bg-[#D4AF37]/20 rounded-lg text-xs font-bold transition-colors" title="Jadikan Aktif">Set Aktif</button>
                                        <button @click="openEditModal(year)" class="p-1.5 text-[#7B1113] hover:bg-[#7B1113]/10 rounded-lg transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button @click="deleteYear(year)" :disabled="year.is_active" :class="{'opacity-30 cursor-not-allowed': year.is_active}" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="academicYears.data.length === 0">
                                <td colspan="5" class="py-12 px-6 text-center text-slate-400 font-medium">
                                    Tidak ada data tahun ajaran.
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
                    <div class="flex items-center gap-1.5" v-if="academicYears.links && academicYears.links.length > 3">
                        <Link v-for="(link, i) in academicYears.links" :key="i" :href="link.url || '#'" :class="[
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
            <div class="bg-white rounded-3xl shadow-xl max-w-sm w-full border border-slate-100 overflow-hidden transform transition-all">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-extrabold text-slate-800 font-montserrat text-lg">Tambah Tahun Ajaran</h3>
                    <button @click="showAddModal = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-50"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <form @submit.prevent="submitAdd" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Tahun Ajaran</label>
                        <input type="text" v-model="addForm.name" placeholder="Misal: 2026/2027" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-medium transition-all" />
                        <span v-if="addForm.errors.name" class="text-rose-500 text-xs font-semibold mt-1 block">{{ addForm.errors.name }}</span>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Semester</label>
                        <select v-model="addForm.semester" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold transition-all">
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                        </select>
                        <span v-if="addForm.errors.semester" class="text-rose-500 text-xs font-semibold mt-1 block">{{ addForm.errors.semester }}</span>
                    </div>
                    <div class="flex items-center mt-2">
                        <input type="checkbox" id="add-is-active" v-model="addForm.is_active" class="w-4 h-4 text-[#7B1113] border-slate-300 rounded focus:ring-[#7B1113]" />
                        <label for="add-is-active" class="ml-2 text-sm font-medium text-slate-700">Set sebagai Tahun Ajaran Aktif</label>
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
            <div class="bg-white rounded-3xl shadow-xl max-w-sm w-full border border-slate-100 overflow-hidden transform transition-all">
                <div class="px-6 py-5 border-b border-slate-100 flex items-center justify-between">
                    <h3 class="font-extrabold text-slate-800 font-montserrat text-lg">Ubah Tahun Ajaran</h3>
                    <button @click="showEditModal = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-50"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <form @submit.prevent="submitEdit" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Tahun Ajaran</label>
                        <input type="text" v-model="editForm.name" placeholder="Misal: 2026/2027" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-medium transition-all" />
                        <span v-if="editForm.errors.name" class="text-rose-500 text-xs font-semibold mt-1 block">{{ editForm.errors.name }}</span>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Semester</label>
                        <select v-model="editForm.semester" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold transition-all">
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                        </select>
                        <span v-if="editForm.errors.semester" class="text-rose-500 text-xs font-semibold mt-1 block">{{ editForm.errors.semester }}</span>
                    </div>
                    <div class="flex items-center mt-2">
                        <input type="checkbox" id="edit-is-active" v-model="editForm.is_active" class="w-4 h-4 text-[#7B1113] border-slate-300 rounded focus:ring-[#7B1113]" />
                        <label for="edit-is-active" class="ml-2 text-sm font-medium text-slate-700">Set sebagai Tahun Ajaran Aktif</label>
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

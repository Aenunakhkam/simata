<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm, router, Link } from "@inertiajs/vue3";
import { ref } from "vue";
import Swal from "sweetalert2";

const props = defineProps({
    users: Object,
    roles: Array,
    filters: Object,
});

const search = ref(props.filters.search || "");
const perPage = ref(props.filters.per_page || "10");
const roleFilter = ref(props.filters.role || "");

const showAddModal = ref(false);
const showEditModal = ref(false);
const activeUser = ref(null);

const addForm = useForm({
    name: "",
    email: "",
    password: "",
    role: "siswa",
    npsn: "",
});

const editForm = useForm({
    name: "",
    email: "",
    password: "",
    role: "",
    npsn: "",
});

const handleSearch = () => {
    router.get(
        route("users.index"),
        { 
            search: search.value, 
            per_page: perPage.value,
            role: roleFilter.value
        },
        { preserveState: true, replace: true }
    );
};

const handlePerPageChange = () => {
    handleSearch();
};

const handleRoleFilterChange = () => {
    handleSearch();
};

const openAddModal = () => {
    addForm.reset();
    addForm.role = "siswa";
    showAddModal.value = true;
};

const submitAdd = () => {
    addForm.post(route("users.store"), {
        onSuccess: () => {
            showAddModal.value = false;
            addForm.reset();
        },
    });
};

const openEditModal = (user) => {
    activeUser.value = user;
    editForm.name = user.name;
    editForm.email = user.email;
    editForm.role = user.role;
    editForm.npsn = user.npsn ?? "";
    editForm.password = "";
    showEditModal.value = true;
};

const submitEdit = () => {
    editForm.put(route("users.update", activeUser.value.id), {
        onSuccess: () => {
            showEditModal.value = false;
            activeUser.value = null;
        },
    });
};

const resetPassword = (user) => {
    if (user.id === authUser().id) {
        Swal.fire("Akses Ditolak", "Anda tidak dapat mereset kata sandi Anda sendiri dari sini!", "error");
        return;
    }

    Swal.fire({
        title: "Reset Kata Sandi?",
        text: `Kata sandi akun "${user.name}" akan direset menjadi: Belajar123*`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#f59e0b",
        cancelButtonColor: "#94a3b8",
        confirmButtonText: "Ya, Reset!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            router.post(route("users.reset-password", user.id));
        }
    });
};

const deleteUser = (user) => {
    if (user.id === authUser().id) {
        Swal.fire("Akses Ditolak", "Anda tidak dapat menghapus akun Anda sendiri!", "error");
        return;
    }

    Swal.fire({
        title: "Apakah Anda yakin?",
        text: `Akun "${user.name}" akan dihapus permanen!`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#6366f1",
        cancelButtonColor: "#f43f5e",
        confirmButtonText: "Ya, hapus!",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route("users.destroy", user.id));
        }
    });
};

import { computed } from "vue";
import { usePage } from "@inertiajs/vue3";

const page = usePage();
// Helper to access auth user ID in vue setup securely
const authUser = () => {
    return page.props.auth.user;
};
</script>

<template>
    <Head title="Data Pengguna" />

    <AuthenticatedLayout>
        <template #header> Data Pengguna / Akun </template>

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
                            placeholder="Cari nama, email, atau identitas..."
                            class="w-full pl-10 pr-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-medium placeholder-slate-400 transition-all duration-200"
                        />
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                        </div>
                    </div>
                    <!-- Role filter -->
                    <div class="w-full sm:w-48 shrink-0">
                        <select v-model="roleFilter" @change="handleRoleFilterChange" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold text-slate-700 transition-all">
                            <option value="">Semua Hak Akses</option>
                            <option v-for="role in roles" :key="role" :value="role">{{ role.toUpperCase() }}</option>
                        </select>
                    </div>
                </div>

                <!-- Add action -->
                <div>
                    <button @click="openAddModal" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2.5 bg-[#7B1113] hover:bg-[#5A0D0E] active:scale-95 text-xs font-bold text-white rounded-2xl shadow-lg shadow-[#7B1113]/15 transition-all duration-200">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                        Tambah Pengguna
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
                                <th class="py-4 px-6">Nama Pengguna</th>
                                <th class="py-4 px-6">Alamat Email</th>
                                <th class="py-4 px-6 w-44">Identitas (NPSN/NIP)</th>
                                <th class="py-4 px-6 w-32 text-center">Hak Akses</th>
                                <th class="py-4 px-6 w-36 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 text-slate-700 text-sm">
                            <tr v-for="(user, index) in users.data" :key="user.id" class="hover:bg-slate-50/50 transition-colors">
                                <td class="py-4 px-6 text-center font-semibold text-slate-400">
                                    {{ (users.current_page - 1) * users.per_page + index + 1 }}
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-800">
                                    <div class="flex items-center gap-2">
                                        {{ user.name }}
                                        <span v-if="user.id === authUser().id" class="px-1.5 py-0.5 bg-[#7B1113]/10 border border-indigo-150 text-[9px] font-extrabold rounded-md text-[#7B1113] uppercase tracking-wide">Saya</span>
                                    </div>
                                </td>
                                <td class="py-4 px-6 font-semibold text-slate-500">{{ user.email }}</td>
                                <td class="py-4 px-6 font-semibold">{{ user.npsn ?? '-' }}</td>
                                <td class="py-4 px-6 text-center">
                                    <span :class="[
                                        'px-2.5 py-1 rounded-full text-[10px] font-black uppercase tracking-wider',
                                        user.role === 'admin' ? 'bg-[#7B1113]/10 text-[#5A0D0E]' : 
                                        (user.role === 'guru' ? 'bg-[#D4AF37]/10 text-[#967C26]' : 
                                        (user.role === 'siswa' ? 'bg-blue-50 text-blue-700' : 'bg-slate-100 text-slate-700'))
                                    ]">
                                        {{ user.role }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center">
                                    <div class="flex items-center justify-center gap-1.5">
                                        <button @click="resetPassword(user)" class="p-1.5 text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Reset Kata Sandi" :disabled="user.id === authUser().id" :class="{'opacity-30 cursor-not-allowed': user.id === authUser().id}">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4v-3.252l7.743-7.743A6 6 0 0115 7z"></path></svg>
                                        </button>
                                        <button @click="openEditModal(user)" class="p-1.5 text-[#7B1113] hover:bg-[#7B1113]/10 rounded-lg transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button @click="deleteUser(user)" class="p-1.5 text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" :disabled="user.id === authUser().id" :class="{'opacity-30 cursor-not-allowed': user.id === authUser().id}" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="users.data.length === 0">
                                <td colspan="6" class="py-12 px-6 text-center text-slate-400 font-medium">
                                    Tidak ada data pengguna ditemukan.
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
                    <div class="flex items-center gap-1.5" v-if="users.links && users.links.length > 3">
                        <Link v-for="(link, i) in users.links" :key="i" :href="link.url || '#'" :class="[
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
                    <h3 class="font-extrabold text-slate-800 font-montserrat text-lg">Tambah Pengguna Baru</h3>
                    <button @click="showAddModal = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-50"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <form @submit.prevent="submitAdd" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                        <input type="text" v-model="addForm.name" placeholder="Nama lengkap pengguna" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-medium transition-all" />
                        <span v-if="addForm.errors.name" class="text-rose-500 text-xs font-semibold mt-1 block">{{ addForm.errors.name }}</span>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Alamat Email</label>
                        <input type="email" v-model="addForm.email" placeholder="nama@sekolah.com" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-medium transition-all" />
                        <span v-if="addForm.errors.email" class="text-rose-500 text-xs font-semibold mt-1 block">{{ addForm.errors.email }}</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Identitas (NPSN/NIP)</label>
                            <input type="text" v-model="addForm.npsn" placeholder="Opsional" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-medium transition-all" />
                            <span v-if="addForm.errors.npsn" class="text-rose-500 text-xs font-semibold mt-1 block">{{ addForm.errors.npsn }}</span>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Hak Akses</label>
                            <select v-model="addForm.role" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold transition-all">
                                <option v-for="r in roles" :key="r" :value="r">{{ r.toUpperCase() }}</option>
                            </select>
                            <span v-if="addForm.errors.role" class="text-rose-500 text-xs font-semibold mt-1 block">{{ addForm.errors.role }}</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Kata Sandi</label>
                        <input type="password" v-model="addForm.password" placeholder="Min. 6 karakter" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-medium transition-all" />
                        <span v-if="addForm.errors.password" class="text-rose-500 text-xs font-semibold mt-1 block">{{ addForm.errors.password }}</span>
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
                    <h3 class="font-extrabold text-slate-800 font-montserrat text-lg">Ubah Data Pengguna</h3>
                    <button @click="showEditModal = false" class="p-1.5 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-50"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg></button>
                </div>
                <form @submit.prevent="submitEdit" class="p-6 space-y-4">
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                        <input type="text" v-model="editForm.name" placeholder="Nama lengkap pengguna" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-medium transition-all" />
                        <span v-if="editForm.errors.name" class="text-rose-500 text-xs font-semibold mt-1 block">{{ editForm.errors.name }}</span>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Alamat Email</label>
                        <input type="email" v-model="editForm.email" placeholder="nama@sekolah.com" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-medium transition-all" />
                        <span v-if="editForm.errors.email" class="text-rose-500 text-xs font-semibold mt-1 block">{{ editForm.errors.email }}</span>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Identitas (NPSN/NIP)</label>
                            <input type="text" v-model="editForm.npsn" placeholder="Opsional" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-medium transition-all" />
                            <span v-if="editForm.errors.npsn" class="text-rose-500 text-xs font-semibold mt-1 block">{{ editForm.errors.npsn }}</span>
                        </div>
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Hak Akses</label>
                            <select v-model="editForm.role" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-semibold transition-all">
                                <option v-for="r in roles" :key="r" :value="r">{{ r.toUpperCase() }}</option>
                            </select>
                            <span v-if="editForm.errors.role" class="text-rose-500 text-xs font-semibold mt-1 block">{{ editForm.errors.role }}</span>
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Kata Sandi Baru (Opsional)</label>
                        <input type="password" v-model="editForm.password" placeholder="Kosongkan jika tidak diubah" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-medium transition-all" />
                        <span v-if="editForm.errors.password" class="text-rose-500 text-xs font-semibold mt-1 block">{{ editForm.errors.password }}</span>
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

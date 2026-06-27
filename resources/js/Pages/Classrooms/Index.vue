<script setup>
import { ref } from 'vue';
import { useForm, Head } from '@inertiajs/vue3';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import SecondaryButton from '@/Components/SecondaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import InputLabel from '@/Components/InputLabel.vue';
import InputError from '@/Components/InputError.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    classrooms: Object,
    majors: Array,
    filters: Object,
});

const isCreateModalOpen = ref(false);
const isEditModalOpen = ref(false);
const editingClassroom = ref(null);

const form = useForm({
    major_id: '',
    level: '',
    name: '',
});

const openCreateModal = () => {
    form.reset();
    isCreateModalOpen.value = true;
};

const openEditModal = (classroom) => {
    editingClassroom.value = classroom;
    form.major_id = classroom.major_id;
    form.level = classroom.level;
    form.name = classroom.name;
    isEditModalOpen.value = true;
};

const closeModals = () => {
    isCreateModalOpen.value = false;
    isEditModalOpen.value = false;
    form.reset();
    form.clearErrors();
};

const storeClassroom = () => {
    form.post(route('classrooms.store'), {
        onSuccess: () => closeModals(),
    });
};

const updateClassroom = () => {
    form.put(route('classrooms.update', editingClassroom.value.id), {
        onSuccess: () => closeModals(),
    });
};

const deleteClassroom = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus kelas ini?')) {
        useForm({}).delete(route('classrooms.destroy', id));
    }
};

const searchForm = useForm({ 
    search: props.filters?.search || '',
    per_page: props.filters?.per_page || '10'
});

const onSearch = () => {
    searchForm.get(route('classrooms.index'), { preserveState: true });
};
</script>

<template>
    <Head title="Data Kelas" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Data Kelas</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <!-- Data Table Container -->
                <div class="bg-white overflow-hidden shadow-[0_8px_30px_rgb(0,0,0,0.04)] sm:rounded-2xl border border-gray-100">
                    <div class="p-6 md:px-8 border-b border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                        <div class="flex items-center space-x-3 w-full md:w-auto">
                            <span class="text-sm font-medium text-gray-500">Tampilkan</span>
                            <select v-model="searchForm.per_page" @change="onSearch" class="border-gray-200 rounded-lg text-sm text-gray-700 focus:ring-green-600 focus:border-green-600">
                                <option value="10">10</option>
                                <option value="15">15</option>
                                <option value="25">25</option>
                                <option value="50">50</option>
                                <option value="100">100</option>
                                <option value="all">Semua</option>
                            </select>
                            <span class="text-sm font-medium text-gray-500">data</span>
                        </div>
                        
                        <div class="flex items-center space-x-3 w-full md:w-auto">
                            <div class="relative w-full md:w-64">
                                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                    <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>
                                <TextInput 
                                    v-model="searchForm.search" 
                                    @keyup.enter="onSearch"
                                    type="text" 
                                    class="w-full pl-10 pr-4 py-2 rounded-xl border-gray-200 focus:ring-green-600 focus:border-green-600 bg-gray-50 focus:bg-white transition-colors text-sm" 
                                    placeholder="Cari kelas atau jurusan..." 
                                />
                            </div>
                            <PrimaryButton @click="openCreateModal" class="shadow-md hover:shadow-lg transition-all rounded-xl whitespace-nowrap">
                                + Tambah Kelas
                            </PrimaryButton>
                        </div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-500 uppercase tracking-wider bg-gray-50/80 border-b border-gray-100">
                                <tr>
                                    <th class="px-6 py-4 font-bold text-center w-12">No</th>
                                    <th class="px-6 py-4 font-bold">Tingkat</th>
                                    <th class="px-6 py-4 font-bold">Nama Kelas</th>
                                    <th class="px-6 py-4 font-bold">Jurusan</th>
                                    <th class="px-6 py-4 text-right font-bold">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(classroom, index) in classrooms.data" :key="classroom.id" class="bg-white border-b last:border-0 hover:bg-green-50/30 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-center text-gray-500">
                                        {{ (classrooms.current_page - 1) * classrooms.per_page + index + 1 }}
                                    </td>
                                    <td class="px-6 py-4 font-medium text-gray-900">{{ classroom.level }}</td>
                                    <td class="px-6 py-4 font-medium">{{ classroom.name }}</td>
                                    <td class="px-6 py-4">
                                        <span class="px-2 py-1 bg-gray-100 rounded text-xs font-medium text-gray-600">
                                            {{ classroom.major?.name }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-2">
                                        <button @click="openEditModal(classroom)" class="inline-flex items-center justify-center text-blue-600 hover:text-blue-900 bg-blue-50 hover:bg-blue-100 p-2 rounded-lg transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                        <button @click="deleteClassroom(classroom.id)" class="inline-flex items-center justify-center text-red-600 hover:text-red-900 bg-red-50 hover:bg-red-100 p-2 rounded-lg transition-colors" title="Hapus">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="classrooms.data.length === 0">
                                    <td colspan="5" class="px-6 py-8 text-center text-gray-500 bg-gray-50/50">Belum ada data kelas.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="p-6 md:px-8 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4" v-if="classrooms.links && classrooms.data.length > 0">
                        <span class="text-sm text-gray-500">Menampilkan <span class="font-bold text-gray-900">{{ classrooms.from }}</span> sampai <span class="font-bold text-gray-900">{{ classrooms.to }}</span> dari <span class="font-bold text-gray-900">{{ classrooms.total }}</span> data</span>
                        <div class="flex space-x-1">
                            <template v-for="(link, p) in classrooms.links" :key="p">
                                <a 
                                    v-if="link.url"
                                    :href="link.url"
                                    class="px-3 py-1.5 border rounded-md text-sm transition-colors"
                                    :class="link.active ? 'bg-indigo-600 text-white border-indigo-600' : 'bg-white text-gray-700 hover:bg-gray-50 border-gray-300'"
                                    v-html="link.label"
                                ></a>
                                <span v-else class="px-3 py-1.5 border rounded-md text-sm text-gray-400 bg-gray-50 border-gray-200" v-html="link.label"></span>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Modal :show="isCreateModalOpen || isEditModalOpen" @close="closeModals">
            <div class="bg-gradient-to-b from-green-50/50 to-white px-6 py-5 border-b border-gray-100">
                <h2 class="text-xl font-bold text-[#0f7632] flex items-center">
                    <svg v-if="!isEditModalOpen" class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                    <svg v-else class="w-6 h-6 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    {{ isEditModalOpen ? 'Perbarui Data Kelas' : 'Tambah Kelas Baru' }}
                </h2>
            </div>
            <div class="p-6 sm:p-8">

                <form @submit.prevent="isEditModalOpen ? updateClassroom() : storeClassroom()">
                    <div class="mb-4">
                        <InputLabel for="major_id" value="Jurusan" />
                        <select 
                            id="major_id" 
                            v-model="form.major_id" 
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-600 focus:ring-green-600 sm:text-sm"
                            required
                        >
                            <option value="" disabled>Pilih Jurusan...</option>
                            <option v-for="major in majors" :key="major.id" :value="major.id">
                                {{ major.name }} ({{ major.code }})
                            </option>
                        </select>
                        <InputError :message="form.errors.major_id" class="mt-2" />
                    </div>

                    <div class="mb-4">
                        <InputLabel for="level" value="Tingkat (Contoh: 10, 11, 12)" />
                        <TextInput id="level" v-model="form.level" type="number" min="1" max="15" class="mt-1 block w-full" required placeholder="10" />
                        <InputError :message="form.errors.level" class="mt-2" />
                    </div>

                    <div class="mb-6">
                        <InputLabel for="name" value="Nama Kelas Lengkap (Contoh: X RPL 1)" />
                        <TextInput id="name" v-model="form.name" type="text" class="mt-1 block w-full" required placeholder="X RPL 1" />
                        <InputError :message="form.errors.name" class="mt-2" />
                    </div>

                    <div class="flex justify-end space-x-3 pt-4 border-t border-gray-100">
                        <SecondaryButton @click="closeModals">Batal</SecondaryButton>
                        <PrimaryButton :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Simpan Data
                        </PrimaryButton>
                    </div>
                </form>
            </div>
        </Modal>
    </AuthenticatedLayout>
</template>

<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const props = defineProps({
    settings: Object,
});

const form = useForm({
    school_name: props.settings.school_name || "",
    school_address: props.settings.school_address || "",
    school_phone: props.settings.school_phone || "",
    school_email: props.settings.school_email || "",
    school_website: props.settings.school_website || "",
    school_logo: null,
});

const logoPreview = ref(props.settings.school_logo || null);

const handleLogoChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.school_logo = file;
        logoPreview.value = URL.createObjectURL(file);
    }
};

const submitForm = () => {
    form.post(route("system-settings.update"), {
        preserveScroll: true,
    });
};
</script>

<template>
    <Head title="Pengaturan Sistem" />

    <AuthenticatedLayout>
        <template #header> Pengaturan Identitas Sekolah </template>

        <div class="max-w-4xl mx-auto py-6">
            <div class="bg-white p-8 rounded-3xl border border-slate-100 shadow-sm">
                <form @submit.prevent="submitForm" class="space-y-6">
                    
                    <div class="flex items-center gap-6 pb-6 border-b border-slate-100">
                        <div class="shrink-0">
                            <div class="h-24 w-24 rounded-2xl border-2 border-dashed border-slate-300 flex items-center justify-center overflow-hidden bg-slate-50 relative group cursor-pointer" @click="$refs.logoInput.click()">
                                <img v-if="logoPreview" :src="logoPreview" class="h-full w-full object-cover" />
                                <svg v-else class="h-8 w-8 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                                <div class="absolute inset-0 bg-slate-900/50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="text-xs font-bold text-white">Ubah Logo</span>
                                </div>
                            </div>
                            <input type="file" ref="logoInput" class="hidden" accept="image/*" @change="handleLogoChange" />
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-slate-800">Logo Sekolah</h3>
                            <p class="text-sm text-slate-500 mt-1">Upload logo resmi institusi Anda. Rekomendasi ukuran 256x256px.</p>
                            <span v-if="form.errors.school_logo" class="text-rose-500 text-xs font-semibold mt-1">{{ form.errors.school_logo }}</span>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="md:col-span-2">
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Nama Institusi / Sekolah</label>
                            <input type="text" v-model="form.school_name" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-medium transition-all" />
                            <span v-if="form.errors.school_name" class="text-rose-500 text-xs font-semibold mt-1">{{ form.errors.school_name }}</span>
                        </div>
                        
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Telepon</label>
                            <input type="text" v-model="form.school_phone" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-medium transition-all" />
                        </div>
                        
                        <div>
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Email Resmi</label>
                            <input type="email" v-model="form.school_email" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-medium transition-all" />
                        </div>
                        
                        <div class="md:col-span-2">
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Website</label>
                            <input type="text" v-model="form.school_website" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-medium transition-all" />
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-xs font-black text-slate-500 uppercase tracking-wider mb-1.5">Alamat Lengkap</label>
                            <textarea v-model="form.school_address" rows="3" class="w-full px-4 py-2.5 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-sm font-medium transition-all"></textarea>
                        </div>
                    </div>

                    <div class="pt-6 border-t border-slate-100 flex justify-end">
                        <button type="submit" :disabled="form.processing" class="px-6 py-2.5 text-sm font-bold text-white bg-[#7B1113] hover:bg-[#5A0D0E] active:scale-95 disabled:opacity-50 disabled:cursor-not-allowed rounded-2xl transition-all shadow-lg shadow-indigo-600/20">
                            {{ form.processing ? 'Menyimpan...' : 'Simpan Pengaturan' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

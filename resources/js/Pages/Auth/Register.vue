<script setup>
import GuestLayout from "@/Layouts/GuestLayout.vue";
import InputError from "@/Components/InputError.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const form = useForm({
    name: "",
    npsn: "",
    email: "",
    password: "",
    password_confirmation: "",
});

const showPassword = ref(false);
const showConfirmPassword = ref(false);

const submit = () => {
    form.clearErrors();
    let hasError = false;

    if (!form.name) {
        form.setError("name", "Nama Lengkap wajib diisi.");
        hasError = true;
    }
    if (!form.npsn) {
        form.setError("npsn", "NISN/NIP wajib diisi.");
        hasError = true;
    }
    if (!form.email) {
        form.setError("email", "Alamat Email wajib diisi.");
        hasError = true;
    }
    if (!form.password) {
        form.setError("password", "Kata Sandi wajib diisi.");
        hasError = true;
    }
    if (!form.password_confirmation) {
        form.setError("password_confirmation", "Konfirmasi Kata Sandi wajib diisi.");
        hasError = true;
    } else if (form.password !== form.password_confirmation) {
        form.setError("password_confirmation", "Konfirmasi Kata Sandi tidak cocok.");
        hasError = true;
    }

    if (hasError) return;

    form.post(route('register'), {
        onFinish: () => form.reset("password", "password_confirmation"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Daftar Akun Belajar SIMATA" />

        <div class="mb-8">
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight font-montserrat mb-2">Daftar Akun Baru</h2>
            <p class="text-sm text-slate-500 font-medium leading-relaxed">
                Silakan isi form di bawah ini untuk membuat akun.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label for="name" class="block font-bold text-slate-700 mb-1.5 text-xs uppercase tracking-wider">Nama Lengkap</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    </div>
                    <input
                        id="name"
                        type="text"
                        class="block w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-slate-800 transition-all duration-200 text-sm font-medium placeholder-slate-400"
                        v-model="form.name"
                        autofocus
                        autocomplete="name"
                        placeholder="Contoh: Budi Santoso"
                    />
                </div>
                <InputError class="mt-1 text-xs font-semibold text-rose-500" :message="form.errors.name" />
            </div>

            <div>
                <label for="npsn" class="block font-bold text-slate-700 mb-1.5 text-xs uppercase tracking-wider">NISN / NIP</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.37 0 2.494 1.08 2.5 2.448m5-2.448a2 2 0 100-4 2 2 0 000 4zm0 0c1.37 0 2.494 1.08 2.5 2.448"></path></svg>
                    </div>
                    <input
                        id="npsn"
                        type="text"
                        class="block w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-slate-800 transition-all duration-200 text-sm font-medium placeholder-slate-400"
                        v-model="form.npsn"
                        autocomplete="npsn"
                        placeholder="Contoh: 12345678"
                    />
                </div>
                <InputError class="mt-1 text-xs font-semibold text-rose-500" :message="form.errors.npsn" />
            </div>

            <div>
                <label for="email" class="block font-bold text-slate-700 mb-1.5 text-xs uppercase tracking-wider">Alamat Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"></path></svg>
                    </div>
                    <input
                        id="email"
                        type="email"
                        class="block w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-slate-800 transition-all duration-200 text-sm font-medium placeholder-slate-400"
                        v-model="form.email"
                        autocomplete="username"
                        placeholder="nama@email.com"
                    />
                </div>
                <InputError class="mt-1 text-xs font-semibold text-rose-500" :message="form.errors.email" />
            </div>

            <div>
                <label for="password" class="block font-bold text-slate-700 mb-1.5 text-xs uppercase tracking-wider">Kata Sandi</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <input
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        class="block w-full pl-11 pr-12 py-3 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-slate-800 transition-all duration-200 text-sm font-medium placeholder-slate-400"
                        v-model="form.password"
                        autocomplete="new-password"
                        placeholder="Masukkan kata sandi"
                    />
                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center cursor-pointer text-slate-400 hover:text-slate-600 transition-colors" @click="showPassword = !showPassword">
                        <svg v-if="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                    </div>
                </div>
                <InputError class="mt-1 text-xs font-semibold text-rose-500" :message="form.errors.password" />
            </div>

            <div>
                <label for="password_confirmation" class="block font-bold text-slate-700 mb-1.5 text-xs uppercase tracking-wider">Konfirmasi Kata Sandi</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <input
                        id="password_confirmation"
                        :type="showConfirmPassword ? 'text' : 'password'"
                        class="block w-full pl-11 pr-12 py-3 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-slate-800 transition-all duration-200 text-sm font-medium placeholder-slate-400"
                        v-model="form.password_confirmation"
                        autocomplete="new-password"
                        placeholder="Ulangi kata sandi"
                    />
                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center cursor-pointer text-slate-400 hover:text-slate-600 transition-colors" @click="showConfirmPassword = !showConfirmPassword">
                        <svg v-if="!showConfirmPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                    </div>
                </div>
                <InputError class="mt-1 text-xs font-semibold text-rose-500" :message="form.errors.password_confirmation" />
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full flex justify-center items-center py-3 px-4 rounded-2xl shadow-lg shadow-[#7B1113]/20 text-sm font-bold text-[#D4AF37] bg-[#7B1113] hover:bg-[#5A0D0E] focus:outline-none focus:ring-4 focus:ring-[#7B1113]/20 transition-all duration-200 active:scale-[0.98]"
                    :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-[#D4AF37]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Daftar Akun Belajar
                </button>
            </div>
            
            <div class="text-center mt-6">
                <p class="text-sm text-slate-500 font-medium">Sudah punya akun? <Link :href="route('login')" class="font-bold text-[#7B1113] hover:text-[#D4AF37] transition-colors ml-1">Masuk Sekarang</Link></p>
            </div>
        </form>
    </GuestLayout>
</template>

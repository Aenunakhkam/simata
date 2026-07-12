<script setup>
import InputError from "@/Components/InputError.vue";
import GuestLayout from "@/Layouts/GuestLayout.vue";
import { Head, Link, useForm } from "@inertiajs/vue3";
import { ref } from "vue";

const form = useForm({
    email: "",
    password: "",
    remember: false,
});

const showPassword = ref(false);

const submit = () => {
    form.clearErrors();
    
    if (!form.email || !form.password) {
        if (!form.email) form.setError("email", "Email wajib diisi");
        if (!form.password) form.setError("password", "Kata sandi wajib diisi");
        return;
    }

    form.post(route("login"), {
        onFinish: () => form.reset("password"),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Masuk Portal E-Learning SIMATA" />

        <div class="mb-8">
            <h2 class="text-3xl font-extrabold text-slate-900 tracking-tight font-montserrat mb-2">Selamat Datang</h2>
            <p class="text-sm text-slate-500 font-medium leading-relaxed">
                Silakan masuk dengan email Anda untuk mulai belajar.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <label for="email" class="block font-bold text-slate-700 mb-2 text-xs uppercase tracking-wider">Alamat Email</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.206"></path></svg>
                    </div>
                    <input
                        id="email"
                        type="email"
                        class="block w-full pl-11 pr-4 py-3 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-slate-800 transition-all duration-200 text-sm font-medium placeholder-slate-400"
                        v-model="form.email"
                        autofocus
                        autocomplete="username"
                        placeholder="nama@email.com"
                    />
                </div>
                <InputError class="mt-1.5 text-xs font-semibold text-rose-500" :message="form.errors.email" />
            </div>

            <div>
                <div class="flex justify-between items-center mb-2">
                    <label for="password" class="block font-bold text-slate-700 text-xs uppercase tracking-wider">Kata Sandi</label>
                    <Link
                        :href="route('password.request')"
                        class="text-xs font-bold text-[#7B1113] hover:text-[#D4AF37] transition-colors"
                    >
                        Lupa Sandi?
                    </Link>
                </div>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    </div>
                    <input
                        id="password"
                        :type="showPassword ? 'text' : 'password'"
                        class="block w-full pl-11 pr-12 py-3 bg-slate-50 border border-slate-200 focus:bg-white focus:ring-4 focus:ring-[#7B1113]/10 focus:border-[#7B1113] rounded-2xl text-slate-800 transition-all duration-200 text-sm font-medium placeholder-slate-400"
                        v-model="form.password"
                        autocomplete="current-password"
                        placeholder="Masukkan kata sandi Anda"
                    />
                    <div class="absolute inset-y-0 right-0 pr-3.5 flex items-center cursor-pointer text-slate-400 hover:text-slate-600 transition-colors" @click="showPassword = !showPassword">
                        <svg v-if="!showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"></path></svg>
                    </div>
                </div>
                <InputError class="mt-1.5 text-xs font-semibold text-rose-500" :message="form.errors.password" />
            </div>

            <div class="flex items-center pt-1">
                <label class="flex items-center cursor-pointer select-none">
                    <input type="checkbox" v-model="form.remember" class="w-4.5 h-4.5 text-[#7B1113] bg-slate-50 border-slate-300 rounded-lg focus:ring-[#7B1113]/20 focus:ring-4 transition-all">
                    <span class="ml-2.5 text-sm text-slate-500 font-medium">Ingat saya di perangkat ini</span>
                </label>
            </div>

            <div class="pt-2">
                <button
                    type="submit"
                    class="w-full flex justify-center items-center py-3 px-4 rounded-2xl shadow-lg shadow-[#7B1113]/20 text-sm font-bold text-[#D4AF37] bg-[#7B1113] hover:bg-[#5A0D0E] focus:outline-none focus:ring-4 focus:ring-[#7B1113]/20 transition-all duration-200 active:scale-[0.98]"
                    :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-[#D4AF37]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Masuk Sekarang
                </button>
            </div>
            
            <div class="text-center mt-6">
                <p class="text-sm text-slate-500 font-medium">
                    Belum memiliki akun belajar? 
                    <Link :href="route('register')" class="font-bold text-[#7B1113] hover:text-[#D4AF37] transition-colors ml-1">
                        Daftar Baru
                    </Link>
                </p>
            </div>
        </form>
    </GuestLayout>
</template>

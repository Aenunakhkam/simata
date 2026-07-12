<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
</script>

<template>
    <GuestLayout>
        <Head title="Lupa Kata Sandi" />

        <div class="mb-6">
            <h2 class="text-3xl font-extrabold text-[#7B1113] tracking-tight mb-2">Lupa Kata Sandi?</h2>
            <p class="text-[15px] text-slate-500 font-medium">Bantuan Pemulihan Akun SIMATA</p>
        </div>

        <div class="mb-6 p-5 bg-rose-50 border border-rose-100 rounded-2xl flex flex-col items-center text-center">
            <div class="w-16 h-16 bg-rose-100 text-rose-600 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            </div>
            <h3 class="text-lg font-bold text-rose-900 mb-2">Panduan Pemulihan Akun</h3>
            <div class="text-sm text-rose-800 leading-relaxed text-left inline-block w-full">
                <p class="mb-3 text-center">Karena aplikasi Simata berjalan secara lokal (offline), pemulihan kata sandi Administrator dilakukan melalui perangkat server.</p>
                <div class="bg-rose-100/50 p-4 rounded-xl border border-rose-200">
                    <p class="font-bold text-rose-900 mb-2">Langkah-langkah Reset Sandi (Khusus Admin):</p>
                    <ol class="list-decimal list-inside space-y-2 text-rose-900">
                        <li>Buka folder instalasi Simata di komputer server.</li>
                        <li>Cari dan klik dua kali file <code class="font-bold bg-white px-1.5 py-0.5 rounded text-rose-900 shadow-sm border border-rose-100">Reset-Password-Admin.bat</code>.</li>
                        <li>Tekan <kbd class="font-bold bg-white px-1.5 py-0.5 rounded text-rose-900 shadow-sm border border-rose-100">Enter</kbd> pada jendela hitam yang muncul.</li>
                        <li>Setelah sukses, kata sandi Anda akan direset.</li>
                    </ol>
                </div>
            </div>
        </div>

        <div class="pt-2">
            <Link
                :href="route('login')"
                class="w-full flex justify-center items-center py-3.5 px-4 rounded-xl shadow-sm text-[15px] font-bold text-slate-700 bg-white border border-slate-200 hover:bg-slate-50 hover:text-[#7B1113] transition-all duration-300"
            >
                Kembali ke Halaman Masuk
            </Link>
        </div>
    </GuestLayout>
</template>

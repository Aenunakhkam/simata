<script setup>
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import { Head, router } from "@inertiajs/vue3";
import { ref } from "vue";
import Swal from "sweetalert2";

const props = defineProps({
    appUpdates: Array,
    remoteCommits: Array,
    newCommitsCount: Number,
    lastFetch: String,
    filters: Object,
});

const isPulling = ref(false);

const pullUpdates = () => {
    Swal.fire({
        title: "Tarik Pembaruan?",
        text: "Proses ini akan mengunduh dan menerapkan kode terbaru dari server pusat (GitHub).",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#7B1113",
        cancelButtonColor: "#94a3b8",
        confirmButtonText: "Ya, Tarik Sekarang",
        cancelButtonText: "Batal",
    }).then((result) => {
        if (result.isConfirmed) {
            isPulling.value = true;
            router.post(route("updates.pull"), {}, {
                onFinish: () => {
                    isPulling.value = false;
                }
            });
        }
    });
};
</script>

<template>
    <Head title="Cek Pembaruan" />

    <AuthenticatedLayout>
        <template #header> Cek Pembaruan Sistem </template>

        <div class="space-y-6 max-w-7xl mx-auto">
            
            <div class="bg-white rounded-3xl border border-slate-100 shadow-sm p-6 lg:p-8">
                <div class="flex flex-col md:flex-row gap-6 items-start justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800 mb-2">Status Sistem SIMATA</h2>
                        <p class="text-slate-500 text-sm mb-4">Pastikan sistem sekolah Anda selalu menggunakan versi terbaru untuk mendapatkan fitur terkini dan perbaikan keamanan.</p>
                        
                        <div class="flex items-center gap-2">
                            <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Pengecekan Terakhir:</span>
                            <span class="text-xs font-bold text-[#7B1113]">{{ lastFetch }}</span>
                        </div>
                    </div>

                    <div class="flex flex-col items-end gap-3 shrink-0 bg-slate-50 p-5 rounded-2xl border border-slate-200">
                        <div class="flex items-center gap-3">
                            <div class="flex flex-col items-end">
                                <span class="text-xs font-bold text-slate-400 uppercase tracking-wider">Status Versi</span>
                                <span v-if="newCommitsCount > 0" class="text-lg font-black text-rose-600">{{ newCommitsCount }} Pembaruan Tersedia!</span>
                                <span v-else class="text-lg font-black text-[#B5952F]">Sistem Sudah Versi Terbaru</span>
                            </div>
                            <div :class="newCommitsCount > 0 ? 'bg-rose-100 text-rose-600' : 'bg-[#D4AF37]/20 text-[#B5952F]'" class="w-12 h-12 rounded-full flex items-center justify-center">
                                <svg v-if="newCommitsCount > 0" class="w-6 h-6 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path></svg>
                                <svg v-else class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                        </div>

                        <button v-if="newCommitsCount > 0" @click="pullUpdates" :disabled="isPulling" class="mt-2 w-full flex items-center justify-center gap-2 px-6 py-2.5 bg-gradient-to-r from-[#5A0D0E] to-[#7B1113] text-[#D4AF37] hover:from-[#7B1113] hover:to-[#5A0D0E] rounded-xl font-bold text-sm transition-all shadow-lg shadow-[#7B1113]/20 disabled:opacity-50">
                            <svg v-if="isPulling" class="animate-spin -ml-1 mr-2 h-4 w-4 text-[#D4AF37]" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span v-if="isPulling">Menerapkan Pembaruan...</span>
                            <span v-else>Update Sekarang</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Tab Navigation for Catatan Rilis vs History Commit -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Catatan Rilis -->
                <div class="lg:col-span-2 space-y-4">
                    <h3 class="text-lg font-bold text-slate-800 px-2">Catatan Rilis (Release Notes)</h3>
                    
                    <div v-for="(release, index) in appUpdates" :key="index" class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                        <div class="flex items-center gap-3 mb-4 border-b border-slate-100 pb-4">
                            <div class="w-10 h-10 rounded-xl bg-[#7B1113]/10 text-[#7B1113] flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-black text-slate-800 text-lg">{{ release.version }}</h4>
                            </div>
                        </div>
                        
                        <ul class="space-y-2.5">
                            <li v-for="(change, i) in release.changes" :key="i" class="flex items-start gap-2.5 text-sm text-slate-600 font-medium">
                                <svg class="w-5 h-5 text-[#B5952F] shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>{{ change }}</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Riwayat Sistem (Git Commits) -->
                <div class="space-y-4">
                    <h3 class="text-lg font-bold text-slate-800 px-2">Riwayat Sistem Terbaru</h3>
                    
                    <div class="bg-white rounded-3xl border border-slate-100 shadow-sm overflow-hidden">
                        <div class="p-4 border-b border-slate-100 bg-slate-50">
                            <p class="text-xs font-bold text-slate-500 text-center">Log Teknis (Github Commits)</p>
                        </div>
                        
                        <div class="divide-y divide-slate-100 max-h-[500px] overflow-y-auto">
                            <div v-for="commit in remoteCommits" :key="commit.hash" class="p-4 hover:bg-slate-50 transition-colors">
                                <p class="text-sm font-bold text-slate-800 mb-1 leading-snug">{{ commit.message }}</p>
                                <div class="flex items-center justify-between text-[11px] font-semibold text-slate-400">
                                    <span>Oleh: <span class="text-[#7B1113]">{{ commit.author }}</span></span>
                                    <span>{{ commit.date }}</span>
                                </div>
                            </div>
                            <div v-if="remoteCommits.length === 0" class="p-8 text-center text-sm font-semibold text-slate-400">
                                Riwayat pembaruan tidak ditemukan.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

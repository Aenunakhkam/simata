<script setup>
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const props = defineProps({
    transaction: Object,
});

const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
};

const formatDate = (dateString) => {
    const options = { year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit', second: '2-digit', timeZoneName: 'short' };
    return new Date(dateString).toLocaleDateString('id-ID', options).replace('pukul ', '');
};

const printReceipt = () => {
    window.print();
};
</script>

<template>
    <Head title="Bukti Transaksi" />

    <div class="min-h-screen bg-[#005fb8] flex flex-col items-center pt-8 pb-12 relative overflow-hidden font-sans">
        
        <!-- Top Section / Header -->
        <div class="text-center text-white z-10 mb-6">
            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                <svg class="w-8 h-8 text-[#005fb8]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h1 class="text-2xl font-bold mb-1">Transaksi Berhasil</h1>
            <p class="text-blue-100 text-sm">{{ formatDate(transaction.created_at) }}</p>
        </div>

        <!-- Receipt Card -->
        <div class="bg-white w-[90%] max-w-[400px] rounded-3xl p-6 relative z-10 shadow-2xl printable-area overflow-hidden">
            
            <!-- Watermark Image (Guaranteed to print without 'background graphics' checked) -->
            <img src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiPjxkZWZzPjxwYXR0ZXJuIGlkPSJ3bSIgd2lkdGg9IjE2MCIgaGVpZ2h0PSIxMjAiIHBhdHRlcm5Vbml0cz0idXNlclNwYWNlT25Vc2UiPjx0ZXh0IHg9IjEwIiB5PSI4MCIgZm9udC1mYW1pbHk9IkFyaWFsLCBzYW5zLXNlcmlmIiBmb250LXdlaWdodD0iOTAwIiBmb250LXNpemU9IjI2IiBmaWxsPSIjMDA1ZmI4IiB0cmFuc2Zvcm09InJvdGF0ZSgtMzUgOTAgNzApIj5CQU5LTUlOSTwvdGV4dD48L3BhdHRlcm4+PC9kZWZzPjxyZWN0IHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiIGZpbGw9InVybCgjd20pIiAvPjwvc3ZnPg==" 
                 class="absolute inset-0 w-full h-full z-0 opacity-5 pointer-events-none" alt="Watermark" />

            <!-- Foreground Content -->
            <div class="relative z-10">
                <div class="text-center mb-6 border-b border-gray-100 pb-6">
                    <p class="text-gray-500 text-sm mb-1">Total Transaksi</p>
                    <h2 class="text-3xl font-black text-[#005fb8] tracking-tight">{{ formatRupiah(transaction.amount) }}</h2>
                </div>

            <div class="flex justify-between items-center mb-6">
                <span class="text-gray-500 text-sm">No. Ref</span>
                <span class="font-bold text-gray-800">{{ transaction.transaction_number }}</span>
            </div>

            <!-- Sumber Dana & Tujuan Container -->
            <div class="border border-gray-200 rounded-2xl p-4 mb-6 relative">
                
                <!-- Sumber Dana -->
                <div class="mb-4">
                    <p class="text-gray-800 font-bold mb-2">Sumber Dana</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-[#005fb8] text-white rounded-full flex items-center justify-center font-bold text-sm shrink-0">
                            {{ transaction.type === 'deposit' ? transaction.student.name.substring(0,2).toUpperCase() : 'BM' }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-800 text-sm">{{ transaction.type === 'deposit' ? transaction.student.name : 'BANK MINI SEKOLAH' }}</p>
                            <p class="text-xs text-gray-500 uppercase">{{ transaction.type === 'deposit' ? (transaction.student.nasabah_type || 'NASABAH') : 'KAS UTAMA' }}</p>
                            <p class="text-xs text-gray-500">{{ transaction.type === 'deposit' ? transaction.student.nisn : '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="border-t border-gray-100 my-4 relative">
                    <div class="absolute -left-6 -top-3 w-4 h-4 bg-white rounded-full border-r border-gray-200"></div>
                    <div class="absolute -right-6 -top-3 w-4 h-4 bg-white rounded-full border-l border-gray-200"></div>
                </div>

                <!-- Tujuan -->
                <div>
                    <p class="text-gray-800 font-bold mb-2">Tujuan</p>
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 bg-gray-100 text-gray-500 rounded-full flex items-center justify-center font-bold text-sm shrink-0">
                            {{ transaction.type === 'withdrawal' ? transaction.student.name.substring(0,1).toUpperCase() : 'B' }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-800 text-sm">{{ transaction.type === 'withdrawal' ? transaction.student.name : 'BANK MINI SEKOLAH' }}</p>
                            <p class="text-xs text-gray-500 uppercase">{{ transaction.type === 'withdrawal' ? (transaction.student.nasabah_type || 'NASABAH') : 'KAS UTAMA' }}</p>
                            <p class="text-xs text-gray-500">{{ transaction.type === 'withdrawal' ? transaction.student.nisn : '-' }}</p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Details -->
            <div class="space-y-3 border-b border-gray-100 pb-6 mb-6">
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Jenis Transaksi</span>
                    <span class="font-bold text-gray-800 text-sm">{{ transaction.type === 'deposit' ? 'Setor Tunai' : 'Tarik Tunai' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Catatan</span>
                    <span class="font-bold text-gray-800 text-sm">{{ transaction.description || '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Nominal</span>
                    <span class="font-bold text-gray-800 text-sm">{{ formatRupiah(transaction.amount) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-500 text-sm">Biaya Admin</span>
                    <span class="font-bold text-gray-800 text-sm">Rp0</span>
                </div>
            </div>

            <!-- Info -->
            <div class="text-xs text-gray-400 leading-relaxed mb-6">
                <p class="mb-1 text-gray-500 font-bold">INFORMASI:</p>
                <p>Biaya Termasuk PPN (Apabila Dikenakan/Apabila Ada).</p>
                <p>Bank Mini Sekolah (Simaku Bank).</p>
                <p>Dokumen ini adalah bukti transaksi yang sah.</p>
            </div>
            
            <div class="flex gap-3 no-print">
                <a :href="route('teller.index')" class="flex-1 py-3 text-center rounded-xl bg-gray-100 text-gray-700 font-bold text-sm hover:bg-gray-200 transition-colors">Kembali</a>
                <button @click="printReceipt" class="flex-1 py-3 rounded-xl bg-[#005fb8] text-white font-bold text-sm shadow-md hover:bg-blue-700 transition-colors">Cetak (Print)</button>
            </div>
            
            </div> <!-- End Foreground Content -->
        </div>

        <div class="mt-8 text-center text-blue-200 text-xs no-print">
            &copy; {{ new Date().getFullYear() }} Aplikasi Bank Mini Sekolah.
        </div>
    </div>

    <!-- Print Styles -->
    <style>
        @media print {
            @page {
                margin: 0;
                size: 80mm auto;
            }
            body {
                background: white !important;
                margin: 0 !important;
                padding: 0 !important;
                width: 80mm !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .min-h-screen {
                display: block !important;
                background: white !important;
                padding: 0 !important;
                margin: 0 !important;
                min-height: auto !important;
                width: 80mm !important;
            }
            .text-center.text-white.z-10.mb-6 {
                margin: 0 !important;
                padding: 4mm 0 !important;
            }
            .text-white { color: #000 !important; }
            .bg-\[\#005fb8\] { background: white !important; }
            .w-16.h-16.bg-white { border: 2px solid #005fb8; }
            .text-blue-100 { color: #666 !important; }
            
            .printable-area {
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                width: 80mm !important;
                max-width: 80mm !important;
                padding: 2mm 4mm !important;
                margin: 0 !important;
            }
            .border-gray-200 { border-color: #eee !important; }
            .no-print { display: none !important; }
        }
    </style>
</template>

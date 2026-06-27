<script setup>
import { ref, computed } from 'vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import axios from 'axios';
import Swal from 'sweetalert2';

const accountNumber = ref('');
const customer = ref(null);
const isLoading = ref(false);
const searchError = ref('');
const perPage = ref(10);
const currentPage = ref(1);

const paginatedTransactions = computed(() => {
    if (!customer.value || !customer.value.recent_transactions) return [];
    let items = customer.value.recent_transactions;
    if (perPage.value === 'all') return items;
    const start = (currentPage.value - 1) * perPage.value;
    const end = start + perPage.value;
    return items.slice(start, end);
});

const totalPages = computed(() => {
    if (!customer.value || !customer.value.recent_transactions || perPage.value === 'all') return 1;
    return Math.ceil(customer.value.recent_transactions.length / perPage.value);
});

const transactionForm = useForm({
    student_id: '',
    type: 'deposit', // 'deposit' or 'withdrawal'
    amount: ''
});

const formattedAmount = computed({
    get: () => {
        if (!transactionForm.amount) return '';
        return new Intl.NumberFormat('id-ID').format(transactionForm.amount);
    },
    set: (val) => {
        // Remove non-numeric characters
        const numericVal = String(val).replace(/\D/g, '');
        transactionForm.amount = numericVal ? parseInt(numericVal, 10) : '';
    }
});

const searchCustomer = async () => {
    if (!accountNumber.value) return;
    
    isLoading.value = true;
    searchError.value = '';
    customer.value = null;
    currentPage.value = 1;

    try {
        const response = await axios.post(route('teller.search'), {
            account_number: accountNumber.value
        });
        customer.value = response.data;
        transactionForm.student_id = customer.value.id;
        transactionForm.amount = ''; // reset
    } catch (error) {
        searchError.value = error.response?.data?.error || 'Nasabah tidak ditemukan';
        Swal.fire({
            icon: 'error',
            title: 'Gagal',
            text: searchError.value
        });
    } finally {
        isLoading.value = false;
    }
};

const formatRupiah = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(value);
};

const formatDate = (dateString) => {
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric', hour: '2-digit', minute: '2-digit' };
    return new Date(dateString).toLocaleDateString('id-ID', options);
};

const submitTransaction = () => {
    if (!transactionForm.amount || transactionForm.amount < 1000) {
        Swal.fire('Peringatan', 'Minimal nominal transaksi adalah Rp 1.000', 'warning');
        return;
    }

    if (transactionForm.type === 'withdrawal' && transactionForm.amount > customer.value.balance) {
        Swal.fire('Gagal', 'Saldo nasabah tidak mencukupi untuk penarikan ini.', 'error');
        return;
    }

    const typeText = transactionForm.type === 'deposit' ? 'Setor' : 'Tarik';

    Swal.fire({
        title: 'Konfirmasi Transaksi',
        text: `Anda yakin akan memproses ${typeText} sejumlah ${formatRupiah(transactionForm.amount)} untuk ${customer.value.name}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#0f7632',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, Proses Sekarang!'
    }).then((result) => {
        if (result.isConfirmed) {
            transactionForm.post(route('teller.store'), {
                preserveScroll: true,
                onSuccess: (page) => {
                    Swal.fire('Berhasil!', page.props.flash.success, 'success');
                    // Refresh data
                    searchCustomer();
                },
                onError: (errors) => {
                    Swal.fire('Gagal!', errors.amount || 'Terjadi kesalahan sistem', 'error');
                }
            });
        }
    });
};
</script>

<template>
    <Head title="Layanan Teller" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="p-2 bg-gradient-to-br from-[#0f7632] to-[#f59e0b] rounded-xl shadow-lg">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <h2 class="font-extrabold text-2xl text-gray-800 leading-tight">Layanan Teller</h2>
                    <p class="text-sm text-gray-500 font-medium">Transaksi Tabungan & Penarikan Tunai</p>
                </div>
            </div>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Search Section -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                    <label class="block text-sm font-bold text-gray-700 mb-2">Cari Nomor Rekening / Nomor Induk (NISN)</label>
                    <div class="flex gap-4">
                        <div class="relative flex-1">
                            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input 
                                v-model="accountNumber"
                                @keyup.enter="searchCustomer"
                                type="text" 
                                class="pl-11 w-full border-gray-300 focus:border-[#0f7632] focus:ring-[#0f7632] rounded-xl shadow-sm py-3 text-lg font-bold tracking-wider uppercase"
                                placeholder="Masukkan Nomor Rekening / NISN..."
                            >
                        </div>
                        <button 
                            @click="searchCustomer" 
                            :disabled="isLoading"
                            class="px-8 py-3 bg-[#0f7632] hover:bg-[#064e3b] text-white font-bold rounded-xl shadow-md transition-all flex items-center gap-2 disabled:opacity-75"
                        >
                            <svg v-if="isLoading" class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            <span>Cari Nasabah</span>
                        </button>
                    </div>
                </div>

                <div v-if="customer" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Bio & Balance -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative overflow-hidden">
                        <div class="absolute top-0 right-0 p-4 opacity-5">
                            <svg class="w-32 h-32 text-[#0f7632]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4 border-b pb-2">Informasi Biodata</h3>
                        
                        <div class="space-y-4 relative z-10">
                            <div>
                                <p class="text-sm text-gray-500 font-medium">Nama Nasabah</p>
                                <p class="text-2xl font-black text-gray-800">{{ customer.name }}</p>
                            </div>
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-500 font-medium">Nomor Rekening</p>
                                    <p class="text-lg font-bold text-gray-700 tracking-wider">{{ customer.account_number }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="px-3 py-1 bg-blue-100 text-blue-800 text-xs font-bold rounded-full border border-blue-200">Kategori: {{ customer.type }}</span>
                                </div>
                            </div>
                            
                            <div class="mt-6 pt-6 border-t border-dashed border-gray-200">
                                <p class="text-sm text-gray-500 font-bold mb-1">Total Saldo Saat Ini</p>
                                <p class="text-4xl font-black text-green-600">{{ formatRupiah(customer.balance) }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Transaction Form -->
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 border-l-4 border-l-[#f59e0b]">
                        <h3 class="text-xs font-bold text-[#f59e0b] uppercase tracking-widest mb-4 border-b pb-2">Proses Transaksi</h3>
                        
                        <form @submit.prevent="submitTransaction" class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Jenis Transaksi</label>
                                <div class="grid grid-cols-2 gap-4">
                                    <label class="cursor-pointer relative">
                                        <input type="radio" v-model="transactionForm.type" value="deposit" class="peer sr-only">
                                        <div class="p-4 rounded-xl border-2 peer-checked:border-[#0f7632] peer-checked:bg-green-50 hover:bg-gray-50 transition-all text-center">
                                            <div class="w-8 h-8 mx-auto bg-green-100 text-green-600 rounded-full flex items-center justify-center mb-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path></svg>
                                            </div>
                                            <span class="font-bold text-green-700">Setoran Uang</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer relative">
                                        <input type="radio" v-model="transactionForm.type" value="withdrawal" class="peer sr-only">
                                        <div class="p-4 rounded-xl border-2 peer-checked:border-orange-500 peer-checked:bg-orange-50 hover:bg-gray-50 transition-all text-center">
                                            <div class="w-8 h-8 mx-auto bg-orange-100 text-orange-600 rounded-full flex items-center justify-center mb-2">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 10l7-7m0 0l7 7m-7-7v18"></path></svg>
                                            </div>
                                            <span class="font-bold text-orange-700">Tarik Tunai</span>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Nominal Transaksi (Rp)</label>
                                <input 
                                    v-model="formattedAmount"
                                    type="text" 
                                    class="w-full border-gray-300 focus:border-[#f59e0b] focus:ring-[#f59e0b] rounded-xl shadow-sm py-4 text-2xl font-black text-right"
                                    placeholder="0"
                                >
                            </div>

                            <button 
                                type="submit" 
                                :disabled="transactionForm.processing"
                                class="w-full py-4 bg-gradient-to-r from-[#0f7632] to-[#f59e0b] hover:from-[#064e3b] hover:to-[#0f7632] text-white font-bold rounded-xl shadow-lg transition-all active:scale-[0.98] text-lg uppercase tracking-wider disabled:opacity-75"
                            >
                                Proses Transaksi
                            </button>
                        </form>
                    </div>

                    <!-- History Table -->
                    <div class="md:col-span-2 bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <div class="flex justify-between items-center mb-4 border-b pb-2">
                            <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest">Riwayat Transaksi Terakhir</h3>
                            <div v-if="customer?.recent_transactions?.length > 0" class="flex items-center gap-2">
                                <label class="text-xs text-gray-500 font-bold">Tampilkan:</label>
                                <select v-model="perPage" @change="currentPage = 1" class="text-xs border-gray-300 rounded-lg shadow-sm focus:border-[#0f7632] focus:ring-[#0f7632] py-1 pl-2 pr-8">
                                    <option :value="10">10</option>
                                    <option :value="15">15</option>
                                    <option :value="25">25</option>
                                    <option :value="50">50</option>
                                    <option value="all">Semua</option>
                                </select>
                            </div>
                        </div>
                        
                        <div v-if="customer.recent_transactions?.length > 0" class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead>
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">No</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Waktu Transaksi</th>
                                        <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">Tipe</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Nominal</th>
                                        <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase tracking-wider">Sisa Saldo</th>
                                        <th class="px-6 py-3 text-center text-xs font-bold text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    <tr v-for="(trx, index) in paginatedTransactions" :key="trx.id" class="hover:bg-gray-50">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ (currentPage - 1) * (perPage === 'all' ? paginatedTransactions.length : perPage) + index + 1 }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900 font-medium">
                                            {{ formatDate(trx.created_at) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span :class="trx.type === 'deposit' ? 'bg-green-100 text-green-800 border-green-200' : 'bg-orange-100 text-orange-800 border-orange-200'" class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full border">
                                                {{ trx.type === 'deposit' ? 'Setoran' : 'Penarikan' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-right" :class="trx.type === 'deposit' ? 'text-green-600' : 'text-orange-600'">
                                            {{ trx.type === 'deposit' ? '+' : '-' }} {{ formatRupiah(trx.amount) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900 text-right">
                                            {{ formatRupiah(trx.balance_after) }}
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-center">
                                            <a :href="route('teller.receipt', trx.id)" target="_blank" class="inline-flex items-center text-xs font-bold text-[#005fb8] bg-blue-50 hover:bg-blue-100 px-3 py-1.5 rounded-lg border border-blue-200 transition-colors">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                                Cetak
                                            </a>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                            
                            <div v-if="perPage !== 'all' && totalPages > 1" class="flex justify-between items-center mt-4">
                                <span class="text-xs text-gray-500">Halaman {{ currentPage }} dari {{ totalPages }}</span>
                                <div class="flex gap-1">
                                    <button @click="currentPage--" :disabled="currentPage === 1" class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-bold hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">Sebelumnya</button>
                                    <button @click="currentPage++" :disabled="currentPage === totalPages" class="px-3 py-1 bg-gray-100 text-gray-600 rounded-lg text-xs font-bold hover:bg-gray-200 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">Selanjutnya</button>
                                </div>
                            </div>
                        </div>
                        <div v-else class="text-center py-8">
                            <p class="text-gray-500 text-sm">Belum ada riwayat transaksi.</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>

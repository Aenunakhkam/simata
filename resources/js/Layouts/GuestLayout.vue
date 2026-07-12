<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { watch, onMounted } from 'vue';
import Swal from 'sweetalert2';

const page = usePage();

const showNotification = () => {
    const flash = page.props.flash;
    if (flash?.success) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: flash.success,
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
        });
    }
    if (flash?.status) {
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: 'success',
            title: flash.status,
            showConfirmButton: false,
            timer: 4000,
            timerProgressBar: true,
        });
    }
};

onMounted(() => {
    showNotification();
});

watch(() => page.props.flash, () => {
    showNotification();
}, { deep: true });
</script>

<template>
    <!-- Background Merah Maroon dengan Geometric Kotak Transparan -->
    <div class="min-h-screen font-sans bg-[#7B1113] relative overflow-hidden flex items-center justify-center selection:bg-white selection:text-[#7B1113]">
        
        <!-- Geometric Kotak Transparan -->
        <div class="absolute top-[-5%] left-[-5%] w-48 h-48 sm:w-72 sm:h-72 bg-white/5 border border-white/10 rotate-12 rounded-3xl pointer-events-none"></div>
        <div class="absolute bottom-[5%] right-[-10%] w-64 h-64 sm:w-[450px] sm:h-[450px] bg-white/5 border border-white/10 rotate-45 rounded-[40px] pointer-events-none"></div>
        <div class="hidden lg:block absolute top-[20%] right-[10%] w-32 h-32 bg-white/10 border border-white/20 -rotate-12 rounded-2xl pointer-events-none"></div>
        <div class="absolute bottom-[25%] left-[5%] w-32 h-32 sm:w-48 sm:h-48 bg-white/5 border border-white/10 rotate-45 rounded-2xl pointer-events-none"></div>
        
        <!-- Subtle Pattern Overlap -->
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+PHBhdGggZD0iTTAgMGg0MHY0MEgwVjB6bTEgMWgzOHYzOEgxVjF6IiBmaWxsPSJyZ2JhKDI1NSwyNTUsMjU1LDAuMDUpIiBmaWxsLXJ1bGU9ImV2ZW5vZGQiLz48L3N2Zz4=')] opacity-50 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-6 lg:px-12 w-full grid grid-cols-1 lg:grid-cols-2 gap-12 lg:gap-20 items-center relative z-10 min-h-screen py-12">
            
            <!-- Kiri: Info Text -->
            <div class="flex flex-col justify-center text-center lg:text-left items-center lg:items-start max-w-xl mx-auto lg:mx-0 text-white">
                
                <!-- Logo -->
                <div class="flex items-center space-x-3.5 mb-8">
                    <div class="w-12 h-12 shrink-0 rounded-xl bg-white flex items-center justify-center shadow-lg shadow-black/10">
                        <svg class="w-7 h-7 text-[#7B1113]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                    </div>
                    <div class="flex flex-col text-left">
                        <span class="text-3xl font-black text-white tracking-tighter font-montserrat leading-none">SIMATA</span>
                        <span class="text-[10px] font-bold text-white/70 tracking-widest uppercase mt-1">E-Learning Portal</span>
                    </div>
                </div>

                <div class="inline-flex items-center gap-2 px-4 py-1.5 bg-white/10 border border-white/20 text-white text-xs font-bold rounded-full mb-6 backdrop-blur-sm w-max">
                    Platform Pembelajaran Digital
                </div>
                
                <h1 class="text-4xl sm:text-5xl xl:text-6xl font-extrabold text-white tracking-tight font-montserrat mb-6 leading-[1.2] xl:leading-[1.1]">
                    Belajar Lebih <br class="hidden xl:block">
                    <span class="text-[#D4AF37]">Mudah & Interaktif</span>
                </h1>
                
                <p class="text-white/80 text-base sm:text-lg font-medium mb-12 leading-relaxed max-w-lg">
                    Tingkatkan keahlianmu bersama instruktur terbaik. Akses materi pembelajaran secara terstruktur di mana saja dan kapan saja.
                </p>

            </div>

            <!-- Kanan: Form Card Putih -->
            <div class="flex justify-center lg:justify-end w-full">
                <div class="w-full max-w-[440px] bg-white rounded-[32px] shadow-2xl shadow-black/20 p-8 sm:p-10 relative overflow-hidden">
                    <slot />
                </div>
            </div>

        </div>
    </div>
</template>

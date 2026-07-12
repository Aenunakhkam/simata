<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },
});
</script>

<template>
    <button
        :type="type"
        class="inline-flex items-center justify-center rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-bold uppercase tracking-widest text-slate-700 shadow-sm transition-all duration-200 ease-in-out hover:bg-slate-50 hover:border-slate-400 focus:outline-none focus:ring-2 focus:ring-[#7B1113] focus:ring-offset-2 active:scale-[0.98] disabled:opacity-25"
    >
        <slot />
    </button>
</template>

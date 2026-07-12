<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
    },
    icon: {
        type: String,
        default: ''
    }
});

const classes = computed(() =>
    props.active
        ? 'flex items-center px-5 py-3 mt-1 text-sm font-bold bg-[#D4AF37]/10 text-[#D4AF37] border-l-[3px] border-[#D4AF37] transition-all duration-200'
        : 'flex items-center px-5 py-3 mt-1 text-sm font-medium text-white/60 hover:bg-black/10 hover:text-white border-l-[3px] border-transparent transition-all duration-200'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <span class="w-5 h-5 mr-3 flex items-center justify-center shrink-0" v-html="icon" v-if="icon"></span>
        <slot />
    </Link>
</template>

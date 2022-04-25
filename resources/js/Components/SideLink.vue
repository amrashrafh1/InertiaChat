<script setup>
    import {
        computed
    } from 'vue';
    import {
        Link
    } from '@inertiajs/inertia-vue3';

    const props = defineProps(['href', 'active']);

    const classes = computed(() => props.active ?
        'block py-4 px-12 border-l-4 border-gray-800 bg-gray-300 text-black hover:bg-gray-300 hover:text-black' :
        'block py-4 px-12 border-l-4 text-gray-600 hover:bg-gray-300 hover:text-black'
    );

</script>

<template>
    <Link :href="href" :class="classes">
        <span class="inline-block align-text-bottom mr-2">
            <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                viewBox="0 0 24 24" class="w-4 h-4">
                <path
                    d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                </path>
            </svg>
        </span>
        <slot />
    </Link>
</template>

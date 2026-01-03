<script setup>
import { Link } from '@inertiajs/vue3';
defineProps({
    href: { type: String, default: null },
    method: { type: String, default: 'get' },
    data: { type: Object, default: () => ({}) },
    as: { type: String, default: 'a' },
    type: { type: String, default: 'submit' },
});
</script>

<template>
    <Link
        v-if="href"
        :href="href"
        :method="method"
        :data="data"
        :as="as"
        class="inline-flex items-center justify-center rounded-full surf-button px-5 py-2 text-sm font-semibold tracking-wide transition transform hover:-translate-y-0.5 active:scale-95 focus:outline-none focus:ring-2 focus:ring-amber-300"
    >
        <slot />
    </Link>
    <button
        v-else
        :type="type"
        class="inline-flex items-center justify-center rounded-full surf-button px-5 py-2 text-sm font-semibold tracking-wide transition transform hover:-translate-y-0.5 active:scale-95 focus:outline-none focus:ring-2 focus:ring-amber-300"
    >
        <slot />
    </button>
</template>

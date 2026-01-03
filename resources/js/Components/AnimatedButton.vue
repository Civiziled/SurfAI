<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
  type: { type: String, default: 'button' },
  loading: { type: Boolean, default: false },
  href: { type: String, default: null },
  method: { type: String, default: 'get' },
  data: { type: Object, default: () => ({}) },
  as: { type: String, default: 'a' },
});

const emit = defineEmits(['click']);

const handleClick = (e) => {
  if ((loading)) return;
  emit('click', e);
};
</script>

<template>
  <Link
    v-if="href"
    :href="href"
    :method="method"
    :data="data"
    :as="as"
    class="surf-button inline-flex items-center gap-2 px-4 py-2 rounded-lg shadow-md transform transition-transform duration-200 hover:-translate-y-0.5 active:scale-95 disabled:opacity-60"
  >
    <span><slot /></span>
  </Link>
  <button
    v-else
    :type="type"
    @click="handleClick"
    class="surf-button inline-flex items-center gap-2 px-4 py-2 rounded-lg shadow-md transform transition-transform duration-200 hover:-translate-y-0.5 active:scale-95 disabled:opacity-60"
    :aria-busy="loading"
  >
    <svg v-if="loading" class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
    </svg>
    <span><slot /></span>
  </button>
</template>

<style scoped>
/* Extra micro interaction */
.surf-button:hover { box-shadow: 0 14px 40px rgba(126,211,229,0.12); }
</style>

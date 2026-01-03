<script setup lang="ts">
const props = defineProps<{ text: string }>();
</script>

<template>
  <transition name="fade-slide">
    <div v-if="text" class="p-3 bg-white/90 dark:bg-gray-800/80 border rounded-lg shadow-sm">
      <pre class="whitespace-pre-wrap text-sm leading-relaxed">{{ text }}</pre>
    </div>
  </transition>
</template>

<style scoped>
.fade-slide-enter-from { opacity: 0; transform: translateY(8px); }
.fade-slide-enter-active { transition: all 280ms cubic-bezier(.2,.9,.2,1); }
.fade-slide-leave-to { opacity: 0; transform: translateY(6px); }
</style>

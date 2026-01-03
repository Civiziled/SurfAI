<script setup>
const props = defineProps({
  author: { type: String, default: 'ai' },
  text: { type: String, default: '' },
  time: { type: String, default: '' },
});
</script>

<template>
  <transition name="fade-slide">
    <div class="p-3 rounded-lg shadow-sm bg-gray-50 dark:bg-gray-700" v-if="text">
      <div class="flex items-start gap-3">
        <div class="flex-shrink-0">
          <div class="h-8 w-8 rounded-full bg-indigo-400 text-white flex items-center justify-center">{{ author.charAt(0).toUpperCase() }}</div>
        </div>
        <div class="flex-1">
          <div class="text-sm leading-relaxed text-gray-900 dark:text-gray-100">{{ text }}</div>
          <div v-if="time" class="text-xs text-gray-400 mt-1">{{ time }}</div>
        </div>
      </div>
    </div>
  </transition>
</template>

<style scoped>
.fade-slide-enter-from { opacity: 0; transform: translateY(8px); }
.fade-slide-enter-active { transition: all 260ms cubic-bezier(.2,.9,.2,1); }
.fade-slide-leave-to { opacity: 0; transform: translateY(6px); }
</style>

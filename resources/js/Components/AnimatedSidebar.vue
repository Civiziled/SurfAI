<script setup>
const props = defineProps({ title: { type: String, default: '' } });
</script>

<template>
  <aside class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
    <div class="p-6 text-gray-900 dark:text-gray-100">
      <h3 v-if="props.title" class="text-lg font-semibold mb-4">{{ props.title }}</h3>
      <div>
        <slot />
      </div>
    </div>
  </aside>
</template>

<style scoped>
aside { transition: transform 260ms cubic-bezier(.2,.9,.2,1), box-shadow 200ms; }
aside:hover { transform: translateY(-4px); }
</style>

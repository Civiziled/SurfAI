<script setup>
import { ref, onMounted } from 'vue';

const isDark = ref(false);

const toggleTheme = () => {
    // If no manual preference was set, we might be in 'system' mode.
    // Switching toggles to the OPPOSITE of current state.
    const currentIsDark = document.documentElement.classList.contains('dark');
    
    if (currentIsDark) {
        document.documentElement.classList.remove('dark');
        localStorage.theme = 'light';
        isDark.value = false;
    } else {
        document.documentElement.classList.add('dark');
        localStorage.theme = 'dark';
        isDark.value = true;
    }
};

onMounted(() => {
    // Sync state with DOM on mount
    isDark.value = document.documentElement.classList.contains('dark');
});
</script>

<template>
    <button 
        @click="toggleTheme" 
        class="p-2 rounded-full transition-all duration-300 ease-in-out focus:outline-none"
        :class="isDark ? 'bg-slate-800 text-yellow-400 hover:bg-slate-700' : 'bg-sky-100 text-orange-500 hover:bg-sky-200'"
        title="Toggle Dark Mode"
    >
        <transition name="rotate" mode="out-in">
            <svg v-if="isDark" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
            </svg>
            <svg v-else xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
        </transition>
    </button>
</template>

<style scoped>
.rotate-enter-active,
.rotate-leave-active {
  transition: all 0.3s ease;
}

.rotate-enter-from,
.rotate-leave-to {
  transform: rotate(90deg);
  opacity: 0;
}
</style>

<script setup>
import { Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
</script>

<template>
    <div class="min-h-screen flex bg-white dark:bg-slate-900 transition-colors duration-500">
        <!-- Left Side: Visual / Brand -->
        <div class="hidden lg:flex lg:w-1/2 relative overflow-hidden bg-surf-ocean/20 dark:bg-slate-800">
            <img 
                src="https://images.unsplash.com/photo-1502680390469-be75c86b636f?q=80&w=2940&auto=format&fit=crop" 
                alt="Surfer catching a wave" 
                class="absolute inset-0 w-full h-full object-cover"
            />
            <div class="absolute inset-0 bg-gradient-to-tr from-surf-ocean/80 to-transparent dark:from-slate-900/80 dark:to-slate-900/40"></div>
            
            <div class="relative z-10 p-12 flex flex-col justify-between h-full text-white">
                <div>
                    <Link href="/">
                        <ApplicationLogo class="w-16 h-16 fill-current text-white mb-6" />
                    </Link>
                    <h1 class="text-4xl font-display font-bold leading-tight max-w-md">
                        Catch the perfect wave with SurferAI.
                    </h1>
                </div>
                
                <div class="backdrop-blur-sm bg-white/10 p-6 rounded-2xl border border-white/20">
                    <p class="text-lg font-medium italic">"The best way to predict the future is to create it."</p>
                    <p class="mt-2 text-sm opacity-80">— SurferAI Philosophy</p>
                </div>
            </div>
        </div>

        <!-- Right Side: Content / Form -->
        <div class="w-full lg:w-1/2 flex flex-col justify-center items-center p-8 lg:p-12 relative">
             <div class="absolute top-6 right-6">
                 <ThemeToggle />
             </div>
             <!-- Mobile Logo -->
            <div class="lg:hidden absolute top-6 left-6">
                 <Link href="/">
                    <ApplicationLogo class="w-12 h-12 text-surf-ocean dark:text-white" />
                </Link>
            </div>

            <div class="w-full max-w-md">
                <slot />
            </div>
        </div>
    </div>
</template>

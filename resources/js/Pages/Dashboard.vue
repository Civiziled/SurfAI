<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import AnimatedButton from '@/Components/AnimatedButton.vue';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-display text-2xl font-bold text-slate-800 leading-tight">
                Dashboard <span class="text-2xl ml-2">🏄‍♂️</span>
            </h2>
        </template>

        <div class="py-12 px-4 sm:px-6 lg:px-8 max-w-7xl mx-auto space-y-8">
            
            <!-- Welcome Hero Card -->
            <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-indigo-500 via-purple-500 to-pink-500 shadow-2xl transform hover:scale-[1.01] transition-all duration-500">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-20 mix-blend-overlay"></div>
                <div class="absolute -right-20 -top-20 w-80 h-80 bg-white/20 rounded-full blur-3xl animate-pulse-slow"></div>
                <div class="absolute -left-20 -bottom-20 w-60 h-60 bg-amber-300/30 rounded-full blur-3xl animate-pulse-slow delay-700"></div>

                <div class="relative z-10 p-8 md:p-12 text-white flex flex-col md:flex-row items-center justify-between gap-8">
                    <div class="space-y-4 max-w-2xl">
                        <div class="inline-block px-4 py-1.5 rounded-full bg-white/20 backdrop-blur-md border border-white/30 text-sm font-medium shadow-sm mb-2 animate-fade-in-up">
                            ✨ Ready to ride the wave?
                        </div>
                        <h1 class="text-4xl md:text-5xl font-display font-bold leading-tight drop-shadow-lg animate-fade-in-up delay-100">
                            Aloha, Welcome Back! 🌺
                        </h1>
                        <p class="text-lg md:text-xl text-indigo-50 max-w-lg leading-relaxed animate-fade-in-up delay-200">
                            The ocean of productivity is calm and ready. Dive into your AI chats or check your latest stats.
                        </p>
                    </div>
                    
                    <div class="flex-shrink-0 animate-fade-in-up delay-300">
                         <Link :href="route('chat.index')" class="group relative px-8 py-4 bg-white text-indigo-600 font-bold rounded-2xl shadow-xl hover:shadow-2xl hover:bg-slate-50 transition-all duration-300 overflow-hidden flex items-center gap-3">
                            <span class="relative z-10">Start Exploring</span>
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5 relative z-10 group-hover:translate-x-1 transition-transform">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                            </svg>
                            <div class="absolute inset-0 bg-gradient-to-r from-amber-200/50 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                        </Link>
                    </div>
                </div>
            </div>

            <!-- Main Grid -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Stats Card: My Vibe -->
                <div class="md:col-span-2 grid grid-cols-1 sm:grid-cols-2 gap-6">
                     <div class="glass-card hover-card p-6 flex flex-col justify-between h-48 group">
                        <div class="flex justify-between items-start">
                            <div class="p-3 rounded-2xl bg-sky-100 text-sky-600 group-hover:bg-sky-500 group-hover:text-white transition-colors duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15a4.5 4.5 0 0 0 4.5 4.5H18a3.75 3.75 0 0 0 1.332-7.257 3 3 0 0 0-3.758-3.848 5.25 5.25 0 0 0-10.233 2.33A4.502 4.502 0 0 0 2.25 15Z" />
                                </svg>
                            </div>
                            <span class="text-xs font-bold px-2 py-1 bg-green-100 text-green-700 rounded-lg">+12% this week</span>
                        </div>
                        <div>
                            <p class="text-slate-500 font-medium text-sm">Active Conversations</p>
                            <h4 class="text-3xl font-bold text-slate-800">24</h4>
                        </div>
                    </div>

                    <div class="glass-card hover-card p-6 flex flex-col justify-between h-48 group">
                        <div class="flex justify-between items-start">
                             <div class="p-3 rounded-2xl bg-amber-100 text-amber-600 group-hover:bg-amber-500 group-hover:text-white transition-colors duration-300">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-8 h-8">
                                  <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904 9 18.75l-.813-2.846a4.5 4.5 0 0 0-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 0 0 3.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 0 0 3.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 0 0-3.09 3.09ZM18.259 8.715 18 9.75l-.259-1.035a3.375 3.375 0 0 0-2.455-2.456L14.25 6l1.036-.259a3.375 3.375 0 0 0 2.455-2.456L18 2.25l.259 1.035a3.375 3.375 0 0 0 2.456 2.456L21.75 6l-1.035.259a3.375 3.375 0 0 0-2.456 2.456ZM16.894 20.567 16.5 21.75l-.394-1.183a2.25 2.25 0 0 0-1.423-1.423L13.5 18.75l1.183-.394a2.25 2.25 0 0 0 1.423-1.423l.394-1.183.394 1.183a2.25 2.25 0 0 0 1.423 1.423l1.183.394-1.183.394a2.25 2.25 0 0 0-1.423 1.423Z" />
                                </svg>
                            </div>
                            <span class="text-xs font-bold px-2 py-1 bg-indigo-100 text-indigo-700 rounded-lg">Pro Plan</span>
                        </div>
                        <div>
                            <p class="text-slate-500 font-medium text-sm">Tokens Used</p>
                            <h4 class="text-3xl font-bold text-slate-800">142k</h4>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions / Menu -->
                <div class="glass-card p-6 flex flex-col justify-center space-y-4 border-l-4 border-l-surf-teal">
                    <h3 class="font-display text-xl font-semibold text-slate-800 mb-2">Quick Dive 🐬</h3>
                    
                    <Link :href="route('chat.create')" method="post" as="button" class="w-full text-left group flex items-center p-3 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-all">
                        <div class="h-10 w-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                            +
                        </div>
                        <div>
                            <p class="font-semibold text-slate-800">New Chat</p>
                            <p class="text-xs text-slate-500">Start a fresh conversation</p>
                        </div>
                        <div class="ml-auto opacity-0 group-hover:opacity-100 transition-opacity">→</div>
                    </Link>

                    <Link :href="route('profile.edit')" class="w-full text-left group flex items-center p-3 rounded-xl hover:bg-slate-50 border border-transparent hover:border-slate-200 transition-all">
                        <div class="h-10 w-10 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center mr-4 group-hover:scale-110 transition-transform">
                            ⚙️
                        </div>
                        <div>
                            <p class="font-semibold text-slate-800">Settings</p>
                            <p class="text-xs text-slate-500">Manage your profile</p>
                        </div>
                        <div class="ml-auto opacity-0 group-hover:opacity-100 transition-opacity">→</div>
                    </Link>
                </div>

            </div>

             <!-- Recent Waves Section -->
            <div class="glass-card p-8 shadow-xl">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-xl font-bold text-slate-800 flex items-center gap-2">
                        <span>Recent Waves</span>
                        <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-500 border border-slate-200">History</span>
                    </h3>
                    <Link :href="route('chat.index')" class="text-sm font-medium text-surf-ocean hover:underline hover:text-surf-teal transition-colors">View All</Link>
                </div>
                
                <div class="space-y-3">
                    <!-- Placeholder items since we might not have 'recent conversations' prop here yet. -->
                    <!-- We can just static placeholders or leave it empty if cleaner. Let's add static visual placeholders for the 'vibe' -->
                    <div v-for="i in 3" :key="i" class="flex items-center justify-between p-4 rounded-2xl bg-slate-50/50 hover:bg-white border border-transparent hover:border-slate-200 hover:shadow-md transition-all cursor-pointer group">
                        <div class="flex items-center gap-4">
                            <div class="h-10 w-10 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white shadow-sm font-bold text-sm">
                                AI
                            </div>
                            <div>
                                <p class="font-semibold text-slate-700 group-hover:text-indigo-600 transition-colors">Surfing the web for ideas...</p>
                                <p class="text-xs text-slate-400">2 hours ago • GPT-4</p>
                            </div>
                        </div>
                        <div class="text-slate-300 group-hover:text-indigo-400 transition-colors">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                              <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.glass-card {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(12px);
    border: 1px solid rgba(255, 255, 255, 0.6);
    border-radius: 1.5rem; /* 24px */
    box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05);
}

.hover-card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.hover-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.1);
    background: rgba(255, 255, 255, 0.85);
}

.animate-pulse-slow {
    animation: pulse 8s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 0.3; transform: scale(1); }
    50% { opacity: 0.6; transform: scale(1.1); }
}

.animate-fade-in-up {
    animation: fadeInUp 0.8s cubic-bezier(0.16, 1, 0.3, 1) forwards;
    opacity: 0;
    transform: translateY(20px);
}

@keyframes fadeInUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.delay-100 { animation-delay: 0.1s; }
.delay-200 { animation-delay: 0.2s; }
.delay-300 { animation-delay: 0.3s; }
.delay-700 { animation-delay: 0.7s; }
</style>

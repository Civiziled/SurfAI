<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import ThemeToggle from '@/Components/ThemeToggle.vue';
import { Link } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
</script>

<template>
    <div>
        <div class="min-h-screen bg-surf-sand/30 dark:bg-slate-950 bg-[url('/images/hero-bg.png')] dark:bg-none bg-fixed bg-cover bg-center bg-no-repeat bg-blend-overlay transition-colors duration-500">
            <div class="fixed inset-0 bg-white/40 dark:bg-slate-950/90 backdrop-blur-[2px] z-0 pointer-events-none"></div>
            
            <!-- Innovative Floating Navbar -->
            <div class="fixed top-4 left-0 right-0 z-50 px-4 md:px-8">
                <nav class="mx-auto max-w-5xl bg-white/70 dark:bg-slate-900/80 backdrop-blur-xl border border-white/50 dark:border-slate-700/50 rounded-full shadow-lg hover:shadow-xl transition-all duration-300">
                    <div class="px-6 relative flex items-center justify-between h-14 md:h-16">
                        <!-- Brand -->
                        <div class="flex items-center gap-2">
                            <Link :href="route('dashboard')" class="group flex items-center gap-2">
                                <span class="text-2xl font-display bg-gradient-to-r from-surf-teal to-surf-ocean bg-clip-text text-transparent group-hover:scale-105 transition-transform">
                                    SurferAI
                                </span>
                                <span class="text-2xl animate-bounce delay-700">🏄‍♂️</span>
                            </Link>
                        </div>

                        <!-- Desktop Navigation -->
                        <div class="hidden md:flex items-center space-x-1 bg-white/40 rounded-full p-1 ml-4 border border-white/40 shadow-inner">
                             <NavLink :href="route('dashboard')" :active="route().current('dashboard')" class="rounded-full px-5 py-1.5 text-sm font-medium transition-all duration-300" :class="route().current('dashboard') ? 'bg-white dark:bg-slate-800 text-surf-ocean shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-surf-ocean hover:bg-white/50 dark:hover:bg-slate-800/50'">
                                Dashboard
                            </NavLink>
                            <NavLink :href="route('chat.index')" :active="route().current('chat.index')" class="rounded-full px-5 py-1.5 text-sm font-medium transition-all duration-300" :class="route().current('chat.index') ? 'bg-white dark:bg-slate-800 text-surf-ocean shadow-sm' : 'text-slate-600 dark:text-slate-300 hover:text-surf-ocean hover:bg-white/50 dark:hover:bg-slate-800/50'">
                                Sessions 🌊
                            </NavLink>
                        </div>

                        <!-- User Menu -->
                        <div class="flex items-center gap-3">
                             <ThemeToggle />
                             <!-- Settings Dropdown -->
                             <div class="relative">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <button class="flex items-center gap-2 pl-3 pr-1 py-1 rounded-full bg-gradient-to-r from-surf-ocean/10 to-surf-teal/10 dark:from-surf-ocean/20 dark:to-surf-teal/20 hover:from-surf-ocean/20 hover:to-surf-teal/20 border border-surf-teal/10 dark:border-surf-teal/20 transition-all group">
                                            <span class="text-sm font-bold text-surf-ocean dark:text-surf-teal">{{ $page.props.auth.user.name }}</span>
                                            <div class="h-8 w-8 rounded-full bg-white dark:bg-slate-800 flex items-center justify-center text-surf-ocean shadow-sm group-hover:scale-110 transition-transform">
                                                 <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-4">
                                                  <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A7.5 7.5 0 0 1 4.501 20.118Z" />
                                                </svg>
                                            </div>
                                        </button>
                                    </template>

                                    <template #content>
                                        <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                                            <span class="text-xs text-gray-500 dark:text-gray-400">Compte connecté</span>
                                        </div>
                                        <DropdownLink :href="route('profile.edit')"> Profile 👤 </DropdownLink>
                                        <DropdownLink :href="route('logout')" method="post" as="button" class="text-red-500 hover:text-red-600">
                                            Log Out 👋
                                        </DropdownLink>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <!-- Hamburger (Mobile) -->
                        <div class="-me-2 flex items-center md:hidden ml-4">
                            <button
                                @click="showingNavigationDropdown = !showingNavigationDropdown"
                                class="inline-flex items-center justify-center rounded-full p-2 bg-white/50 dark:bg-slate-800/50 text-surf-ocean hover:bg-white dark:hover:bg-slate-800 focus:outline-none transition"
                            >
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path
                                        :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M4 6h16M4 12h16M4 18h16"
                                    />
                                    <path
                                        :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }"
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12"
                                    />
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Mobile Menu -->
                    <div
                        v-show="showingNavigationDropdown"
                        class="md:hidden border-t border-gray-100/50 dark:border-gray-700/50 p-4 space-y-2 bg-white/90 dark:bg-slate-900/90 backdrop-blur-xl rounded-b-3xl absolute top-full left-0 right-0 mt-2 shadow-xl mx-4 animate-slide-up"
                    >
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')" class="rounded-xl">
                            Dashboard
                        </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('chat.index')" :active="route().current('chat.index')" class="rounded-xl">
                            Sessions 🌊
                        </ResponsiveNavLink>
                        
                        <div class="border-t border-gray-200/50 my-2"></div>
                        
                        <ResponsiveNavLink :href="route('profile.edit')" class="rounded-xl"> Profile </ResponsiveNavLink>
                        <ResponsiveNavLink :href="route('logout')" method="post" as="button" class="text-red-500 rounded-xl">
                            Log Out
                        </ResponsiveNavLink>
                    </div>
                </nav>
            </div>



            <!-- Page Heading -->
            <header class="relative z-10" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <!-- Page Content -->
            <main class="relative z-10">
                <transition name="fade" mode="out-in">
                    <slot />
                </transition>
            </main>
        </div>
    </div>
</template>

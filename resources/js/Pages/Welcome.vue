<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
    laravelVersion: {
        type: String,
        required: true,
    },
    phpVersion: {
        type: String,
        required: true,
    },
});

const faqs = ref([
    {
        question: "Est-ce que l'IA sait surfer ?",
        answer: "Bien sûr ! Elle surfe sur le web à la vitesse de la lumière pour t'apporter les meilleures réponses. 🏄‍♂️",
        open: false
    },
    {
        question: "C'est quoi le 'Mode Chill' ?",
        answer: "C'est notre façon à nous de dire que l'interface est conçue pour ne pas te stresser. Couleurs douces, réponses claires, zéro pression. 🌴",
        open: false
    },
    {
        question: "Puis-je annuler à tout moment ?",
        answer: "Absolument. Pas d'engagement, tu restes libre comme l'air... ou comme une mouette. 🐦",
        open: false
    },
    {
        question: "Mes données sont-elles en sécurité ?",
        answer: "Aussi sûres que dans un coffre-fort au fond de l'océan (mais accessibles pour toi !). Nous prenons la confidentialité très au sérieux. 🔒",
        open: false
    },
    {
        question: "Y a-t-il une application mobile ?",
        answer: "Notre site est 100% responsive, tu peux chatter depuis ta planche (si tu as un téléphone étanche) ! 📱",
        open: false
    }
]);

const toggleFaq = (index) => {
    faqs.value[index].open = !faqs.value[index].open;
};

const isYearly = ref(false);
</script>

<template>
    <Head title="SurferAI - Ride the Wave of Intelligence" />

    <div class="min-h-screen bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 font-sans selection:bg-surf-sunset selection:text-white overflow-x-hidden transition-colors duration-300">
        
        <!-- Navbar -->
        <nav class="fixed w-full z-50 transition-all duration-300 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-100 dark:border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-20 items-center">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center gap-2">
                        <span class="text-4xl">🌊</span>
                        <span class="font-display text-2xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-surf-ocean to-surf-teal">
                            SurferAI
                        </span>
                    </div>
                    
                    <!-- Desktop Menu -->
                    <div v-if="canLogin" class="hidden md:flex gap-4 items-center">
                        <div class="flex gap-6 mr-8 font-medium text-slate-600 dark:text-slate-300">
                            <a href="#features" class="hover:text-surf-ocean transition">Features</a>
                            <a href="#comparison" class="hover:text-surf-ocean transition">Comparison</a>
                            <a href="#pricing" class="hover:text-surf-ocean transition">Pricing</a>
                            <a href="#faq" class="hover:text-surf-ocean transition">FAQ</a>
                        </div>

                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="px-6 py-2 rounded-full font-bold text-white bg-surf-ocean hover:bg-surf-teal transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                        >
                            Dashboard 🏄
                        </Link>
                        <template v-else>
                            <Link
                                :href="route('login')"
                                class="px-5 py-2 rounded-full font-bold text-slate-700 dark:text-slate-200 hover:text-surf-ocean transition"
                            >
                                Log in
                            </Link>
                            <Link
                                v-if="canRegister"
                                :href="route('register')"
                                class="px-6 py-2 rounded-full font-bold text-white bg-surf-sunset hover:bg-surf-coral transition shadow-lg hover:shadow-xl transform hover:-translate-y-0.5"
                            >
                                Get Started Free
                            </Link>
                        </template>
                    </div>

                    <!-- Mobile Menu Button (Simple implementation) -->
                     <div class="md:hidden">
                        <Link
                            v-if="$page.props.auth.user"
                            :href="route('dashboard')"
                            class="p-2 text-surf-ocean font-bold"
                        >
                            Dashboard
                        </Link>
                        <template v-else>
                             <Link :href="route('login')" class="p-2 text-surf-ocean font-bold">Login</Link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative pt-32 pb-16 lg:pt-48 lg:pb-24 overflow-hidden">
             <!-- Background elements -->
            <div class="absolute inset-0 z-0 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-surf-sand/40 via-white to-white"></div>
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 bg-surf-teal/10 rounded-full blur-3xl animate-float"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 bg-surf-sunset/10 rounded-full blur-3xl animate-float" style="animation-delay: 2s"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
                <span class="inline-flex items-center gap-2 py-1.5 px-4 rounded-full bg-white dark:bg-slate-800 border border-surf-ocean/20 dark:border-surf-ocean/10 shadow-sm text-surf-ocean dark:text-surf-teal font-bold text-sm mb-8 animate-pop-in">
                    <span class="relative flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-surf-ocean opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-surf-ocean"></span>
                    </span>
                    The AI Wave is Here
                </span>
                
                <h1 class="text-5xl md:text-7xl font-display font-extrabold text-slate-900 dark:text-white mb-8 leading-tight tracking-tight animate-slide-up">
                    Catch the Perfect Wave of <br />
                    <span class="text-transparent bg-clip-text bg-gradient-to-r from-surf-ocean via-surf-teal to-surf-success">Artificial Intelligence</span>
                </h1>
                
                <p class="mt-4 max-w-2xl mx-auto text-xl text-slate-600 dark:text-slate-400 mb-10 leading-relaxed animate-slide-up" style="animation-delay: 0.1s">
                    Meet your new AI co-pilot. Intelligent, lightning fast, and always chill. Stop drowning in tasks and start riding the wave of productivity. 🤙
                </p>
                
                <div class="flex flex-col sm:flex-row justify-center gap-4 animate-slide-up" style="animation-delay: 0.2s">
                     <Link
                        v-if="!$page.props.auth.user"
                        :href="route('register')"
                        class="px-8 py-4 rounded-full font-bold text-lg text-white bg-slate-900 dark:bg-surf-ocean hover:bg-slate-800 dark:hover:bg-surf-teal shadow-xl hover:shadow-2xl transition transform hover:-translate-y-1"
                    >
                        Start Surfing Free 🥥
                    </Link>
                    <Link
                        v-else
                        :href="route('dashboard')"
                        class="px-8 py-4 rounded-full font-bold text-lg text-white bg-slate-900 dark:bg-surf-ocean hover:bg-slate-800 dark:hover:bg-surf-teal shadow-xl hover:shadow-2xl transition transform hover:-translate-y-1"
                    >
                        Go to Dashboard 🌊
                    </Link>
                    <a href="#features" class="px-8 py-4 rounded-full font-bold text-lg text-slate-600 dark:text-slate-200 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition flex items-center justify-center gap-2 group">
                        Live Demo <span class="group-hover:translate-x-1 transition-transform">→</span>
                    </a>
                </div>
                
                <div class="mt-12 text-sm text-slate-400 font-medium">
                    No credit card required · Free 14-day Pro trial · Cancel anytime
                </div>
            </div>
            
            <!-- Trusted By Section -->
            <div class="mt-20 pt-10 border-t border-slate-100/50 bg-white/50 backdrop-blur-sm">
                <p class="text-center text-sm font-bold text-slate-400 uppercase tracking-widest mb-8">Trusted by teams riding the wave</p>
                <div class="flex flex-wrap justify-center items-center gap-12 opacity-60 grayscale hover:grayscale-0 transition-all duration-500">
                    <!-- Logos (Using text for now as placeholders, but styled like logos) -->
                    <span class="text-2xl font-black text-slate-700 dark:text-slate-400 italic">Waave</span>
                    <span class="text-xl font-bold text-slate-600 dark:text-slate-400 tracking-tighter">OCEANIC</span>
                    <span class="text-2xl font-serif text-slate-800 dark:text-slate-300 font-bold">BlueWater</span>
                    <span class="text-xl font-sans font-extrabold text-slate-700 dark:text-slate-400">SURF&CO</span>
                    <span class="text-2xl font-display text-slate-600 dark:text-slate-400">AlohaTech</span>
                </div>
            </div>
        </section>

        <!-- Hero Image Mockup Area -->
        <div class="px-4 sm:px-6 lg:px-8 -mt-12 mb-24 relative z-20">
             <div class="max-w-6xl mx-auto rounded-2xl bg-slate-900/5 p-2 ring-1 ring-inset ring-slate-900/10 lg:rounded-[2rem] lg:p-4 shadow-2xl backdrop-blur-xl">
                 <div class="bg-white dark:bg-slate-800 rounded-xl shadow-inner overflow-hidden border border-slate-200 dark:border-slate-700 aspect-[16/9] flex items-center justify-center bg-cover bg-center" style="background-image: url('/images/hero-bg.png');">
                     <div class="glass-card p-12 text-center max-w-2xl mx-auto dark:bg-slate-900/80 dark:backdrop-blur-xl dark:rounded-xl">
                        <h3 class="text-3xl font-display mb-4 text-slate-800 dark:text-white">Your Productivity OS</h3>
                        <p class="text-slate-600 dark:text-slate-300">Experience the flow state like never before. The mockup of your actual dashboard would appear here.</p>
                     </div>
                 </div>
            </div>
        </div>

        <!-- Features Section (Alternating) -->
        <section id="features" class="py-24 bg-white dark:bg-slate-900 relative">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-32">
                <!-- Feature 1 -->
                <div class="flex flex-col lg:flex-row items-center gap-16">
                    <div class="lg:w-1/2">
                        <div class="bg-surf-ocean/10 w-12 h-12 rounded-2xl flex items-center justify-center text-2xl mb-6 text-surf-ocean">🧠</div>
                        <h2 class="text-4xl font-display font-bold text-slate-900 dark:text-white mb-6">Smarter than your average surfboard.</h2>
                        <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed mb-8">
                            Our AI models aren't just trained; they're coached. They understand context, nuance, and the subtle art of "chill". Whether you need code, copy, or creative ideas, we've got the perfect board for the wave.
                        </p>
                        <ul class="space-y-4">
                            <li class="flex items-center gap-3 text-slate-700 dark:text-slate-300">
                                <span class="bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 p-1 rounded-full"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></span>
                                GPT-4 & Claude 3 Models
                            </li>
                            <li class="flex items-center gap-3 text-slate-700 dark:text-slate-300">
                                <span class="bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 p-1 rounded-full"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></span>
                                Context-aware memory
                            </li>
                        </ul>
                    </div>
                    <div class="lg:w-1/2 relative group">
                        <div class="absolute -inset-4 bg-gradient-to-r from-surf-ocean to-surf-teal rounded-[2rem] opacity-30 blur-2xl group-hover:opacity-50 transition duration-500"></div>
                        <div class="relative bg-white border border-slate-100 rounded-3xl shadow-2xl p-8 transform rotate-2 group-hover:rotate-0 transition duration-500">
                             <div class="flex gap-4 items-start mb-6">
                                <div class="w-10 h-10 rounded-full bg-slate-100 dark:bg-slate-700 flex-shrink-0"></div>
                                <div class="bg-slate-100 dark:bg-slate-700 rounded-2xl p-4 text-sm text-slate-600 dark:text-slate-300 w-3/4">Review this code for performance issues.</div>
                             </div>
                             <div class="flex gap-4 items-start flex-row-reverse">
                                <div class="w-10 h-10 rounded-full bg-surf-ocean flex-shrink-0"></div>
                                <div class="bg-surf-ocean/10 dark:bg-surf-ocean/20 rounded-2xl p-4 text-sm text-slate-800 dark:text-slate-200 w-full border border-surf-ocean/20">
                                    <p class="font-bold text-surf-ocean mb-2">Analysis Complete ✨</p>
                                    I found 3 areas for optimization. Using `memo` here would reduce re-renders by 40%...
                                </div>
                             </div>
                        </div>
                    </div>
                </div>

                <!-- Feature 2 -->
                <div class="flex flex-col lg:flex-row-reverse items-center gap-16">
                    <div class="lg:w-1/2">
                        <div class="bg-surf-sunset/10 w-12 h-12 rounded-2xl flex items-center justify-center text-2xl mb-6 text-surf-sunset">⚡</div>
                        <h2 class="text-4xl font-display font-bold text-slate-900 dark:text-white mb-6">Speed that breaks the sound barrier.</h2>
                        <p class="text-lg text-slate-600 dark:text-slate-400 leading-relaxed mb-8">
                           Waiting for AI is so 2023. Our Turbo Infrastructure ensures you get responses faster than you can paddle out. Perfect for real-time collaboration and flow state work sessions.
                        </p>
                         <ul class="space-y-4">
                            <li class="flex items-center gap-3 text-slate-700 dark:text-slate-300">
                                <span class="bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 p-1 rounded-full"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></span>
                                Sub-100ms latency
                            </li>
                             <li class="flex items-center gap-3 text-slate-700 dark:text-slate-300">
                                <span class="bg-green-100 dark:bg-green-900/30 text-green-600 dark:text-green-400 p-1 rounded-full"><svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg></span>
                                Edge caching worldwide
                            </li>
                        </ul>
                    </div>
                    <div class="lg:w-1/2 relative group">
                         <div class="absolute -inset-4 bg-gradient-to-r from-surf-sunset to-surf-coral rounded-[2rem] opacity-30 blur-2xl group-hover:opacity-50 transition duration-500"></div>
                        <div class="relative bg-slate-900 rounded-3xl shadow-2xl p-8 transform -rotate-1 group-hover:rotate-0 transition duration-500 text-white overflow-hidden">
                            <div class="absolute top-0 right-0 p-32 bg-surf-sunset opacity-20 blur-[80px]"></div>
                            <div class="relative z-10">
                                <div class="flex items-center justify-between mb-8 border-b border-white/10 pb-4">
                                    <span class="font-mono text-surf-warning">Latency Monitor</span>
                                    <span class="px-2 py-0.5 rounded bg-green-500/20 text-green-400 text-xs font-bold">ONLINE</span>
                                </div>
                                <div class="space-y-4 font-mono text-sm">
                                    <div class="flex justify-between"><span class="text-slate-400">Request ID</span> <span>#99281-AX</span></div>
                                    <div class="flex justify-between"><span class="text-slate-400">Processing</span> <span class="text-surf-teal">12ms</span></div>
                                    <div class="flex justify-between"><span class="text-slate-400">Completion</span> <span class="text-surf-teal">0.4s</span></div>
                                    <div class="w-full bg-white/10 h-1 mt-4 rounded-full overflow-hidden">
                                        <div class="bg-surf-success h-full w-[98%]"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Comparison Table -->
        <section id="comparison" class="py-24 bg-slate-50 dark:bg-slate-950 border-y border-slate-200 dark:border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                 <div class="text-center mb-16">
                     <h2 class="text-surf-ocean font-bold tracking-wide uppercase text-sm mb-2">The Better Choice</h2>
                    <h2 class="text-3xl font-display font-extrabold text-slate-900 dark:text-white sm:text-4xl">
                        Why surfers choose us
                    </h2>
                </div>

                <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-xl overflow-hidden border border-slate-200 dark:border-slate-800">
                    <div class="grid grid-cols-3 bg-slate-50 dark:bg-slate-800/50 p-6 border-b border-slate-200 dark:border-slate-800 font-bold text-slate-700 dark:text-slate-300">
                        <div class="col-span-1">Features</div>
                        <div class="col-span-1 text-center text-slate-400">Other "Pro" Tools</div>
                        <div class="col-span-1 text-center text-surf-ocean text-xl">SurferAI</div>
                    </div>
                     <div class="grid grid-cols-3 p-6 border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                        <div class="col-span-1 font-medium text-slate-700 dark:text-slate-300 flex items-center gap-2">🌊 Vibe Check</div>
                        <div class="col-span-1 text-center text-slate-300 dark:text-slate-600 text-lg">✕</div>
                        <div class="col-span-1 text-center text-surf-success text-lg">✓</div>
                    </div>
                     <div class="grid grid-cols-3 p-6 border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                        <div class="col-span-1 font-medium text-slate-700 dark:text-slate-300 flex items-center gap-2">🚀 Response Time</div>
                        <div class="col-span-1 text-center text-slate-500">Slow (>2s)</div>
                        <div class="col-span-1 text-center text-slate-800 dark:text-white font-bold">Instant (&lt;0.5s)</div>
                    </div>
                     <div class="grid grid-cols-3 p-6 border-b border-slate-100 dark:border-slate-800 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                        <div class="col-span-1 font-medium text-slate-700 dark:text-slate-300 flex items-center gap-2">💰 Cost per user</div>
                        <div class="col-span-1 text-center text-slate-500">$20+/mo</div>
                        <div class="col-span-1 text-center text-slate-800 dark:text-white font-bold">$0 - $19/mo</div>
                    </div>
                     <div class="grid grid-cols-3 p-6 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition">
                        <div class="col-span-1 font-medium text-slate-700 dark:text-slate-300 flex items-center gap-2">🏝️ UI Experience</div>
                        <div class="col-span-1 text-center text-slate-500">Boring gray</div>
                        <div class="col-span-1 text-center text-slate-800 dark:text-white font-bold">Tropical Paradise</div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Pricing Section -->
        <section id="pricing" class="py-24 bg-white dark:bg-slate-900 relative overflow-hidden">
             <!-- Decor -->
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center mb-10">
                     <h2 class="text-3xl font-display font-extrabold text-slate-900 dark:text-white sm:text-4xl">
                        Simple Pricing, No Wipeouts
                    </h2>
                    <p class="mt-4 text-xl text-slate-600 dark:text-slate-400">
                        Choose the board that fits your style.
                    </p>
                    
                     <!-- Toggle -->
                    <div class="mt-8 flex justify-center items-center gap-4 select-none">
                        <span class="font-medium transition-colors duration-300" :class="isYearly ? 'text-slate-500' : 'text-slate-900 dark:text-white font-bold'">Monthly</span>
                        <div 
                            @click="isYearly = !isYearly"
                            class="relative w-14 h-8 bg-surf-ocean rounded-full cursor-pointer shadow-inner transition-all duration-300"
                        >
                            <div 
                                class="absolute top-1 left-1 w-6 h-6 bg-white rounded-full shadow-md transform transition-transform duration-300 ease-[cubic-bezier(0.4,0.0,0.2,1)]"
                                :class="isYearly ? 'translate-x-6' : 'translate-x-0'"
                            ></div>
                        </div>
                        <span class="transition-colors duration-300" :class="isYearly ? 'text-slate-900 dark:text-white font-bold' : 'text-slate-500 font-medium'">
                            Yearly <span class="text-green-500 text-xs ml-1">-20%</span>
                        </span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-start pt-10">
                    <!-- Basic Tier -->
                    <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-xl transition relative group">
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">Grommet</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm mt-2">Perfect for dipping your toes.</p>
                        <div class="my-6">
                            <span class="text-4xl font-extrabold text-slate-900 dark:text-white">€0</span>
                            <span class="text-slate-500">/mo</span>
                        </div>
                        <Link :href="route('register')" class="block w-full text-center py-3 px-6 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-900 dark:text-white font-bold group-hover:bg-slate-200 dark:group-hover:bg-slate-600 transition">
                            Start Free
                        </Link>
                        <ul class="space-y-4 mt-8">
                            <li class="flex items-center text-slate-600 dark:text-slate-300 text-sm"><span class="text-surf-success mr-2 text-lg">✓</span> 10 chats / daily</li>
                            <li class="flex items-center text-slate-600 dark:text-slate-300 text-sm"><span class="text-surf-success mr-2 text-lg">✓</span> Standard Speed</li>
                            <li class="flex items-center text-slate-600 dark:text-slate-300 text-sm"><span class="text-surf-success mr-2 text-lg">✓</span> Community Support</li>
                        </ul>
                    </div>

                    <!-- Pro Tier -->
                    <div class="bg-slate-900 p-8 rounded-3xl shadow-2xl scale-105 relative z-10 text-white transform hover:-translate-y-2 transition-transform duration-300 ring-4 ring-surf-ocean/20">
                        <div class="absolute top-0 right-0 bg-gradient-to-l from-surf-ocean to-surf-teal text-white px-4 py-1 rounded-bl-xl rounded-tr-2xl text-xs font-bold shadow-lg">
                            POPULAR
                        </div>
                        <h3 class="text-2xl font-bold">Pro Surfer</h3>
                        <p class="text-slate-400 text-sm mt-2">For serious riders.</p>
                        <div class="my-6">
                            <span class="text-5xl font-extrabold">€{{ isYearly ? '15' : '19' }}</span>
                            <span class="text-slate-400">/mo</span>
                            <div v-if="isYearly" class="text-sm text-surf-ocean font-bold mt-1">Billed €180 yearly</div>
                        </div>
                        <Link :href="route('register')" class="block w-full text-center py-4 px-6 rounded-xl bg-gradient-to-r from-surf-ocean to-surf-teal text-white font-bold hover:shadow-lg transition hover:scale-[1.02]">
                            Get Pro Access ⚡
                        </Link>
                         <p class="text-center text-xs text-slate-500 mt-2">7-day money-back guarantee</p>
                        <ul class="space-y-4 mt-8">
                            <li class="flex items-center text-slate-300 text-sm font-medium"><span class="text-surf-ocean mr-2 text-lg">✓</span> Unlimited Chats</li>
                            <li class="flex items-center text-slate-300 text-sm font-medium"><span class="text-surf-ocean mr-2 text-lg">✓</span> Fast Pass Priority</li>
                            <li class="flex items-center text-slate-300 text-sm font-medium"><span class="text-surf-ocean mr-2 text-lg">✓</span> Access to GPT-4</li>
                            <li class="flex items-center text-slate-300 text-sm font-medium"><span class="text-surf-ocean mr-2 text-lg">✓</span> Priority Support</li>
                        </ul>
                    </div>

                    <!-- Enterprise Tier -->
                    <div class="bg-white dark:bg-slate-800 p-8 rounded-3xl border border-slate-200 dark:border-slate-700 shadow-sm hover:shadow-xl transition relative group">
                        <h3 class="text-xl font-bold text-slate-900 dark:text-white">Big Wave</h3>
                        <p class="text-slate-500 dark:text-slate-400 text-sm mt-2">For teams & agencies.</p>
                        <div class="my-6">
                            <span class="text-4xl font-extrabold text-slate-900 dark:text-white">€{{ isYearly ? '79' : '99' }}</span>
                            <span class="text-slate-500 dark:text-slate-400">/mo</span>
                             <div v-if="isYearly" class="text-sm text-surf-ocean font-bold mt-1">Billed €948 yearly</div>
                        </div>
                        <Link :href="route('register')" class="block w-full text-center py-3 px-6 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-900 dark:text-white font-bold group-hover:bg-slate-200 dark:group-hover:bg-slate-600 transition">
                            Contact Sales
                        </Link>
                        <ul class="space-y-4 mt-8">
                            <li class="flex items-center text-slate-600 dark:text-slate-300 text-sm"><span class="text-surf-success mr-2 text-lg">✓</span> Everything in Pro</li>
                            <li class="flex items-center text-slate-600 dark:text-slate-300 text-sm"><span class="text-surf-success mr-2 text-lg">✓</span> Admin Dashboard</li>
                            <li class="flex items-center text-slate-600 dark:text-slate-300 text-sm"><span class="text-surf-success mr-2 text-lg">✓</span> API Access</li>
                            <li class="flex items-center text-slate-600 dark:text-slate-300 text-sm"><span class="text-surf-success mr-2 text-lg">✓</span> Dedicated Success Manager</li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>

        <!-- Testimonials -->
        <section class="py-24 bg-gradient-to-br from-indigo-900 to-slate-900 text-white relative overflow-hidden">
             <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/stardust.png')] opacity-20"></div>
             
             <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="text-center mb-16">
                     <h2 class="text-3xl font-display font-extrabold sm:text-4xl">
                        Vibe Check Passed ✅
                    </h2>
                    <p class="text-indigo-200 mt-4">Join 10,000+ happy surfers.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                     <!-- Testimonial 1 -->
                     <div class="bg-white/5 backdrop-blur-md p-8 rounded-2xl border border-white/10 hover:border-surf-teal/50 transition duration-300">
                         <div class="flex gap-1 mb-4 text-yellow-400 text-sm">★★★★★</div>
                         <p class="text-slate-300 mb-6 leading-relaxed">"Honestly, I was skeptical. But the 'Chill Mode' actually helps me focus better than any other sterile AI tool. It's a vibe."</p>
                         <div class="flex items-center gap-4">
                             <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center font-bold">EA</div>
                             <div>
                                 <h4 class="font-bold text-sm">Elena Aris</h4>
                                 <p class="text-white/40 text-xs">Product Designer</p>
                             </div>
                         </div>
                     </div>

                     <!-- Testimonial 2 -->
                     <div class="bg-white/5 backdrop-blur-md p-8 rounded-2xl border border-white/10 hover:border-surf-teal/50 transition duration-300">
                         <div class="flex gap-1 mb-4 text-yellow-400 text-sm">★★★★★</div>
                         <p class="text-slate-300 mb-6 leading-relaxed">"The speed is incredible. I generated an entire marketing surfing campaign in minutes. Best €19 I spend every month."</p>
                         <div class="flex items-center gap-4">
                             <div class="w-10 h-10 rounded-full bg-pink-500 flex items-center justify-center font-bold">TC</div>
                             <div>
                                 <h4 class="font-bold text-sm">Tom Cruise</h4>
                                 <p class="text-white/40 text-xs">Stuntman (Maybe)</p>
                             </div>
                         </div>
                     </div>

                     <!-- Testimonial 3 -->
                     <div class="bg-white/5 backdrop-blur-md p-8 rounded-2xl border border-white/10 hover:border-surf-teal/50 transition duration-300">
                         <div class="flex gap-1 mb-4 text-yellow-400 text-sm">★★★★★</div>
                         <p class="text-slate-300 mb-6 leading-relaxed">"Finally, an AI that doesn't feel like a spreadsheet. SurferAI brings a bit of sunshine to my coding workflow."</p>
                         <div class="flex items-center gap-4">
                             <div class="w-10 h-10 rounded-full bg-teal-500 flex items-center justify-center font-bold">DJ</div>
                             <div>
                                 <h4 class="font-bold text-sm">Dev John</h4>
                                 <p class="text-white/40 text-xs">Senior Developer</p>
                             </div>
                         </div>
                     </div>
                </div>
            </div>
        </section>

        <!-- FAQ Section -->
        <section id="faq" class="py-24 bg-white dark:bg-slate-900">
             <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                     <h2 class="text-3xl font-display font-extrabold text-slate-900 dark:text-white sm:text-4xl">
                        FAQ
                    </h2>
                </div>

                <div class="space-y-4">
                    <div v-for="(faq, index) in faqs" :key="index" class="border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden transition-all duration-300 hover:shadow-md" :class="{'bg-slate-50 dark:bg-slate-800': faq.open}">
                        <button @click="toggleFaq(index)" class="w-full flex justify-between items-center p-6 text-left">
                            <span class="font-bold text-lg text-slate-800 dark:text-white">{{ faq.question }}</span>
                            <span class="text-surf-ocean text-2xl transform transition-transform duration-300" :class="{'rotate-180': faq.open}">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="size-5"><path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" /></svg>
                            </span>
                        </button>
                        <div v-show="faq.open" class="px-6 pb-6 text-slate-600 dark:text-slate-300 leading-relaxed animate-fade-in-up">
                            {{ faq.answer }}
                        </div>
                    </div>
                </div>
             </div>
        </section>

        <!-- Footer -->
        <footer class="bg-slate-50 dark:bg-slate-950 text-slate-600 dark:text-slate-400 py-16 border-t border-slate-200 dark:border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                 <div class="grid grid-cols-1 md:grid-cols-12 gap-12 mb-12">
                     <div class="md:col-span-5 space-y-6">
                         <div class="flex items-center gap-2">
                            <span class="text-3xl">🌊</span>
                            <span class="font-display text-2xl font-bold text-slate-900 dark:text-white">SurferAI</span>
                        </div>
                        <p class="text-slate-500 dark:text-slate-400 max-w-sm leading-relaxed">
                            The productivity platform that brings the ocean to your office. Built for makers, dreamers, and wave chasers.
                        </p>
                        <!-- Newsletter -->
                        <div class="pt-4">
                            <label class="text-xs font-bold uppercase text-slate-400 tracking-wider">Stay in the loop</label>
                            <div class="flex gap-2 mt-2">
                                <input type="email" placeholder="Enter your email" class="bg-white dark:bg-slate-700 border border-slate-300 dark:border-slate-600 text-slate-800 dark:text-white px-4 py-2 rounded-lg w-full focus:ring-2 focus:ring-surf-ocean focus:border-transparent" />
                                <button class="bg-slate-900 dark:bg-surf-ocean text-white px-4 py-2 rounded-lg font-bold hover:bg-surf-ocean dark:hover:bg-surf-teal transition">Subscribe</button>
                            </div>
                        </div>
                     </div>
                     
                     <div class="md:col-span-2 md:col-start-7">
                         <h4 class="font-bold text-slate-900 dark:text-white mb-4">Product</h4>
                         <ul class="space-y-3 text-sm">
                             <li><a href="#" class="hover:text-surf-ocean transition">Features</a></li>
                             <li><a href="#" class="hover:text-surf-ocean transition">Pricing</a></li>
                             <li><a href="#" class="hover:text-surf-ocean transition">Changelog</a></li>
                             <li><a href="#" class="hover:text-surf-ocean transition">Docs</a></li>
                         </ul>
                     </div>

                     <div class="md:col-span-2">
                         <h4 class="font-bold text-slate-900 dark:text-white mb-4">Company</h4>
                         <ul class="space-y-3 text-sm">
                             <li><a href="#" class="hover:text-surf-ocean transition">About</a></li>
                             <li><a href="#" class="hover:text-surf-ocean transition">Blog</a></li>
                             <li><a href="#" class="hover:text-surf-ocean transition">Careers</a></li>
                             <li><a href="#" class="hover:text-surf-ocean transition">Contact</a></li>
                         </ul>
                     </div>

                     <div class="md:col-span-2">
                         <h4 class="font-bold text-slate-900 dark:text-white mb-4">Legal</h4>
                         <ul class="space-y-3 text-sm">
                             <li><a href="#" class="hover:text-surf-ocean transition">Privacy</a></li>
                             <li><a href="#" class="hover:text-surf-ocean transition">Terms</a></li>
                             <li><a href="#" class="hover:text-surf-ocean transition">Security</a></li>
                         </ul>
                     </div>
                 </div>
                 
                 <div class="pt-8 border-t border-slate-200 dark:border-slate-800 flex flex-col md:flex-row justify-between items-center gap-4">
                     <p class="text-sm text-slate-400">
                         &copy; {{ new Date().getFullYear() }} SurferAI Inc. All rights reserved. Laravel v{{ laravelVersion }} (PHP v{{ phpVersion }}).
                     </p>
                     <div class="flex gap-6">
                         <a href="#" class="text-slate-400 hover:text-surf-ocean transition text-xl"><i class="fab fa-twitter"></i>🐦</a>
                         <a href="#" class="text-slate-400 hover:text-surf-ocean transition text-xl"><i class="fab fa-github"></i>🐙</a>
                         <a href="#" class="text-slate-400 hover:text-surf-ocean transition text-xl"><i class="fab fa-instagram"></i>📸</a>
                     </div>
                 </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Custom animations if needed specifically for this page */
.pattern-dots {
    background-image: radial-gradient(white 1px, transparent 1px);
    background-size: 20px 20px;
}
</style>

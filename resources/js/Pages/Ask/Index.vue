<script setup>
import { useForm } from '@inertiajs/vue3'
import { ref, computed } from 'vue'
import MarkdownIt from 'markdown-it'
import hljs from 'highlight.js'
import 'highlight.js/styles/github-dark.css'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
    models: Array,
    selectedModel: String,
    message: String,
    response: String,
    error: String,
})

const form = useForm({
    message: props.message ?? '',
    model: props.selectedModel,
})

const isLoading = ref(false)

const md = new MarkdownIt({
    highlight: function (str, lang) {
        if (lang && hljs.getLanguage(lang)) {
            try {
                return hljs.highlight(str, { language: lang }).value
            } catch (__) {}
        }
        return '' // use external default escaping
    }
})

const renderedResponse = computed(() => {
    return props.response ? md.render(props.response) : ''
})

const submit = () => {
    isLoading.value = true
    form.post(route('ask.post'), {
        onFinish: () => {
            isLoading.value = false
        },
    })
}
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-full bg-amber-300 shadow-lg shadow-amber-200/70 animate-pulse-slow"></div>
                <div>
                    <h2 class="font-semibold text-xl text-slate-800 leading-tight">Ask AI - Summer Edition</h2>
                    <p class="text-sm text-slate-600">Coco vibes, soleil qui brille, et réponses fraîches.</p>
                </div>
            </div>
        </template>

        <div class="relative py-12 overflow-hidden summer-sky">
            <div class="sun"></div>
            <div class="wave wave-top"></div>
            <div class="wave wave-bottom"></div>
            <div class="floating-coconut float-slow"></div>
            <div class="floating-board float-delay"></div>

            <div class="max-w-6xl mx-auto px-4 lg:px-8 relative z-10">
                <div class="bg-white/70 backdrop-blur-lg border border-white/50 shadow-2xl shadow-amber-200/50 rounded-3xl overflow-hidden card-shell">
                    <div class="absolute inset-0 opacity-60 shimmer"></div>
                    <div class="relative grid lg:grid-cols-5 gap-8 p-8">
                        <div class="lg:col-span-2 space-y-5">
                            <p class="inline-flex items-center gap-2 px-3 py-1 text-xs font-semibold bg-amber-100 text-amber-700 rounded-full border border-amber-200 shadow-sm">
                                🌴 Mode plage activé
                            </p>
                            <h3 class="text-3xl font-bold text-slate-900 leading-tight">
                                Pose ta question sous le soleil
                            </h3>
                            <p class="text-slate-700">
                                Sirote une noix de coco, laisse l'IA s'occuper du reste. Les réponses arrivent avec une brise marine et un peu de chaleur d'été.
                            </p>
                            <div class="flex flex-wrap gap-3">
                                <span class="badge-chip">☀️ Inspiration</span>
                                <span class="badge-chip">🏖️ Plan de voyage</span>
                                <span class="badge-chip">🍹 Recettes fraîches</span>
                                <span class="badge-chip">🕶️ Idées créatives</span>
                            </div>
                            <div class="flex items-center gap-3 text-sm text-emerald-700 font-semibold">
                                <span class="inline-block w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                Brise marine activée, prêt à répondre.
                            </div>
                        </div>

                        <div class="lg:col-span-3 space-y-6">
                            <form @submit.prevent="submit" class="space-y-6">
                                <div>
                                    <label for="model" class="block text-sm font-semibold text-slate-700 mb-2">
                                        Choisir un modèle
                                    </label>
                                    <select
                                        id="model"
                                        v-model="form.model"
                                        class="w-full px-4 py-3 border border-amber-200 rounded-xl bg-white/80 text-slate-900 focus:ring-2 focus:ring-amber-400 focus:border-transparent shadow-inner"
                                    >
                                        <option v-for="model in models" :key="model.id" :value="model.id">
                                            {{ model.name }}
                                        </option>
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label for="message" class="block text-sm font-semibold text-slate-700">
                                        Votre question
                                    </label>
                                    <div class="relative">
                                        <textarea
                                            id="message"
                                            v-model="form.message"
                                            rows="6"
                                            placeholder="Demande d'itinéraire, recette tropicale, idée créative..."
                                            class="w-full px-4 py-3 border border-sky-200 rounded-xl bg-white/85 text-slate-900 focus:ring-2 focus:ring-sky-400 focus:border-transparent shadow-inner"
                                        ></textarea>
                                        <span class="absolute right-4 bottom-4 text-xs text-slate-400">🏄‍♀️</span>
                                    </div>
                                    <div v-if="form.errors.message" class="text-red-500 text-sm">
                                        {{ form.errors.message }}
                                    </div>
                                </div>

                                <div class="flex flex-col sm:flex-row sm:items-center gap-3">
                                    <PrimaryButton :disabled="form.processing || isLoading" class="flex-1 justify-center text-base summer-button">
                                        <span v-if="form.processing || isLoading">Traitement en cours...</span>
                                        <span v-else>Envoyer avec le soleil</span>
                                    </PrimaryButton>
                                    <div class="text-xs text-slate-500 flex items-center gap-2">
                                        <span class="inline-block w-2 h-2 rounded-full bg-orange-400 animate-pulse"></span>
                                        Animation des vagues en cours
                                    </div>
                                </div>
                            </form>

                            <div v-if="error" class="p-4 bg-red-50/90 border border-red-200 rounded-xl shadow-sm">
                                <p class="text-red-800 font-semibold">Erreur :</p>
                                <p class="text-red-700">{{ error }}</p>
                            </div>

                            <div v-if="response" class="space-y-3">
                                <h3 class="text-lg font-semibold text-slate-900">Réponse :</h3>
                                <div
                                    class="prose prose-slate max-w-none bg-white/85 p-5 rounded-2xl border border-sky-100 shadow-sm"
                                    v-html="renderedResponse"
                                ></div>
                            </div>

                            <div v-if="!response && !error && !form.processing" class="text-center text-slate-500">
                                <p>Pose une question et laisse le soleil illuminer la réponse 🌞</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.summer-sky {
    background: radial-gradient(circle at 10% 20%, rgba(186, 230, 253, 0.6), transparent 35%),
        radial-gradient(circle at 80% 0%, rgba(253, 213, 161, 0.55), transparent 40%),
        linear-gradient(135deg, #c5f6fa 0%, #fff6e3 45%, #c7f9cc 100%);
}

.sun {
    position: absolute;
    top: -90px;
    right: -120px;
    width: 320px;
    height: 320px;
    border-radius: 9999px;
    background: radial-gradient(circle, #ffef9f 0%, #fbbf24 55%, #f97316 100%);
    filter: blur(6px);
    opacity: 0.65;
    animation: shimmer 12s ease-in-out infinite alternate;
}

.wave {
    position: absolute;
    left: -10%;
    right: -10%;
    height: 200px;
    background: radial-gradient(circle at 20% 40%, rgba(59, 130, 246, 0.08), transparent 40%),
        radial-gradient(circle at 70% 60%, rgba(37, 99, 235, 0.08), transparent 45%),
        rgba(255, 255, 255, 0.6);
    filter: blur(25px);
    transform: rotate(-2deg);
    animation: drift 18s ease-in-out infinite alternate;
}

.wave-top {
    top: -120px;
}

.wave-bottom {
    bottom: -140px;
    animation-delay: 5s;
    opacity: 0.7;
}

.floating-coconut,
.floating-board {
    position: absolute;
    width: 140px;
    height: 140px;
    border-radius: 9999px;
    opacity: 0.35;
    filter: blur(0px);
    mix-blend-mode: multiply;
}

.floating-coconut {
    background: radial-gradient(circle at 30% 30%, #f59e0b 0%, #d97706 45%, #b45309 90%);
    top: 12%;
    left: 4%;
}

.floating-board {
    background: radial-gradient(circle at 60% 40%, #22d3ee 0%, #0ea5e9 50%, #0369a1 100%);
    bottom: 8%;
    right: 6%;
}

.float-slow {
    animation: float 10s ease-in-out infinite;
}

.float-delay {
    animation: float 12s ease-in-out infinite 1.5s;
}

.card-shell {
    position: relative;
}

.card-shell .shimmer {
    background: linear-gradient(120deg, rgba(255, 255, 255, 0.6), rgba(255, 255, 255, 0)),
        linear-gradient(300deg, rgba(255, 255, 255, 0.35), rgba(255, 255, 255, 0));
    animation: shimmer 10s ease-in-out infinite alternate;
}

.badge-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    padding: 0.5rem 0.85rem;
    border-radius: 9999px;
    background: linear-gradient(135deg, #fff7ed, #fffbeb);
    color: #92400e;
    font-weight: 600;
    font-size: 0.85rem;
    border: 1px solid #fed7aa;
    box-shadow: 0 10px 40px -20px rgba(247, 148, 30, 0.5);
}

.summer-button {
    background: linear-gradient(120deg, #f97316, #fbbf24, #22d3ee);
    box-shadow: 0 10px 40px -12px rgba(0, 0, 0, 0.25), 0 0 0 1px rgba(255, 255, 255, 0.4) inset;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.summer-button:hover:not([disabled]) {
    transform: translateY(-2px);
    box-shadow: 0 14px 45px -12px rgba(17, 94, 89, 0.35);
}

@keyframes float {
    0%, 100% {
        transform: translateY(0px) rotate(-2deg);
    }
    50% {
        transform: translateY(-14px) rotate(2deg);
    }
}

@keyframes drift {
    0% {
        transform: translateX(0) rotate(-2deg);
    }
    100% {
        transform: translateX(20px) rotate(1deg);
    }
}

@keyframes shimmer {
    0% {
        opacity: 0.35;
    }
    100% {
        opacity: 0.75;
    }
}

@keyframes pulse-slow {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.04);
    }
}

.animate-pulse-slow {
    animation: pulse-slow 6s ease-in-out infinite;
}
</style>

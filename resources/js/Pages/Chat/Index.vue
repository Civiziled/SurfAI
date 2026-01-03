<script setup>
import { ref, computed } from 'vue'
import { router } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import AnimatedButton from '@/Components/AnimatedButton.vue'
import AnimatedSidebar from '@/Components/AnimatedSidebar.vue'
import AnimatedChatBubble from '@/Components/AnimatedChatBubble.vue'

const props = defineProps({
    conversations: Array,
    models: Array,
    userPreferredModel: String,
})

const selectedModel = ref(props.userPreferredModel)

const createNewChat = () => {
    router.post(route('chat.create'), {
        model: selectedModel.value,
    })
}

const openConversation = (conversationId) => {
    router.get(route('chat.show', conversationId))
}

const deleteConversation = (conversationId) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette conversation ?')) {
        router.delete(route('chat.destroy', conversationId))
    }
}
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Chat IA</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                    <!-- Sidebar avec conversations -->
                    <div class="lg:col-span-1">
                        <AnimatedSidebar title="Conversations">
                            <div class="mb-6">
                                <div class="mb-3">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                        Modèle
                                    </label>
                                    <select
                                        v-model="selectedModel"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-sm focus:ring-2 focus:ring-indigo-500"
                                    >
                                        <option v-for="model in models" :key="model.id" :value="model.id">
                                            {{ model.name.substring(0, 30) }}...
                                        </option>
                                    </select>
                                </div>
                                <AnimatedButton
                                    :href="route('chat.create')"
                                    method="post"
                                    :data="{ model: selectedModel }"
                                    as="button"
                                    class="w-full"
                                >
                                    + Nouveau chat
                                </AnimatedButton>
                                <a href="/instructions" class="mt-3 block text-center text-sm text-indigo-600 dark:text-indigo-300">📝 Instructions personnalisées</a>
                            </div>

                            <transition-group name="fade" tag="div" class="space-y-2 max-h-96 overflow-y-auto">
                                <div v-for="conv in conversations" :key="conv.id">
                                    <div @click="openConversation(conv.id)" class="cursor-pointer">
                                        <AnimatedChatBubble :author="conv.user_name || 'U'" :text="conv.title" :time="conv.updated_at" />
                                    </div>
                                    <div class="text-right mt-1">
                                        <button @click="deleteConversation(conv.id)" class="p-1 text-red-500 hover:bg-red-100 dark:hover:bg-red-900/30 rounded opacity-80">✕</button>
                                    </div>
                                </div>
                            </transition-group>

                            <div v-if="conversations.length === 0" class="text-center py-8">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Aucune conversation</p>
                            </div>
                        </AnimatedSidebar>
                    </div>

                    <!-- Zone principale - Page d'accueil -->
                    <div class="lg:col-span-3">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg h-full">
                            <div class="p-8 text-gray-900 dark:text-gray-100 flex flex-col items-center justify-center min-h-96">
                                <div class="text-center">
                                    <h1 class="text-4xl font-bold mb-4">Bienvenue dans ChatGPT-like</h1>
                                    <p class="text-lg text-gray-600 dark:text-gray-400 mb-8">
                                        Créez une nouvelle conversation ou sélectionnez-en une existante pour commencer
                                    </p>
                                    <PrimaryButton
                                        :href="route('chat.create')"
                                        method="post"
                                        :data="{ model: selectedModel }"
                                        as="button"
                                        class="text-lg px-8 py-3"
                                    >
                                        + Créer une nouvelle conversation
                                    </PrimaryButton>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

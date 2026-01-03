<script setup>
import { ref } from 'vue'
import { useForm } from '@inertiajs/vue3'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'

const props = defineProps({
    instructions: [String, Array],
})

const form = useForm({
    instructions: typeof props.instructions === 'string' ? props.instructions : (props.instructions ? props.instructions.join('\n') : ''),
})

const save = () => {
    form.post(route('instructions.update'))
}
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">Instructions personnalisées</h2>
        </template>

        <div class="py-12">
            <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900 dark:text-gray-100">
                        <p class="mb-4 text-sm text-gray-600 dark:text-gray-400">Définissez ici vos instructions personnalisées. Elles seront injectées automatiquement dans le prompt système.</p>

                        <textarea v-model="form.instructions" rows="10" class="w-full p-4 border rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"></textarea>

                        <div class="mt-4 flex gap-2">
                            <button @click="save" class="px-4 py-2 bg-indigo-600 text-white rounded">Enregistrer</button>
                            <a href="/chat" class="px-4 py-2 border rounded">Retour au chat</a>
                        </div>

                        <div class="mt-6">
                            <h3 class="font-semibold mb-2">Aperçu</h3>
                            <div class="prose dark:prose-invert max-w-none p-4 bg-gray-50 dark:bg-gray-900/40 rounded">
                                <pre class="whitespace-pre-wrap">{{ form.instructions }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<script setup>
import { ref, nextTick, computed, onMounted, onUnmounted } from 'vue'
import { useForm } from '@inertiajs/vue3'
import { router } from '@inertiajs/vue3'
import MarkdownIt from 'markdown-it'
import hljs from 'highlight.js'
import 'highlight.js/styles/github-dark.css'
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue'
import PrimaryButton from '@/Components/PrimaryButton.vue'

const props = defineProps({
    conversation: Object,
    conversations: Array,
    models: Array,
    userPreferredModel: String,
    error: String,
})
const messagesContainer = ref(null)
const selectedModel = ref(props.userPreferredModel)
const isLoading = ref(false)
const imageDataUrl = ref(null)
const imageInput = ref(null)

const md = new MarkdownIt({
    highlight: function (str, lang) {
        if (lang && hljs.getLanguage(lang)) {
            try {
                return hljs.highlight(str, { language: lang }).value
            } catch (__) {}
        }
        return ''
    }
})

const form = useForm({
    message: '',
    model: props.conversation.model,
})

const messages = ref(props.conversation.messages || [])

const isStreaming = ref(false)

const streamFetch = async (url, payload, onChunk, onFinish, onError) => {
    try {
        isStreaming.value = true

        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || ''

        console.log('[streamFetch] opening', url, payload)
        const res = await fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'text/event-stream, application/json',
                'X-CSRF-TOKEN': csrfToken,
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(payload),
        })

        console.log('[streamFetch] response status', res.status)
        if (!res.ok) {
            const text = await res.text()
            console.error('[streamFetch] non-ok response', res.status, text)
            throw new Error(text || `HTTP ${res.status}`)
        }

        const reader = res.body.getReader()
        const decoder = new TextDecoder('utf-8')
        let { value, done } = await reader.read()
        let buffer = ''

        while (!done) {
            buffer += decoder.decode(value, { stream: true })

            const parts = buffer.split(/\r\n|\n|\r/)
            buffer = parts.pop() || ''

            for (const line of parts) {
                let l = line
                console.log('[streamFetch] raw line:', l)
                if (l.trim() === '') continue
                if (l.startsWith('data:')) l = l.replace(/^data:\s*/, '')
                l = l.replace(/^[\s:]*OPENROUTER PROCESSING[:\s]*/i, '')
                if (l.trim() === '') continue
                if (l === '[DONE]') {
                    if (onFinish) onFinish()
                    await reader.cancel()
                    isStreaming.value = false
                    return
                }

                try {
                    const parsed = JSON.parse(l)
                    if (parsed && parsed.error) {
                        console.error('[streamFetch] server signaled error', parsed.error)
                        if (onError) onError(new Error(parsed.error))
                        await reader.cancel()
                        isStreaming.value = false
                        return
                    }

                    let chunk = parsed?.choices?.[0]?.delta?.content ?? parsed?.choices?.[0]?.message?.content ?? parsed?.text ?? ''
                    if (chunk) {
                        if (typeof chunk === 'string') {
                            chunk = chunk.replace(/OPENROUTER PROCESSING[:\s]*/gi, '')
                        }
                        console.log('[streamFetch] parsed chunk:', String(chunk).slice(0, 120))
                        if (onChunk) onChunk(chunk)
                    }
                } catch (e) {
                    const cleaned = l.replace(/OPENROUTER PROCESSING[:\s]*/gi, '')
                    if (cleaned && onChunk) onChunk(cleaned)
                }
            }

            ;({ value, done } = await reader.read())
        }

        if (buffer && onChunk) onChunk(buffer)
        if (onFinish) onFinish()
        isStreaming.value = false
    } catch (err) {
        isStreaming.value = false
        if (onError) onError(err)
        else console.error(err)
    }
}

const submit = async () => {
    const text = form.message || ''
    const hasImage = !!imageDataUrl.value
    if (!text.trim() && !hasImage) return

    if (isStreaming.value || isLoading.value) return

    isLoading.value = true

    const userBlocks = []

    if (text.trim()) {
        userBlocks.push({
            type: 'text',
            text,
        })
    }

    if (hasImage) {
        userBlocks.push({
            type: 'image_url',
            image_url: {
                url: imageDataUrl.value,
            },
        })
    }

    messages.value.push({
        role: 'user',
        content: userBlocks,
        id: Date.now(),
        created_at: new Date().toISOString(),
    })

    messages.value.push({
        role: 'assistant',
        content: '',
        id: Date.now() + 1,
        created_at: new Date().toISOString(),
    })

    await nextTick()
    if (messagesContainer.value) messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight

    try {
        const cleanChunk = (raw, prev = '') => {
            let s = String(raw ?? '')
            s = s.replace(/OPENROUTER PROCESSING[:\s]*/gi, '')

            const tryParse = (str) => {
                try {
                    const parsed = JSON.parse(str)
                    if (Array.isArray(parsed)) {
                        return parsed
                            .map((b) => {
                                if (!b) return ''
                                if (typeof b === 'string') return b
                                if (b.type === 'text' && typeof b.text === 'string') return b.text
                                if (b.text && typeof b.text === 'string') return b.text
                                return ''
                            })
                            .filter(Boolean)
                            .join(' ')
                    }
                    if (parsed && typeof parsed === 'object') {
                        if (parsed.text && typeof parsed.text === 'string') return parsed.text
                        return Object.values(parsed).filter(v => typeof v === 'string').join(' ')
                    }
                } catch (e) {
                    return null
                }
                return null
            }

            const parsed = tryParse(s)
            if (parsed !== null) s = parsed

            s = s.replace(/\r?\n/g, '\n')
            s = s.replace(/\s+/, ' ')

            if (prev && prev.length > 0 && !/\s$/.test(prev) && s.length > 0 && !/^\s/.test(s)) {
                s = ' ' + s
            }

            return s
        }

        await streamFetch(route('chat.stream', props.conversation.id), { message: text, model: selectedModel.value, image_base64: imageDataUrl.value }, (chunk) => {
            const last = messages.value[messages.value.length - 1]
            if (last && last.role === 'assistant') {
                const prev = String(last.content || '')
                const cleaned = cleanChunk(chunk, prev)
                last.content = prev + cleaned
                nextTick(() => {
                    if (messagesContainer.value) messagesContainer.value.scrollTop = messagesContainer.value.scrollHeight
                })
            }
        }, () => {
            form.message = ''
            imageDataUrl.value = null
            if (imageInput.value) imageInput.value.value = ''
            isLoading.value = false
        }, (err) => {
            console.error('Stream error', err)
            // Replace assistant placeholder with a visible error message and keep the user message
            const last = messages.value[messages.value.length - 1]
            const errText = err?.message ? String(err.message) : 'Erreur de streaming'
            if (last && last.role === 'assistant') {
                last.content = `⚠️ ${errText}`
            } else {
                messages.value.push({
                    role: 'assistant',
                    content: `⚠️ ${errText}`,
                    id: Date.now(),
                    created_at: new Date().toISOString(),
                })
            }
            imageDataUrl.value = null
            isLoading.value = false
        })
    } catch (err) {
        console.error('Erreur lors du streaming:', err)
        messages.value.pop()
        messages.value.pop()
        imageDataUrl.value = null
        isLoading.value = false
    }
}

const renderMarkdown = (content) => {
    return md.render(content)
}

const parseThought = (content) => {
    if (typeof content !== 'string') return { thought: null, main: content }
    
    // Check for <think> tags
    const thinkMatch = content.match(/<think>([\s\S]*?)(?:<\/think>|$)/)
    
    if (thinkMatch) {
         const thought = thinkMatch[1].trim()
         // Remove the think block from content for the main display
         // We handle the case where </think> might be missing (streaming) by replacing the match
         let main = content.replace(/<think>[\s\S]*?(?:<\/think>|$)/, '').trim()
         
         return { thought, main }
    }
    
    return { thought: null, main: content }
}

const onImageChange = (event) => {
    const [file] = event.target.files || []
    if (!file) {
        imageDataUrl.value = null
        return
    }

    const reader = new FileReader()
    reader.onload = (e) => {
        imageDataUrl.value = e.target?.result
    }
    reader.readAsDataURL(file)
}

const openConversation = (conversationId) => {
    router.get(route('chat.show', conversationId))
}

const createNewChat = () => {
    router.post(route('chat.create'), {
        model: selectedModel.value,
    })
}

const deleteConversation = (conversationId) => {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette conversation ?')) {
        router.delete(route('chat.destroy', conversationId))
    }
}
// --- Voice Logic ---
const isRecording = ref(false)
let recognition = null

const toggleRecording = () => {
    if (isRecording.value) {
        stopRecording()
    } else {
        startRecording()
    }
}

const startRecording = () => {
    if (!('webkitSpeechRecognition' in window) && !('SpeechRecognition' in window)) {
        alert("Votre navigateur ne supporte pas la reconnaissance vocale.")
        return
    }
    
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition
    recognition = new SpeechRecognition()
    recognition.lang = 'fr-FR'
    recognition.continuous = false
    recognition.interimResults = true 
    
    recognition.onstart = () => {
        isRecording.value = true
    }
    
    recognition.onerror = (event) => {
        console.error("Speech recognition error", event.error)
        isRecording.value = false
    }

    recognition.onend = () => {
        isRecording.value = false
    }
    
    // Handle manual concatenation if we use interimResults or just replace
    // Ideally we append to existing text
    let finalTranscript = ''
    
    recognition.onresult = (event) => {
        let interimTranscript = ''
        for (let i = event.resultIndex; i < event.results.length; ++i) {
            if (event.results[i].isFinal) {
                finalTranscript += event.results[i][0].transcript
            } else {
                interimTranscript += event.results[i][0].transcript
            }
        }
        
        // We can't easily "append" safely with interim without managing cursor state, 
        // so simple approach: simple append when final
         if (finalTranscript) {
             form.message = (form.message ? form.message + ' ' : '') + finalTranscript
             finalTranscript = '' // reset for next sentence if continuous (though we set continuous false above)
         }
    }
    
    recognition.start()
}

const stopRecording = () => {
    if (recognition) {
        recognition.stop()
    }
    isRecording.value = false
}

const speakMessage = (text) => {
    if (!('speechSynthesis' in window)) return
    
    // If already speaking, stop
    if (window.speechSynthesis.speaking) {
        window.speechSynthesis.cancel()
        return // Toggle behavior: click again to stop
    }
    
    // Clean text (remove markdown mostly) or just speak raw? 
    // Ideally strip markdown chars slightly for better reading
    const cleanText = text.replace(/[*#`_]/g, '')
    
    const utterance = new SpeechSynthesisUtterance(cleanText)
    utterance.lang = 'fr-FR' 
    utterance.rate = 1.0
    utterance.pitch = 1.0
    
    window.speechSynthesis.speak(utterance)
}
</script>

<template>
    <AuthenticatedLayout>
        <template #header>
            <div class="flex items-center gap-3">
                <div class="h-9 w-9 rounded-full bg-amber-300 shadow-lg shadow-amber-200/60 animate-pulse-slow"></div>
                <div>
                    <h2 class="font-semibold text-xl text-slate-800 leading-tight">{{ conversation.title }}</h2>
                    <p class="text-sm text-slate-600">Chat en bord de mer • modèle {{ selectedModel }}</p>
                </div>
            </div>
        </template>

        <div class="relative py-10 summer-bg overflow-hidden">
            <div class="sunset"></div>
            <div class="wave wave-1"></div>
            <div class="wave wave-2"></div>
            <div class="palms"></div>

            <div class="max-w-6xl mx-auto px-4 lg:px-8 relative z-10">
                <div class="grid lg:grid-cols-4 gap-6">
                    <div class="lg:col-span-1 space-y-4">
                        <div class="glass-card p-5 border border-white/50 shadow-xl summer-panel">
                            <div class="flex items-center justify-between mb-4">
                                <span class="text-sm font-semibold text-amber-800">Tiki modèles</span>
                                <span class="px-2 py-1 text-xs bg-white/60 border border-white/70 rounded-full">☀️</span>
                            </div>
                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-slate-600 mb-2">Modèle</label>
                                <select
                                    v-model="selectedModel"
                                    class="w-full px-3 py-2 rounded-xl border border-amber-200 bg-white/80 text-slate-900 focus:ring-2 focus:ring-amber-400 focus:border-transparent"
                                >
                                    <option v-for="model in models" :key="model.id" :value="model.id">
                                        {{ model.name.substring(0, 30) }}...
                                    </option>
                                </select>
                            </div>
                            <PrimaryButton
                                :href="route('chat.create')"
                                method="post"
                                :data="{ model: selectedModel }"
                                as="button"
                                class="w-full justify-center summer-button text-base"
                            >
                                + Nouveau chat
                            </PrimaryButton>
                            <a href="/instructions" class="mt-3 block text-center text-sm text-sky-700 hover:text-sky-900">📝 Instructions personnalisées</a>
                        </div>

                        <div class="glass-card p-5 border border-white/50 shadow-lg max-h-[60vh] overflow-y-auto">
                            <div class="flex items-center justify-between mb-3">
                                <h3 class="text-sm font-semibold text-slate-800">Conversations</h3>
                                <span class="text-xs text-slate-500">{{ conversations.length }} actives</span>
                            </div>
                            <div
                                v-for="conv in conversations"
                                :key="conv.id"
                                :class="[
                                    'p-3 rounded-xl transition-all group border',
                                    conv.id === conversation.id
                                        ? 'bg-amber-100/80 border-amber-200 shadow-inner'
                                        : 'bg-white/60 border-white/70 hover:translate-x-1 hover:shadow-md'
                                ]"
                            >
                                <div class="flex items-start justify-between">
                                    <div class="flex-1 min-w-0" @click="openConversation(conv.id)">
                                        <p class="font-medium text-slate-800 truncate group-hover:underline">{{ conv.title }}</p>
                                        <p class="text-[11px] text-slate-500">{{ conv.updated_at }}</p>
                                    </div>
                                    <button
                                        @click.stop="deleteConversation(conv.id)"
                                        class="ml-2 p-1 text-red-500 hover:bg-red-50 rounded-full opacity-0 group-hover:opacity-100 transition"
                                    >
                                        ✕
                                    </button>
                                </div>
                            </div>

                            <div v-if="conversations.length === 0" class="text-center py-6 text-sm text-slate-500">
                                Aucune conversation
                            </div>
                        </div>
                    </div>

                    <div class="lg:col-span-3">
                        <div class="glass-card border border-white/50 shadow-2xl chat-shell flex flex-col h-[78vh] p-6">
                            <div class="flex items-center justify-between pb-4 border-b border-white/50">
                                <div class="ml-4">
                                    <p class="text-xs uppercase tracking-widest text-amber-700">Beach chat</p>
                                    <h3 class="text-xl font-bold text-slate-900">Sous le soleil et les palmiers</h3>
                                </div>
                                <span class="px-3 py-1 text-xs font-semibold rounded-full bg-white/70 border border-amber-100 text-amber-700">
                                    Modèle: {{ selectedModel }}
                                </span>
                            </div>

                            <div
                                ref="messagesContainer"
                                class="flex-1 overflow-y-auto p-4 space-y-4 rounded-2xl bg-white/55 border border-white/60 shadow-inner wave-mask"
                            >
                                <div v-if="error" class="p-4 bg-red-50/90 border border-red-200 rounded-xl">
                                    <p class="text-red-800 font-semibold">Erreur :</p>
                                    <p class="text-red-700">{{ error }}</p>
                                </div>

                                <div
                                    v-for="(msg, index) in messages"
                                    :key="index"
                                    :class="[
                                        'flex',
                                        msg.role === 'user' ? 'justify-end' : 'justify-start',
                                    ]"
                                >
                                    <div
                                        :class="[
                                            'max-w-xl px-4 py-3 rounded-2xl shadow-md',
                                            msg.role === 'user'
                                                ? 'bg-gradient-to-br from-amber-400 via-orange-400 to-rose-400 text-white rounded-br-none'
                                                : 'bg-white/90 border border-sky-100 text-slate-900 rounded-bl-none backdrop-blur'
                                        ]"
                                    >
                                        <div v-if="msg.role === 'assistant'" class="w-full">
                                             <!-- Thinking Trace Block -->
                                            <div v-if="parseThought(msg.content).thought" class="mb-4">
                                                <details class="group bg-amber-50/50 border border-amber-100 rounded-xl overflow-hidden open:bg-amber-50 transition-all duration-300">
                                                    <summary class="flex items-center gap-2 p-3 text-xs font-bold text-amber-600 uppercase tracking-wide cursor-pointer select-none hover:bg-amber-100/50 transition-colors">
                                                        <span class="animate-pulse">🧠</span> Trace de réflexion
                                                        <svg class="w-4 h-4 transition-transform duration-300 group-open:rotate-180 ml-auto opacity-50" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                                    </summary>
                                                    <div class="px-4 pb-4 pt-1 text-xs text-slate-600 font-mono leading-relaxed whitespace-pre-wrap border-t border-amber-100/50 bg-white/40">
                                                        {{ parseThought(msg.content).thought }}
                                                    </div>
                                                </details>
                                            </div>

                                            <div class="prose prose-slate max-w-none text-sm dark:prose-invert" v-html="renderMarkdown(parseThought(msg.content).main)"></div>
                                            
                                            <!-- Voice Output Button -->
                                            <div class="mt-2 flex justify-end">
                                                <button 
                                                    @click="speakMessage(parseThought(msg.content).main)"
                                                    class="text-slate-400 hover:text-amber-500 transition-colors p-1 rounded-full hover:bg-slate-100"
                                                    title="Écouter la réponse"
                                                >
                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                      <path stroke-linecap="round" stroke-linejoin="round" d="M19.114 5.636a9 9 0 0 1 0 12.728M16.463 8.288a5.25 5.25 0 0 1 0 7.424M6.75 8.25l4.72-4.72a.75.75 0 0 1 1.28.53v15.88a.75.75 0 0 1-1.28.53l-4.72-4.72H4.51c-.88 0-1.704-.507-1.938-1.354A9.009 9.009 0 0 1 2.25 12c0-.83.112-1.633.322-2.396C2.806 8.756 3.63 8.25 4.51 8.25H6.75Z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        </div>

                                        <!-- Render user content: support array-of-blocks, plain string, or image blocks -->
                                        <div v-else class="text-sm">
                                            <template v-if="Array.isArray(msg.content)">
                                                <div v-for="(block, bi) in msg.content" :key="bi" class="mb-1">
                                                    <template v-if="block && block.type === 'text'">
                                                        <p class="whitespace-pre-wrap">{{ block.text }}</p>
                                                    </template>
                                                    <template v-else-if="block && block.type === 'image_url'">
                                                        <img :src="(block.image_url && block.image_url.url) ? block.image_url.url : block.image_url" alt="user image" class="max-w-xs rounded" />
                                                    </template>
                                                    <template v-else>
                                                        <p class="whitespace-pre-wrap">{{ typeof block === 'string' ? block : JSON.stringify(block) }}</p>
                                                    </template>
                                                </div>
                                            </template>
                                            <template v-else>
                                                <p class="whitespace-pre-wrap">{{ msg.content }}</p>
                                            </template>
                                        </div>
                                    </div>
                                </div>

                                <div v-if="isLoading" class="flex justify-start">
                                    <div class="bg-white/90 border border-sky-100 px-4 py-3 rounded-2xl rounded-bl-none shadow-inner">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex space-x-1">
                                                <div class="w-2 h-2 bg-sky-400 rounded-full animate-bounce"></div>
                                                <div class="w-2 h-2 bg-amber-400 rounded-full animate-bounce delay-100"></div>
                                                <div class="w-2 h-2 bg-emerald-400 rounded-full animate-bounce delay-200"></div>
                                            </div>
                                            <span class="text-xs font-medium text-slate-500 animate-pulse">Réflexion en cours...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="pt-4 mt-4 border-t border-white/50">
                                <form @submit.prevent="submit" class="flex gap-3 items-center">
                                    <input
                                        v-model="form.message"
                                        type="text"
                                        placeholder="Envoie ta vague de texte..."
                                        class="flex-1 px-4 py-3 rounded-2xl tropical-input focus:ring-2 focus:ring-amber-300 focus:border-transparent pr-12"
                                        :disabled="isLoading"
                                    />
                                    
                                    <!-- Voice Input Button -->
                                    <button 
                                        type="button"
                                        @click="toggleRecording"
                                        class="absolute right-36 top-1/2 -translate-y-1/2 p-2 rounded-full transition-all duration-300"
                                        :class="isRecording ? 'bg-red-500 text-white animate-pulse shadow-lg shadow-red-500/30' : 'text-slate-400 hover:text-amber-600 hover:bg-amber-50'"
                                        :disabled="isLoading"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5">
                                          <path stroke-linecap="round" stroke-linejoin="round" d="M12 18.75a6 6 0 0 0 6-6v-1.5m-6 7.5a6 6 0 0 1-6-6v-1.5m6 7.5v3.75m-3.75 0h7.5M12 15.75a3 3 0 0 1-3-3V4.5a3 3 0 1 1 6 0v8.25a3 3 0 0 1-3 3Z" />
                                        </svg>
                                    </button>

                                    <div class="flex items-center gap-2">
                                        <label class="cursor-pointer inline-flex items-center p-2 rounded-md border border-white/40 bg-white/60">
                                            <input ref="imageInput" type="file" accept="image/*" class="hidden" @change="onImageChange" />
                                            <svg class="w-5 h-5 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V7M16 3v4M8 3v4"></path></svg>
                                        </label>

                                        <PrimaryButton :disabled="isLoading || (!form.message.trim() && !imageDataUrl)" class="summer-button px-5 text-base">
                                            {{ isLoading ? 'Envoi...' : 'Envoyer la vague' }}
                                        </PrimaryButton>
                                    </div>
                                </form>

                                <div v-if="imageDataUrl" class="mt-3 flex items-center gap-3">
                                    <img :src="imageDataUrl" alt="preview" class="w-24 h-16 object-cover rounded-md border" />
                                    <button @click.prevent="() => { imageDataUrl = null; if (imageInput.value) imageInput.value.value = '' }" class="text-sm text-red-500">Retirer l'image</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

<style scoped>
.summer-bg {
    background: radial-gradient(circle at 10% 30%, rgba(186, 230, 253, 0.55), transparent 35%),
        radial-gradient(circle at 80% 10%, rgba(254, 215, 170, 0.6), transparent 40%),
        linear-gradient(135deg, #c7f9cc 0%, #fff3bf 40%, #e0f2fe 100%);
}

.sunset {
    position: absolute;
    right: -120px;
    top: -140px;
    width: 380px;
    height: 380px;
    border-radius: 9999px;
    background: radial-gradient(circle, #ffeda3 0%, #f59e0b 55%, #f97316 100%);
    filter: blur(8px);
    opacity: 0.65;
    animation: shimmer 14s ease-in-out infinite alternate;
}

.wave {
    position: absolute;
    left: -8%;
    right: -8%;
    height: 220px;
    background: radial-gradient(circle at 20% 40%, rgba(14, 165, 233, 0.1), transparent 40%),
        radial-gradient(circle at 70% 70%, rgba(59, 130, 246, 0.08), transparent 45%),
        rgba(255, 255, 255, 0.55);
    filter: blur(22px);
    transform: rotate(-3deg);
    animation: drift 18s ease-in-out infinite alternate;
}

.wave-1 {
    top: -140px;
}

.wave-2 {
    bottom: -150px;
    animation-delay: 6s;
}

.palms {
    position: absolute;
    inset: 0;
    background: radial-gradient(circle at 15% 85%, rgba(16, 185, 129, 0.08), transparent 35%),
        radial-gradient(circle at 90% 75%, rgba(14, 116, 144, 0.08), transparent 40%);
    pointer-events: none;
}

.glass-card {
    background: rgba(255, 255, 255, 0.75);
    backdrop-filter: blur(18px);
    border-radius: 24px;
}

.summer-panel {
    box-shadow: 0 20px 80px -30px rgba(245, 158, 11, 0.45);
}

.chat-shell {
    background: rgba(255, 255, 255, 0.8);
    backdrop-filter: blur(18px);
    border-radius: 28px;
    box-shadow: 0 24px 80px -32px rgba(14, 116, 144, 0.35);
}

.wave-mask {
    background-image: linear-gradient(180deg, rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.75));
}

.tropical-input {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.95), rgba(255, 255, 255, 0.75));
    border: 1px solid rgba(255, 214, 165, 0.7);
    box-shadow: inset 0 0 0 1px rgba(255, 255, 255, 0.5);
    transition: box-shadow 0.2s ease, transform 0.15s ease;
}

.tropical-input:focus {
    box-shadow: 0 10px 40px -20px rgba(14, 165, 233, 0.45);
    transform: translateY(-1px);
}

.summer-button {
    background: linear-gradient(120deg, #f97316, #fbbf24, #22d3ee);
    box-shadow: 0 12px 40px -16px rgba(15, 118, 110, 0.45), 0 0 0 1px rgba(255, 255, 255, 0.45) inset;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.summer-button:hover:not([disabled]) {
    transform: translateY(-2px);
    box-shadow: 0 14px 48px -14px rgba(249, 115, 22, 0.45);
}

@keyframes drift {
    0% {
        transform: translateX(0) rotate(-3deg);
    }
    100% {
        transform: translateX(18px) rotate(1deg);
    }
}

@keyframes shimmer {
    0% {
        opacity: 0.35;
    }
    100% {
        opacity: 0.8;
    }
}

@keyframes pulse-slow {
    0%, 100% {
        transform: scale(1);
    }
    50% {
        transform: scale(1.05);
    }
}

.animate-pulse-slow {
    animation: pulse-slow 6s ease-in-out infinite;
}

.delay-100 {
    animation-delay: 0.1s;
}

.delay-200 {
    animation-delay: 0.2s;
}
</style>

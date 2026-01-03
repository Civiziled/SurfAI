<script setup lang="ts">
import { ref, computed } from 'vue';
import { useStream } from '@laravel/stream-vue';
import { usePage } from '@inertiajs/vue3';
import AnimatedButton from '../../Components/AnimatedButton.vue';
import AnimatedMessage from '../../Components/AnimatedMessage.vue';

const page = usePage();
const props = page.props.value;

const message = ref('');
const model = ref(props.selectedModel ?? 'openai/gpt-4o-mini');
const temperature = ref(1.0);
const reasoningEffort = ref(null as null | 'low' | 'medium' | 'high');
const imageFile = ref<File|null>(null);
const imagePreview = ref<string | null>(null);
const imageUrl = ref<string | null>(null); // data URL returned from server or public URL
const uploading = ref(false);
const uploadError = ref<string | null>(null);

const { data, isFetching, isStreaming, send, cancel } = useStream(
  '/ask-stream',
  {
    onData: () => {
      // Chunk reçu — `data` est automatiquement concaténé
    },
    onFinish: () => {
      message.value = '';
    },
    onError: (err: Error) => {
      console.error('Erreur streaming:', err);
    },
  }
);

const submit = () => {
  if (!message.value.trim() && !imageUrl.value) return;

  send({
    message: message.value,
    image_url: imageUrl.value ?? null,
    model: model.value,
    temperature: temperature.value,
    reasoning_effort: reasoningEffort.value,
  });
};

const onFileChange = async (e: Event) => {
  const input = e.target as HTMLInputElement;
  if (!input.files || input.files.length === 0) return;
  const file = input.files[0];
  imageFile.value = file;
  imagePreview.value = URL.createObjectURL(file);
  uploadError.value = null;

  const fd = new FormData();
  fd.append('image', file);

  uploading.value = true;
  try {
    const tokenMeta = document.querySelector('meta[name="csrf-token"]') as HTMLMetaElement | null;
    const csrf = tokenMeta?.getAttribute('content') ?? '';

    const res = await fetch('/ask-stream/upload-image', {
      method: 'POST',
      headers: {
        'X-CSRF-TOKEN': csrf,
      },
      body: fd,
      credentials: 'same-origin',
    });

    if (!res.ok) {
      const txt = await res.text();
      uploadError.value = 'Upload failed: ' + txt;
      imageUrl.value = null;
    } else {
      const json = await res.json();
      imageUrl.value = json.url || null;
    }
  } catch (err: any) {
    uploadError.value = err?.message || String(err);
    imageUrl.value = null;
  } finally {
    uploading.value = false;
  }
};

const removeImage = () => {
  imageFile.value = null;
  imagePreview.value && URL.revokeObjectURL(imagePreview.value);
  imagePreview.value = null;
  imageUrl.value = null;
  uploadError.value = null;
};

const streamedContent = computed(() => {
  if (!data.value) return '';
  return data.value.replace(/\[REASONING\][\s\S]*?\[\/REASONING\]/g, '').trim();
});

const streamedReasoning = computed(() => {
  if (!data.value) return '';
  const matches = data.value.match(/\[REASONING\]([\s\S]*?)\[\/REASONING\]/g);
  if (!matches) return '';
  return matches
    .map((m) => m.replace(/\[REASONING\]/g, '').replace(/\[\/REASONING\]/g, ''))
    .join('');
});
</script>

<template>
  <div class="container mx-auto p-4">
    <h1 class="text-2xl font-bold mb-4">Streaming SSE — Démo</h1>

    <div class="mb-4">
      <label class="block text-sm font-medium text-gray-700">Modèle</label>
      <select v-model="model" class="mt-1 block w-full">
        <option v-for="m in $page.props.models" :key="m.id" :value="m.id">{{ m.name }}</option>
      </select>
    </div>

    <div class="mb-4">
      <label class="block text-sm font-medium text-gray-700">Message</label>
      <textarea v-model="message" rows="4" class="mt-1 block w-full border rounded p-2"></textarea>
    </div>

    <div class="mb-4">
      <label class="block text-sm font-medium text-gray-700">Image (optionnel)</label>
      <input type="file" accept="image/*" @change="onFileChange" class="mt-1" />
      <div v-if="imagePreview" class="mt-2">
        <img :src="imagePreview" alt="preview" class="max-w-xs border rounded" />
      </div>
      <div v-if="uploading" class="text-sm text-gray-500 mt-2">Optimisation en cours…</div>
      <div v-if="uploadError" class="text-sm text-red-600 mt-2">{{ uploadError }}</div>
      <div v-if="imageUrl && !uploadError" class="text-sm text-green-600 mt-2">Image prête à l'envoi (optimisée)</div>
      <button v-if="imagePreview" @click.prevent="removeImage" class="mt-2 text-sm text-gray-600">Supprimer l'image</button>
    </div>

    <div class="flex items-center gap-2 mb-6">
      <AnimatedButton @click="submit" :loading="isStreaming || isFetching">Envoyer (stream)</AnimatedButton>
      <AnimatedButton v-if="isStreaming" @click="cancel" type="button" :loading="false">Annuler</AnimatedButton>
      <div v-if="isFetching" class="text-sm text-gray-500">Connexion au stream…</div>
    </div>

    <div class="mb-6">
      <h3 class="font-semibold">Contenu (stream)</h3>
      <div class="mt-2">
        <AnimatedMessage :text="streamedContent" />
      </div>
    </div>

    <div v-if="streamedReasoning" class="mb-6">
      <h3 class="font-semibold">Trace de raisonnement</h3>
      <div class="mt-2">
        <AnimatedMessage :text="streamedReasoning" />
      </div>
    </div>
  </div>
</template>

<style scoped>
.container { max-width: 800px; }
</style>

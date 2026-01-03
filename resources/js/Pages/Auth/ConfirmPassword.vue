<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

const submit = () => {
    form.post(route('password.confirm'), {
        onFinish: () => form.reset(),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Confirm Password" />

        <div class="mb-8 text-center animate-fade-in-up">
            <h2 class="text-3xl font-display font-bold text-gray-900">Secure Area</h2>
            <p class="mt-2 text-gray-600">
                Please confirm your password before continuing.
            </p>
        </div>

        <form @submit.prevent="submit" class="space-y-6 animate-fade-in-up" style="animation-delay: 100ms;">
            <div>
                <InputLabel for="password" value="Password" class="text-gray-700 font-medium" />
                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full px-4 py-3 rounded-xl border-gray-200 focus:border-surf-ocean focus:ring-surf-ocean transition-all duration-300 shadow-sm hover:border-surf-ocean/50"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    autofocus
                    placeholder="Enter your password"
                />
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full flex justify-center py-3.5 text-base font-semibold shadow-lg hover:shadow-xl transition-all duration-300 active:scale-[0.98]"
                    :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    Confirm
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

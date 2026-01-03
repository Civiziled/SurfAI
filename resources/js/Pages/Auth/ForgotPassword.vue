<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';

defineProps({
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <GuestLayout>
        <Head title="Forgot Password" />

        <div class="mb-8 text-center animate-fade-in-up">
            <h2 class="text-3xl font-display font-bold text-gray-900">Reset Password</h2>
            <p class="mt-2 text-gray-600">
                Forgot your password? No problem. Enter your email to receive a reset link.
            </p>
        </div>

        <div v-if="status" class="mb-4 text-sm font-medium text-green-600 bg-green-50 p-3 rounded-lg border border-green-100 animate-pulse">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-6 animate-fade-in-up" style="animation-delay: 100ms;">
            <div>
                <InputLabel for="email" value="Email" class="text-gray-700 font-medium" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full px-4 py-3 rounded-xl border-gray-200 focus:border-surf-ocean focus:ring-surf-ocean transition-all duration-300 shadow-sm hover:border-surf-ocean/50"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="Enter your email address"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full flex justify-center py-3.5 text-base font-semibold shadow-lg hover:shadow-xl transition-all duration-300 active:scale-[0.98]"
                    :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    Email Password Reset Link
                </PrimaryButton>
            </div>
        </form>
    </GuestLayout>
</template>

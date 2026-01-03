<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

defineProps({
    canResetPassword: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
        onSuccess: () => {
            window.location.reload();
        },
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Log in" />

        <div class="mb-8 text-center animate-fade-in-up">
            <h2 class="text-3xl font-display font-bold text-gray-900">Welcome Back</h2>
            <p class="mt-2 text-gray-600">Please enter your details to sign in.</p>
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
                    placeholder="Enter your email"
                />

                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Password" class="text-gray-700 font-medium" />

                <TextInput
                    id="password"
                    type="password"
                    class="mt-1 block w-full px-4 py-3 rounded-xl border-gray-200 focus:border-surf-ocean focus:ring-surf-ocean transition-all duration-300 shadow-sm hover:border-surf-ocean/50"
                    v-model="form.password"
                    required
                    autocomplete="current-password"
                    placeholder="Enter your password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div class="flex items-center justify-between">
                <label class="flex items-center cursor-pointer group">
                    <Checkbox name="remember" v-model:checked="form.remember" class="text-surf-ocean focus:ring-surf-ocean rounded border-gray-300 transition-colors" />
                    <span class="ms-2 text-sm text-gray-600 group-hover:text-gray-900 transition-colors">Remember me</span>
                </label>

                <Link
                    v-if="canResetPassword"
                    :href="route('password.request')"
                    class="text-sm font-medium text-surf-ocean hover:text-surf-ocean-dark transition-colors"
                >
                    Forgot password?
                </Link>
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full flex justify-center py-3.5 text-base font-semibold shadow-lg hover:shadow-xl transition-all duration-300 active:scale-[0.98]"
                    :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    Sign in
                </PrimaryButton>
            </div>

            <p class="text-center text-sm text-gray-600 mt-8">
                Don't have an account? 
                <Link :href="route('register')" class="font-semibold text-surf-ocean hover:text-surf-ocean-dark hover:underline transition-colors">
                    Sign up for free
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>

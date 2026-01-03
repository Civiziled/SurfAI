<script setup>
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post(route('register'), {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Register" />

        <div class="mb-8 text-center animate-fade-in-up">
            <h2 class="text-3xl font-display font-bold text-gray-900">Start Your Journey</h2>
            <p class="mt-2 text-gray-600">Create an account to join the wave.</p>
        </div>

        <form @submit.prevent="submit" class="space-y-5 animate-fade-in-up" style="animation-delay: 100ms;">
            <div>
                <InputLabel for="name" value="Name" class="text-gray-700 font-medium" />

                <TextInput
                    id="name"
                    type="text"
                    class="mt-1 block w-full px-4 py-3 rounded-xl border-gray-200 focus:border-surf-ocean focus:ring-surf-ocean transition-all duration-300 shadow-sm hover:border-surf-ocean/50"
                    v-model="form.name"
                    required
                    autofocus
                    autocomplete="name"
                    placeholder="Enter your full name"
                />

                <InputError class="mt-2" :message="form.errors.name" />
            </div>

            <div>
                <InputLabel for="email" value="Email" class="text-gray-700 font-medium" />

                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full px-4 py-3 rounded-xl border-gray-200 focus:border-surf-ocean focus:ring-surf-ocean transition-all duration-300 shadow-sm hover:border-surf-ocean/50"
                    v-model="form.email"
                    required
                    autocomplete="username"
                    placeholder="Enter your email address"
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
                    autocomplete="new-password"
                    placeholder="Create a password"
                />

                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <div>
                <InputLabel
                    for="password_confirmation"
                    value="Confirm Password"
                    class="text-gray-700 font-medium"
                />

                <TextInput
                    id="password_confirmation"
                    type="password"
                    class="mt-1 block w-full px-4 py-3 rounded-xl border-gray-200 focus:border-surf-ocean focus:ring-surf-ocean transition-all duration-300 shadow-sm hover:border-surf-ocean/50"
                    v-model="form.password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Confirm your password"
                />

                <InputError
                    class="mt-2"
                    :message="form.errors.password_confirmation"
                />
            </div>

            <div class="pt-2">
                <PrimaryButton
                    class="w-full flex justify-center py-3.5 text-base font-semibold shadow-lg hover:shadow-xl transition-all duration-300 active:scale-[0.98]"
                    :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    Create Account
                </PrimaryButton>
            </div>

            <p class="text-center text-sm text-gray-600 mt-6">
                Already have an account? 
                <Link :href="route('login')" class="font-semibold text-surf-ocean hover:text-surf-ocean-dark hover:underline transition-colors">
                    Sign in here
                </Link>
            </p>
        </form>
    </GuestLayout>
</template>

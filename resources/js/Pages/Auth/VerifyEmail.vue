<script setup>
import { computed } from 'vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(
    () => props.status === 'verification-link-sent',
);
</script>

<template>
    <GuestLayout>
        <Head title="Email Verification" />

        <div class="mb-8 text-center animate-fade-in-up">
            <h2 class="text-3xl font-display font-bold text-gray-900">Verify Email</h2>
            <p class="mt-2 text-gray-600">
                Thanks for signing up! Please verify your email to continue.
            </p>
        </div>

        <div class="mb-6 text-sm text-gray-600 bg-blue-50 p-4 rounded-xl border border-blue-100 animate-fade-in-up" style="animation-delay: 50ms;">
            Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
        </div>

        <div v-if="verificationLinkSent" class="mb-6 text-sm font-medium text-green-600 bg-green-50 p-4 rounded-xl border border-green-100 animate-pulse">
            A new verification link has been sent to the email address you provided during registration.
        </div>

        <form @submit.prevent="submit" class="animate-fade-in-up" style="animation-delay: 100ms;">
            <div class="flex flex-col gap-4">
                <PrimaryButton
                    class="w-full flex justify-center py-3.5 text-base font-semibold shadow-lg hover:shadow-xl transition-all duration-300 active:scale-[0.98]"
                    :class="{ 'opacity-75 cursor-not-allowed': form.processing }"
                    :disabled="form.processing"
                >
                    Resend Verification Email
                </PrimaryButton>

                <div class="text-center">
                    <Link
                        :href="route('logout')"
                        method="post"
                        as="button"
                        class="text-sm text-gray-600 hover:text-gray-900 underline transition-colors"
                        >Log Out</Link
                    >
                </div>
            </div>
        </form>
    </GuestLayout>
</template>

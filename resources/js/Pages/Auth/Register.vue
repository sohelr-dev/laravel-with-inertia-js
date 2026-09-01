<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '../../Layouts/GuestLayout.vue';

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
});

const submit = () => {
    form.post('/register', {
        onFinish: () => form.reset('password', 'password_confirmation'),
    });
};
</script>

<template>
    <GuestLayout>
        <div class="w-100" style="max-width: 420px;">
            <div class="card shadow-sm border-0 card-bd-accent">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h3 mb-4 text-center">Create Account</h1>

                    <form @submit.prevent="submit" novalidate>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input
                                id="name"
                                type="text"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.name }"
                                v-model="form.name"
                                autofocus
                            >
                            <div class="invalid-feedback" v-if="form.errors.name">{{ form.errors.name }}</div>
                        </div>

                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input
                                id="email"
                                type="email"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.email }"
                                v-model="form.email"
                            >
                            <div class="invalid-feedback" v-if="form.errors.email">{{ form.errors.email }}</div>
                        </div>

                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input
                                id="password"
                                type="password"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.password }"
                                v-model="form.password"
                            >
                            <div class="invalid-feedback" v-if="form.errors.password">{{ form.errors.password }}</div>
                        </div>

                        <div class="mb-4">
                            <label for="password_confirmation" class="form-label">Confirm Password</label>
                            <input
                                id="password_confirmation"
                                type="password"
                                class="form-control"
                                v-model="form.password_confirmation"
                            >
                        </div>

                        <button type="submit" class="btn btn-primary w-100" :disabled="form.processing">
                            <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                            Register
                        </button>
                    </form>

                    <p class="text-center mt-4 mb-0">
                        Already have an account?
                        <Link href="/login" class="link-primary text-decoration-none fw-semibold">Login</Link>
                    </p>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

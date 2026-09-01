<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import GuestLayout from '../../Layouts/GuestLayout.vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post('/login', {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <div class="w-100" style="max-width: 420px;">
            <div class="card shadow-sm border-0 card-bd-accent">
                <div class="card-body p-4 p-md-5">
                    <h1 class="h3 mb-4 text-center">Login</h1>

                    <form @submit.prevent="submit" novalidate>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input
                                id="email"
                                type="email"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.email }"
                                v-model="form.email"
                                autofocus
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

                        <div class="mb-4 form-check">
                            <input id="remember" type="checkbox" class="form-check-input" v-model="form.remember">
                            <label for="remember" class="form-check-label">Remember Me</label>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" :disabled="form.processing">
                            <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                            Login
                        </button>
                    </form>

                    <p class="text-center mt-4 mb-0">
                        Don't have an account?
                        <Link href="/register" class="link-primary text-decoration-none fw-semibold">Register</Link>
                    </p>
                </div>
            </div>
        </div>
    </GuestLayout>
</template>

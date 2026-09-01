<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';

const page = usePage();
</script>

<template>
    <AppLayout>
        <div class="card shadow-sm border-0 card-bd-accent text-center">
            <div class="card-body p-5">
                <h1 class="mb-3">Inertia + Vue + Laravel</h1>
                <p class="text-muted mb-4">Authentication demo — Bangladesh flag theme</p>

                <div v-if="page.props.auth.user" class="mb-4">
                    <p class="mb-3">Logged in as <strong>{{ page.props.auth.user.name }}</strong></p>
                    <Link href="/dashboard" class="btn btn-primary">Go to Dashboard</Link>
                </div>
                <div v-else class="mb-4 d-flex justify-content-center gap-2">
                    <Link href="/login" class="btn btn-outline-primary">Login</Link>
                    <Link href="/register" class="btn btn-danger">Register</Link>
                </div>

                <Link href="/about" class="link-primary text-decoration-none">Go to About Page</Link>
            </div>
        </div>
    </AppLayout>
</template>

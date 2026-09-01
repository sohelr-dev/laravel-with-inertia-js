<script setup>
import { Link } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';
</script>

<template>
    <AppLayout>
        <div class="card shadow-sm border-0 card-bd-accent text-center">
            <div class="card-body p-5">
                <h1 class="mb-3">About Page</h1>
                <p class="text-muted mb-4">For Testing Navigation</p>
                <Link href="/" class="link-primary text-decoration-none">Back to Home</Link>
            </div>
        </div>
    </AppLayout>
</template>

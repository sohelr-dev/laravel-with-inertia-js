<script setup>
import { usePage } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';

const page = usePage();
</script>

<template>
    <AppLayout>
        <div class="card shadow-sm border-0 card-bd-accent">
            <div class="card-body p-4 p-md-5">
                <h1 class="h3 mb-3">Dashboard</h1>
                <p class="mb-1">
                    Welcome, <strong>{{ page.props.auth.user.name }}</strong>
                </p>
                <p class="text-muted">{{ page.props.auth.user.email }}</p>
            </div>
        </div>
    </AppLayout>
</template>

<script setup>
import { Link, usePage } from '@inertiajs/vue3';

const page = usePage();
</script>

<template>
    <div class="min-vh-100 d-flex flex-column bg-light">
        <div class="text-center pt-5 pb-4">
            <Link href="/" class="text-decoration-none">
                <span class="fs-4 fw-bold text-primary">{{ page.props.appName }}</span>
            </Link>
        </div>

        <div class="flex-grow-1 d-flex align-items-start justify-content-center px-3">
            <slot />
        </div>
    </div>
</template>

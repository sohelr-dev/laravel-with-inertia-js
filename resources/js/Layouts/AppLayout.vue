<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';

const page = usePage();

const logout = () => {
    router.post('/logout');
};
</script>

<template>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm">
        <div class="container">
            <Link class="navbar-brand fw-bold" href="/">{{ page.props.appName }}</Link>

            <div class="d-flex align-items-center gap-3">
                <Link class="nav-link text-white" href="/">Home</Link>
                <Link class="nav-link text-white" href="/about">About</Link>

                <template v-if="page.props.auth.user">
                    <Link class="nav-link text-white" href="/dashboard">Dashboard</Link>
                    <Link class="nav-link text-white" href="/products">Products</Link>
                    <Link class="nav-link text-white" href="/pos">POS</Link>
                    <button class="btn btn-sm btn-outline-light" @click="logout">Logout</button>
                </template>
                <template v-else>
                    <Link class="nav-link text-white" href="/login">Login</Link>
                    <Link class="btn btn-sm btn-danger" href="/register">Register</Link>
                </template>
            </div>
        </div>
    </nav>

    <main class="py-5">
        <div class="container">
            <slot />
        </div>
    </main>
</template>

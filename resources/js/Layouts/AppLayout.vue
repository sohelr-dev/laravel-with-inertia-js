<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';

const page = usePage();

const logout = () => {
    router.post('/logout');
};
</script>

<template>
    <nav class="site-navbar navbar navbar-expand-lg sticky-top">
        <div class="container">
            <Link class="navbar-brand d-flex align-items-center gap-2 fw-bold" href="/">
                <span class="admin-sidebar__logo">R</span>
                {{ page.props.appName }}
            </Link>

            <div class="d-flex align-items-center gap-2 gap-lg-3">
                <Link class="nav-link" href="/">Home</Link>
                <Link class="nav-link" href="/about">About</Link>

                <template v-if="page.props.auth.user">
                    <Link class="btn btn-sm btn-primary" href="/dashboard">Dashboard</Link>
                    <button class="btn btn-sm btn-outline-secondary" @click="logout">Logout</button>
                </template>
                <template v-else>
                    <Link class="nav-link" href="/login">Login</Link>
                    <Link class="btn btn-sm btn-danger" href="/register">Register</Link>
                </template>
            </div>
        </div>
    </nav>

    <main>
        <slot />
    </main>

    <footer class="site-footer">
        <div class="container d-flex flex-wrap justify-content-between align-items-center gap-2 py-4">
            <span class="text-muted small">&copy; {{ new Date().getFullYear() }} {{ page.props.appName }}</span>
            <div class="d-flex gap-3">
                <Link class="text-muted small text-decoration-none" href="/">Home</Link>
                <Link class="text-muted small text-decoration-none" href="/about">About</Link>
                <Link class="text-muted small text-decoration-none" href="/login">Login</Link>
            </div>
        </div>
    </footer>
</template>

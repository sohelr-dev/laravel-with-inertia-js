<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    title: { type: String, default: '' },
});

const page = usePage();
const sidebarOpen = ref(false);

const nav = [
    {
        label: 'Dashboard',
        href: '/dashboard',
        match: '/dashboard',
        icon: 'M3 13h4v8H3v-8Zm7-9h4v17h-4V4Zm7 5h4v12h-4V9Z',
    },
    {
        label: 'Point of Sale',
        href: '/pos',
        match: '/pos',
        icon: 'M4 6h16l-1.5 9h-13L4 6Zm0 0-.5-2H2M8 19a1 1 0 1 0 0 2 1 1 0 0 0 0-2Zm9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2Z',
    },
    {
        label: 'Products',
        href: '/products',
        match: '/products',
        icon: 'M21 8 12 3 3 8l9 5 9-5Zm0 0v8l-9 5-9-5V8m18 4-9 5-9-5',
    },
];

const isActive = (item) => page.url === item.match || page.url.startsWith(item.match + '/') || page.url.startsWith(item.match + '?');

const logout = () => {
    router.post('/logout');
};
</script>

<template>
    <div class="admin-shell">
        <div v-if="sidebarOpen" class="sidebar-backdrop d-lg-none" @click="sidebarOpen = false"></div>

        <aside class="admin-sidebar" :class="{ 'admin-sidebar--open': sidebarOpen }">
            <div class="admin-sidebar__brand">
                <Link href="/dashboard" class="d-flex align-items-center gap-2 text-decoration-none">
                    <span class="admin-sidebar__logo">R</span>
                    <span class="admin-sidebar__brand-text">{{ page.props.appName }}</span>
                </Link>
            </div>

            <nav class="admin-sidebar__nav">
                <Link
                    v-for="item in nav"
                    :key="item.href"
                    :href="item.href"
                    class="admin-sidebar__link"
                    :class="{ 'admin-sidebar__link--active': isActive(item) }"
                    @click="sidebarOpen = false"
                >
                    <svg class="admin-sidebar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path :d="item.icon" />
                    </svg>
                    <span>{{ item.label }}</span>
                </Link>
            </nav>

            <div class="admin-sidebar__footer">
                <Link href="/" class="admin-sidebar__link">
                    <svg class="admin-sidebar__icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 12 12 3l9 9M5 10v10h5v-6h4v6h5V10" />
                    </svg>
                    <span>Public Site</span>
                </Link>
            </div>
        </aside>

        <div class="admin-main">
            <header class="admin-topbar">
                <div class="d-flex align-items-center gap-3">
                    <button type="button" class="admin-topbar__toggle d-lg-none" @click="sidebarOpen = true">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                            <path d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <h1 class="admin-topbar__title">{{ title }}</h1>
                </div>

                <div class="dropdown">
                    <button
                        class="admin-topbar__user btn"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false"
                    >
                        <span class="admin-topbar__avatar">{{ page.props.auth.user?.name?.charAt(0)?.toUpperCase() }}</span>
                        <span class="d-none d-sm-inline">{{ page.props.auth.user?.name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><span class="dropdown-item-text text-muted small">{{ page.props.auth.user?.email }}</span></li>
                        <li><hr class="dropdown-divider"></li>
                        <li><button type="button" class="dropdown-item" @click="logout">Logout</button></li>
                    </ul>
                </div>
            </header>

            <main class="admin-content">
                <div v-if="page.props.flash?.success" class="alert alert-success">{{ page.props.flash.success }}</div>
                <slot />
            </main>
        </div>
    </div>
</template>

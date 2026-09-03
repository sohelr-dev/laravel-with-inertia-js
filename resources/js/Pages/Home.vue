<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import AppLayout from '../Layouts/AppLayout.vue';

const page = usePage();

const features = [
    {
        title: 'Point of Sale',
        description: 'A fast, touch-friendly checkout screen — search or tap products, build a cart, and take cash, card or mobile payments.',
        icon: 'M4 6h16l-1.5 9h-13L4 6Zm0 0-.5-2H2M8 19a1 1 0 1 0 0 2 1 1 0 0 0 0-2Zm9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2Z',
        bg: '#e6f4f0',
        color: 'var(--bd-green)',
    },
    {
        title: 'Product Catalog',
        description: 'Manage products, pricing, categories and stock levels in one place, with low-stock alerts before you run out.',
        icon: 'M21 8 12 3 3 8l9 5 9-5Zm0 0v8l-9 5-9-5V8m18 4-9 5-9-5',
        bg: '#fdeaea',
        color: 'var(--bd-red)',
    },
    {
        title: 'Customer Records',
        description: 'Keep customer contact details and outstanding balances organized, and attach them to sales at checkout.',
        icon: 'M17 20h5v-2a4 4 0 0 0-3-3.87M9 20H4v-2a4 4 0 0 1 3-3.87m5-3.13a4 4 0 1 0 0-8 4 4 0 0 0 0 8Zm6 0a4 4 0 1 0 0-8',
        bg: '#eef6fb',
        color: '#0d6efd',
    },
    {
        title: 'Live Dashboard',
        description: "See today's sales, revenue and low-stock items at a glance, plus a running log of recent transactions.",
        icon: 'M3 13h4v8H3v-8Zm7-9h4v17h-4V4Zm7 5h4v12h-4V9Z',
        bg: '#fff4e5',
        color: '#e08a00',
    },
];
</script>

<template>
    <AppLayout>
        <section class="hero">
            <div class="container py-5">
                <div class="row align-items-center g-5">
                    <div class="col-lg-7">
                        <span class="badge rounded-pill hero-badge mb-3">Retail &amp; POS management</span>
                        <h1 class="hero-title mb-3">Run your shop from one clean, fast dashboard</h1>
                        <p class="hero-subtitle mb-4">
                            {{ page.props.appName }} brings your point of sale, product catalog and customer
                            records together — built for single or multi-outlet retail businesses.
                        </p>

                        <div v-if="page.props.auth.user" class="d-flex flex-wrap gap-2">
                            <Link href="/dashboard" class="btn btn-primary btn-lg px-4">Go to Dashboard</Link>
                            <Link href="/pos" class="btn btn-outline-light btn-lg px-4">Open POS</Link>
                        </div>
                        <div v-else class="d-flex flex-wrap gap-2">
                            <Link href="/register" class="btn btn-danger btn-lg px-4">Get Started</Link>
                            <Link href="/login" class="btn btn-outline-light btn-lg px-4">Login</Link>
                        </div>
                    </div>

                    <div class="col-lg-5">
                        <div class="hero-panel">
                            <div class="hero-panel__row">
                                <span>Today's Revenue</span>
                                <strong>৳ 4,280.00</strong>
                            </div>
                            <div class="hero-panel__row">
                                <span>Sales Completed</span>
                                <strong>36</strong>
                            </div>
                            <div class="hero-panel__row">
                                <span>Low Stock Items</span>
                                <strong class="text-danger">3</strong>
                            </div>
                            <div class="hero-panel__note">Sample preview — sign in to see your real numbers.</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section class="container py-5">
            <div class="text-center mb-5">
                <h2 class="h3 fw-bold mb-2">Everything a growing shop needs</h2>
                <p class="text-muted">One system for checkout, inventory and customers.</p>
            </div>

            <div class="row g-4">
                <div v-for="feature in features" :key="feature.title" class="col-sm-6 col-lg-3">
                    <div class="feature-card h-100">
                        <span class="stat-card__icon mb-3" :style="{ backgroundColor: feature.bg, color: feature.color }">
                            <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                <path :d="feature.icon" />
                            </svg>
                        </span>
                        <h3 class="h6 fw-bold mb-2">{{ feature.title }}</h3>
                        <p class="text-muted small mb-0">{{ feature.description }}</p>
                    </div>
                </div>
            </div>
        </section>

        <section class="cta-band">
            <div class="container py-5 text-center">
                <h2 class="h3 fw-bold mb-3 text-white">Ready to see it in action?</h2>
                <p class="text-white-50 mb-4">Create an account and start ringing up sales in minutes.</p>
                <div v-if="page.props.auth.user" class="d-flex justify-content-center gap-2">
                    <Link href="/pos" class="btn btn-light btn-lg px-4">Open POS</Link>
                </div>
                <div v-else class="d-flex justify-content-center gap-2">
                    <Link href="/register" class="btn btn-light btn-lg px-4">Create Free Account</Link>
                </div>
            </div>
        </section>
    </AppLayout>
</template>

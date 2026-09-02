<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import AdminLayout from '../Layouts/AdminLayout.vue';

const props = defineProps({
    stats: Object,
    lowStockProducts: Array,
    recentSales: Array,
});

const page = usePage();

const formatMoney = (value) => Number(value).toFixed(2);

const formatTime = (value) => new Date(value).toLocaleString(undefined, {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
});

const statCards = [
    {
        key: 'productCount',
        label: 'Active Products',
        icon: 'M21 8 12 3 3 8l9 5 9-5Zm0 0v8l-9 5-9-5V8m18 4-9 5-9-5',
        bg: '#e6f4f0',
        color: 'var(--bd-green)',
        format: (v) => v,
    },
    {
        key: 'lowStockCount',
        label: 'Low Stock Items',
        icon: 'M12 9v4m0 4h.01M10.29 3.86 1.82 18a1 1 0 0 0 .86 1.5h18.64a1 1 0 0 0 .86-1.5L13.71 3.86a1 1 0 0 0-1.72 0Z',
        bg: '#fdeaea',
        color: 'var(--bd-red)',
        format: (v) => v,
    },
    {
        key: 'todaySalesCount',
        label: "Today's Sales",
        icon: 'M4 6h16l-1.5 9h-13L4 6Zm0 0-.5-2H2M8 19a1 1 0 1 0 0 2 1 1 0 0 0 0-2Zm9 0a1 1 0 1 0 0 2 1 1 0 0 0 0-2Z',
        bg: '#e6f4f0',
        color: 'var(--bd-green)',
        format: (v) => v,
    },
    {
        key: 'todayRevenue',
        label: "Today's Revenue",
        icon: 'M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6',
        bg: '#eef6fb',
        color: '#0d6efd',
        format: (v) => formatMoney(v),
    },
];
</script>

<template>
    <AdminLayout title="Dashboard">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h5 mb-1">Welcome back, {{ page.props.auth.user.name }}</h2>
                <p class="text-muted mb-0 small">Here's what's happening in your store today.</p>
            </div>
            <div class="d-flex gap-2">
                <Link href="/pos" class="btn btn-primary btn-sm">New Sale</Link>
                <Link href="/products/create" class="btn btn-outline-secondary btn-sm">+ Product</Link>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div v-for="card in statCards" :key="card.key" class="col-6 col-xl-3">
                <div class="stat-card">
                    <span class="stat-card__icon" :style="{ backgroundColor: card.bg, color: card.color }">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path :d="card.icon" />
                        </svg>
                    </span>
                    <div>
                        <div class="stat-card__value">{{ card.format(stats[card.key]) }}</div>
                        <div class="stat-card__label">{{ card.label }}</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="h6 mb-0">Recent Sales</h2>
                            <Link href="/pos" class="small link-primary text-decoration-none">Open POS</Link>
                        </div>

                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Invoice</th>
                                        <th>Cashier</th>
                                        <th>Items</th>
                                        <th>Payment</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-end">When</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="sale in recentSales" :key="sale.id">
                                        <td class="fw-semibold">{{ sale.invoice_no }}</td>
                                        <td>{{ sale.user?.name ?? '—' }}</td>
                                        <td>{{ sale.items_count }}</td>
                                        <td class="text-capitalize">{{ sale.payment_method }}</td>
                                        <td class="text-end">{{ formatMoney(sale.total) }}</td>
                                        <td class="text-end text-muted small">{{ formatTime(sale.created_at) }}</td>
                                    </tr>
                                    <tr v-if="recentSales.length === 0">
                                        <td colspan="6" class="text-center text-muted py-4">No sales yet — head to the POS to make one.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="h6 mb-0">Low Stock Alerts</h2>
                            <Link href="/products" class="small link-primary text-decoration-none">View all</Link>
                        </div>

                        <ul class="list-unstyled mb-0">
                            <li
                                v-for="product in lowStockProducts"
                                :key="product.id"
                                class="d-flex justify-content-between align-items-center py-2 border-bottom"
                            >
                                <div>
                                    <div class="fw-semibold">{{ product.name }}</div>
                                    <div class="text-muted small">{{ product.sku }}</div>
                                </div>
                                <span class="badge text-bg-danger">{{ product.stock }} {{ product.unit }} left</span>
                            </li>
                            <li v-if="lowStockProducts.length === 0" class="text-center text-muted py-4">
                                All products are well stocked.
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

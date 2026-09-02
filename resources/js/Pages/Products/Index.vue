<script setup>
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '../../Layouts/AdminLayout.vue';

const props = defineProps({
    products: Object,
    filters: Object,
});

const search = ref(props.filters?.search ?? '');

let debounceTimer = null;
const onSearch = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get('/products', { search: search.value }, { preserveState: true, replace: true });
    }, 300);
};

const formatMoney = (value) => Number(value).toFixed(2);
const stockBadge = (product) => {
    if (product.stock <= 0) return 'text-bg-danger';
    if (product.stock <= 10) return 'text-bg-warning';
    return 'text-bg-light';
};
</script>

<template>
    <AdminLayout title="Products">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h5 mb-1">Product Catalog</h2>
                <p class="text-muted small mb-0">{{ products.total }} product{{ products.total === 1 ? '' : 's' }} total</p>
            </div>
            <Link href="/products/create" class="btn btn-primary btn-sm">+ Create Product</Link>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="input-group mb-3" style="max-width: 360px;">
                    <span class="input-group-text bg-white border-end-0">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="7" /><path d="m21 21-4.3-4.3" />
                        </svg>
                    </span>
                    <input
                        type="search"
                        class="form-control border-start-0"
                        placeholder="Search by name, SKU or barcode"
                        v-model="search"
                        @input="onSearch"
                    >
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>SKU</th>
                                <th>Category</th>
                                <th class="text-end">Price</th>
                                <th class="text-end">Stock</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="product in products.data" :key="product.id">
                                <td class="fw-semibold">{{ product.name }}</td>
                                <td class="text-muted">{{ product.sku }}</td>
                                <td>{{ product.category ?? '—' }}</td>
                                <td class="text-end">{{ formatMoney(product.price) }}</td>
                                <td class="text-end">
                                    <span class="badge" :class="stockBadge(product)">{{ product.stock }} {{ product.unit }}</span>
                                </td>
                                <td>
                                    <span class="badge" :class="product.is_active ? 'text-bg-success' : 'text-bg-secondary'">
                                        {{ product.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="products.data.length === 0">
                                <td colspan="6" class="text-center text-muted py-5">No products found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <nav v-if="products.links.length > 3" class="d-flex justify-content-end mt-3">
                    <ul class="pagination pagination-sm mb-0">
                        <li v-for="link in products.links" :key="link.label" class="page-item" :class="{ active: link.active, disabled: !link.url }">
                            <Link class="page-link" :href="link.url || '#'" v-html="link.label" />
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </AdminLayout>
</template>

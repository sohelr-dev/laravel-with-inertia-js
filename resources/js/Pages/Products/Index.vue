<script setup>
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '../../Layouts/AppLayout.vue';

const props = defineProps({
    products: Object,
    filters: Object,
});

const page = usePage();
const search = ref(props.filters?.search ?? '');

let debounceTimer = null;
const onSearch = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get('/products', { search: search.value }, { preserveState: true, replace: true });
    }, 300);
};

const formatMoney = (value) => Number(value).toFixed(2);
</script>

<template>
    <AppLayout>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Products</h1>
            <Link href="/products/create" class="btn btn-primary btn-sm">+ Create Product</Link>
        </div>

        <div v-if="page.props.flash?.success" class="alert alert-success">{{ page.props.flash.success }}</div>

        <div class="card shadow-sm border-0 card-bd-accent">
            <div class="card-body p-4">
                <input
                    type="search"
                    class="form-control mb-3"
                    style="max-width: 320px;"
                    placeholder="Search by name, SKU or barcode"
                    v-model="search"
                    @input="onSearch"
                >

                <div class="table-responsive">
                    <table class="table align-middle">
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
                                <td>{{ product.name }}</td>
                                <td>{{ product.sku }}</td>
                                <td>{{ product.category ?? '—' }}</td>
                                <td class="text-end">{{ formatMoney(product.price) }}</td>
                                <td class="text-end">{{ product.stock }} {{ product.unit }}</td>
                                <td>
                                    <span class="badge" :class="product.is_active ? 'text-bg-success' : 'text-bg-secondary'">
                                        {{ product.is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="products.data.length === 0">
                                <td colspan="6" class="text-center text-muted py-4">No products found.</td>
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
    </AppLayout>
</template>

<script setup>
import { Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AdminLayout from '../../Layouts/AdminLayout.vue';

const props = defineProps({
    customers: Object,
    filters: Object,
});

const search = ref(props.filters?.search ?? '');

let debounceTimer = null;
const onSearch = () => {
    clearTimeout(debounceTimer);
    debounceTimer = setTimeout(() => {
        router.get('/customers', { search: search.value }, { preserveState: true, replace: true });
    }, 300);
};

const formatMoney = (value) => Number(value).toFixed(2);
</script>

<template>
    <AdminLayout title="Customers">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h5 mb-1">Customers</h2>
                <p class="text-muted small mb-0">{{ customers.total }} customer{{ customers.total === 1 ? '' : 's' }}
                    total</p>
            </div>
            <Link href="/customers/create" class="btn btn-primary btn-sm">+ Create Customer</Link>
        </div>

        <div class="card border-0 shadow-sm">
            <div class="card-body p-4">
                <div class="input-group mb-3" style="max-width: 360px;">
                    <span class="input-group-text bg-white border-end-0">
                        <svg viewBox="0 0 24 24" width="16" height="16" fill="none" stroke="currentColor"
                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="7" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                    </span>
                    <input type="search" class="form-control border-start-0"
                        placeholder="Search by name, phone or email" v-model="search" @input="onSearch">
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th class="text-end">Total Due</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="customer in customers.data" :key="customer.id">
                                <td class="fw-semibold">
                                    <Link :href="`/customers/${customer.id}`" class="text-decoration-none">{{ customer.name }}</Link>
                                </td>
                                <td>{{ customer.phone ?? '—' }}</td>
                                <td class="text-muted">{{ customer.email ?? '—' }}</td>
                                <td class="text-end">
                                    <span class="badge"
                                        :class="Number(customer.total_due) > 0 ? 'text-bg-danger' : 'text-bg-light'">
                                        {{ formatMoney(customer.total_due) }}
                                    </span>
                                </td>
                            </tr>
                            <tr v-if="customers.data.length === 0">
                                <td colspan="4" class="text-center text-muted py-5">No customers found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <nav v-if="customers.links.length > 3" class="d-flex justify-content-end mt-3">
                    <ul class="pagination pagination-sm mb-0">
                        <li v-for="link in customers.links" :key="link.label" class="page-item"
                            :class="{ active: link.active, disabled: !link.url }">
                            <Link class="page-link" :href="link.url || '#'" v-html="link.label" />
                        </li>
                    </ul>
                </nav>
            </div>
        </div>
    </AdminLayout>
</template>

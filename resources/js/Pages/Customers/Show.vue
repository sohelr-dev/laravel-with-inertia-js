<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';

const props = defineProps({
    customer: Object,
    payments: Object,
    sales: Object,
});

const paymentForm = useForm({
    amount: '',
    note: '',
});

const formatMoney = (value) => Number(value).toFixed(2);

const formatDate = (value) => new Date(value).toLocaleString(undefined, {
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
});

const recordPayment = () => {
    paymentForm.post(`/customers/${props.customer.id}/payments`, {
        preserveScroll: true,
        onSuccess: () => paymentForm.reset(),
    });
};
</script>

<template>
    <AdminLayout :title="customer.name">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h5 mb-1">{{ customer.name }}</h2>
                <p class="text-muted small mb-0">{{ customer.phone ?? 'No phone' }} · {{ customer.email ?? 'No email' }}</p>
            </div>
            <Link href="/customers" class="btn btn-outline-secondary btn-sm">Back to Customers</Link>
        </div>

        <div class="row g-4">
            <div class="col-lg-4">
                <div class="stat-card mb-4">
                    <span class="stat-card__icon" :style="{ backgroundColor: Number(customer.total_due) > 0 ? '#fdeaea' : '#e6f4f0', color: Number(customer.total_due) > 0 ? 'var(--bd-red)' : 'var(--bd-green)' }">
                        <svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M12 1v22M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6" />
                        </svg>
                    </span>
                    <div>
                        <div class="stat-card__value">{{ formatMoney(customer.total_due) }}</div>
                        <div class="stat-card__label">Outstanding Due</div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h3 class="h6 mb-2">Contact</h3>
                        <p class="text-muted small mb-1">{{ customer.address ?? 'No address on file' }}</p>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="h6 mb-3">Record Payment</h3>

                        <div v-if="Number(customer.total_due) <= 0" class="text-muted small">
                            No outstanding due — nothing to record.
                        </div>

                        <form v-else @submit.prevent="recordPayment">
                            <div class="mb-2">
                                <label for="amount" class="form-label small mb-1">Amount</label>
                                <input
                                    id="amount"
                                    type="number"
                                    min="0.01"
                                    step="0.01"
                                    :max="customer.total_due"
                                    class="form-control form-control-sm"
                                    :class="{ 'is-invalid': paymentForm.errors.amount }"
                                    v-model="paymentForm.amount"
                                >
                                <div class="invalid-feedback" v-if="paymentForm.errors.amount">{{ paymentForm.errors.amount }}</div>
                            </div>

                            <div class="mb-3">
                                <label for="note" class="form-label small mb-1">Note (optional)</label>
                                <input id="note" type="text" class="form-control form-control-sm" v-model="paymentForm.note">
                            </div>

                            <button type="submit" class="btn btn-primary btn-sm w-100" :disabled="paymentForm.processing">
                                <span v-if="paymentForm.processing" class="spinner-border spinner-border-sm me-2"></span>
                                Record Payment
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h3 class="h6 mb-3">Recent Sales</h3>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th>Invoice</th>
                                        <th class="text-end">Total</th>
                                        <th class="text-end">Paid</th>
                                        <th class="text-end">Due</th>
                                        <th class="text-end">When</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="sale in sales.data" :key="sale.id">
                                        <td class="fw-semibold">{{ sale.invoice_no }}</td>
                                        <td class="text-end">{{ formatMoney(sale.total) }}</td>
                                        <td class="text-end">{{ formatMoney(sale.paid_amount) }}</td>
                                        <td class="text-end">
                                            <span class="badge" :class="Number(sale.due_amount) > 0 ? 'text-bg-danger' : 'text-bg-light'">
                                                {{ formatMoney(sale.due_amount) }}
                                            </span>
                                        </td>
                                        <td class="text-end text-muted small">{{ formatDate(sale.created_at) }}</td>
                                    </tr>
                                    <tr v-if="sales.data.length === 0">
                                        <td colspan="5" class="text-center text-muted py-4">No sales yet.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <nav v-if="sales.links.length > 3" class="d-flex justify-content-end mt-3">
                            <ul class="pagination pagination-sm mb-0">
                                <li v-for="link in sales.links" :key="link.label" class="page-item" :class="{ active: link.active, disabled: !link.url }">
                                    <Link class="page-link" :href="link.url || '#'" v-html="link.label" />
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>

                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <h3 class="h6 mb-3">Payment History</h3>
                        <div class="table-responsive">
                            <table class="table align-middle mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-end">Amount</th>
                                        <th>Note</th>
                                        <th>Recorded By</th>
                                        <th class="text-end">When</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="payment in payments.data" :key="payment.id">
                                        <td class="text-end fw-semibold text-success">{{ formatMoney(payment.amount) }}</td>
                                        <td class="text-muted">{{ payment.note ?? '—' }}</td>
                                        <td>{{ payment.user?.name ?? '—' }}</td>
                                        <td class="text-end text-muted small">{{ formatDate(payment.created_at) }}</td>
                                    </tr>
                                    <tr v-if="payments.data.length === 0">
                                        <td colspan="4" class="text-center text-muted py-4">No payments recorded yet.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <nav v-if="payments.links.length > 3" class="d-flex justify-content-end mt-3">
                            <ul class="pagination pagination-sm mb-0">
                                <li v-for="link in payments.links" :key="link.label" class="page-item" :class="{ active: link.active, disabled: !link.url }">
                                    <Link class="page-link" :href="link.url || '#'" v-html="link.label" />
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

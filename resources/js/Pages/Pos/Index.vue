<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AdminLayout from '../../Layouts/AdminLayout.vue';

const props = defineProps({
    products: Array,
    categories: Array,
});

const page = usePage();

const search = ref('');
const activeCategory = ref('');
const cart = ref([]);
const lastReceipt = ref(page.props.flash?.receipt ?? null);

const filteredProducts = computed(() => {
    return props.products.filter((product) => {
        const matchesSearch = !search.value
            || product.name.toLowerCase().includes(search.value.toLowerCase())
            || product.sku.toLowerCase().includes(search.value.toLowerCase())
            || (product.barcode ?? '').toLowerCase().includes(search.value.toLowerCase());

        const matchesCategory = !activeCategory.value || product.category === activeCategory.value;

        return matchesSearch && matchesCategory;
    });
});

const cartQuantity = (productId) => cart.value.find((line) => line.product.id === productId)?.quantity ?? 0;
const cartItemCount = computed(() => cart.value.reduce((sum, line) => sum + line.quantity, 0));

const addToCart = (product) => {
    lastReceipt.value = null;
    const line = cart.value.find((entry) => entry.product.id === product.id);

    if (line) {
        if (line.quantity < product.stock) {
            line.quantity++;
        }
    } else if (product.stock > 0) {
        cart.value.push({ product, quantity: 1 });
    }
};

const decrement = (productId) => {
    const line = cart.value.find((entry) => entry.product.id === productId);
    if (!line) return;

    line.quantity--;
    if (line.quantity <= 0) {
        cart.value = cart.value.filter((entry) => entry.product.id !== productId);
    }
};

const removeLine = (productId) => {
    cart.value = cart.value.filter((entry) => entry.product.id !== productId);
};

const clearCart = () => {
    cart.value = [];
};

const subtotal = computed(() => cart.value.reduce((sum, line) => sum + line.product.price * line.quantity, 0));

const form = useForm({
    items: [],
    discount: 0,
    paid_amount: '',
    payment_method: 'cash',
});

const total = computed(() => Math.max(subtotal.value - (Number(form.discount) || 0), 0));
const changeDue = computed(() => Math.max((Number(form.paid_amount) || 0) - total.value, 0));

const formatMoney = (value) => Number(value).toFixed(2);

const checkout = () => {
    lastReceipt.value = null;
    form.items = cart.value.map((line) => ({ product_id: line.product.id, quantity: line.quantity }));
    form.paid_amount = form.paid_amount || total.value;

    form.post('/pos', {
        preserveScroll: true,
        onSuccess: () => {
            clearCart();
            form.reset('discount', 'paid_amount');
            lastReceipt.value = page.props.flash?.receipt ?? null;
        },
    });
};
</script>

<template>
    <AdminLayout title="Point of Sale">
        <div v-if="form.errors.items" class="alert alert-danger">{{ form.errors.items }}</div>
        <div v-if="form.errors.paid_amount" class="alert alert-danger">{{ form.errors.paid_amount }}</div>

        <div v-if="lastReceipt" class="alert alert-success d-flex justify-content-between align-items-center">
            <div>
                <strong>{{ lastReceipt.invoice_no }}</strong> completed —
                {{ formatMoney(lastReceipt.total) }} paid via {{ lastReceipt.payment_method }},
                change due {{ formatMoney(lastReceipt.change_amount) }}.
            </div>
            <button type="button" class="btn-close" @click="lastReceipt = null"></button>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="input-group mb-3">
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
                                autofocus
                            >
                        </div>

                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button
                                type="button"
                                class="pos-category-pill"
                                :class="{ 'pos-category-pill--active': activeCategory === '' }"
                                @click="activeCategory = ''"
                            >
                                All
                            </button>
                            <button
                                v-for="category in categories"
                                :key="category"
                                type="button"
                                class="pos-category-pill"
                                :class="{ 'pos-category-pill--active': activeCategory === category }"
                                @click="activeCategory = category"
                            >
                                {{ category }}
                            </button>
                        </div>

                        <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3">
                            <div v-for="product in filteredProducts" :key="product.id" class="col">
                                <div
                                    class="card pos-product h-100 position-relative"
                                    :class="{ 'pos-product--disabled': product.stock <= 0 }"
                                    role="button"
                                    :tabindex="product.stock > 0 ? 0 : -1"
                                    @click="addToCart(product)"
                                    @keydown.enter="addToCart(product)"
                                >
                                    <span v-if="cartQuantity(product.id)" class="badge text-bg-primary position-absolute top-0 end-0 m-2">
                                        {{ cartQuantity(product.id) }}
                                    </span>
                                    <div class="card-body p-3">
                                        <div class="fw-semibold">{{ product.name }}</div>
                                        <div class="text-muted small">{{ product.sku }}</div>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <span class="fw-bold text-primary">{{ formatMoney(product.price) }}</span>
                                            <span class="badge" :class="product.stock > 0 ? 'text-bg-light' : 'text-bg-danger'">
                                                {{ product.stock > 0 ? `${product.stock} ${product.unit}` : 'Out of stock' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div v-if="filteredProducts.length === 0" class="col-12 text-center text-muted py-5">
                                No products match your search.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card border-0 shadow-sm pos-cart">
                    <div class="card-body p-4 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h2 class="h6 mb-0">Cart</h2>
                            <span class="badge text-bg-light">{{ cartItemCount }} item{{ cartItemCount === 1 ? '' : 's' }}</span>
                        </div>

                        <div class="flex-grow-1" style="overflow-y: auto; max-height: 300px;">
                            <div v-if="cart.length === 0" class="text-muted text-center py-4">Cart is empty — tap a product to add it.</div>

                            <div v-for="line in cart" :key="line.product.id" class="d-flex justify-content-between align-items-center mb-3">
                                <div>
                                    <div class="fw-semibold">{{ line.product.name }}</div>
                                    <div class="text-muted small">{{ formatMoney(line.product.price) }} x {{ line.quantity }}</div>
                                </div>
                                <div class="d-flex align-items-center gap-2">
                                    <button type="button" class="btn btn-sm btn-outline-secondary" @click="decrement(line.product.id)">−</button>
                                    <span>{{ line.quantity }}</span>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" @click="addToCart(line.product)">+</button>
                                    <button type="button" class="btn btn-sm btn-outline-danger" @click="removeLine(line.product.id)">×</button>
                                </div>
                            </div>
                        </div>

                        <hr>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">Subtotal</span>
                            <span>{{ formatMoney(subtotal) }}</span>
                        </div>

                        <div class="mb-2">
                            <label for="discount" class="form-label small mb-1">Discount</label>
                            <input id="discount" type="number" min="0" step="0.01" class="form-control form-control-sm" v-model="form.discount">
                        </div>

                        <div class="d-flex justify-content-between align-items-baseline mb-3">
                            <span class="fw-semibold">Total</span>
                            <span class="fs-4 fw-bold text-primary">{{ formatMoney(total) }}</span>
                        </div>

                        <div class="mb-2">
                            <label for="payment_method" class="form-label small mb-1">Payment Method</label>
                            <select id="payment_method" class="form-select form-select-sm" v-model="form.payment_method">
                                <option value="cash">Cash</option>
                                <option value="card">Card</option>
                                <option value="mobile">Mobile Payment</option>
                            </select>
                        </div>

                        <div class="mb-2">
                            <label for="paid_amount" class="form-label small mb-1">Amount Paid</label>
                            <input id="paid_amount" type="number" min="0" step="0.01" class="form-control form-control-sm" v-model="form.paid_amount" :placeholder="formatMoney(total)">
                        </div>

                        <div class="d-flex justify-content-between text-success mb-3">
                            <span>Change Due</span>
                            <span class="fw-semibold">{{ formatMoney(changeDue) }}</span>
                        </div>

                        <button
                            type="button"
                            class="btn btn-primary w-100"
                            :disabled="cart.length === 0 || form.processing"
                            @click="checkout"
                        >
                            <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                            Complete Sale
                        </button>
                        <button
                            type="button"
                            class="btn btn-outline-secondary w-100 mt-2"
                            :disabled="cart.length === 0"
                            @click="clearCart"
                        >
                            Clear Cart
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

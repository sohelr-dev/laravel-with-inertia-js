<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import AppLayout from '../../Layouts/AppLayout.vue';

const props = defineProps({
    products: Array,
    categories: Array,
});

const page = usePage();

const search = ref('');
const activeCategory = ref('');
const cart = ref([]);

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

const addToCart = (product) => {
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
    paid_amount: 0,
    payment_method: 'cash',
});

const total = computed(() => Math.max(subtotal.value - (Number(form.discount) || 0), 0));
const changeDue = computed(() => Math.max((Number(form.paid_amount) || 0) - total.value, 0));

const formatMoney = (value) => Number(value).toFixed(2);

const checkout = () => {
    form.items = cart.value.map((line) => ({ product_id: line.product.id, quantity: line.quantity }));
    form.paid_amount = form.paid_amount || total.value;

    form.post('/pos', {
        preserveScroll: true,
        onSuccess: () => {
            clearCart();
            form.reset('discount', 'paid_amount');
        },
    });
};
</script>

<template>
    <AppLayout>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1 class="h3 mb-0">Point of Sale</h1>
        </div>

        <div v-if="page.props.flash?.success" class="alert alert-success">{{ page.props.flash.success }}</div>
        <div v-if="form.errors.items" class="alert alert-danger">{{ form.errors.items }}</div>
        <div v-if="form.errors.paid_amount" class="alert alert-danger">{{ form.errors.paid_amount }}</div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 card-bd-accent">
                    <div class="card-body p-4">
                        <input
                            type="search"
                            class="form-control mb-3"
                            placeholder="Search by name, SKU or barcode"
                            v-model="search"
                        >

                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button
                                type="button"
                                class="btn btn-sm"
                                :class="activeCategory === '' ? 'btn-primary' : 'btn-outline-secondary'"
                                @click="activeCategory = ''"
                            >
                                All
                            </button>
                            <button
                                v-for="category in categories"
                                :key="category"
                                type="button"
                                class="btn btn-sm"
                                :class="activeCategory === category ? 'btn-primary' : 'btn-outline-secondary'"
                                @click="activeCategory = category"
                            >
                                {{ category }}
                            </button>
                        </div>

                        <div class="row row-cols-2 row-cols-md-3 row-cols-xl-4 g-3">
                            <div v-for="product in filteredProducts" :key="product.id" class="col">
                                <div
                                    class="card h-100 border-0 shadow-sm"
                                    :class="product.stock <= 0 ? 'opacity-50' : 'cursor-pointer'"
                                    role="button"
                                    :tabindex="product.stock > 0 ? 0 : -1"
                                    @click="addToCart(product)"
                                    @keydown.enter="addToCart(product)"
                                >
                                    <div class="card-body p-3">
                                        <div class="fw-semibold">{{ product.name }}</div>
                                        <div class="text-muted small">{{ product.sku }}</div>
                                        <div class="d-flex justify-content-between align-items-center mt-2">
                                            <span class="fw-bold">{{ formatMoney(product.price) }}</span>
                                            <span class="badge" :class="product.stock > 0 ? 'text-bg-light' : 'text-bg-danger'">
                                                {{ product.stock > 0 ? `${product.stock} ${product.unit}` : 'Out of stock' }}
                                            </span>
                                        </div>
                                        <span v-if="cartQuantity(product.id)" class="badge text-bg-primary mt-2">
                                            In cart: {{ cartQuantity(product.id) }}
                                        </span>
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
                <div class="card shadow-sm border-0 card-bd-accent">
                    <div class="card-body p-4 d-flex flex-column" style="min-height: 400px;">
                        <h2 class="h5 mb-3">Cart</h2>

                        <div class="flex-grow-1" style="overflow-y: auto; max-height: 320px;">
                            <div v-if="cart.length === 0" class="text-muted text-center py-4">Cart is empty</div>

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
                            <span>Subtotal</span>
                            <span>{{ formatMoney(subtotal) }}</span>
                        </div>

                        <div class="mb-2">
                            <label for="discount" class="form-label small mb-1">Discount</label>
                            <input id="discount" type="number" min="0" step="0.01" class="form-control form-control-sm" v-model="form.discount">
                        </div>

                        <div class="d-flex justify-content-between fw-bold mb-3">
                            <span>Total</span>
                            <span>{{ formatMoney(total) }}</span>
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
                            <input id="paid_amount" type="number" min="0" step="0.01" class="form-control form-control-sm" v-model="form.paid_amount">
                        </div>

                        <div class="d-flex justify-content-between text-success mb-3">
                            <span>Change Due</span>
                            <span>{{ formatMoney(changeDue) }}</span>
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
    </AppLayout>
</template>

<style scoped>
.cursor-pointer {
    cursor: pointer;
}
</style>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';

const form = useForm({
    name: '',
    sku: '',
    barcode: '',
    category: '',
    description: '',
    price: '',
    cost: '',
    stock: 0,
    unit: 'pcs',
    is_active: true,
});

const submit = () => {
    form.post('/products');
};
</script>

<template>
    <AdminLayout title="Create Product">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h5 mb-1">Create Product</h2>
                <p class="text-muted small mb-0">Add a new item to your catalog and starting inventory.</p>
            </div>
            <Link href="/products" class="btn btn-outline-secondary btn-sm">Back to Products</Link>
        </div>

        <div class="card shadow-sm border-0 card-bd-accent">
            <div class="card-body p-4 p-md-5">
                <form @submit.prevent="submit" novalidate>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Product Name</label>
                            <input
                                id="name"
                                type="text"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.name }"
                                v-model="form.name"
                                autofocus
                            >
                            <div class="invalid-feedback" v-if="form.errors.name">{{ form.errors.name }}</div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="sku" class="form-label">SKU</label>
                            <input
                                id="sku"
                                type="text"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.sku }"
                                v-model="form.sku"
                            >
                            <div class="invalid-feedback" v-if="form.errors.sku">{{ form.errors.sku }}</div>
                        </div>

                        <div class="col-md-3 mb-3">
                            <label for="barcode" class="form-label">Barcode</label>
                            <input
                                id="barcode"
                                type="text"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.barcode }"
                                v-model="form.barcode"
                            >
                            <div class="invalid-feedback" v-if="form.errors.barcode">{{ form.errors.barcode }}</div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="category" class="form-label">Category</label>
                            <input
                                id="category"
                                type="text"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.category }"
                                v-model="form.category"
                                placeholder="e.g. Beverages"
                            >
                            <div class="invalid-feedback" v-if="form.errors.category">{{ form.errors.category }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="unit" class="form-label">Unit</label>
                            <input
                                id="unit"
                                type="text"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.unit }"
                                v-model="form.unit"
                                placeholder="e.g. pcs, kg, box"
                            >
                            <div class="invalid-feedback" v-if="form.errors.unit">{{ form.errors.unit }}</div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea
                            id="description"
                            class="form-control"
                            :class="{ 'is-invalid': form.errors.description }"
                            v-model="form.description"
                            rows="3"
                        ></textarea>
                        <div class="invalid-feedback" v-if="form.errors.description">{{ form.errors.description }}</div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="price" class="form-label">Selling Price</label>
                            <input
                                id="price"
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.price }"
                                v-model="form.price"
                            >
                            <div class="invalid-feedback" v-if="form.errors.price">{{ form.errors.price }}</div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="cost" class="form-label">Cost Price</label>
                            <input
                                id="cost"
                                type="number"
                                step="0.01"
                                min="0"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.cost }"
                                v-model="form.cost"
                            >
                            <div class="invalid-feedback" v-if="form.errors.cost">{{ form.errors.cost }}</div>
                        </div>

                        <div class="col-md-4 mb-3">
                            <label for="stock" class="form-label">Opening Stock</label>
                            <input
                                id="stock"
                                type="number"
                                min="0"
                                class="form-control"
                                :class="{ 'is-invalid': form.errors.stock }"
                                v-model="form.stock"
                            >
                            <div class="invalid-feedback" v-if="form.errors.stock">{{ form.errors.stock }}</div>
                        </div>
                    </div>

                    <div class="form-check form-switch mb-4">
                        <input
                            id="is_active"
                            class="form-check-input"
                            type="checkbox"
                            v-model="form.is_active"
                        >
                        <label class="form-check-label" for="is_active">Active (available for sale)</label>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" :disabled="form.processing">
                            <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                            Save Product
                        </button>
                        <Link href="/products" class="btn btn-outline-secondary">Cancel</Link>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Link, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../Layouts/AdminLayout.vue';

const form = useForm({
    name: '',
    phone: '',
    email: '',
    address: '',
});

const submit = () => {
    form.post('/customers');
};
</script>

<template>
    <AdminLayout title="Create Customer">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2 class="h5 mb-1">Create Customer</h2>
                <p class="text-muted small mb-0">Add a new customer record.</p>
            </div>
            <Link href="/customers" class="btn btn-outline-secondary btn-sm">Back to Customers</Link>
        </div>

        <div class="card shadow-sm border-0 card-bd-accent">
            <div class="card-body p-4 p-md-5">
                <form @submit.prevent="submit" novalidate>
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input id="name" type="text" class="form-control" :class="{ 'is-invalid': form.errors.name }"
                            v-model="form.name" autofocus>
                        <div class="invalid-feedback" v-if="form.errors.name">{{ form.errors.name }}</div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone</label>
                            <input id="phone" type="text" class="form-control"
                                :class="{ 'is-invalid': form.errors.phone }" v-model="form.phone">
                            <div class="invalid-feedback" v-if="form.errors.phone">{{ form.errors.phone }}</div>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" type="email" class="form-control"
                                :class="{ 'is-invalid': form.errors.email }" v-model="form.email">
                            <div class="invalid-feedback" v-if="form.errors.email">{{ form.errors.email }}</div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="address" class="form-label">Address</label>
                        <textarea id="address" class="form-control" :class="{ 'is-invalid': form.errors.address }"
                            v-model="form.address" rows="3"></textarea>
                        <div class="invalid-feedback" v-if="form.errors.address">{{ form.errors.address }}</div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary" :disabled="form.processing">
                            <span v-if="form.processing" class="spinner-border spinner-border-sm me-2"></span>
                            Save Customer
                        </button>
                        <Link href="/customers" class="btn btn-outline-secondary">Cancel</Link>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

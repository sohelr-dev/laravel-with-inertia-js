<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;
use Inertia\Response;

class CustomerController extends Controller
{
    public function index(Request $request): Response
    {
        $customer = Customer::query()
            ->when($request->string('search')->toString(), function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(15)
            ->withQueryString();

        return Inertia::render('Customers/Index', [
            'customers' => $customer,
            'filters' => $request->only('search'),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Customers/Create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:20', 'unique:customers,phone'],
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:customers,email'],
            'address' => ['nullable', 'string'],

        ]);

        Customer::create($validated);

        return redirect()->route('customers.index')->with('success', 'Customer created.');
    }

    public function show(Customer $customer): Response
    {
        return Inertia::render('Customers/Show', [
            'customer' => $customer,
            'payments' => $customer->payments()->with('user:id,name')->latest()->paginate(10, ['*'], 'payments_page'),
            'sales' => $customer->sales()->latest()->paginate(10, ['id', 'invoice_no', 'total', 'paid_amount', 'due_amount', 'created_at'], 'sales_page'),
        ]);
    }

    public function storePayment(Request $request, Customer $customer): RedirectResponse
    {
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', 'max:'.$customer->total_due],
            'note' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($customer, $validated, $request) {
            $customer->payments()->create([
                'user_id' => $request->user()->id,
                'amount' => $validated['amount'],
                'note' => $validated['note'] ?? null,
            ]);

            $customer->decrement('total_due', $validated['amount']);
        });

        return redirect()->route('customers.show', $customer)->with('success', 'Payment recorded.');
    }
}

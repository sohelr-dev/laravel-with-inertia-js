<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Inertia\Inertia;
use Inertia\Response;

class PosController extends Controller
{
    public function index(): Response
    {
        $products = Product::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'sku', 'barcode', 'category', 'price', 'stock', 'unit']);

        return Inertia::render('Pos/Index', [
            'products' => $products,
            'categories' => $products->pluck('category')->filter()->unique()->values(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'discount' => ['nullable', 'numeric', 'min:0'],
            'paid_amount' => ['required', 'numeric', 'min:0'],
            'payment_method' => ['required', 'string', 'in:cash,card,mobile'],
        ]);

        $sale = DB::transaction(function () use ($validated, $request) {
            $productIds = collect($validated['items'])->pluck('product_id');
            $products = Product::whereIn('id', $productIds)->lockForUpdate()->get()->keyBy('id');

            $subtotal = 0;
            $lineItems = [];

            foreach ($validated['items'] as $item) {
                $product = $products->get($item['product_id']);

                if (! $product || $product->stock < $item['quantity']) {
                    throw ValidationException::withMessages([
                        'items' => "Not enough stock for {$product?->name}.",
                    ]);
                }

                $lineSubtotal = $product->price * $item['quantity'];
                $subtotal += $lineSubtotal;

                $lineItems[] = [
                    'product' => $product,
                    'quantity' => $item['quantity'],
                    'subtotal' => $lineSubtotal,
                ];
            }

            $discount = $validated['discount'] ?? 0;
            $total = max($subtotal - $discount, 0);

            if ($validated['paid_amount'] < $total) {
                throw ValidationException::withMessages([
                    'paid_amount' => 'Paid amount is less than the total due.',
                ]);
            }

            $sale = Sale::create([
                'invoice_no' => 'INV-'.now()->format('Ymd-His').'-'.random_int(100, 999),
                'user_id' => $request->user()->id,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'total' => $total,
                'paid_amount' => $validated['paid_amount'],
                'change_amount' => $validated['paid_amount'] - $total,
                'payment_method' => $validated['payment_method'],
            ]);

            foreach ($lineItems as $lineItem) {
                $sale->items()->create([
                    'product_id' => $lineItem['product']->id,
                    'product_name' => $lineItem['product']->name,
                    'price' => $lineItem['product']->price,
                    'quantity' => $lineItem['quantity'],
                    'subtotal' => $lineItem['subtotal'],
                ]);

                $lineItem['product']->decrement('stock', $lineItem['quantity']);
            }

            return $sale;
        });

        return redirect()->route('pos.index')
            ->with('success', "Sale {$sale->invoice_no} completed.")
            ->with('receipt', [
                'invoice_no' => $sale->invoice_no,
                'total' => $sale->total,
                'paid_amount' => $sale->paid_amount,
                'change_amount' => $sale->change_amount,
                'payment_method' => $sale->payment_method,
            ]);
    }
}

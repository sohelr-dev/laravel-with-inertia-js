<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Sale;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    private const LOW_STOCK_THRESHOLD = 10;

    public function index(): Response
    {
        $todaySales = Sale::whereDate('created_at', today());

        $recentSales = Sale::with('user:id,name')
            ->withCount('items')
            ->latest()
            ->take(5)
            ->get(['id', 'invoice_no', 'user_id', 'total', 'payment_method', 'created_at']);

        return Inertia::render('Dashboard', [
            'stats' => [
                'productCount' => Product::where('is_active', true)->count(),
                'lowStockCount' => Product::where('is_active', true)->where('stock', '<=', self::LOW_STOCK_THRESHOLD)->count(),
                'todaySalesCount' => (clone $todaySales)->count(),
                'todayRevenue' => (clone $todaySales)->sum('total'),
            ],
            'lowStockProducts' => Product::where('is_active', true)
                ->where('stock', '<=', self::LOW_STOCK_THRESHOLD)
                ->orderBy('stock')
                ->take(5)
                ->get(['id', 'name', 'sku', 'stock', 'unit']),
            'recentSales' => $recentSales,
        ]);
    }
}

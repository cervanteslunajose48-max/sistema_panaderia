<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ProductVariant;
use App\Models\Supply;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());

        // 1. Sales Trend (Daily Sales)
        $salesData = Order::whereBetween('created_at', [
            Carbon::parse($startDate)->startOfDay(),
            Carbon::parse($endDate)->endOfDay()
        ])
            ->where('status', '!=', 'cancelled')
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(total) as total')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartLabels = $salesData->pluck('date');
        $chartValues = $salesData->pluck('total');

        // 2. Top Products (Pie Chart)
        $topProducts = OrderItem::whereHas('order', function ($q) use ($startDate, $endDate) {
            $q->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ])->where('status', '!=', 'cancelled');
        })
            ->select('product_variant_id', DB::raw('SUM(quantity) as total_qty'))
            ->groupBy('product_variant_id')
            ->with(['variant.product']) // Eager load for names
            ->orderByDesc('total_qty')
            ->limit(5)
            ->get();

        $topProductsLabels = $topProducts->map(function ($item) {
            return $item->variant->product->name . ' (' . $item->variant->name . ')';
        });
        $topProductsValues = $topProducts->pluck('total_qty');

        // 3. Low Stock Alerts - Products (using fixed threshold since no min_stock column exists)
        $lowStockProducts = ProductVariant::where('stock_track', true)
            ->where('current_stock', '<=', 5) // Fixed threshold of 5 units
            ->with('product')
            ->limit(10)
            ->get();

        // Low Stock Alerts - Supplies (using model accessor to calculate stock)
        $allSupplies = Supply::with('stocks')->get();
        $lowStockSupplies = $allSupplies->filter(function ($supply) {
            return $supply->current_stock <= $supply->min_stock;
        })->take(10);

        return view('reports.index', compact(
            'startDate',
            'endDate',
            'chartLabels',
            'chartValues',
            'topProductsLabels',
            'topProductsValues',
            'lowStockProducts',
            'lowStockSupplies'
        ));
    }
    public function production(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        $categoryId = $request->input('category_id');
        $productId = $request->input('product_id');

        // Base Query
        $query = \App\Models\InventoryMovement::where('type', 'production_in')
            ->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ])
            ->with(['productVariant.product.category', 'user']);

        // Filters
        if ($categoryId) {
            $query->whereHas('productVariant.product', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        if ($productId) {
            $query->whereHas('productVariant', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            });
        }

        \Illuminate\Support\Facades\Log::info('Production Report Request', $request->all());
        \Illuminate\Support\Facades\Log::info('Production Report SQL', ['sql' => $query->toSql(), 'bindings' => $query->getBindings()]);
        \Illuminate\Support\Facades\Log::info('Production Report Count', ['count' => $query->count()]);

        // Get Data for Table (Paginated)
        $movements = $query->clone()->latest()->paginate(20)->withQueryString();

        // Get Data for Chart (Daily Sum)
        $chartData = $query->clone()
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('SUM(quantity) as total_qty')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $chartLabels = $chartData->pluck('date');
        $chartValues = $chartData->pluck('total_qty');

        // Filters Data
        $categories = \App\Models\Category::where('status', true)->orderBy('name')->get();
        $products = \App\Models\Product::where('type', 'finished')->where('status', true)->orderBy('name')->get();

        return view('reports.production', compact(
            'movements',
            'startDate',
            'endDate',
            'categoryId',
            'productId',
            'categories',
            'products',
            'chartLabels',
            'chartValues'
        ));
    }

    public function exportProductionCsv(Request $request)
    {
        $startDate = $request->input('start_date', Carbon::now()->toDateString());
        $endDate = $request->input('end_date', Carbon::now()->toDateString());
        $categoryId = $request->input('category_id');
        $productId = $request->input('product_id');

        $query = \App\Models\InventoryMovement::where('type', 'production_in')
            ->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ])
            ->with(['productVariant.product.category', 'user']);

        if ($categoryId) {
            $query->whereHas('productVariant.product', function ($q) use ($categoryId) {
                $q->where('category_id', $categoryId);
            });
        }

        if ($productId) {
            $query->whereHas('productVariant', function ($q) use ($productId) {
                $q->where('product_id', $productId);
            });
        }

        $filename = "reporte-produccion-" . date('d-m-Y-H-i') . ".csv";

        $headers = [
            "Content-type" => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ];

        $callback = function () use ($query) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // BOM for user checking in Excel

            fputcsv($file, ['Fecha y Hora', 'Producto', 'Variante', 'Categoría', 'Cantidad', 'Usuario']);

            $query->chunk(100, function ($movements) use ($file) {
                foreach ($movements as $m) {
                    fputcsv($file, [
                        $m->created_at->format('d/m/Y H:i'),
                        $m->productVariant?->product?->name ?? 'Desconocido',
                        $m->productVariant?->name ?? '',
                        $m->productVariant?->product?->category?->name ?? 'Sin Categoría',
                        $m->quantity,
                        $m->user?->name ?? 'Sistema'
                    ]);
                }
            });

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

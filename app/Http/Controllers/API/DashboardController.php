<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Product;
use App\Models\TransactionItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        // Check if custom date range is provided
        if ($request->has('start_date') && $request->has('end_date')) {
            // Custom date range
            $startDate = \Carbon\Carbon::parse($request->start_date)->startOfDay();
            $endDate = \Carbon\Carbon::parse($request->end_date)->endOfDay();
            $period = 'custom';
        } else {
            // Get period filter (today, week, month, year)
            $period = $request->get('period', 'today');
            
            // Calculate date range based on period
            switch ($period) {
                case 'today':
                    $startDate = now()->startOfDay();
                    $endDate = now()->endOfDay();
                    break;
                case 'week':
                    $startDate = now()->startOfWeek();
                    $endDate = now()->endOfWeek();
                    break;
                case 'month':
                    $startDate = now()->startOfMonth();
                    $endDate = now()->endOfMonth();
                    break;
                case 'year':
                    $startDate = now()->startOfYear();
                    $endDate = now()->endOfYear();
                    break;
                default:
                    $startDate = now()->startOfDay();
                    $endDate = now()->endOfDay();
            }
        }

        // Calculate days difference for smart grouping
        $daysDiff = $startDate->diffInDays($endDate);

        // Total revenue
        $totalRevenue = Transaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('total');

        // Total transactions
        $totalTransactions = Transaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->count();

        // Total products sold
        $totalProductsSold = TransactionItem::whereHas('transaction', function($query) use ($startDate, $endDate) {
            $query->whereBetween('transaction_date', [$startDate, $endDate]);
        })->sum('quantity');

        // Average transaction value
        $averageTransaction = $totalTransactions > 0 ? $totalRevenue / $totalTransactions : 0;

        // Low stock products
        $lowStockCount = Product::lowStock()->count();

        // Top selling products
        $topSellingProducts = TransactionItem::select('product_id', 'product_name', DB::raw('SUM(quantity) as total_sold'), DB::raw('SUM(total) as total_revenue'))
            ->whereHas('transaction', function($query) use ($startDate, $endDate) {
                $query->whereBetween('transaction_date', [$startDate, $endDate]);
            })
            ->groupBy('product_id', 'product_name')
            ->orderBy('total_sold', 'desc')
            ->limit(10)
            ->get();

        // Sales by payment method
        $salesByPaymentMethod = Transaction::select('payment_method', DB::raw('COUNT(*) as count'), DB::raw('SUM(total) as total'))
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->groupBy('payment_method')
            ->get();

        // Smart grouping for daily sales chart based on date range
        if ($daysDiff > 365) {
            // More than 1 year: group by year
            $dailySales = Transaction::select(
                    DB::raw('YEAR(transaction_date) as date'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(total) as total')
                )
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();
        } elseif ($daysDiff > 90) {
            // More than 3 months: group by month
            $dailySales = Transaction::select(
                    DB::raw('DATE_FORMAT(transaction_date, "%Y-%m") as date'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(total) as total')
                )
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();
        } else {
            // Less than 3 months: group by day
            $dailySales = Transaction::select(
                    DB::raw('DATE(transaction_date) as date'),
                    DB::raw('COUNT(*) as count'),
                    DB::raw('SUM(total) as total')
                )
                ->whereBetween('transaction_date', [$startDate, $endDate])
                ->groupBy('date')
                ->orderBy('date', 'asc')
                ->get();
        }

        return response()->json([
            'success' => true,
            'data' => [
                'summary' => [
                    'total_revenue' => round($totalRevenue, 2),
                    'total_transactions' => $totalTransactions,
                    'total_products_sold' => $totalProductsSold,
                    'average_transaction' => round($averageTransaction, 2),
                    'low_stock_count' => $lowStockCount,
                ],
                'top_selling_products' => $topSellingProducts,
                'sales_by_payment_method' => $salesByPaymentMethod,
                'daily_sales' => $dailySales,
                'period' => $period,
                'date_range' => [
                    'start' => $startDate->format('Y-m-d H:i:s'),
                    'end' => $endDate->format('Y-m-d H:i:s'),
                    'days' => $daysDiff,
                ]
            ]
        ]);
    }

    public function lowStockAlert()
    {
        $products = Product::with('category')
            ->lowStock()
            ->active()
            ->orderBy('stock', 'asc')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $products
        ]);
    }
}

<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Order;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    /**
     * Hiển thị dashboard với thống kê
     */
    public function dashboard()
    {
        // Thống kê tổng quan
        $totalProducts = Product::count();
        $totalOrders = Order::count();
        $totalUsers = User::where('role', 'user')->count();
        $pendingOrders = Order::where('status', 'pending')->count();

        // Tổng doanh thu
        $totalRevenue = Order::whereIn('status', ['completed', 'shipping', 'confirmed'])
            ->sum('total_amount');

        // Doanh thu tháng này
        $monthlyRevenue = Order::whereIn('status', ['completed', 'shipping', 'confirmed'])
            ->whereMonth('created_at', Carbon::now()->month)
            ->whereYear('created_at', Carbon::now()->year)
            ->sum('total_amount');

        // Doanh thu hôm nay
        $todayRevenue = Order::whereIn('status', ['completed', 'shipping', 'confirmed'])
            ->whereDate('created_at', Carbon::today())
            ->sum('total_amount');

        // Đơn hàng gần đây
        $recentOrders = Order::with('orderDetails')
            ->latest()
            ->take(5)
            ->get();

        // Dữ liệu biểu đồ doanh thu 7 ngày gần đây
        $revenueChart = $this->getRevenueChartData(7);

        // Dữ liệu biểu đồ đơn hàng theo trạng thái
        $orderStatusChart = $this->getOrderStatusChartData();

        // Top 5 sản phẩm bán chạy
        $topProducts = $this->getTopSellingProducts(5);

        return view('admin.dashboard', compact(
            'totalProducts',
            'totalOrders',
            'totalUsers',
            'pendingOrders',
            'totalRevenue',
            'monthlyRevenue',
            'todayRevenue',
            'recentOrders',
            'revenueChart',
            'orderStatusChart',
            'topProducts'
        ));
    }

    /**
     * Lấy dữ liệu biểu đồ doanh thu
     */
    private function getRevenueChartData($days = 7)
    {
        $data = [];
        $labels = [];

        for ($i = $days - 1; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $revenue = Order::whereIn('status', ['completed', 'shipping', 'confirmed'])
                ->whereDate('created_at', $date)
                ->sum('total_amount');

            $labels[] = $date->format('d/m');
            $data[] = $revenue;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Lấy dữ liệu biểu đồ trạng thái đơn hàng
     */
    private function getOrderStatusChartData()
    {
        $statuses = Order::select('status', DB::raw('count(*) as count'))
            ->groupBy('status')
            ->get();

        $labels = [];
        $data = [];
        $colors = [
            'pending' => '#ffc107',
            'confirmed' => '#17a2b8',
            'shipping' => '#007bff',
            'completed' => '#28a745',
            'cancelled' => '#dc3545'
        ];

        foreach ($statuses as $status) {
            $labels[] = Order::getStatusLabels()[$status->status] ?? $status->status;
            $data[] = $status->count;
        }

        return [
            'labels' => $labels,
            'data' => $data,
            'colors' => array_values($colors)
        ];
    }

    /**
     * Lấy top sản phẩm bán chạy
     */
private function getTopSellingProducts($limit = 5)
{
    $topProductIds = DB::table('order_details')
        ->join('orders', 'order_details.order_id', '=', 'orders.id')
        ->whereIn('orders.status', ['completed', 'shipping', 'confirmed'])
        ->select('order_details.product_id', DB::raw('SUM(order_details.quantity) as total_sold'))
        ->groupBy('order_details.product_id')
        ->orderBy('total_sold', 'desc')
        ->limit($limit)
        ->get();

    $productIds = $topProductIds->pluck('product_id');

    $products = Product::whereIn('id', $productIds)->get();

    // Gắn thông tin total_sold vào products
    return $products->map(function($product) use ($topProductIds) {
        $soldInfo = $topProductIds->firstWhere('product_id', $product->id);
        $product->total_sold = $soldInfo ? $soldInfo->total_sold : 0;
        return $product;
    })->sortByDesc('total_sold')->values();
}
    /**
     * Trang thống kê doanh thu chi tiết
     */
    public function revenue(Request $request)
    {
        $period = $request->get('period', 'month'); // day, week, month, year

        switch ($period) {
            case 'day':
                $chartData = $this->getRevenueChartData(7);
                break;
            case 'week':
                $chartData = $this->getWeeklyRevenueChartData(8);
                break;
            case 'year':
                $chartData = $this->getYearlyRevenueChartData();
                break;
            default: // month
                $chartData = $this->getMonthlyRevenueChartData(12);
        }

        // Thống kê theo khoảng thời gian
        $startDate = $request->get('start_date', Carbon::now()->startOfMonth());
        $endDate = $request->get('end_date', Carbon::now()->endOfMonth());

        $orders = Order::whereIn('status', ['completed', 'shipping', 'confirmed'])
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with('orderDetails.product')
            ->get();

        $totalRevenue = $orders->sum('total_amount');
        $totalOrders = $orders->count();
        $averageOrder = $totalOrders > 0 ? $totalRevenue / $totalOrders : 0;

        return view('admin.revenue', compact(
            'chartData',
            'period',
            'totalRevenue',
            'totalOrders',
            'averageOrder',
            'startDate',
            'endDate'
        ));
    }

    
    /**
     * Doanh thu theo tuần
     */
    private function getWeeklyRevenueChartData($weeks = 8)
    {
        $data = [];
        $labels = [];

        for ($i = $weeks - 1; $i >= 0; $i--) {
            $startOfWeek = Carbon::now()->subWeeks($i)->startOfWeek();
            $endOfWeek = Carbon::now()->subWeeks($i)->endOfWeek();

            $revenue = Order::whereIn('status', ['completed', 'shipping', 'confirmed'])
                ->whereBetween('created_at', [$startOfWeek, $endOfWeek])
                ->sum('total_amount');

            $labels[] = 'Tuần ' . $startOfWeek->format('d/m');
            $data[] = $revenue;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Doanh thu theo tháng
     */
    private function getMonthlyRevenueChartData($months = 12)
    {
        $data = [];
        $labels = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);

            $revenue = Order::whereIn('status', ['completed', 'shipping', 'confirmed'])
                ->whereMonth('created_at', $month->month)
                ->whereYear('created_at', $month->year)
                ->sum('total_amount');

            $labels[] = 'T' . $month->format('m/Y');
            $data[] = $revenue;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }

    /**
     * Doanh thu theo năm
     */
    private function getYearlyRevenueChartData()
    {
        $currentYear = Carbon::now()->year;
        $data = [];
        $labels = [];

        for ($i = 4; $i >= 0; $i--) {
            $year = $currentYear - $i;

            $revenue = Order::whereIn('status', ['completed', 'shipping', 'confirmed'])
                ->whereYear('created_at', $year)
                ->sum('total_amount');

            $labels[] = 'Năm ' . $year;
            $data[] = $revenue;
        }

        return [
            'labels' => $labels,
            'data' => $data
        ];
    }
}

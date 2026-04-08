{{-- resources/views/dashboard.blade.php --}}
@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="container my-5">
    <h2 class="mb-4">Dashboard</h2>

    <div class="row">
        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-shopping-bag fa-3x text-primary mb-3"></i>
                    <h5>Đơn hàng của tôi</h5>
                    <h3 class="text-primary">{{ Auth::user()->total_orders }}</h3>
                    <a href="{{ route('orders.index') }}" class="btn btn-primary btn-sm mt-2">
                        Xem chi tiết
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-clock fa-3x text-warning mb-3"></i>
                    <h5>Đang chờ xử lý</h5>
                    <h3 class="text-warning">{{ Auth::user()->orders()->where('status', 'pending')->count() }}</h3>
                    <a href="{{ route('orders.index', ['status' => 'pending']) }}" class="btn btn-warning btn-sm mt-2">
                        Xem chi tiết
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-shipping-fast fa-3x text-info mb-3"></i>
                    <h5>Đang giao hàng</h5>
                    <h3 class="text-info">{{ Auth::user()->orders()->where('status', 'shipping')->count() }}</h3>
                    <a href="{{ route('orders.index', ['status' => 'shipping']) }}" class="btn btn-info btn-sm mt-2">
                        Xem chi tiết
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-center">
                <div class="card-body">
                    <i class="fas fa-dollar-sign fa-3x text-success mb-3"></i>
                    <h5>Tổng chi tiêu</h5>
                    <h3 class="text-success">{{ number_format(Auth::user()->total_spent) }}₫</h3>
                    <a href="{{ route('orders.index', ['status' => 'completed']) }}" class="btn btn-success btn-sm mt-2">
                        Xem chi tiết
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Đơn hàng gần đây</h5>
                </div>
                <div class="card-body">
                    @php
                        $recentOrders = Auth::user()->orders()->with('orderDetails')->latest()->take(5)->get();
                    @endphp

                    @if($recentOrders->isEmpty())
                        <p class="text-muted text-center py-4">Chưa có đơn hàng nào</p>
                    @else
                        <div class="table-responsive">
                            <table class="table">
                                <thead>
                                    <tr>
                                        <th>Mã đơn</th>
                                        <th>Ngày đặt</th>
                                        <th>Tổng tiền</th>
                                        <th>Trạng thái</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentOrders as $order)
                                    <tr>
                                        <td><strong>{{ $order->order_code }}</strong></td>
                                        <td>{{ $order->created_at->format('d/m/Y') }}</td>
                                        <td>{{ number_format($order->final_amount) }}₫</td>
                                        <td>
                                            <span class="badge bg-{{ $order->status_color }}">
                                                {{ $order->status_label }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                                Chi tiết
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        
                        <div class="text-center mt-3">
                            <a href="{{ route('orders.index') }}" class="btn btn-outline-primary">
                                Xem tất cả đơn hàng
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
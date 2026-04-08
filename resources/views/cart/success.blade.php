{{-- resources/views/cart/success.blade.php --}}
@extends('layouts.app')

@section('title', 'Đặt hàng thành công')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0">
                <div class="card-body text-center p-5">
                    <div class="mb-4">
                        <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                    </div>
                    
                    <h2 class="text-success mb-3">Đặt hàng thành công!</h2>
                    <p class="lead mb-4">Cảm ơn bạn đã đặt hàng tại ElectroShop</p>
                    
                    <div class="alert alert-info">
                        <h5><i class="fas fa-info-circle"></i> Thông tin đơn hàng</h5>
                        <p class="mb-1"><strong>Mã đơn hàng:</strong> {{ $order->order_code }}</p>
                        @if($order->customer_code)
                            <p class="mb-1"><strong>Mã khách hàng:</strong> {{ $order->customer_code }}</p>
                        @endif
                        <p class="mb-1"><strong>Tổng tiền:</strong> {{ number_format($order->final_amount) }}₫</p>
                        <p class="mb-0"><strong>Trạng thái:</strong> {{ $order->status_label }}</p>
                    </div>

                    <p class="text-muted mb-4">
                        Chúng tôi đã gửi email xác nhận đến <strong>{{ $order->email }}</strong>
                    </p>

                    <div class="row mt-4">
                        <div class="col-md-4">
                            <div class="p-3 border rounded">
                                <i class="fas fa-box fa-2x text-primary mb-2"></i>
                                <h6>Đóng gói</h6>
                                <small class="text-muted">Trong 24h</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded">
                                <i class="fas fa-truck fa-2x text-primary mb-2"></i>
                                <h6>Vận chuyển</h6>
                                <small class="text-muted">2-3 ngày</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="p-3 border rounded">
                                <i class="fas fa-home fa-2x text-primary mb-2"></i>
                                <h6>Giao hàng</h6>
                                <small class="text-muted">Đến tay bạn</small>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <a href="{{ route('home') }}" class="btn btn-primary btn-lg me-2">
                            <i class="fas fa-home"></i> Về trang chủ
                        </a>
                        @auth
                            <a href="{{ route('orders.show', $order->id) }}" class="btn btn-outline-primary btn-lg">
                                <i class="fas fa-eye"></i> Xem đơn hàng
                            </a>
                        @endauth
                    </div>
                </div>
            </div>

            <div class="card shadow mt-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-box"></i> Chi tiết sản phẩm</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead class="table-light">
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th width="80">SL</th>
                                    <th width="120">Đơn giá</th>
                                    <th width="120">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderDetails as $detail)
                                <tr>
                                    <td>
                                        <strong>{{ $detail->product->name }}</strong>
                                        @if($detail->discount_percent > 0)
                                            <br><span class="badge bg-danger">-{{ $detail->discount_percent }}%</span>
                                        @endif
                                    </td>
                                    <td class="text-center">{{ $detail->quantity }}</td>
                                    <td class="text-end">{{ number_format($detail->final_price) }}₫</td>
                                    <td class="text-end"><strong>{{ number_format($detail->subtotal) }}₫</strong></td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow mt-4">
                <div class="card-body">
                    <h5 class="card-title"><i class="fas fa-question-circle"></i> Cần hỗ trợ?</h5>
                    <p class="mb-2">
                        <i class="fas fa-phone text-primary"></i> 
                        Hotline: <strong>1900xxxx</strong> (8:00 - 22:00)
                    </p>
                    <p class="mb-0">
                        <i class="fas fa-envelope text-primary"></i> 
                        Email: <strong>support@electroshop.vn</strong>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
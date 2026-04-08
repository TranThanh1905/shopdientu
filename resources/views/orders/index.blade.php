{{-- resources/views/orders/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Đơn hàng của tôi')

@section('content')
<div class="container my-5">
    <div class="row g-4">
        {{-- Sidebar --}}
        <aside class="col-lg-3 col-md-4">
            {{-- Account Menu --}}
            <div class="card account-card shadow-sm mb-4">
                <div class="card-header account-header text-white">
                    <h5 class="mb-0 d-flex align-items-center">
                        <i class="fas fa-user me-2"></i>
                        <span>Tài khoản</span>
                    </h5>
                </div>
                <nav class="list-group list-group-flush">
                    <a href="{{ route('profile.edit') }}" 
                       class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="fas fa-user-edit me-2"></i>
                        <span>Thông tin cá nhân</span>
                    </a>
                    <a href="{{ route('orders.index') }}" 
                       class="list-group-item list-group-item-action active d-flex align-items-center justify-content-between">
                        <span>
                            <i class="fas fa-shopping-bag me-2"></i>
                            Đơn hàng của tôi
                        </span>
                        @if(Auth::user()->orders()->where('status', 'pending')->count() > 0)
                            <span class="badge bg-warning text-dark">
                                {{ Auth::user()->orders()->where('status', 'pending')->count() }}
                            </span>
                        @endif
                    </a>
                    <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                        <i class="fas fa-heart me-2"></i>
                        <span>Sản phẩm yêu thích</span>
                    </a>
                </nav>
            </div>

            {{-- Customer Statistics --}}
            <div class="card shadow-sm mt-3">
                <div class="card-body">
                    <h6 class="card-title fw-semibold mb-3">
                        <i class="fas fa-chart-line me-2 text-primary"></i>
                        Thống kê của bạn
                    </h6>
                    <hr class="my-3">
                    
                    <div class="mb-2">
                        <small class="text-muted d-block mb-1">Mã khách hàng:</small>
                        <strong class="text-dark">{{ Auth::user()->customer_code ?? 'Chưa có' }}</strong>
                    </div>
                    
                    <div class="mb-2">
                        <small class="text-muted d-block mb-1">Tổng đơn hàng:</small>
                        <strong class="text-dark">{{ Auth::user()->total_orders }} đơn</strong>
                    </div>
                    
                    <div class="mb-0">
                        <small class="text-muted d-block mb-1">Tổng chi tiêu:</small>
                        <strong class="text-success fs-5">{{ number_format(Auth::user()->total_spent) }}₫</strong>
                    </div>
                </div>
            </div>
        </aside>

        {{-- Main Content --}}
        <main class="col-lg-9 col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-white border-bottom">
                    <h4 class="mb-0 fw-bold">
                        <i class="fas fa-shopping-bag me-2 text-primary"></i>
                        Đơn hàng của tôi
                    </h4>
                </div>

                <div class="card-body p-4">
                    {{-- Status Filter Pills --}}
                    <nav aria-label="Bộ lọc trạng thái đơn hàng">
                        <ul class="nav nav-pills mb-4">
                            <li class="nav-item">
                                <a class="nav-link {{ !request('status') ? 'active' : '' }}" 
                                   href="{{ route('orders.index') }}"
                                   aria-current="{{ !request('status') ? 'page' : 'false' }}">
                                    Tất cả 
                                    <span class="badge bg-secondary ms-2">{{ Auth::user()->orders()->count() }}</span>
                                </a>
                            </li>
                            @foreach(\App\Models\Order::getStatusLabels() as $key => $label)
                                @php
                                    $count = Auth::user()->orders()->where('status', $key)->count();
                                @endphp
                                @if($count > 0)
                                <li class="nav-item">
                                    <a class="nav-link {{ request('status') == $key ? 'active' : '' }}" 
                                       href="{{ route('orders.index', ['status' => $key]) }}"
                                       aria-current="{{ request('status') == $key ? 'page' : 'false' }}">
                                        {{ $label }}
                                        <span class="badge bg-secondary ms-2">{{ $count }}</span>
                                    </a>
                                </li>
                                @endif
                            @endforeach
                        </ul>
                    </nav>

                    {{-- Empty State --}}
                    @if($orders->isEmpty())
                        <div class="text-center py-5">
                            <i class="fas fa-inbox fa-5x text-muted mb-4 opacity-50"></i>
                            <h5 class="text-muted fw-semibold mb-2">Chưa có đơn hàng nào</h5>
                            <p class="text-muted mb-4">Hãy khám phá và đặt hàng những sản phẩm yêu thích</p>
                            <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg shadow-sm">
                                <i class="fas fa-shopping-bag me-2"></i>
                                Mua sắm ngay
                            </a>
                        </div>
                    @else
                        {{-- Orders List --}}
                        <div class="orders-list">
                            @foreach($orders as $order)
                            <article class="card mb-3 border" aria-label="Đơn hàng {{ $order->order_code }}">
                                {{-- Order Header --}}
                                <header class="card-header bg-light">
                                    <div class="row align-items-center g-3">
                                        <div class="col-lg-3 col-md-6">
                                            <small class="text-muted d-block mb-1">Mã đơn hàng</small>
                                            <strong class="text-dark">{{ $order->order_code }}</strong>
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <small class="text-muted d-block mb-1">Ngày đặt</small>
                                            <strong class="text-dark">
                                                <i class="fas fa-calendar-alt me-1 text-muted"></i>
                                                {{ $order->created_at->format('d/m/Y H:i') }}
                                            </strong>
                                        </div>
                                        <div class="col-lg-3 col-md-6">
                                            <small class="text-muted d-block mb-1">Tổng tiền</small>
                                            <strong class="text-primary fs-5">{{ number_format($order->final_amount) }}₫</strong>
                                        </div>
                                        <div class="col-lg-3 col-md-6 text-lg-end">
                                            <span class="badge bg-{{ $order->status_color }} p-2 px-3 fs-6">
                                                {{ $order->status_label }}
                                            </span>
                                        </div>
                                    </div>
                                </header>

                                {{-- Order Body --}}
                                <div class="card-body">
                                    {{-- Product List --}}
                                    <div class="order-products">
                                        @foreach($order->orderDetails->take(3) as $detail)
                                        <div class="d-flex align-items-center mb-3 pb-3 {{ !$loop->last ? 'border-bottom' : '' }}">
                                            @if($detail->product->image)
                                                <img src="{{ asset($detail->product->image) }}" 
                                                     alt="{{ $detail->product->name }}"
                                                     class="rounded me-3"
                                                     style="width: 80px; height: 80px; object-fit: cover;"
                                                     loading="lazy">
                                            @endif
                                            
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1 fw-semibold">{{ $detail->product->name }}</h6>
                                                <div class="text-muted small">
                                                    <span class="me-2">
                                                        <i class="fas fa-tag me-1"></i>
                                                        {{ $detail->product->category->name }}
                                                    </span>
                                                    <span>
                                                        <i class="fas fa-times me-1"></i>
                                                        {{ $detail->quantity }}
                                                    </span>
                                                </div>
                                            </div>
                                            
                                            <div class="text-end">
                                                @if($detail->discount_percent > 0)
                                                    <small class="text-muted text-decoration-line-through d-block mb-1">
                                                        {{ number_format($detail->selling_price) }}₫
                                                    </small>
                                                @endif
                                                <strong class="text-primary fs-6">{{ number_format($detail->final_price) }}₫</strong>
                                            </div>
                                        </div>
                                        @endforeach

                                        @if($order->orderDetails->count() > 3)
                                            <div class="text-center py-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-ellipsis-h me-1"></i>
                                                    Và {{ $order->orderDetails->count() - 3 }} sản phẩm khác
                                                </small>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Shipping Info Alert --}}
                                    @if($order->status !== 'pending' && $order->status !== 'cancelled')
                                    <div class="alert alert-info border-start border-4 mb-0 mt-3">
                                        <div class="d-flex">
                                            <i class="fas fa-shipping-fast me-3 mt-1"></i>
                                            <div>
                                                <strong class="d-block mb-1">Địa chỉ giao hàng:</strong>
                                                <p class="mb-1">{{ $order->address }}</p>
                                                <p class="mb-0">
                                                    <i class="fas fa-phone me-1"></i>
                                                    {{ $order->phone }}
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    @endif

                                    {{-- Return/Damage Reason Alert --}}
                                    @if($order->return_reason)
                                    <div class="alert alert-{{ $order->status === 'returned' ? 'warning' : 'danger' }} border-start border-4 mb-0 mt-3">
                                        <div class="d-flex">
                                            <i class="fas fa-exclamation-triangle me-3 mt-1"></i>
                                            <div>
                                                <strong class="d-block mb-1">
                                                    {{ $order->status === 'returned' ? 'Lý do trả hàng' : 'Lý do hàng hỏng' }}:
                                                </strong>
                                                <p class="mb-0">{{ $order->return_reason }}</p>
                                            </div>
                                        </div>
                                    </div>
                                    @endif
                                </div>

                                {{-- Order Footer --}}
                                <footer class="card-footer bg-white">
                                    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
                                        <div class="text-muted small">
                                            <i class="fas fa-box me-1"></i>
                                            {{ $order->orderDetails->sum('quantity') }} sản phẩm
                                            <span class="mx-2">•</span>
                                            <span>Thành tiền: </span>
                                            <strong class="text-primary fs-6">{{ number_format($order->final_amount) }}₫</strong>
                                        </div>
                                        
                                        <div class="d-flex flex-wrap gap-2">
                                            @if($order->status === 'pending')
                                                <button type="button" 
                                                        class="btn btn-outline-danger btn-sm"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#cancelOrderModal"
                                                        data-order-id="{{ $order->id }}"
                                                        data-order-code="{{ $order->order_code }}"
                                                        aria-label="Hủy đơn hàng {{ $order->order_code }}">
                                                    <i class="fas fa-times me-1"></i>
                                                    Hủy đơn
                                                </button>
                                            @endif

                                            @if($order->status === 'completed')
                                                <button class="btn btn-outline-warning btn-sm" aria-label="Đánh giá đơn hàng">
                                                    <i class="fas fa-star me-1"></i>
                                                    Đánh giá
                                                </button>
                                            @endif

                                            @if(in_array($order->status, ['completed', 'shipping']))
                                                <button class="btn btn-outline-info btn-sm" aria-label="Hỗ trợ đơn hàng">
                                                    <i class="fas fa-headset me-1"></i>
                                                    Hỗ trợ
                                                </button>
                                            @endif

                                            <a href="{{ route('orders.show', $order->id) }}" 
                                               class="btn btn-primary btn-sm"
                                               aria-label="Xem chi tiết đơn hàng {{ $order->order_code }}">
                                                <i class="fas fa-eye me-1"></i>
                                                Chi tiết
                                            </a>
                                        </div>
                                    </div>
                                </footer>
                            </article>
                            @endforeach
                        </div>

                        {{-- Pagination --}}
                        @if($orders->hasPages())
                        <nav aria-label="Phân trang đơn hàng" class="mt-4">
                            {{ $orders->links() }}
                        </nav>
                        @endif
                    @endif
                </div>
            </div>
        </main>
    </div>
</div>
@endsection

{{-- Single Cancel Order Modal - Đặt NGOÀI @section để tránh bị ảnh hưởng bởi z-index --}}
<div class="modal fade"
     id="cancelOrderModal"
     tabindex="-1"
     aria-labelledby="cancelOrderModalLabel"
     aria-hidden="true">

    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">

            <form id="cancelOrderForm" method="POST">
                @csrf
                @method('PATCH')

                <div class="modal-header">
                    <h5 class="modal-title" id="cancelOrderModalLabel">
                        <i class="fas fa-exclamation-circle text-warning me-2"></i>
                        Xác nhận hủy đơn hàng
                    </h5>
                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <p>
                        Bạn có chắc chắn muốn hủy đơn hàng
                        <strong class="text-primary" id="cancelOrderCode"></strong>?
                    </p>

                    <div class="alert alert-warning border-start border-4">
                        <strong>Lưu ý:</strong> Đơn hàng đã hủy không thể hoàn tác!
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button"
                            class="btn btn-secondary"
                            data-bs-dismiss="modal">
                        Đóng
                    </button>

                    <button type="submit"
                            class="btn btn-danger">
                        Xác nhận hủy
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const cancelModal = document.getElementById('cancelOrderModal');
    const cancelForm = document.getElementById('cancelOrderForm');
    const orderCodeSpan = document.getElementById('cancelOrderCode');

    cancelModal.addEventListener('show.bs.modal', function (event) {

        const button = event.relatedTarget;

        const orderId = button.getAttribute('data-order-id');
        const orderCode = button.getAttribute('data-order-code');

        orderCodeSpan.textContent = orderCode;

        // Set action đúng route Laravel
        cancelForm.action = `/orders/${orderId}/cancel`;

    });

});
</script>
@endpush

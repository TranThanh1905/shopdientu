{{-- resources/views/orders/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng #' . $order->order_code)

@section('content')
<div class="container my-5">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('orders.index') }}">
                    <i class="fas fa-shopping-bag me-1"></i>
                    Đơn hàng của tôi
                </a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">
                {{ $order->order_code }}
            </li>
        </ol>
    </nav>

    <div class="row g-4">
        {{-- Main Content --}}
        <main class="col-lg-8">
            {{-- Order Header --}}
            <article class="card shadow-sm mb-4">
                <div class="card-body p-4">
                    <header class="d-flex flex-wrap justify-content-between align-items-start gap-3 mb-4">
                        <div>
                            <h1 class="h3 mb-2 fw-bold">
                                <i class="fas fa-file-invoice me-2 text-primary"></i>
                                Đơn hàng {{ $order->order_code }}
                            </h1>
                            <p class="text-muted mb-0">
                                <i class="fas fa-calendar-alt me-1"></i>
                                Đặt ngày {{ $order->created_at->format('d/m/Y \l\ú\c H:i') }}
                            </p>
                        </div>
                        <span class="badge bg-{{ $order->status_color }} p-3 fs-5" role="status">
                            {{ $order->status_label }}
                        </span>
                    </header>

                    {{-- Order Timeline --}}
                    <section class="order-timeline" aria-label="Tiến trình đơn hàng">
                        @php
                            $statuses = ['pending', 'confirmed', 'shipping', 'completed'];
                            $currentIndex = array_search($order->status, $statuses);
                            if ($currentIndex === false) $currentIndex = -1;
                            $labels = \App\Models\Order::getStatusLabels();
                            $icons = [
                                'pending' => 'fa-clock',
                                'confirmed' => 'fa-check-circle',
                                'shipping' => 'fa-shipping-fast',
                                'completed' => 'fa-check-double'
                            ];
                        @endphp

                        <div class="d-flex justify-content-between position-relative">
                            @foreach($statuses as $index => $status)
                                @php
                                    $isActive = $index <= $currentIndex;
                                    $isCurrent = $index === $currentIndex;
                                @endphp
                                <div class="text-center flex-fill">
                                    <div class="timeline-icon {{ $isActive ? 'active' : '' }} {{ $isCurrent ? 'current' : '' }}"
                                         role="img" 
                                         aria-label="{{ $labels[$status] }}{{ $isCurrent ? ' - Hiện tại' : '' }}">
                                        <i class="fas {{ $icons[$status] }}"></i>
                                    </div>
                                    <small class="d-block mt-2 {{ $isActive ? 'fw-bold text-dark' : 'text-muted' }}">
                                        {{ $labels[$status] }}
                                    </small>
                                </div>
                                @if(!$loop->last)
                                    <div class="timeline-line {{ $index < $currentIndex ? 'active' : '' }}" 
                                         role="presentation"></div>
                                @endif
                            @endforeach
                        </div>
                    </section>

                    {{-- Cancelled/Returned/Damaged Status --}}
                    @if(in_array($order->status, ['cancelled', 'returned', 'damaged']))
                        <div class="alert alert-{{ $order->status === 'cancelled' ? 'secondary' : 'danger' }} border-start border-4 mt-4 mb-0">
                            <div class="d-flex">
                                <i class="fas fa-info-circle me-3 mt-1"></i>
                                <div>
                                    <strong class="d-block mb-1">Trạng thái:</strong>
                                    <p class="mb-0">{{ $order->status_label }}</p>
                                    @if($order->return_reason)
                                        <hr class="my-2">
                                        <strong class="d-block mb-1">Lý do:</strong>
                                        <p class="mb-0">{{ $order->return_reason }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </article>

            {{-- Product Details --}}
            <section class="card shadow-sm mb-4" aria-labelledby="product-details-heading">
                <div class="card-header bg-white border-bottom">
                    <h2 id="product-details-heading" class="h5 mb-0 fw-semibold">
                        <i class="fas fa-box me-2 text-primary"></i>
                        Chi tiết sản phẩm
                    </h2>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th scope="col" class="border-0">Sản phẩm</th>
                                    <th scope="col" class="border-0 text-center" style="width: 80px">SL</th>
                                    <th scope="col" class="border-0 text-end" style="width: 120px">Đơn giá</th>
                                    <th scope="col" class="border-0 text-end" style="width: 120px">Thành tiền</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderDetails as $detail)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($detail->product->image)
                                                <img src="{{ asset($detail->product->image) }}" 
                                                     alt="{{ $detail->product->name }}"
                                                     class="rounded me-3"
                                                     style="width: 60px; height: 60px; object-fit: cover;"
                                                     loading="lazy">
                                            @endif
                                            <div>
                                                <strong class="d-block mb-1">{{ $detail->product->name }}</strong>
                                                <small class="text-muted d-block">
                                                    <i class="fas fa-tag me-1"></i>
                                                    {{ $detail->product->category->name }}
                                                </small>
                                                @if($detail->discount_percent > 0)
                                                    <span class="badge bg-danger mt-1">
                                                        <i class="fas fa-percent me-1"></i>
                                                        -{{ $detail->discount_percent }}%
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center align-middle">
                                        <span class="badge bg-light text-dark border">{{ $detail->quantity }}</span>
                                    </td>
                                    <td class="text-end align-middle">
                                        @if($detail->discount_percent > 0)
                                            <small class="text-muted text-decoration-line-through d-block">
                                                {{ number_format($detail->selling_price) }}₫
                                            </small>
                                        @endif
                                        <strong class="text-primary">{{ number_format($detail->final_price) }}₫</strong>
                                    </td>
                                    <td class="text-end align-middle">
                                        <strong class="fs-6">{{ number_format($detail->subtotal) }}₫</strong>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td colspan="3" class="text-end border-0"><strong>Tạm tính:</strong></td>
                                    <td class="text-end border-0">{{ number_format($order->total_amount) }}₫</td>
                                </tr>
                                @if($order->discount_amount > 0)
                                <tr>
                                    <td colspan="3" class="text-end border-0">
                                        <strong>
                                            <i class="fas fa-tag me-1"></i>
                                            Giảm giá:
                                        </strong>
                                    </td>
                                    <td class="text-end text-danger border-0">-{{ number_format($order->discount_amount) }}₫</td>
                                </tr>
                                @endif
                                <tr>
                                    <td colspan="3" class="text-end border-0">
                                        <strong>
                                            <i class="fas fa-shipping-fast me-1"></i>
                                            Phí vận chuyển:
                                        </strong>
                                    </td>
                                    <td class="text-end border-0">
                                        @if($order->final_amount >= 500000)
                                            <span class="text-success fw-semibold">
                                                <i class="fas fa-check-circle me-1"></i>
                                                Miễn phí
                                            </span>
                                        @else
                                            30,000₫
                                        @endif
                                    </td>
                                </tr>
                                <tr class="table-success">
                                    <td colspan="3" class="text-end border-0 fw-bold fs-5">Tổng cộng:</td>
                                    <td class="text-end border-0">
                                        <strong class="text-success fs-4">{{ number_format($order->final_amount) }}₫</strong>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </section>

            {{-- Shipping Information --}}
            <section class="card shadow-sm" aria-labelledby="shipping-info-heading">
                <div class="card-header bg-white border-bottom">
                    <h2 id="shipping-info-heading" class="h5 mb-0 fw-semibold">
                        <i class="fas fa-shipping-fast me-2 text-primary"></i>
                        Thông tin giao hàng
                    </h2>
                </div>
                <div class="card-body">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <dl class="mb-0">
                                <dt class="text-muted small mb-1">
                                    <i class="fas fa-user me-1"></i>
                                    Người nhận:
                                </dt>
                                <dd class="mb-3 fw-semibold">{{ $order->fullname }}</dd>

                                <dt class="text-muted small mb-1">
                                    <i class="fas fa-phone me-1"></i>
                                    Số điện thoại:
                                </dt>
                                <dd class="mb-3 fw-semibold">{{ $order->phone }}</dd>

                                <dt class="text-muted small mb-1">
                                    <i class="fas fa-envelope me-1"></i>
                                    Email:
                                </dt>
                                <dd class="mb-0 fw-semibold">{{ $order->email }}</dd>
                            </dl>
                        </div>
                        <div class="col-md-6">
                            <dt class="text-muted small mb-1">
                                <i class="fas fa-map-marker-alt me-1"></i>
                                Địa chỉ giao hàng:
                            </dt>
                            <dd class="fw-semibold">{{ $order->address }}</dd>
                        </div>
                    </div>

                    @if($order->note)
                        <hr class="my-4">
                        <div class="alert alert-light border mb-0">
                            <dt class="text-muted small mb-2">
                                <i class="fas fa-sticky-note me-1"></i>
                                Ghi chú:
                            </dt>
                            <dd class="mb-0">{{ $order->note }}</dd>
                        </div>
                    @endif
                </div>
            </section>
        </main>

        {{-- Sidebar --}}
        <aside class="col-lg-4">
            {{-- Actions Card --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h3 class="h5 mb-0">
                        <i class="fas fa-tasks me-2"></i>
                        Hành động
                    </h3>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        @if($order->status === 'pending')
                            <button type="button" 
                                    class="btn btn-danger"
                                    data-bs-toggle="modal" 
                                    data-bs-target="#cancelModal"
                                    aria-label="Hủy đơn hàng">
                                <i class="fas fa-times me-2"></i>
                                Hủy đơn hàng
                            </button>
                        @endif

                        @if($order->status === 'completed')
                            <button class="btn btn-warning" aria-label="Đánh giá sản phẩm">
                                <i class="fas fa-star me-2"></i>
                                Đánh giá sản phẩm
                            </button>
                        @endif

                        @if(in_array($order->status, ['completed', 'shipping']))
                            <button class="btn btn-info text-white" aria-label="Liên hệ hỗ trợ">
                                <i class="fas fa-headset me-2"></i>
                                Liên hệ hỗ trợ
                            </button>
                        @endif

                        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
                            <i class="fas fa-list me-2"></i>
                            Xem đơn hàng khác
                        </a>

                        <button class="btn btn-outline-primary" onclick="window.print()" aria-label="In đơn hàng">
                            <i class="fas fa-print me-2"></i>
                            In đơn hàng
                        </button>
                    </div>
                </div>
            </div>

            {{-- Order Info Card --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-info text-white">
                    <h3 class="h5 mb-0">
                        <i class="fas fa-info-circle me-2"></i>
                        Thông tin đơn hàng
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless mb-0">
                        <tbody>
                            <tr>
                                <td class="text-muted small">
                                    <i class="fas fa-hashtag me-1"></i>
                                    Mã đơn:
                                </td>
                                <td class="text-end fw-semibold">{{ $order->order_code }}</td>
                            </tr>
                            @if($order->customer_code)
                            <tr>
                                <td class="text-muted small">
                                    <i class="fas fa-user-tag me-1"></i>
                                    Mã KH:
                                </td>
                                <td class="text-end fw-semibold">{{ $order->customer_code }}</td>
                            </tr>
                            @endif
                            <tr>
                                <td class="text-muted small">
                                    <i class="fas fa-calendar-plus me-1"></i>
                                    Ngày đặt:
                                </td>
                                <td class="text-end fw-semibold">{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted small">
                                    <i class="fas fa-sync me-1"></i>
                                    Cập nhật:
                                </td>
                                <td class="text-end fw-semibold">{{ $order->updated_at->format('d/m/Y H:i') }}</td>
                            </tr>
                            <tr>
                                <td class="text-muted small">
                                    <i class="fas fa-flag me-1"></i>
                                    Trạng thái:
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-{{ $order->status_color }}">
                                        {{ $order->status_label }}
                                    </span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted small">
                                    <i class="fas fa-credit-card me-1"></i>
                                    Thanh toán:
                                </td>
                                <td class="text-end">
                                    <span class="badge bg-success">COD</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Support Card --}}
            <div class="card shadow-sm">
                <div class="card-body text-center p-4">
                    <i class="fas fa-headset fa-3x text-primary mb-3"></i>
                    <h4 class="h6 fw-semibold mb-2">Cần hỗ trợ?</h4>
                    <p class="text-muted small mb-4">
                        Liên hệ với chúng tôi để được hỗ trợ tốt nhất
                    </p>
                    <div class="d-grid gap-2">
                        <a href="tel:1900xxxx" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-phone me-2"></i>
                            1900 xxxx
                        </a>
                        <a href="mailto:support@electroshop.vn" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-envelope me-2"></i>
                            support@electroshop.vn
                        </a>
                    </div>
                </div>
            </div>
        </aside>
    </div>
</div>
@endsection

{{-- Cancel Order Modal - Đặt NGOÀI @section để tránh bị ảnh hưởng bởi z-index --}}
<div class="modal fade" id="cancelModal" tabindex="-1" aria-labelledby="cancelModalLabel" aria-hidden="true" data-bs-backdrop="true" data-bs-keyboard="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="{{ route('orders.cancel', $order->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="cancelModalLabel">
                        <i class="fas fa-exclamation-circle text-warning me-2"></i>
                        Xác nhận hủy đơn hàng
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
                </div>
                <div class="modal-body">
                    <p class="mb-3">
                        Bạn có chắc chắn muốn hủy đơn hàng <strong class="text-primary">{{ $order->order_code }}</strong>?
                    </p>
                    <div class="alert alert-warning border-start border-4 mb-3">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Lưu ý:</strong> Đơn hàng đã hủy không thể hoàn tác!
                    </div>
                    <div class="mb-0">
                        <label for="cancel_reason" class="form-label fw-semibold">
                            Lý do hủy (tùy chọn):
                        </label>
                        <textarea name="cancel_reason" 
                                  id="cancel_reason"
                                  class="form-control" 
                                  rows="3" 
                                  placeholder="VD: Đặt nhầm, tìm được giá tốt hơn..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>
                        Đóng
                    </button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-check me-1"></i>
                        Xác nhận hủy
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('styles')
<style>
/* Đảm bảo modal luôn ở trên cùng */
.modal {
    z-index: 10000 !important;
}

.modal-backdrop {
    z-index: 9999 !important;
}

/* Đảm bảo body không bị scroll khi modal mở */
body.modal-open {
    overflow: hidden;
    padding-right: 0 !important;
}

@media print {
    .no-print,
    .btn,
    .modal,
    nav,
    footer,
    aside {
        display: none !important;
    }
    
    .col-lg-8 {
        width: 100% !important;
        max-width: 100% !important;
    }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const cancelModal = document.getElementById('cancelModal');
    
    if (cancelModal) {
        // Đảm bảo modal và backdrop có z-index đúng khi hiển thị
        cancelModal.addEventListener('shown.bs.modal', function () {
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.style.zIndex = '9999';
            }
            this.style.zIndex = '10000';
        });
        
        // Clean up when modal is hidden
        cancelModal.addEventListener('hidden.bs.modal', function () {
            document.body.classList.remove('modal-open');
            const backdrop = document.querySelector('.modal-backdrop');
            if (backdrop) {
                backdrop.remove();
            }
            // Reset padding
            document.body.style.paddingRight = '';
        });
    }
});
</script>
@endpush
{-- resources/views/admin/orders/show.blade.php --}
@extends('admin.layouts.admin')

@section('title', 'Đơn hàng ' . $order->order_code)

@section('breadcrumb')
    <a href="{{ route('admin.orders.index') }}"
       style="color: #64748b; text-decoration: none;">Đơn hàng</a>
    <i class="fa-solid fa-chevron-right" style="font-size: 0.6rem; color: #cbd5e1;"></i>
    <span style="color: #1f2933; font-weight: 600;">{{ $order->order_code }}</span>
@endsection

@push('styles')
<style>
    /* ── Dùng đúng biến màu từ _variables.scss ── */
    :root {
        --navy:       #0f172a;
        --blue-dark:  #1e3a8a;
        --blue-main:  #2563eb;
        --blue-light: #38bdf8;
        --indigo:     #4f46e5;
    }

    /* ── Status badges ── */
    .order-status {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        padding: 0.35rem 0.875rem;
        border-radius: 999px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.02em;
    }
    .order-status::before {
        content: '';
        width: 6px; height: 6px;
        border-radius: 50%;
        background: currentColor;
        opacity: 0.65;
    }
    .order-status.pending   { background: #fef9c3; color: #854d0e; }
    .order-status.confirmed { background: #e0f2fe; color: #0c4a6e; }
    .order-status.shipping  { background: #ede9fe; color: #3730a3; }
    .order-status.completed { background: #dcfce7; color: #14532d; }
    .order-status.cancelled { background: #f1f5f9; color: #475569; }
    .order-status.returned  { background: #fee2e2; color: #991b1b; }
    .order-status.damaged   { background: #0f172a; color: #e2e8f0; }

    /* ── Confirmation log timeline ── */
    .log-item {
        display: flex;
        gap: 0.875rem;
        padding: 1rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .log-item:last-child { border-bottom: none; }

    .log-avatar {
        width: 38px; height: 38px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-size: 0.72rem;
        font-weight: 700;
        color: #fff;
        flex-shrink: 0;
        background: linear-gradient(135deg, #2563eb, #4f46e5);
    }
    .log-avatar.mob {
        background: linear-gradient(135deg, #fbbf24, #f97316);
    }

    .log-role-badge {
        font-size: 0.65rem;
        font-weight: 700;
        padding: 0.1rem 0.45rem;
        border-radius: 999px;
        background: rgba(37,99,235,0.1);
        color: #1e3a8a;
    }
    .log-role-badge.mob {
        background: rgba(249,115,22,0.12);
        color: #9a3412;
    }

    .log-action-arrow {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        font-size: 0.8rem;
        color: #475569;
    }

    .log-note {
        background: #f8fafc;
        border-left: 3px solid #e2e8f0;
        padding: 0.375rem 0.625rem;
        border-radius: 0 6px 6px 0;
        font-size: 0.78rem;
        color: #64748b;
        font-style: italic;
        margin-top: 0.375rem;
    }

    /* ── Product table ── */
    .product-img {
        width: 42px; height: 42px;
        object-fit: cover;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        background: #f8fafc;
    }

    .discount-pill {
        display: inline-block;
        background: #fef9c3;
        color: #854d0e;
        font-size: 0.68rem;
        font-weight: 700;
        padding: 0.1rem 0.45rem;
        border-radius: 999px;
    }

    .profit-pill {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        background: #dcfce7;
        color: #166534;
        font-size: 0.7rem;
        font-weight: 700;
        padding: 0.15rem 0.5rem;
        border-radius: 999px;
    }

    /* ── Info list ── */
    .info-row {
        display: flex;
        gap: 0.75rem;
        padding: 0.55rem 0;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.85rem;
    }
    .info-row:last-child { border-bottom: none; }
    .info-row .ir-label {
        width: 130px;
        flex-shrink: 0;
        color: #94a3b8;
        font-size: 0.76rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        padding-top: 1px;
    }
    .info-row .ir-value {
        color: #1f2933;
        font-weight: 500;
    }

    /* ── Summary ── */
    .summary-line {
        display: flex;
        justify-content: space-between;
        padding: 0.5rem 0;
        font-size: 0.875rem;
        border-bottom: 1px solid #f1f5f9;
    }
    .summary-line:last-child { border-bottom: none; }
    .summary-line .sl-label { color: #64748b; }
    .summary-line .sl-val   { font-weight: 600; color: #1f2933; }

    /* ── Warning banner ── */
    .warn-banner {
        background: linear-gradient(135deg, rgba(251,191,36,0.12), rgba(249,115,22,0.12));
        border: 1px solid rgba(251,191,36,0.4);
        border-radius: 10px;
        padding: 0.625rem 0.875rem;
        font-size: 0.78rem;
        color: #92400e;
        display: flex;
        gap: 0.5rem;
        align-items: flex-start;
    }
</style>
@endpush

@section('content')

{{-- ── Page header ── --}}
<div class="d-flex flex-wrap align-items-center gap-2 mb-4">
    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary btn-sm">
        <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
    </a>

    <h5 class="mb-0 fw-bold" style="color: var(--navy);">
        {{ $order->order_code }}
    </h5>

    <span class="order-status {{ $order->status }}">
        {{ $order->status_label }}
    </span>

    <div class="ms-auto d-flex gap-2 flex-wrap">
        <a href="{{ route('admin.orders.preview', $order->id) }}"
           target="_blank"
           class="btn btn-info btn-sm">
            <i class="fa-solid fa-eye me-1"></i> Xem hoá đơn
        </a>
        <a href="{{ route('admin.orders.print', $order->id) }}"
           class="btn btn-secondary btn-sm">
            <i class="fa-solid fa-print me-1"></i> In PDF
        </a>
        @if(in_array($order->status, ['pending', 'cancelled']) && Auth::user()->role === 'admin')
            <form method="POST"
                  action="{{ route('admin.orders.destroy', $order->id) }}"
                  onsubmit="return confirm('Xoá vĩnh viễn {{ $order->order_code }}? Không thể hoàn tác!')">
                @csrf @method('DELETE')
                <button type="submit" class="btn btn-danger btn-sm">
                    <i class="fa-solid fa-trash me-1"></i> Xoá đơn
                </button>
            </form>
        @endif
    </div>
</div>

{{-- ── Stats cards (dùng đúng .stats-card từ _admin.scss) ── --}}
@php
    $totalCost    = $order->orderDetails->sum(fn($d) => $d->purchase_price * $d->quantity);
    $totalRevenue = $order->final_amount;
    $totalProfit  = $order->orderDetails->sum(fn($d) => ($d->final_price - $d->purchase_price) * $d->quantity);
    $totalItems   = $order->orderDetails->sum('quantity');
@endphp

<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="stats-card stats-card--info">
            <div class="stats-card__content">
                <div>
                    <div class="stats-card__value">{{ $totalItems }}</div>
                    <div class="stats-card__label">Sản phẩm</div>
                </div>
                <div class="stats-card__icon">
                    <i class="fa-solid fa-box"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stats-card stats-card--warning">
            <div class="stats-card__content">
                <div>
                    <div class="stats-card__value" style="font-size: 1.1rem;">
                        {{ number_format($totalCost / 1000) }}K
                    </div>
                    <div class="stats-card__label">Giá vốn</div>
                </div>
                <div class="stats-card__icon">
                    <i class="fa-solid fa-coins"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stats-card stats-card--primary">
            <div class="stats-card__content">
                <div>
                    <div class="stats-card__value" style="font-size: 1.1rem;">
                        {{ number_format($totalRevenue / 1000) }}K
                    </div>
                    <div class="stats-card__label">Doanh thu</div>
                </div>
                <div class="stats-card__icon">
                    <i class="fa-solid fa-money-bill-wave"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="stats-card stats-card--success">
            <div class="stats-card__content">
                <div>
                    <div class="stats-card__value" style="font-size: 1.1rem;">
                        {{ number_format($totalProfit / 1000) }}K
                    </div>
                    <div class="stats-card__label">Lợi nhuận</div>
                </div>
                <div class="stats-card__icon">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- ── Layout 2 cột ── --}}
<div class="row g-3 align-items-start">

    {{-- ════ CỘT TRÁI ════ --}}
    <div class="col-lg-8 d-flex flex-column gap-3">

        {{-- Bảng sản phẩm --}}
        <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
            <div class="card-header bg-primary d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fa-solid fa-box me-2"></i>Sản phẩm trong đơn
                </h6>
                <span class="badge bg-light text-dark">{{ $totalItems }} sản phẩm</span>
            </div>

            <div class="table-responsive">
                <table class="table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 50px;"></th>
                            <th>Sản phẩm</th>
                            <th class="text-end">Giá bán</th>
                            <th class="text-center">Giảm</th>
                            <th class="text-end">Thực thu</th>
                            <th class="text-center">SL</th>
                            <th class="text-end">Thành tiền</th>
                            <th class="text-end">Lợi nhuận</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderDetails as $detail)
                            @php
                                $lineProfit = ($detail->final_price - $detail->purchase_price) * $detail->quantity;
                            @endphp
                            <tr>
                                <td>
                                    @if($detail->product?->image)
                                        <img src="{{ asset($detail->product->image) }}"
                                             alt="{{ $detail->product->name }}"
                                             class="product-img">
                                    @else
                                        <div class="product-img d-flex align-items-center justify-content-center">
                                            <i class="fa-solid fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-semibold" style="color: #1f2933; font-size: 0.875rem;">
                                        {{ $detail->product->name ?? '—' }}
                                    </div>
                                    <div style="font-size: 0.72rem; color: #94a3b8; margin-top: 2px;">
                                        {{ $detail->product->category->name ?? '' }}
                                    </div>
                                </td>
                                <td class="text-end" style="color: #94a3b8; font-size: 0.78rem; text-decoration: line-through;">
                                    {{ number_format($detail->selling_price) }}₫
                                </td>
                                <td class="text-center">
                                    @if($detail->discount_percent > 0)
                                        <span class="discount-pill">-{{ $detail->discount_percent }}%</span>
                                    @else
                                        <span style="color: #cbd5e1;">—</span>
                                    @endif
                                </td>
                                <td class="text-end fw-semibold" style="color: #1f2933;">
                                    {{ number_format($detail->final_price) }}₫
                                </td>
                                <td class="text-center fw-bold">{{ $detail->quantity }}</td>
                                <td class="text-end fw-bold" style="color: #0f172a;">
                                    {{ number_format($detail->final_price * $detail->quantity) }}₫
                                </td>
                                <td class="text-end">
                                    <span class="profit-pill">
                                        <i class="fa-solid fa-arrow-trend-up" style="font-size: 0.62rem;"></i>
                                        {{ number_format($lineProfit) }}₫
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Tổng kết --}}
            <div class="card-body" style="max-width: 300px; margin-left: auto;">
                <div class="summary-line">
                    <span class="sl-label">Tạm tính</span>
                    <span class="sl-val">{{ number_format($order->total_amount) }}₫</span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="summary-line">
                        <span class="sl-label">Giảm giá</span>
                        <span class="sl-val" style="color: #16a34a;">
                            -{{ number_format($order->discount_amount) }}₫
                        </span>
                    </div>
                @endif
                <div class="summary-line">
                    <span class="sl-label fw-bold" style="color: #1f2933;">Tổng thanh toán</span>
                    <span class="sl-val" style="font-size: 1rem; color: #2563eb;">
                        {{ number_format($order->final_amount) }}₫
                    </span>
                </div>
            </div>
        </div>

        {{-- Nhật ký xác nhận --}}
        <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
            <div class="card-header bg-secondary d-flex justify-content-between align-items-center">
                <h6 class="mb-0">
                    <i class="fa-solid fa-clipboard-list me-2"></i>Nhật ký xác nhận
                </h6>
                @php $logCount = $order->confirmationLogs()->count(); @endphp
                @if($logCount)
                    <span class="badge bg-light text-dark">{{ $logCount }} lần</span>
                @endif
            </div>
            <div class="card-body" style="padding: 0.5rem 1.25rem;">
                @forelse($order->confirmationLogs()->with('confirmedBy')->latest()->get() as $log)
                    <div class="log-item">
                        {{-- Avatar --}}
                        <div class="log-avatar {{ $log->confirmedBy?->role === 'mob' ? 'mob' : '' }}">
                            {{ strtoupper(substr($log->confirmedBy?->name ?? 'U', 0, 2)) }}
                        </div>

                        {{-- Body --}}
                        <div style="flex: 1; min-width: 0;">
                            <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                <span style="font-weight: 700; font-size: 0.875rem; color: #1f2933;">
                                    {{ $log->confirmedBy?->name ?? 'Không rõ' }}
                                </span>
                                <span class="log-role-badge {{ $log->confirmedBy?->role === 'mob' ? 'mob' : '' }}">
                                    {{ $log->confirmedBy?->role === 'admin' ? 'Quản trị viên' : 'Trung gian' }}
                                </span>
                                <span class="ms-auto" style="font-size: 0.72rem; color: #94a3b8;">
                                    <i class="fa-regular fa-clock me-1"></i>
                                    {{ $log->created_at->format('H:i — d/m/Y') }}
                                </span>
                            </div>

                            {{-- Chuyển trạng thái --}}
                            <div class="log-action-arrow mb-1">
                                <span class="order-status {{ $log->old_status }}"
                                      style="font-size: 0.68rem; padding: .15rem .5rem;">
                                    {{ \App\Models\Order::getStatusLabels()[$log->old_status] ?? $log->old_status }}
                                </span>
                                <i class="fa-solid fa-arrow-right" style="font-size: 0.65rem; color: #94a3b8;"></i>
                                <span class="order-status {{ $log->new_status }}"
                                      style="font-size: 0.68rem; padding: .15rem .5rem;">
                                    {{ \App\Models\Order::getStatusLabels()[$log->new_status] ?? $log->new_status }}
                                </span>
                            </div>

                            @if($log->note)
                                <div class="log-note">
                                    <i class="fa-solid fa-quote-left me-1" style="opacity: .4; font-size: .65rem;"></i>
                                    {{ $log->note }}
                                </div>
                            @endif

                            @if($log->ip_address)
                                <div style="font-size: 0.7rem; color: #cbd5e1; margin-top: 0.25rem;">
                                    <i class="fa-solid fa-network-wired me-1"></i>{{ $log->ip_address }}
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center py-4" style="color: #cbd5e1;">
                        <i class="fa-solid fa-clipboard fa-2x d-block mb-2"></i>
                        <span style="font-size: .875rem;">Chưa có nhật ký xác nhận</span>
                    </div>
                @endforelse
            </div>
        </div>

    </div>{{-- end cột trái --}}

    {{-- ════ CỘT PHẢI ════ --}}
    <div class="col-lg-4 d-flex flex-column gap-3">

        {{-- Thông tin khách hàng --}}
        <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
            <div class="card-header bg-info">
                <h6 class="mb-0">
                    <i class="fa-solid fa-user me-2"></i>Thông tin khách hàng
                </h6>
            </div>
            <div class="card-body">
                <div class="info-row">
                    <span class="ir-label">Họ tên</span>
                    <span class="ir-value">{{ $order->fullname }}</span>
                </div>
                <div class="info-row">
                    <span class="ir-label">Email</span>
                    <span class="ir-value">{{ $order->email }}</span>
                </div>
                <div class="info-row">
                    <span class="ir-label">Điện thoại</span>
                    <span class="ir-value">{{ $order->phone }}</span>
                </div>
                <div class="info-row">
                    <span class="ir-label">Địa chỉ</span>
                    <span class="ir-value">{{ $order->address }}</span>
                </div>
                @if($order->customer_code)
                    <div class="info-row">
                        <span class="ir-label">Mã KH</span>
                        <span class="ir-value fw-bold" style="color: #2563eb;">
                            {{ $order->customer_code }}
                        </span>
                    </div>
                @endif
                <div class="info-row">
                    <span class="ir-label">Ngày đặt</span>
                    <span class="ir-value">{{ $order->created_at->format('H:i — d/m/Y') }}</span>
                </div>
                <div class="info-row">
                    <span class="ir-label">Cập nhật</span>
                    <span class="ir-value" style="color: #94a3b8;">
                        {{ $order->updated_at->diffForHumans() }}
                    </span>
                </div>
                @if($order->note)
                    <div class="info-row">
                        <span class="ir-label">Ghi chú</span>
                        <span class="ir-value" style="color: #64748b; font-style: italic;">
                            {{ $order->note }}
                        </span>
                    </div>
                @endif
                @if($order->return_reason)
                    <div class="info-row">
                        <span class="ir-label">Lý do trả</span>
                        <span class="ir-value" style="color: #dc2626;">
                            {{ $order->return_reason }}
                        </span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Cập nhật trạng thái — CHỈ ADMIN --}}
        @if(Auth::user()->role === 'admin')
            <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header bg-warning">
                    <h6 class="mb-0">
                        <i class="fa-solid fa-pen-to-square me-2"></i>Cập nhật trạng thái
                    </h6>
                </div>
                <div class="card-body">
                    <div class="warn-banner mb-3">
                        <i class="fa-solid fa-triangle-exclamation flex-shrink-0 mt-1"></i>
                        <span>
                            Mọi thay đổi đều được ghi vào <strong>nhật ký</strong>
                            kèm tên bạn để xác định trách nhiệm.
                        </span>
                    </div>

                    <form method="POST"
                          action="{{ route('admin.orders.updateStatus', $order->id) }}"
                          onsubmit="return confirm('Xác nhận thay đổi trạng thái đơn hàng?')">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size: .82rem; color: #475569;">
                                Trạng thái mới
                            </label>
                            <select name="status" class="form-select form-select-sm">
                                @foreach(\App\Models\Order::getStatusLabels() as $key => $label)
                                    <option value="{{ $key }}"
                                            {{ $order->status === $key ? 'selected' : '' }}>
                                        {{ $label }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size: .82rem; color: #475569;">
                                Ghi chú <span style="color: #94a3b8; font-weight: 400;">(tuỳ chọn)</span>
                            </label>
                            <textarea name="note"
                                      class="form-control form-control-sm"
                                      rows="3"
                                      placeholder="Lý do thay đổi, ghi chú nội bộ..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fa-solid fa-check me-2"></i>Lưu thay đổi
                        </button>
                    </form>
                </div>
            </div>
        @endif

        {{-- Xác nhận đơn — CHỈ MOB & đơn pending --}}
        @if(Auth::user()->role === 'mob' && $order->status === 'pending')
            <div class="card border-0 shadow-sm" style="border-radius: 16px; overflow: hidden;">
                <div class="card-header bg-success">
                    <h6 class="mb-0">
                        <i class="fa-solid fa-circle-check me-2"></i>Xác nhận đơn hàng
                    </h6>
                </div>
                <div class="card-body">
                    <div class="warn-banner mb-3">
                        <i class="fa-solid fa-triangle-exclamation flex-shrink-0 mt-1"></i>
                        <span>
                            Tên bạn sẽ được ghi vào <strong>nhật ký</strong>.
                            Kho bị trừ ngay sau khi xác nhận.
                        </span>
                    </div>
                    <form method="POST"
                          action="{{ route('mob.orders.confirm', $order->id) }}"
                          onsubmit="return confirm('Xác nhận đơn hàng này? Tên bạn sẽ được ghi lại.')">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold" style="font-size: .82rem; color: #475569;">
                                Ghi chú <span style="color: #94a3b8; font-weight: 400;">(tuỳ chọn)</span>
                            </label>
                            <textarea name="note"
                                      class="form-control form-control-sm"
                                      rows="3"
                                      placeholder="Ghi chú khi xác nhận..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fa-solid fa-check me-2"></i>Xác nhận đơn hàng
                        </button>
                    </form>
                </div>
            </div>
        @endif

    </div>{{-- end cột phải --}}

</div>

@endsection
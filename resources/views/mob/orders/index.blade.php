@extends('mob.layouts.mob')

@section('title', 'Danh sách đơn hàng')
@section('breadcrumb', 'Danh sách đơn hàng')

@section('content')

{{-- Page header --}}
<div class="d-flex justify-content-between align-items-center mb-6">
    <div>
        <h4 class="fw-bold mb-0" style="color:#0f172a;">
            <i class="fa-solid fa-list-check text-primary"></i>
            Đơn hàng cần xử lý
        </h4>
        <p class="text-muted mb-0" style="font-size:0.875rem;margin-top:0.25rem;">
            Bạn chỉ có thể <strong>xem</strong> và
            <strong style="color:#22c55e;">xác nhận đơn đang chờ</strong>
        </p>
    </div>
</div>

{{-- Stats cards --}}
<div class="row g-3 mb-6">
    <div class="col-md-4">
        <div class="rounded-lg shadow-sm p-4 bg-white"
             style="border-left:4px solid #fbbf24;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div style="font-size:2rem;font-weight:700;color:#fbbf24;line-height:1;">
                        {{ $stats['pending'] }}
                    </div>
                    <div class="text-muted" style="font-size:0.875rem;margin-top:0.25rem;">
                        Chờ xử lý
                    </div>
                </div>
                <div style="width:48px;height:48px;background:#fef3c7;border-radius:12px;
                            display:flex;align-items:center;justify-content:center;">
                    <i class="fa-solid fa-clock" style="color:#fbbf24;font-size:1.25rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="rounded-lg shadow-sm p-4 bg-white"
             style="border-left:4px solid #205aa7;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div style="font-size:2rem;font-weight:700;color:#205aa7;line-height:1;">
                        {{ $stats['confirmed'] }}
                    </div>
                    <div class="text-muted" style="font-size:0.875rem;margin-top:0.25rem;">
                        Đã xác nhận
                    </div>
                </div>
                <div style="width:48px;height:48px;background:#dbeafe;border-radius:12px;
                            display:flex;align-items:center;justify-content:center;">
                    <i class="fa-solid fa-circle-check" style="color:#205aa7;font-size:1.25rem;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="rounded-lg shadow-sm p-4 bg-white"
             style="border-left:4px solid #fb923c;">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div style="font-size:2rem;font-weight:700;color:#fb923c;line-height:1;">
                        {{ $stats['shipping'] }}
                    </div>
                    <div class="text-muted" style="font-size:0.875rem;margin-top:0.25rem;">
                        Đang giao
                    </div>
                </div>
                <div style="width:48px;height:48px;background:#ffedd5;border-radius:12px;
                            display:flex;align-items:center;justify-content:center;">
                    <i class="fa-solid fa-truck" style="color:#fb923c;font-size:1.25rem;"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Filter bar --}}
<div class="bg-white rounded-lg shadow-sm p-4 mb-4">
    <form method="GET" class="d-flex align-items-center gap-3 flex-wrap">
        <div style="flex:1;min-width:200px;">
            <div style="position:relative;">
                <i class="fa-solid fa-magnifying-glass"
                   style="position:absolute;left:0.75rem;top:50%;transform:translateY(-50%);color:#94a3b8;"></i>
                <input type="text" name="search"
                       class="form-control"
                       style="padding-left:2.25rem;"
                       placeholder="Tìm mã đơn, tên, SĐT..."
                       value="{{ request('search') }}">
            </div>
        </div>

        <div style="min-width:180px;">
            <select name="status" class="form-select">
                <option value="">Tất cả trạng thái</option>
                @foreach(App\Models\Order::getStatusLabels() as $key => $label)
                    <option value="{{ $key }}"
                            {{ request('status') === $key ? 'selected' : '' }}>
                        {{ $label }}
                    </option>
                @endforeach
            </select>
        </div>

        <button type="submit" class="btn btn-primary">
            <i class="fa-solid fa-filter"></i> Lọc
        </button>

        @if(request('search') || request('status'))
            <a href="{{ route('mob.orders.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-xmark"></i> Xóa lọc
            </a>
        @endif
    </form>
</div>

{{-- Table --}}
<div class="bg-white rounded-lg shadow-sm" style="overflow:hidden;">
    <div class="table-responsive">
        <table class="table table-hover mb-0">
            <thead>
                <tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0;">
                    <th style="padding:0.875rem 1rem;font-weight:600;color:#475569;font-size:0.8125rem;text-transform:uppercase;letter-spacing:0.04em;">
                        Mã đơn hàng
                    </th>
                    <th style="padding:0.875rem 1rem;font-weight:600;color:#475569;font-size:0.8125rem;text-transform:uppercase;letter-spacing:0.04em;">
                        Khách hàng
                    </th>
                    <th style="padding:0.875rem 1rem;font-weight:600;color:#475569;font-size:0.8125rem;text-transform:uppercase;letter-spacing:0.04em;">
                        Giá trị
                    </th>
                    <th style="padding:0.875rem 1rem;font-weight:600;color:#475569;font-size:0.8125rem;text-transform:uppercase;letter-spacing:0.04em;">
                        Trạng thái
                    </th>
                    <th style="padding:0.875rem 1rem;font-weight:600;color:#475569;font-size:0.8125rem;text-transform:uppercase;letter-spacing:0.04em;">
                        Ngày đặt
                    </th>
                    <th style="padding:0.875rem 1rem;font-weight:600;color:#475569;font-size:0.8125rem;text-transform:uppercase;letter-spacing:0.04em;">
                        Nhật ký
                    </th>
                    <th style="padding:0.875rem 1rem;"></th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    <tr style="border-bottom:1px solid #f1f5f9;">
                        <td style="padding:1rem;">
                            <span class="fw-semibold" style="color:#205aa7;">
                                {{ $order->order_code }}
                            </span>
                        </td>
                        <td style="padding:1rem;">
                            <div class="fw-semibold" style="color:#0f172a;">
                                {{ $order->fullname }}
                            </div>
                            <div class="text-muted" style="font-size:0.8125rem;">
                                {{ $order->phone }}
                            </div>
                        </td>
                        <td style="padding:1rem;">
                            <span class="fw-bold text-primary">
                                {{ number_format($order->final_amount) }}₫
                            </span>
                        </td>
                        <td style="padding:1rem;">
                            @php
                                $statusColors = [
                                    'pending'   => ['bg'=>'#fef3c7','color'=>'#92400e'],
                                    'confirmed' => ['bg'=>'#dbeafe','color'=>'#1e40af'],
                                    'shipping'  => ['bg'=>'#ffedd5','color'=>'#9a3412'],
                                    'completed' => ['bg'=>'#dcfce7','color'=>'#166534'],
                                    'cancelled' => ['bg'=>'#f1f5f9','color'=>'#475569'],
                                    'returned'  => ['bg'=>'#fee2e2','color'=>'#991b1b'],
                                    'damaged'   => ['bg'=>'#f1f5f9','color'=>'#1e293b'],
                                ];
                                $sc = $statusColors[$order->status] ?? ['bg'=>'#f1f5f9','color'=>'#475569'];
                            @endphp
                            <span style="display:inline-block;padding:0.25rem 0.75rem;
                                        border-radius:9999px;font-size:0.8125rem;font-weight:600;
                                        background:{{ $sc['bg'] }};color:{{ $sc['color'] }};">
                                {{ $order->status_label }}
                            </span>
                        </td>
                        <td style="padding:1rem;color:#64748b;font-size:0.875rem;">
                            {{ $order->created_at->format('d/m/Y') }}<br>
                            <span style="font-size:0.75rem;">{{ $order->created_at->format('H:i') }}</span>
                        </td>
                        <td style="padding:1rem;">
                            @if($order->confirmationLogs->count())
                                <span style="display:inline-flex;align-items:center;gap:0.3rem;
                                            font-size:0.8125rem;color:#64748b;">
                                    <i class="fa-solid fa-clipboard-check text-success"></i>
                                    {{ $order->confirmationLogs->count() }}
                                </span>
                            @else
                                <span class="text-muted" style="font-size:0.8125rem;">—</span>
                            @endif
                        </td>
                        <td style="padding:1rem;">
                            <a href="{{ route('mob.orders.show', $order->id) }}"
                               class="btn btn-sm btn-outline-primary">
                                <i class="fa-solid fa-eye"></i> Xem
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center p-8">
                            <i class="fa-solid fa-inbox" style="font-size:2.5rem;color:#cbd5e1;"></i>
                            <p class="text-muted mt-3 mb-0">Không có đơn hàng nào</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
<div class="mt-4">
    {{ $orders->links() }}
</div>

@endsection
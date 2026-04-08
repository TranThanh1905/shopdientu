@extends('mob.layouts.mob')

@section('title', 'Chi tiết đơn hàng')
@section('breadcrumb', 'Chi tiết đơn hàng')

@section('content')

@php
    $statusMap = [
        'pending'   => ['bg'=>'#fef3c7','color'=>'#92400e','label'=>'Chờ xử lý'],
        'confirmed' => ['bg'=>'#dbeafe','color'=>'#1e40af','label'=>'Đã xác nhận'],
        'shipping'  => ['bg'=>'#ffedd5','color'=>'#9a3412','label'=>'Đang giao'],
        'completed' => ['bg'=>'#dcfce7','color'=>'#166534','label'=>'Hoàn thành'],
        'cancelled' => ['bg'=>'#f1f5f9','color'=>'#475569','label'=>'Đã hủy'],
        'returned'  => ['bg'=>'#fee2e2','color'=>'#991b1b','label'=>'Trả hàng'],
        'damaged'   => ['bg'=>'#f1f5f9','color'=>'#1e293b','label'=>'Hàng hỏng'],
    ];
    $sc = $statusMap[$order->status] ?? ['bg'=>'#f1f5f9','color'=>'#475569','label'=>$order->status];
@endphp

{{-- Header --}}
<div class="d-flex align-items-center mb-6" style="gap:0.75rem;flex-wrap:wrap;">
    <a href="{{ route('mob.orders.index') }}"
       class="btn btn-sm btn-outline-secondary rounded-lg">
        <i class="fa-solid fa-arrow-left"></i>
    </a>
    <div style="flex:1;">
        <h4 class="fw-bold mb-0" style="color:#0f172a;font-size:1.25rem;">
            Đơn hàng
            <span class="text-primary">{{ $order->order_code }}</span>
        </h4>
        <div class="text-muted" style="font-size:0.8125rem;margin-top:0.2rem;">
            <i class="fa-solid fa-calendar-day"></i>
            Đặt lúc {{ $order->created_at->format('H:i, d/m/Y') }}
        </div>
    </div>
    <span style="padding:0.35rem 1.1rem;border-radius:9999px;
                 font-size:0.875rem;font-weight:700;
                 background:{{ $sc['bg'] }};color:{{ $sc['color'] }};">
        {{ $sc['label'] }}
    </span>
</div>

<div class="row g-4">

    {{-- ===== CỘT TRÁI ===== --}}
    <div class="col-lg-8">

        {{-- Sản phẩm --}}
        <div class="bg-white rounded-lg shadow-sm mb-4" style="overflow:hidden;">
            <div style="padding:1rem 1.25rem;border-bottom:1px solid #e2e8f0;">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-semibold" style="color:#0f172a;">
                        <i class="fa-solid fa-box text-primary me-1"></i>
                        Sản phẩm trong đơn
                    </span>
                    <span class="text-muted" style="font-size:0.8125rem;">
                        {{ $order->orderDetails->count() }} sản phẩm
                    </span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table mb-0" style="font-size:0.9rem;">
                    <thead style="background:#f8fafc;">
                        <tr>
                            <th style="padding:0.75rem 1rem;color:#475569;
                                       font-weight:600;font-size:0.8125rem;">Sản phẩm</th>
                            <th style="padding:0.75rem 1rem;color:#475569;
                                       font-weight:600;font-size:0.8125rem;text-align:center;">SL</th>
                            <th style="padding:0.75rem 1rem;color:#475569;
                                       font-weight:600;font-size:0.8125rem;text-align:right;">Đơn giá</th>
                            <th style="padding:0.75rem 1rem;color:#475569;
                                       font-weight:600;font-size:0.8125rem;text-align:right;">Thành tiền</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->orderDetails as $detail)
                            <tr style="border-bottom:1px solid #f1f5f9;">
                                <td style="padding:0.875rem 1rem;">
                                    <div class="fw-semibold" style="color:#0f172a;">
                                        {{ $detail->product->name ?? '(Sản phẩm đã xóa)' }}
                                    </div>
                                    @if($detail->discount_percent > 0)
                                        <span style="font-size:0.75rem;color:#ef4444;">
                                            <i class="fa-solid fa-tag"></i>
                                            Giảm {{ $detail->discount_percent }}%
                                        </span>
                                    @endif
                                </td>
                                <td style="padding:0.875rem 1rem;text-align:center;">
                                    <span style="display:inline-block;padding:0.15rem 0.65rem;
                                                background:#f1f5f9;border-radius:9999px;
                                                font-weight:600;">
                                        {{ $detail->quantity }}
                                    </span>
                                </td>
                                <td style="padding:0.875rem 1rem;text-align:right;color:#475569;">
                                    {{ number_format($detail->final_price) }}₫
                                </td>
                                <td style="padding:0.875rem 1rem;text-align:right;" class="fw-semibold">
                                    {{ number_format($detail->final_price * $detail->quantity) }}₫
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot style="background:#f8fafc;">
                        <tr>
                            <td colspan="3" style="padding:0.875rem 1rem;text-align:right;"
                                class="fw-bold">Tổng thanh toán:</td>
                            <td style="padding:0.875rem 1rem;text-align:right;
                                       font-size:1.1rem;font-weight:700;color:#205aa7;">
                                {{ number_format($order->final_amount) }}₫
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        {{-- Nhật ký xác nhận --}}
        <div class="bg-white rounded-lg shadow-sm" style="overflow:hidden;">
            <div style="padding:1rem 1.25rem;border-bottom:1px solid #e2e8f0;">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-semibold" style="color:#0f172a;">
                        <i class="fa-solid fa-clipboard-list text-primary me-1"></i>
                        Nhật ký xác nhận
                    </span>
                    @if($order->confirmationLogs->count())
                        <span style="background:#dbeafe;color:#1e40af;font-size:0.75rem;
                                    font-weight:700;padding:0.2rem 0.7rem;border-radius:9999px;">
                            {{ $order->confirmationLogs->count() }} bản ghi
                        </span>
                    @endif
                </div>
            </div>
            <div style="padding:1.25rem;">
                @forelse($order->confirmationLogs as $log)
                    <div style="display:flex;gap:1rem;
                        {{ !$loop->last ? 'margin-bottom:1.25rem;padding-bottom:1.25rem;
                                          border-bottom:1px solid #f1f5f9;' : '' }}">

                        {{-- Avatar --}}
                        <div style="flex-shrink:0;width:42px;height:42px;border-radius:50%;
                                    background:{{ $log->confirmedBy->role === 'admin'
                                        ? '#ef4444' : '#205aa7' }};
                                    display:flex;align-items:center;justify-content:center;">
                            <i class="fa-solid fa-user" style="color:#fff;font-size:0.875rem;"></i>
                        </div>

                        <div style="flex:1;">
                            <div class="d-flex align-items-center" style="gap:0.5rem;flex-wrap:wrap;">
                                <span class="fw-semibold" style="color:#0f172a;">
                                    {{ $log->confirmedBy->name }}
                                </span>
                                <span style="padding:0.15rem 0.55rem;border-radius:9999px;
                                            font-size:0.7rem;font-weight:700;text-transform:uppercase;
                                            background:{{ $log->confirmedBy->role === 'admin'
                                                ? '#fee2e2' : '#dbeafe' }};
                                            color:{{ $log->confirmedBy->role === 'admin'
                                                ? '#991b1b' : '#1e40af' }};">
                                    {{ $log->confirmedBy->role }}
                                </span>
                            </div>

                            <div class="text-muted" style="font-size:0.8rem;margin-top:0.2rem;">
                                <i class="fa-solid fa-clock"></i>
                                {{ $log->created_at->format('H:i:s, d/m/Y') }}
                                &nbsp;&bull;&nbsp;
                                <i class="fa-solid fa-network-wired"></i>
                                {{ $log->ip_address }}
                            </div>

                            {{-- Hành động --}}
                            <div style="margin-top:0.4rem;font-size:0.875rem;color:#334155;">
                                @php
                                    $labels = \App\Models\Order::getStatusLabels();
                                    $oldLabel = $labels[$log->old_status] ?? $log->old_status;
                                    $newLabel = $labels[$log->new_status] ?? $log->new_status;
                                @endphp
                                <span style="background:#f1f5f9;padding:0.2rem 0.6rem;
                                            border-radius:4px;font-size:0.8rem;">
                                    {{ $oldLabel }}
                                </span>
                                <i class="fa-solid fa-arrow-right text-muted mx-1"
                                   style="font-size:0.75rem;"></i>
                                <span style="background:#dbeafe;padding:0.2rem 0.6rem;
                                            border-radius:4px;font-size:0.8rem;color:#1e40af;">
                                    {{ $newLabel }}
                                </span>
                            </div>

                            @if($log->note)
                                <div style="margin-top:0.4rem;font-size:0.8125rem;
                                            color:#64748b;font-style:italic;">
                                    <i class="fa-solid fa-quote-left" style="font-size:0.65rem;"></i>
                                    {{ $log->note }}
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="text-center" style="padding:2rem 0;">
                        <i class="fa-solid fa-clipboard text-muted"
                           style="font-size:2rem;opacity:0.4;"></i>
                        <p class="text-muted mb-0 mt-2" style="font-size:0.875rem;">
                            Chưa có nhật ký xác nhận
                        </p>
                    </div>
                @endforelse
            </div>
        </div>

    </div>{{-- /col-lg-8 --}}

    {{-- ===== CỘT PHẢI ===== --}}
    <div class="col-lg-4">

        {{-- Thông tin khách --}}
        <div class="bg-white rounded-lg shadow-sm mb-4" style="overflow:hidden;">
            <div style="padding:1rem 1.25rem;border-bottom:1px solid #e2e8f0;">
                <span class="fw-semibold" style="color:#0f172a;">
                    <i class="fa-solid fa-user text-primary me-1"></i>
                    Thông tin khách hàng
                </span>
            </div>
            <div style="padding:1.25rem;">
                @php
                    $rows = [
                        ['icon'=>'fa-user','label'=>'Họ tên','value'=>$order->fullname],
                        ['icon'=>'fa-envelope','label'=>'Email','value'=>$order->email],
                        ['icon'=>'fa-phone','label'=>'SĐT','value'=>$order->phone],
                        ['icon'=>'fa-location-dot','label'=>'Địa chỉ','value'=>$order->address],
                    ];
                @endphp
                @foreach($rows as $row)
                    <div style="display:flex;gap:0.75rem;margin-bottom:0.875rem;
                                {{ $loop->last ? 'margin-bottom:0' : '' }}">
                        <div style="width:32px;height:32px;background:#f1f5f9;border-radius:8px;
                                    display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                            <i class="fa-solid {{ $row['icon'] }}" style="color:#64748b;font-size:0.8rem;"></i>
                        </div>
                        <div>
                            <div style="font-size:0.75rem;color:#94a3b8;text-transform:uppercase;
                                        letter-spacing:0.04em;font-weight:600;">
                                {{ $row['label'] }}
                            </div>
                            <div style="font-size:0.9rem;color:#1e293b;font-weight:500;margin-top:0.1rem;">
                                {{ $row['value'] }}
                            </div>
                        </div>
                    </div>
                @endforeach

                @if($order->note)
                    <div style="margin-top:0.875rem;padding:0.75rem;background:#fffbeb;
                                border-radius:8px;border-left:3px solid #fbbf24;">
                        <div style="font-size:0.75rem;color:#92400e;font-weight:600;
                                    text-transform:uppercase;margin-bottom:0.25rem;">Ghi chú</div>
                        <div style="font-size:0.875rem;color:#78350f;">{{ $order->note }}</div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Tóm tắt giá trị --}}
        <div class="bg-white rounded-lg shadow-sm mb-4" style="overflow:hidden;">
            <div style="padding:1rem 1.25rem;border-bottom:1px solid #e2e8f0;">
                <span class="fw-semibold" style="color:#0f172a;">
                    <i class="fa-solid fa-receipt text-primary me-1"></i>
                    Tóm tắt đơn hàng
                </span>
            </div>
            <div style="padding:1.25rem;">
                <div class="d-flex justify-content-between align-items-center"
                     style="margin-bottom:0.6rem;">
                    <span class="text-muted" style="font-size:0.875rem;">Tạm tính</span>
                    <span style="font-size:0.9rem;">
                        {{ number_format($order->total_amount) }}₫
                    </span>
                </div>
                @if($order->discount_amount > 0)
                    <div class="d-flex justify-content-between align-items-center"
                         style="margin-bottom:0.6rem;">
                        <span class="text-muted" style="font-size:0.875rem;">Giảm giá</span>
                        <span style="color:#ef4444;font-size:0.9rem;">
                            -{{ number_format($order->discount_amount) }}₫
                        </span>
                    </div>
                @endif
                <div style="border-top:2px solid #e2e8f0;margin-top:0.75rem;padding-top:0.75rem;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold">Thanh toán</span>
                        <span style="font-size:1.2rem;font-weight:800;color:#205aa7;">
                            {{ number_format($order->final_amount) }}₫
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Xác nhận (chỉ pending) --}}
        @if($order->status === 'pending')
            <div class="rounded-lg shadow-sm" style="overflow:hidden;
                        border:2px solid #22c55e;">
                <div style="padding:1rem 1.25rem;background:#22c55e;">
                    <span class="fw-semibold" style="color:#fff;">
                        <i class="fa-solid fa-circle-check me-1"></i>
                        Xác nhận đơn hàng
                    </span>
                </div>
                <div style="padding:1.25rem;background:#fff;">
                    <div style="padding:0.75rem;background:#fef9c3;border-radius:8px;
                                border-left:3px solid #fbbf24;margin-bottom:1rem;
                                font-size:0.8125rem;color:#78350f;line-height:1.5;">
                        <i class="fa-solid fa-triangle-exclamation me-1"></i>
                        <strong>Lưu ý:</strong> Khi xác nhận, hàng sẽ được
                        <strong>trừ khỏi kho</strong> và nhật ký sẽ
                        <strong>ghi lại tên bạn</strong> để chịu trách nhiệm.
                    </div>

                    <form method="POST"
                          action="{{ route('mob.orders.confirm', $order->id) }}">
                        @csrf
                        <div style="margin-bottom:0.875rem;">
                            <label style="display:block;font-size:0.875rem;font-weight:600;
                                          color:#374151;margin-bottom:0.4rem;">
                                Ghi chú xác nhận
                                <span style="font-weight:400;color:#9ca3af;">(tuỳ chọn)</span>
                            </label>
                            <textarea name="note" rows="3"
                                      class="form-control"
                                      style="font-size:0.875rem;resize:none;"
                                      placeholder="Ví dụ: Đã kiểm tra hàng đủ số lượng..."></textarea>
                        </div>
                        <button type="submit"
                                class="btn btn-success w-100 fw-semibold"
                                onclick="return confirm(
                                    'Bạn chắc chắn muốn xác nhận đơn hàng {{ $order->order_code }}?\n\nHành động này sẽ được ghi vào nhật ký với tên của bạn.'
                                )">
                            <i class="fa-solid fa-check me-1"></i>
                            Xác nhận đơn hàng
                        </button>
                    </form>
                </div>
            </div>

        @else
            <div class="bg-white rounded-lg shadow-sm" style="overflow:hidden;">
                <div style="padding:1.5rem;text-align:center;">
                    <div style="width:52px;height:52px;background:#f1f5f9;border-radius:50%;
                                display:flex;align-items:center;justify-content:center;
                                margin:0 auto 0.75rem;">
                        <i class="fa-solid fa-lock" style="color:#94a3b8;font-size:1.25rem;"></i>
                    </div>
                    <p class="text-muted mb-0" style="font-size:0.875rem;line-height:1.6;">
                        Đơn hàng đã được xử lý.<br>
                        Bạn không có quyền thay đổi trạng thái.
                    </p>
                </div>
            </div>
        @endif

    </div>{{-- /col-lg-4 --}}

</div>
@endsection
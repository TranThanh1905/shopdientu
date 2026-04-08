@extends('admin.layouts.admin')
@section('title', 'Lịch sử giao dịch kho')

@section('content')
<div class="admin-content">

    {{-- Header --}}
    <div class="d-flex align-items-center mb-6" style="gap:0.75rem;">
        <a href="{{ route('admin.inventory.index') }}"
           class="btn btn-sm btn-outline-secondary rounded-lg">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0" style="color:#0f172a;">
                <i class="fa-solid fa-clock-rotate-left text-primary me-1"></i>
                Lịch sử giao dịch
            </h4>
            <div class="text-muted" style="font-size:0.8125rem;margin-top:0.15rem;">
                {{ $product->name }}
            </div>
        </div>
        <div style="margin-left:auto;">
            <a href="{{ route('admin.inventory.stockIn', $product->id) }}"
               class="btn btn-success btn-sm">
                <i class="fa-solid fa-plus"></i> Nhập hàng
            </a>
        </div>
    </div>

    {{-- Stats --}}
    <div class="row g-3 mb-6">
        @php
            $statItems = [
                ['label'=>'Tồn kho','value'=>$product->stock,
                 'icon'=>'fa-boxes-stacked',
                 'color'=> $product->stock == 0 ? '#ef4444'
                    : ($product->stock <= 10 ? '#f59e0b' : '#22c55e'),
                 'bg'=> $product->stock == 0 ? '#fee2e2'
                    : ($product->stock <= 10 ? '#fef3c7' : '#dcfce7')],
                ['label'=>'Đã bán','value'=>$product->inventory->quantity_sold ?? 0,
                 'icon'=>'fa-chart-line','color'=>'#22c55e','bg'=>'#dcfce7'],
                ['label'=>'Hàng hỏng','value'=>$product->inventory->quantity_damaged ?? 0,
                 'icon'=>'fa-circle-xmark','color'=>'#ef4444','bg'=>'#fee2e2'],
                ['label'=>'Trả hàng','value'=>$product->inventory->quantity_returned ?? 0,
                 'icon'=>'fa-rotate-left','color'=>'#f59e0b','bg'=>'#fef3c7'],
            ];
        @endphp
        @foreach($statItems as $item)
            <div class="col-md-3 col-6">
                <div class="bg-white rounded-lg shadow-sm p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div style="font-size:2rem;font-weight:800;
                                        color:{{ $item['color'] }};line-height:1;">
                                {{ $item['value'] }}
                            </div>
                            <div class="text-muted" style="font-size:0.8125rem;margin-top:0.3rem;">
                                {{ $item['label'] }}
                            </div>
                        </div>
                        <div style="width:44px;height:44px;border-radius:12px;
                                    background:{{ $item['bg'] }};
                                    display:flex;align-items:center;justify-content:center;">
                            <i class="fa-solid {{ $item['icon'] }}"
                               style="color:{{ $item['color'] }};font-size:1.1rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Table --}}
    <div class="bg-white rounded-lg shadow-sm" style="overflow:hidden;">
        <div style="padding:1rem 1.25rem;border-bottom:1px solid #e2e8f0;">
            <span class="fw-semibold" style="color:#0f172a;">
                Tất cả giao dịch
            </span>
            <span class="text-muted" style="font-size:0.8125rem;margin-left:0.5rem;">
                ({{ $transactions->total() }} bản ghi)
            </span>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0" style="font-size:0.9rem;">
                <thead>
                    <tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0;">
                        @foreach(['Thời gian','Loại','Số lượng',
                                   'Đơn giá','Thành tiền','Người thực hiện',
                                   'Đơn hàng','Ghi chú'] as $th)
                            <th style="padding:0.875rem 1rem;font-weight:600;
                                       color:#475569;font-size:0.8125rem;
                                       text-transform:uppercase;letter-spacing:0.04em;">
                                {{ $th }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($transactions as $trans)
                        @php
                            $typeConfig = [
                                'in'       => ['bg'=>'#dcfce7','color'=>'#166534','label'=>'Nhập kho', 'sign'=>'+','signColor'=>'#22c55e'],
                                'out'      => ['bg'=>'#dbeafe','color'=>'#1e40af','label'=>'Xuất kho', 'sign'=>'-','signColor'=>'#ef4444'],
                                'damaged'  => ['bg'=>'#fee2e2','color'=>'#991b1b','label'=>'Hàng hỏng','sign'=>'-','signColor'=>'#ef4444'],
                                'returned' => ['bg'=>'#fef3c7','color'=>'#92400e','label'=>'Trả hàng', 'sign'=>'+','signColor'=>'#22c55e'],
                            ];
                            $tc = $typeConfig[$trans->type] ?? ['bg'=>'#f1f5f9','color'=>'#475569','label'=>$trans->type,'sign'=>'','signColor'=>'#0f172a'];
                        @endphp
                        <tr style="border-bottom:1px solid #f1f5f9;">
                            <td style="padding:0.875rem 1rem;color:#64748b;font-size:0.875rem;">
                                {{ $trans->created_at->format('d/m/Y') }}<br>
                                <span style="font-size:0.75rem;">
                                    {{ $trans->created_at->format('H:i') }}
                                </span>
                            </td>
                            <td style="padding:0.875rem 1rem;">
                                <span style="padding:0.25rem 0.75rem;border-radius:9999px;
                                            font-size:0.8rem;font-weight:600;
                                            background:{{ $tc['bg'] }};
                                            color:{{ $tc['color'] }};">
                                    {{ $tc['label'] }}
                                </span>
                            </td>
                            <td style="padding:0.875rem 1rem;">
                                <span style="font-size:1rem;font-weight:800;
                                             color:{{ $tc['signColor'] }};">
                                    {{ $tc['sign'] }}{{ $trans->quantity }}
                                </span>
                            </td>
                            <td style="padding:0.875rem 1rem;color:#475569;">
                                {{ number_format($trans->unit_price) }}₫
                            </td>
                            <td style="padding:0.875rem 1rem;font-weight:700;">
                                {{ number_format($trans->unit_price * $trans->quantity) }}₫
                            </td>
                            <td style="padding:0.875rem 1rem;">
                                <span style="font-size:0.875rem;color:#374151;">
                                    {{ $trans->user->name ?? 'Hệ thống' }}
                                </span>
                            </td>
                            <td style="padding:0.875rem 1rem;">
                                @if($trans->order_id)
                                    <a href="{{ route('admin.orders.show', $trans->order_id) }}"
                                       class="text-primary text-decoration-none"
                                       style="font-size:0.875rem;font-weight:600;">
                                        {{ $trans->order->order_code ?? '#'.$trans->order_id }}
                                    </a>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td style="padding:0.875rem 1rem;max-width:200px;">
                                <span class="text-muted" style="font-size:0.8125rem;">
                                    {{ $trans->note ?? '—' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center p-8">
                                <i class="fa-solid fa-inbox"
                                   style="font-size:2.5rem;color:#cbd5e1;"></i>
                                <p class="text-muted mt-3 mb-0">Chưa có giao dịch nào</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $transactions->links() }}</div>

</div>
@endsection
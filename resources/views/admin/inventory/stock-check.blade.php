{{-- resources/views/admin/inventory/stock-check.blade.php --}}
@extends('admin.layouts.admin')
@section('title', 'Kiểm kê kho hàng')

@section('content')
<div class="admin-content">

    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="fw-bold mb-0" style="color:#0f172a;">
                <i class="fa-solid fa-clipboard-check text-primary me-2"></i>
                Kiểm kê kho hàng
            </h4>
            <p class="text-muted mb-0" style="font-size:0.875rem;margin-top:0.2rem;">
                Ngày kiểm: <strong>{{ now()->format('H:i, d/m/Y') }}</strong>
            </p>
        </div>
        <div class="d-flex" style="gap:0.5rem;">
            <a href="{{ route('admin.inventory.index') }}"
               class="btn btn-sm btn-outline-secondary">
                <i class="fa-solid fa-arrow-left"></i> Quay lại kho
            </a>
            <button onclick="window.print()"
                    class="btn btn-sm btn-outline-primary">
                <i class="fa-solid fa-print"></i> In báo cáo
            </button>
        </div>
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-lg shadow-sm p-4 mb-4 no-print">
        <form method="GET" class="d-flex align-items-center" style="gap:0.75rem;flex-wrap:wrap;">
            <div style="min-width:200px;">
                <select name="category_id" class="form-select">
                    <option value="">Tất cả danh mục</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}"
                                {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-filter"></i> Lọc
            </button>
            @if(request('category_id'))
                <a href="{{ route('admin.inventory.stock-check') }}"
                   class="btn btn-outline-secondary btn-sm">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            @endif
        </form>
    </div>

    {{-- Tổng kết --}}
    @php
        $totalInStock  = collect($products)->sum('quantity_in_stock');
        $totalSold     = collect($products)->sum('quantity_sold');
        $totalDamaged  = collect($products)->sum('quantity_damaged');
        $totalReturned = collect($products)->sum('quantity_returned');
        $outCount      = collect($products)->where('status','out_of_stock')->count();
        $lowCount      = collect($products)->where('status','low_stock')->count();
    @endphp

    <div class="row g-3 mb-6">
        @foreach([
            ['label'=>'Tổng tồn kho','value'=>$totalInStock, 'color'=>'#205aa7','bg'=>'#dbeafe','icon'=>'fa-warehouse'],
            ['label'=>'Tổng đã bán', 'value'=>$totalSold,    'color'=>'#22c55e','bg'=>'#dcfce7','icon'=>'fa-cart-flatbed'],
            ['label'=>'Hết hàng',    'value'=>$outCount,     'color'=>'#ef4444','bg'=>'#fee2e2','icon'=>'fa-ban'],
            ['label'=>'Sắp hết',     'value'=>$lowCount,     'color'=>'#f59e0b','bg'=>'#fef3c7','icon'=>'fa-triangle-exclamation'],
            ['label'=>'Hàng hỏng',   'value'=>$totalDamaged, 'color'=>'#8b5cf6','bg'=>'#ede9fe','icon'=>'fa-circle-xmark'],
            ['label'=>'Trả hàng',    'value'=>$totalReturned,'color'=>'#64748b','bg'=>'#f1f5f9','icon'=>'fa-rotate-left'],
        ] as $item)
            <div class="col-md-2 col-4">
                <div class="bg-white rounded-lg shadow-sm p-3 text-center">
                    <div style="width:40px;height:40px;border-radius:10px;
                                background:{{ $item['bg'] }};
                                display:flex;align-items:center;justify-content:center;
                                margin:0 auto 0.5rem;">
                        <i class="fa-solid {{ $item['icon'] }}"
                           style="color:{{ $item['color'] }};font-size:1rem;"></i>
                    </div>
                    <div style="font-size:1.5rem;font-weight:800;
                                color:{{ $item['color'] }};line-height:1;">
                        {{ $item['value'] }}
                    </div>
                    <div class="text-muted" style="font-size:0.75rem;margin-top:0.25rem;">
                        {{ $item['label'] }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Bảng kiểm kê --}}
    <div class="bg-white rounded-lg shadow-sm" style="overflow:hidden;">
        <div style="padding:1rem 1.25rem;border-bottom:1px solid #e2e8f0;">
            <div class="d-flex justify-content-between align-items-center">
                <span class="fw-semibold" style="color:#0f172a;">
                    Kết quả kiểm kê:
                    <span class="text-primary">{{ count($products) }}</span> sản phẩm
                </span>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0" id="stockTable"
                   style="font-size:0.875rem;">
                <thead>
                    <tr style="background:#f8fafc;border-bottom:2px solid #e2e8f0;">
                        <th style="padding:0.875rem 1rem;font-weight:600;color:#475569;
                                   font-size:0.8125rem;text-transform:uppercase;letter-spacing:0.04em;">
                            STT
                        </th>
                        <th style="padding:0.875rem 1rem;font-weight:600;color:#475569;
                                   font-size:0.8125rem;text-transform:uppercase;letter-spacing:0.04em;">
                            Sản phẩm
                        </th>
                        <th style="padding:0.875rem 1rem;font-weight:600;color:#475569;
                                   font-size:0.8125rem;text-transform:uppercase;letter-spacing:0.04em;">
                            Danh mục
                        </th>
                        <th style="padding:0.875rem 1rem;font-weight:600;color:#475569;
                                   font-size:0.8125rem;text-transform:uppercase;letter-spacing:0.04em;
                                   text-align:center;">
                            Tồn kho
                        </th>
                        <th style="padding:0.875rem 1rem;font-weight:600;color:#475569;
                                   font-size:0.8125rem;text-transform:uppercase;letter-spacing:0.04em;
                                   text-align:center;">
                            Đã bán
                        </th>
                        <th style="padding:0.875rem 1rem;font-weight:600;color:#475569;
                                   font-size:0.8125rem;text-transform:uppercase;letter-spacing:0.04em;
                                   text-align:center;">
                            Hỏng
                        </th>
                        <th style="padding:0.875rem 1rem;font-weight:600;color:#475569;
                                   font-size:0.8125rem;text-transform:uppercase;letter-spacing:0.04em;
                                   text-align:center;">
                            Trả
                        </th>
                        <th style="padding:0.875rem 1rem;font-weight:600;color:#475569;
                                   font-size:0.8125rem;text-transform:uppercase;letter-spacing:0.04em;
                                   text-align:center;">
                            Trạng thái
                        </th>
                        <th class="no-print" style="padding:0.875rem 1rem;font-weight:600;
                                   color:#475569;font-size:0.8125rem;text-transform:uppercase;
                                   letter-spacing:0.04em;">
                            Hành động
                        </th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($products as $i => $product)
                        @php
                            $rowBg = match($product['status']) {
                                'out_of_stock' => 'rgba(239,68,68,0.04)',
                                'low_stock'    => 'rgba(245,158,11,0.04)',
                                default        => 'transparent',
                            };
                            $statusBadge = match($product['status']) {
                                'out_of_stock' => ['bg'=>'#fee2e2','color'=>'#991b1b','label'=>'Hết hàng'],
                                'low_stock'    => ['bg'=>'#fef3c7','color'=>'#92400e','label'=>'Sắp hết'],
                                default        => ['bg'=>'#dcfce7','color'=>'#166534','label'=>'Còn hàng'],
                            };
                        @endphp
                        <tr style="border-bottom:1px solid #f1f5f9;background:{{ $rowBg }};">
                            <td style="padding:0.875rem 1rem;color:#94a3b8;font-size:0.8125rem;">
                                {{ $i + 1 }}
                            </td>
                            <td style="padding:0.875rem 1rem;">
                                <span class="fw-semibold" style="color:#0f172a;">
                                    {{ $product['name'] }}
                                </span>
                            </td>
                            <td style="padding:0.875rem 1rem;">
                                <span style="padding:0.2rem 0.65rem;border-radius:9999px;
                                            font-size:0.8rem;font-weight:600;
                                            background:#dbeafe;color:#1e40af;">
                                    {{ $product['category'] }}
                                </span>
                            </td>
                            <td style="padding:0.875rem 1rem;text-align:center;">
                                <span style="font-size:1.125rem;font-weight:800;
                                             color:{{ $statusBadge['color'] }};">
                                    {{ $product['quantity_in_stock'] }}
                                </span>
                            </td>
                            <td style="padding:0.875rem 1rem;text-align:center;
                                       font-weight:700;color:#22c55e;">
                                {{ $product['quantity_sold'] }}
                            </td>
                            <td style="padding:0.875rem 1rem;text-align:center;
                                       font-weight:700;color:#ef4444;">
                                {{ $product['quantity_damaged'] }}
                            </td>
                            <td style="padding:0.875rem 1rem;text-align:center;
                                       font-weight:700;color:#f59e0b;">
                                {{ $product['quantity_returned'] }}
                            </td>
                            <td style="padding:0.875rem 1rem;text-align:center;">
                                <span style="padding:0.25rem 0.8rem;border-radius:9999px;
                                            font-size:0.8rem;font-weight:700;
                                            background:{{ $statusBadge['bg'] }};
                                            color:{{ $statusBadge['color'] }};">
                                    {{ $statusBadge['label'] }}
                                </span>
                            </td>
                            <td style="padding:0.875rem 1rem;" class="no-print">
                                @if($product['status'] !== 'in_stock')
                                    <a href="{{ route('admin.inventory.stockIn', $product['id']) }}"
                                       class="btn btn-sm btn-success">
                                        <i class="fa-solid fa-plus"></i> Nhập hàng
                                    </a>
                                @else
                                    <span class="text-muted" style="font-size:0.8125rem;">—</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

</div>

<style>
    @media print {
        .no-print,
        .admin-sidebar,
        .admin-topbar,
        .btn { display: none !important; }
        .admin-content { margin: 0 !important; padding: 0 !important; }
        .bg-white { box-shadow: none !important; border: 1px solid #e2e8f0 !important; }
    }
</style>
@endsection
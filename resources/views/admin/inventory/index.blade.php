@extends('admin.layouts.admin')
@section('title', 'Quản lý kho hàng')

@section('content')
<div class="admin-content">

    {{-- Page header --}}
    <div class="d-flex justify-content-between align-items-center mb-6">
        <div>
            <h4 class="fw-bold mb-0" style="color:#0f172a;">
                <i class="fa-solid fa-warehouse text-primary me-2"></i>Quản lý kho hàng
            </h4>
            <p class="text-muted mb-0" style="font-size:0.875rem;margin-top:0.2rem;">
                Tổng quan tồn kho tất cả sản phẩm
            </p>
        </div>
        <div class="d-flex" style="gap:0.5rem;">
            <a href="{{ route('admin.inventory.stock-check') }}"
               class="btn btn-outline-primary btn-sm">
                <i class="fa-solid fa-clipboard-check"></i> Kiểm hàng
            </a>
            <a href="{{ route('admin.inventory.import.list') }}"
               class="btn btn-outline-secondary btn-sm">
                <i class="fa-solid fa-list"></i> Phiếu nhập
            </a>
            <a href="{{ route('admin.inventory.import.create') }}"
               class="btn btn-success btn-sm">
                <i class="fa-solid fa-plus"></i> Nhập hàng mới
            </a>
        </div>
    </div>

    {{-- Summary cards --}}
    <div class="row g-3 mb-6">
        @php
            $summaryItems = [
                ['value'=>$summary['total_products'], 'label'=>'Tổng sản phẩm',
                 'icon'=>'fa-boxes-stacked', 'color'=>'#205aa7', 'bg'=>'#dbeafe'],
                ['value'=>$summary['out_of_stock'],  'label'=>'Hết hàng',
                 'icon'=>'fa-ban',          'color'=>'#ef4444', 'bg'=>'#fee2e2'],
                ['value'=>$summary['low_stock'],     'label'=>'Sắp hết (≤10)',
                 'icon'=>'fa-triangle-exclamation','color'=>'#f59e0b','bg'=>'#fef3c7'],
                ['value'=>$summary['total_sold'],    'label'=>'Đã bán',
                 'icon'=>'fa-cart-flatbed', 'color'=>'#22c55e', 'bg'=>'#dcfce7'],
                ['value'=>$summary['total_damaged'], 'label'=>'Hàng hỏng',
                 'icon'=>'fa-circle-xmark','color'=>'#8b5cf6', 'bg'=>'#ede9fe'],
                ['value'=>$summary['total_returned'],'label'=>'Trả hàng',
                 'icon'=>'fa-rotate-left',  'color'=>'#64748b', 'bg'=>'#f1f5f9'],
            ];
        @endphp

        @foreach($summaryItems as $item)
            <div class="col-md-2 col-sm-4 col-6">
                <div class="bg-white rounded-lg shadow-sm p-4"
                     style="border-top:3px solid {{ $item['color'] }};">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div style="font-size:1.75rem;font-weight:800;
                                        color:{{ $item['color'] }};line-height:1;">
                                {{ $item['value'] }}
                            </div>
                            <div class="text-muted" style="font-size:0.8rem;margin-top:0.3rem;">
                                {{ $item['label'] }}
                            </div>
                        </div>
                        <div style="width:36px;height:36px;border-radius:9px;
                                    background:{{ $item['bg'] }};
                                    display:flex;align-items:center;justify-content:center;">
                            <i class="fa-solid {{ $item['icon'] }}"
                               style="color:{{ $item['color'] }};font-size:0.875rem;"></i>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    {{-- Filter --}}
    <div class="bg-white rounded-lg shadow-sm p-4 mb-4">
        <form method="GET" action="{{ route('admin.inventory.index') }}"
              class="d-flex align-items-center" style="gap:0.75rem;flex-wrap:wrap;">
            <div style="flex:1;min-width:200px;position:relative;">
                <i class="fa-solid fa-magnifying-glass"
                   style="position:absolute;left:0.75rem;top:50%;
                          transform:translateY(-50%);color:#94a3b8;font-size:0.875rem;"></i>
                <input type="text" name="search" class="form-control"
                       style="padding-left:2.2rem;"
                       placeholder="Tìm sản phẩm..."
                       value="{{ request('search') }}">
            </div>
            <div style="min-width:160px;">
                <select name="category_id" class="form-select">
                    <option value="">Tất cả danh mục</option>
                    @foreach(\App\Models\Category::all() as $cat)
                        <option value="{{ $cat->id }}"
                                {{ request('category_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div style="min-width:160px;">
                <select name="stock_status" class="form-select">
                    <option value="">Tất cả trạng thái</option>
                    <option value="out_of_stock"
                            {{ request('stock_status') == 'out_of_stock' ? 'selected' : '' }}>
                        Hết hàng
                    </option>
                    <option value="low_stock"
                            {{ request('stock_status') == 'low_stock' ? 'selected' : '' }}>
                        Sắp hết (≤10)
                    </option>
                    <option value="in_stock"
                            {{ request('stock_status') == 'in_stock' ? 'selected' : '' }}>
                        Còn hàng (&gt;10)
                    </option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">
                <i class="fa-solid fa-filter"></i> Lọc
            </button>
            @if(request('search') || request('category_id') || request('stock_status'))
                <a href="{{ route('admin.inventory.index') }}"
                   class="btn btn-outline-secondary">
                    <i class="fa-solid fa-xmark"></i>
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
                        @foreach(['Sản phẩm','Danh mục','Tồn kho','Đã bán','Hỏng','Trả',
                                   'Giá nhập','Giá bán','Thao tác'] as $th)
                            <th style="padding:0.875rem 1rem;font-weight:600;color:#475569;
                                       font-size:0.8125rem;text-transform:uppercase;
                                       letter-spacing:0.04em;white-space:nowrap;">
                                {{ $th }}
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @forelse($products as $product)
                        @php
                            $stock = $product->stock;
                            $rowBg = $stock === 0
                                ? 'rgba(239,68,68,0.04)'
                                : ($stock <= 10 ? 'rgba(251,191,36,0.04)' : 'transparent');
                        @endphp
                        <tr style="border-bottom:1px solid #f1f5f9;background:{{ $rowBg }};">
                            <td style="padding:1rem;">
                                <div class="fw-semibold" style="color:#0f172a;">
                                    {{ $product->name }}
                                </div>
                            </td>
                            <td style="padding:1rem;">
                                <span style="padding:0.2rem 0.65rem;border-radius:9999px;
                                            font-size:0.8rem;font-weight:600;
                                            background:#dbeafe;color:#1e40af;">
                                    {{ $product->category->name }}
                                </span>
                            </td>
                            <td style="padding:1rem;text-align:center;">
                                @php
                                    $stockStyle = $stock === 0
                                        ? 'background:#fee2e2;color:#991b1b;'
                                        : ($stock <= 10
                                            ? 'background:#fef3c7;color:#92400e;'
                                            : 'background:#dcfce7;color:#166534;');
                                @endphp
                                <span style="display:inline-block;padding:0.2rem 0.75rem;
                                            border-radius:9999px;font-weight:800;
                                            font-size:0.9375rem;{{ $stockStyle }}">
                                    {{ $stock }}
                                </span>
                            </td>
                            <td style="padding:1rem;text-align:center;color:#22c55e;font-weight:600;">
                                {{ $product->inventory->quantity_sold ?? 0 }}
                            </td>
                            <td style="padding:1rem;text-align:center;color:#ef4444;font-weight:600;">
                                {{ $product->inventory->quantity_damaged ?? 0 }}
                            </td>
                            <td style="padding:1rem;text-align:center;color:#f59e0b;font-weight:600;">
                                {{ $product->inventory->quantity_returned ?? 0 }}
                            </td>
                            <td style="padding:1rem;color:#64748b;font-size:0.875rem;">
                                {{ number_format($product->purchase_price) }}₫
                            </td>
                            <td style="padding:1rem;font-weight:700;">
                                {{ number_format($product->selling_price) }}₫
                            </td>
                            <td style="padding:1rem;">
                                <div class="d-flex" style="gap:0.35rem;">
                                    <a href="{{ route('admin.inventory.stockIn', $product->id) }}"
                                       class="btn btn-sm btn-success"
                                       title="Nhập hàng">
                                        <i class="fa-solid fa-plus"></i>
                                    </a>
                                    <a href="{{ route('admin.inventory.transactions', $product->id) }}"
                                       class="btn btn-sm btn-outline-secondary"
                                       title="Lịch sử giao dịch">
                                        <i class="fa-solid fa-clock-rotate-left"></i>
                                    </a>
                                    <button type="button"
                                            class="btn btn-sm btn-outline-danger"
                                            data-bs-toggle="modal"
                                            data-bs-target="#dmgModal{{ $product->id }}"
                                            title="Đánh dấu hỏng">
                                        <i class="fa-solid fa-triangle-exclamation"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>

                        {{-- Modal hàng hỏng --}}
                        <div class="modal fade" id="dmgModal{{ $product->id }}" tabindex="-1">
                            <div class="modal-dialog">
                                <div class="modal-content rounded-lg" style="overflow:hidden;">
                                    <form action="{{ route('admin.inventory.markDamaged', $product->id) }}"
                                          method="POST">
                                        @csrf
                                        <div class="modal-header"
                                             style="background:#ef4444;padding:1rem 1.25rem;">
                                            <h5 class="modal-title text-white fw-bold mb-0">
                                                <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                                Đánh dấu hàng hỏng
                                            </h5>
                                            <button type="button" class="btn-close btn-close-white"
                                                    data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body" style="padding:1.25rem;">
                                            <div style="padding:0.75rem;background:#f8fafc;
                                                        border-radius:8px;margin-bottom:1rem;">
                                                <div class="text-muted" style="font-size:0.8rem;">Sản phẩm</div>
                                                <div class="fw-semibold">{{ $product->name }}</div>
                                                <div class="text-muted" style="font-size:0.8125rem;margin-top:0.2rem;">
                                                    Tồn kho: <strong style="color:#22c55e;">{{ $stock }}</strong>
                                                </div>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label fw-semibold">
                                                    Số lượng hỏng
                                                    <span style="color:#ef4444;">*</span>
                                                </label>
                                                <input type="number" name="quantity"
                                                       class="form-control"
                                                       min="1" max="{{ $stock }}" required>
                                            </div>
                                            <div>
                                                <label class="form-label fw-semibold">
                                                    Lý do
                                                    <span style="color:#ef4444;">*</span>
                                                </label>
                                                <textarea name="note" class="form-control"
                                                          rows="3" required
                                                          placeholder="Mô tả nguyên nhân hàng hỏng..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer" style="padding:0.875rem 1.25rem;">
                                            <button type="button" class="btn btn-outline-secondary"
                                                    data-bs-dismiss="modal">Hủy</button>
                                            <button type="submit" class="btn btn-danger fw-semibold">
                                                <i class="fa-solid fa-check me-1"></i>Xác nhận
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>

                    @empty
                        <tr>
                            <td colspan="9" class="text-center p-8">
                                <i class="fa-solid fa-inbox"
                                   style="font-size:2.5rem;color:#cbd5e1;"></i>
                                <p class="text-muted mt-3 mb-0">Chưa có sản phẩm nào</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-4">{{ $products->links() }}</div>

</div>
@endsection
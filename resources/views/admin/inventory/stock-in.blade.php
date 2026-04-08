{{-- resources/views/admin/inventory/stock-in.blade.php --}}
@extends('admin.layouts.admin')
@section('title', 'Nhập hàng vào kho')

@section('content')
<div class="admin-content">

    {{-- Breadcrumb --}}
    <div class="d-flex align-items-center mb-6" style="gap:0.75rem;">
        <a href="{{ route('admin.inventory.index') }}"
           class="btn btn-sm btn-outline-secondary rounded-lg">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0" style="color:#0f172a;">
                <i class="fa-solid fa-plus-circle text-success me-1"></i>
                Nhập hàng vào kho
            </h4>
            <div class="text-muted" style="font-size:0.8125rem;margin-top:0.15rem;">
                {{ $product->name }}
            </div>
        </div>
    </div>

    <div class="row g-4">

        {{-- Form --}}
        <div class="col-md-7">
            <div class="bg-white rounded-lg shadow-sm" style="overflow:hidden;">
                <div style="padding:1rem 1.25rem;border-bottom:1px solid #e2e8f0;">
                    <span class="fw-semibold" style="color:#0f172a;">
                        <i class="fa-solid fa-file-lines text-primary me-1"></i>
                        Thông tin nhập hàng
                    </span>
                </div>
                <div style="padding:1.5rem;">
                    <form action="{{ route('admin.inventory.processStockIn', $product->id) }}"
                          method="POST">
                        @csrf

                        {{-- Sản phẩm --}}
                        <div class="mb-4">
                            <label class="form-label fw-semibold">Sản phẩm</label>
                            <div style="padding:0.875rem 1rem;background:#f8fafc;
                                        border-radius:10px;border:1px solid #e2e8f0;">
                                <div class="fw-semibold" style="color:#0f172a;">
                                    {{ $product->name }}
                                </div>
                                <div class="text-muted" style="font-size:0.875rem;margin-top:0.2rem;">
                                    {{ $product->category->name }}
                                    &bull; Tồn kho hiện tại:
                                    <strong style="color:{{ $product->stock == 0
                                        ? '#ef4444' : ($product->stock <= 10 ? '#f59e0b' : '#22c55e') }};">
                                        {{ $product->stock }}
                                    </strong>
                                </div>
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Số lượng nhập
                                    <span style="color:#ef4444;">*</span>
                                </label>
                                <input type="number" name="quantity"
                                       class="form-control @error('quantity') is-invalid @enderror"
                                       value="{{ old('quantity') }}"
                                       min="1" required
                                       placeholder="Ví dụ: 50">
                                @error('quantity')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">
                                    Giá nhập (₫/sp)
                                    <span style="color:#ef4444;">*</span>
                                </label>
                                <input type="number" name="unit_price"
                                       class="form-control @error('unit_price') is-invalid @enderror"
                                       value="{{ old('unit_price', $product->purchase_price) }}"
                                       step="1000" min="0" required>
                                @error('unit_price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="text-muted" style="font-size:0.8rem;margin-top:0.3rem;">
                                    Giá nhập hiện tại:
                                    <strong>{{ number_format($product->purchase_price) }}₫</strong>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-semibold">Ghi chú</label>
                            <textarea name="note" class="form-control" rows="3"
                                      placeholder="Ví dụ: Nhập từ nhà cung cấp ABC, lô hàng tháng 3...">{{ old('note') }}</textarea>
                        </div>

                        {{-- Preview tổng --}}
                        <div style="padding:1rem;background:#f0fdf4;border-radius:10px;
                                    border:1px solid #bbf7d0;margin-bottom:1.25rem;"
                             id="previewBox">
                            <div class="d-flex justify-content-between align-items-center">
                                <span style="font-size:0.875rem;color:#166534;">
                                    <i class="fa-solid fa-calculator me-1"></i>
                                    Tổng giá trị nhập dự kiến
                                </span>
                                <span style="font-size:1.25rem;font-weight:800;color:#166534;"
                                      id="previewTotal">—</span>
                            </div>
                        </div>

                        <div style="border-top:1px solid #e2e8f0;padding-top:1.25rem;">
                            <button type="submit" class="btn btn-success fw-semibold"
                                    style="padding:0.75rem 2rem;">
                                <i class="fa-solid fa-boxes-stacked me-1"></i>
                                Nhập vào kho
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Sidebar info --}}
        <div class="col-md-5">

            {{-- Tình trạng kho --}}
            <div class="bg-white rounded-lg shadow-sm mb-4" style="overflow:hidden;">
                <div style="padding:1rem 1.25rem;border-bottom:1px solid #e2e8f0;">
                    <span class="fw-semibold" style="color:#0f172a;">
                        <i class="fa-solid fa-warehouse text-primary me-1"></i>
                        Tình trạng kho hiện tại
                    </span>
                </div>
                <div style="padding:1.25rem;">
                    @if($product->inventory)
                        @php
                            $khoRows = [
                                ['label'=>'Tồn kho','value'=>$product->stock,
                                 'color'=> $product->stock == 0 ? '#ef4444'
                                    : ($product->stock <= 10 ? '#f59e0b' : '#22c55e')],
                                ['label'=>'Đã bán','value'=>$product->inventory->quantity_sold,'color'=>'#22c55e'],
                                ['label'=>'Hàng hỏng','value'=>$product->inventory->quantity_damaged,'color'=>'#ef4444'],
                                ['label'=>'Trả hàng','value'=>$product->inventory->quantity_returned,'color'=>'#f59e0b'],
                            ];
                        @endphp
                        @foreach($khoRows as $row)
                            <div class="d-flex justify-content-between align-items-center"
                                 style="{{ !$loop->last ? 'margin-bottom:0.75rem;
                                           padding-bottom:0.75rem;border-bottom:1px solid #f1f5f9;' : '' }}">
                                <span class="text-muted" style="font-size:0.875rem;">
                                    {{ $row['label'] }}
                                </span>
                                <span style="font-size:1.125rem;font-weight:800;
                                             color:{{ $row['color'] }};">
                                    {{ $row['value'] }}
                                </span>
                            </div>
                        @endforeach
                    @else
                        <div style="padding:0.75rem;background:#fef3c7;border-radius:8px;
                                    font-size:0.875rem;color:#92400e;">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i>
                            Chưa có thông tin kho cho sản phẩm này
                        </div>
                    @endif
                </div>
            </div>

            {{-- Giá sản phẩm --}}
            <div class="bg-white rounded-lg shadow-sm" style="overflow:hidden;">
                <div style="padding:1rem 1.25rem;border-bottom:1px solid #e2e8f0;">
                    <span class="fw-semibold" style="color:#0f172a;">
                        <i class="fa-solid fa-tag text-primary me-1"></i>
                        Thông tin giá
                    </span>
                </div>
                <div style="padding:1.25rem;">
                    @php
                        $priceRows = [
                            ['label'=>'Giá nhập','value'=>$product->purchase_price,'color'=>'#64748b'],
                            ['label'=>'Giá bán','value'=>$product->selling_price,'color'=>'#0f172a'],
                            ['label'=>'Giá sau giảm','value'=>$product->final_price,'color'=>'#205aa7','bold'=>true],
                            ['label'=>'Lợi nhuận/sp','value'=>$product->profit_per_unit,'color'=>'#22c55e','bold'=>true],
                        ];
                    @endphp
                    @foreach($priceRows as $row)
                        <div class="d-flex justify-content-between align-items-center"
                             style="{{ !$loop->last ? 'margin-bottom:0.75rem;
                                       padding-bottom:0.75rem;border-bottom:1px solid #f1f5f9;' : '' }}">
                            <span class="text-muted" style="font-size:0.875rem;">
                                {{ $row['label'] }}
                            </span>
                            <span style="font-size:0.9375rem;
                                         font-weight:{{ isset($row['bold']) ? '700' : '500' }};
                                         color:{{ $row['color'] }};">
                                {{ number_format($row['value']) }}₫
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
    const qInput = document.querySelector('input[name="quantity"]');
    const pInput = document.querySelector('input[name="unit_price"]');
    const preview = document.getElementById('previewTotal');

    function updatePreview() {
        const q = parseInt(qInput.value) || 0;
        const p = parseInt(pInput.value) || 0;
        const total = q * p;
        preview.textContent = total > 0
            ? new Intl.NumberFormat('vi-VN').format(total) + '₫'
            : '—';
    }

    qInput?.addEventListener('input', updatePreview);
    pInput?.addEventListener('input', updatePreview);
    updatePreview();
</script>
@endpush
@endsection
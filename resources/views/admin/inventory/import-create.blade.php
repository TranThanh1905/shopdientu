{{-- resources/views/admin/inventory/import-create.blade.php --}}
@extends('admin.layouts.admin')
@section('title', 'Tạo phiếu nhập kho')

@section('content')
<div class="admin-content">

    {{-- Header --}}
    <div class="d-flex align-items-center mb-6" style="gap:0.75rem;">
        <a href="{{ route('admin.inventory.import.list') }}"
           class="btn btn-sm btn-outline-secondary rounded-lg">
            <i class="fa-solid fa-arrow-left"></i>
        </a>
        <div>
            <h4 class="fw-bold mb-0" style="color:#0f172a;">
                <i class="fa-solid fa-file-import text-success me-1"></i>
                Tạo phiếu nhập kho mới
            </h4>
            <div class="text-muted" style="font-size:0.8125rem;margin-top:0.15rem;">
                Phiếu sẽ ở trạng thái <strong>Nháp</strong> — hàng chỉ vào kho sau khi xác nhận
            </div>
        </div>
    </div>

    <form method="POST" action="{{ route('admin.inventory.import.store') }}" id="importForm">
        @csrf

        @if($errors->any())
            <div style="padding:1rem;background:#fee2e2;border-radius:10px;
                        border-left:4px solid #ef4444;margin-bottom:1.5rem;">
                <div class="fw-semibold" style="color:#991b1b;margin-bottom:0.5rem;">
                    <i class="fa-solid fa-circle-exclamation me-1"></i>
                    Vui lòng kiểm tra lại:
                </div>
                <ul style="margin:0;padding-left:1.25rem;color:#b91c1c;font-size:0.875rem;">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="row g-4">

            {{-- Danh sách sản phẩm --}}
            <div class="col-lg-8">
                <div class="bg-white rounded-lg shadow-sm" style="overflow:hidden;">
                    <div style="padding:1rem 1.25rem;border-bottom:1px solid #e2e8f0;">
                        <span class="fw-semibold" style="color:#0f172a;">
                            <i class="fa-solid fa-box text-primary me-1"></i>
                            Danh sách sản phẩm nhập
                        </span>
                    </div>
                    <div style="padding:1.5rem;">

                        {{-- Header cột --}}
                        <div class="row g-2 mb-2" style="font-size:0.8125rem;font-weight:600;
                                                          color:#64748b;text-transform:uppercase;
                                                          letter-spacing:0.03em;">
                            <div class="col-md-5">Sản phẩm</div>
                            <div class="col-md-2">Số lượng</div>
                            <div class="col-md-3">Giá nhập (₫)</div>
                            <div class="col-md-2"></div>
                        </div>

                        <div id="productRows">
                            <div class="product-row row g-2 mb-3 align-items-center">
                                <div class="col-md-5">
                                    <select name="products[0][id]"
                                            class="form-select product-select" required>
                                        <option value="">-- Chọn sản phẩm --</option>
                                        @foreach($products as $product)
                                            <option value="{{ $product->id }}"
                                                    data-price="{{ $product->purchase_price }}">
                                                {{ $product->name }}
                                                (Tồn: {{ $product->inventory->quantity_in_stock ?? 0 }})
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2">
                                    <input type="number" name="products[0][quantity]"
                                           class="form-control qty-input"
                                           min="1" value="1" required>
                                </div>
                                <div class="col-md-3">
                                    <input type="number" name="products[0][price]"
                                           class="form-control price-input"
                                           min="0" step="1000" required
                                           placeholder="0">
                                </div>
                                <div class="col-md-2">
                                    <button type="button"
                                            class="btn btn-outline-danger btn-sm remove-row w-100"
                                            disabled>
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <button type="button" id="addProductBtn"
                                class="btn btn-outline-success btn-sm">
                            <i class="fa-solid fa-plus me-1"></i>
                            Thêm sản phẩm
                        </button>
                    </div>
                </div>
            </div>

            {{-- Sidebar --}}
            <div class="col-lg-4">
                <div class="bg-white rounded-lg shadow-sm mb-4" style="overflow:hidden;">
                    <div style="padding:1rem 1.25rem;border-bottom:1px solid #e2e8f0;">
                        <span class="fw-semibold" style="color:#0f172a;">
                            <i class="fa-solid fa-info-circle text-primary me-1"></i>
                            Thông tin phiếu
                        </span>
                    </div>
                    <div style="padding:1.25rem;">
                        <div class="mb-4">
                            <label class="form-label fw-semibold" style="font-size:0.875rem;">
                                Ghi chú
                            </label>
                            <textarea name="note" class="form-control" rows="4"
                                      placeholder="Nhà cung cấp, nguồn gốc hàng...">{{ old('note') }}</textarea>
                        </div>

                        {{-- Tổng giá trị --}}
                        <div style="padding:1rem;background:linear-gradient(135deg,#205aa7,#fb923c);
                                    border-radius:12px;margin-bottom:1.25rem;">
                            <div style="font-size:0.8rem;color:rgba(255,255,255,0.8);
                                        text-transform:uppercase;letter-spacing:0.05em;
                                        font-weight:600;margin-bottom:0.3rem;">
                                Tổng giá trị nhập
                            </div>
                            <div style="font-size:1.75rem;font-weight:800;color:#fff;"
                                 id="totalValueDisplay">
                                0₫
                            </div>
                        </div>

                        {{-- Note về quy trình --}}
                        <div style="padding:0.875rem;background:#fef3c7;border-radius:10px;
                                    border-left:3px solid #fbbf24;
                                    font-size:0.8125rem;color:#92400e;
                                    line-height:1.6;margin-bottom:1.25rem;">
                            <i class="fa-solid fa-triangle-exclamation me-1"></i>
                            Phiếu tạo ở trạng thái <strong>Nháp</strong>.
                            Hàng chỉ thực sự vào kho sau khi bạn
                            <strong>Xác nhận phiếu</strong>.
                        </div>

                        <button type="submit" class="btn btn-primary fw-semibold w-100"
                                style="padding:0.75rem;">
                            <i class="fa-solid fa-save me-1"></i>
                            Tạo phiếu nhập
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Data sản phẩm từ PHP
    const productData = @json($products->map(fn($p) => [
        'id'    => $p->id,
        'name'  => $p->name,
        'price' => $p->purchase_price,
        'stock' => $p->inventory->quantity_in_stock ?? 0,
    ]));

    let rowIdx = 1;

    function buildOptions() {
        return `<option value="">-- Chọn sản phẩm --</option>`
            + productData.map(p =>
                `<option value="${p.id}" data-price="${p.price}">
                    ${p.name} (Tồn: ${p.stock})
                </option>`
            ).join('');
    }

    function updateTotal() {
        let total = 0;
        document.querySelectorAll('.product-row').forEach(row => {
            const qty   = parseInt(row.querySelector('.qty-input')?.value)   || 0;
            const price = parseInt(row.querySelector('.price-input')?.value) || 0;
            total += qty * price;
        });
        document.getElementById('totalValueDisplay').textContent =
            new Intl.NumberFormat('vi-VN').format(total) + '₫';
    }

    function bindRow(row) {
        row.querySelector('.product-select')?.addEventListener('change', function () {
            const priceInput = this.closest('.product-row').querySelector('.price-input');
            priceInput.value = this.selectedOptions[0]?.dataset.price || '';
            updateTotal();
        });
        row.querySelectorAll('.qty-input, .price-input')
           .forEach(inp => inp.addEventListener('input', updateTotal));
        row.querySelector('.remove-row')?.addEventListener('click', function () {
            this.closest('.product-row').remove();
            updateTotal();
            // Re-enable remove buttons nếu > 1 row
            const rows = document.querySelectorAll('.product-row');
            rows.forEach(r => r.querySelector('.remove-row').disabled = rows.length === 1);
        });
    }

    document.getElementById('addProductBtn').addEventListener('click', function () {
        const row = document.createElement('div');
        row.className = 'product-row row g-2 mb-3 align-items-center';
        row.innerHTML = `
            <div class="col-md-5">
                <select name="products[${rowIdx}][id]"
                        class="form-select product-select" required>
                    ${buildOptions()}
                </select>
            </div>
            <div class="col-md-2">
                <input type="number" name="products[${rowIdx}][quantity]"
                       class="form-control qty-input" min="1" value="1" required>
            </div>
            <div class="col-md-3">
                <input type="number" name="products[${rowIdx}][price]"
                       class="form-control price-input" min="0" step="1000"
                       required placeholder="0">
            </div>
            <div class="col-md-2">
                <button type="button"
                        class="btn btn-outline-danger btn-sm remove-row w-100">
                    <i class="fa-solid fa-trash"></i>
                </button>
            </div>
        `;
        document.getElementById('productRows').appendChild(row);
        bindRow(row);
        rowIdx++;
        updateTotal();
        // Bật nút remove cho tất cả rows
        document.querySelectorAll('.product-row .remove-row')
            .forEach(btn => btn.disabled = false);
    });

    // Bind row đầu tiên
    bindRow(document.querySelector('.product-row'));
</script>
@endpush
@endsection
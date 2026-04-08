{{-- resources/views/admin/products/create.blade.php --}}
@extends('admin.layouts.admin')

@section('title', 'Thêm sản phẩm mới')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2><i class="fas fa-plus-circle"></i> Thêm sản phẩm mới</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
                <li class="breadcrumb-item active">Thêm mới</li>
            </ol>
        </nav>
    </div>

    <form action="{{ route('admin.products.store') }}" method="POST" id="productForm">
        @csrf
        
        <div class="row">
            {{-- Cột trái - Thông tin cơ bản --}}
            <div class="col-md-8">
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle"></i> Thông tin cơ bản</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Tên sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">-- Chọn danh mục --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Mô tả</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Thông số kỹ thuật</label>
                            <textarea name="specifications" class="form-control" rows="4">{{ old('specifications') }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">URL Hình ảnh</label>
                            <input type="text" name="image" class="form-control" 
                                   value="{{ old('image') }}"
                                   placeholder="images/products/example.jpg">
                        </div>
                    </div>
                </div>
            </div>

            {{-- Cột phải - Giá & Kho --}}
            <div class="col-md-4">
                {{-- Giá bán --}}
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-dollar-sign"></i> Giá bán</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Giá nhập <span class="text-danger">*</span></label>
                            <input type="number" name="purchase_price" id="purchase_price"
                                   class="form-control @error('purchase_price') is-invalid @enderror" 
                                   value="{{ old('purchase_price', 0) }}" 
                                   step="1000" min="0" required
                                   onchange="calculateProfit()">
                            @error('purchase_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Giá bán <span class="text-danger">*</span></label>
                            <input type="number" name="selling_price" id="selling_price"
                                   class="form-control @error('selling_price') is-invalid @enderror" 
                                   value="{{ old('selling_price', 0) }}" 
                                   step="1000" min="0" required
                                   onchange="calculateProfit()">
                            @error('selling_price')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Giảm giá (%)</label>
                            <input type="number" name="discount_percent" id="discount_percent"
                                   class="form-control @error('discount_percent') is-invalid @enderror" 
                                   value="{{ old('discount_percent', 0) }}" 
                                   step="0.01" min="0" max="100"
                                   onchange="calculateProfit()">
                            @error('discount_percent')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr>

                        {{-- Hiển thị tính toán --}}
                        <div class="bg-light p-3 rounded">
                            <div class="d-flex justify-content-between mb-2">
                                <span>Giá cuối:</span>
                                <strong class="text-primary" id="final_price">0₫</strong>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span>Lợi nhuận/sp:</span>
                                <strong class="text-success" id="profit_per_unit">0₫</strong>
                            </div>
                            <div class="d-flex justify-content-between">
                                <span>Tỷ suất LN:</span>
                                <strong class="text-info" id="profit_margin">0%</strong>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Kho hàng --}}
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-warehouse"></i> Kho hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label class="form-label">Số lượng nhập kho ban đầu</label>
                            <input type="number" name="initial_stock" 
                                   class="form-control" 
                                   value="{{ old('initial_stock', 0) }}" 
                                   min="0">
                            <small class="text-muted">Để trống hoặc 0 nếu chưa nhập kho</small>
                        </div>
                    </div>
                </div>

                {{-- Trạng thái --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-toggle-on"></i> Trạng thái</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" 
                                   id="status_active" name="status" value="active"
                                   {{ old('status', 'active') === 'active' ? 'checked' : '' }}>
                            <label class="form-check-label" for="status_active">
                                Kích hoạt bán hàng
                            </label>
                        </div>
                        <input type="hidden" name="status" value="inactive">
                    </div>
                </div>

                {{-- Nút Submit --}}
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Lưu sản phẩm
                    </button>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> Hủy
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Tính toán lợi nhuận real-time
function calculateProfit() {
    const purchasePrice = parseFloat(document.getElementById('purchase_price').value) || 0;
    const sellingPrice = parseFloat(document.getElementById('selling_price').value) || 0;
    const discountPercent = parseFloat(document.getElementById('discount_percent').value) || 0;
    
    // Giá cuối sau giảm
    const finalPrice = sellingPrice * (1 - discountPercent / 100);
    
    // Lợi nhuận
    const profitPerUnit = finalPrice - purchasePrice;
    
    // Tỷ suất lợi nhuận
    const profitMargin = finalPrice > 0 ? (profitPerUnit / finalPrice) * 100 : 0;
    
    // Hiển thị
    document.getElementById('final_price').textContent = finalPrice.toLocaleString('vi-VN') + '₫';
    document.getElementById('profit_per_unit').textContent = profitPerUnit.toLocaleString('vi-VN') + '₫';
    document.getElementById('profit_margin').textContent = profitMargin.toFixed(2) + '%';
    
    // Đổi màu nếu lỗ
    if (profitPerUnit < 0) {
        document.getElementById('profit_per_unit').classList.remove('text-success');
        document.getElementById('profit_per_unit').classList.add('text-danger');
    } else {
        document.getElementById('profit_per_unit').classList.remove('text-danger');
        document.getElementById('profit_per_unit').classList.add('text-success');
    }
}

// Xử lý checkbox status
document.getElementById('status_active').addEventListener('change', function() {
    const hiddenInput = document.querySelector('input[name="status"][type="hidden"]');
    hiddenInput.value = this.checked ? 'active' : 'inactive';
});

// Tính toán khi load trang
document.addEventListener('DOMContentLoaded', calculateProfit);
</script>
@endpush
@endsection
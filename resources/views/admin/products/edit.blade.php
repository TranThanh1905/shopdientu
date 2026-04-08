{{-- resources/views/admin/products/edit.blade.php --}}
@extends('admin.layouts.admin')

@section('title', 'Sửa sản phẩm')

@section('content')
<div class="container-fluid">
    <div class="mb-4">
        <h2><i class="fas fa-edit"></i> Sửa sản phẩm: {{ $product->name }}</h2>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
                <li class="breadcrumb-item active">Sửa #{{ $product->id }}</li>
            </ol>
        </nav>
    </div>

    <form action="{{ route('admin.products.update', $product->id) }}" method="POST" id="productForm">
        @csrf
        @method('PUT')
        
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
                            <input type="text" name="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $product->name) }}" required>
                            @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Danh mục <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                <option value="">-- Chọn danh mục --</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" 
                                        {{ old('category_id', $product->category_id) == $cat->id ? 'selected' : '' }}>
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
                            <textarea name="description" class="form-control" rows="4">{{ old('description', $product->description) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Thông số kỹ thuật</label>
                            <textarea name="specifications" class="form-control" rows="4">{{ old('specifications', $product->specifications) }}</textarea>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">URL Hình ảnh</label>
                            <input type="text" name="image" class="form-control" 
                                   value="{{ old('image', $product->image) }}"
                                   placeholder="images/products/example.jpg">
                            
                            @if($product->image)
                                <div class="mt-2">
                                    <img src="{{ asset($product->image) }}" 
                                         alt="{{ $product->name }}"
                                         style="max-width: 200px; border: 1px solid #ddd; border-radius: 8px;">
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- Lịch sử thay đổi giá --}}
                <div class="card mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-history"></i> Thông tin hệ thống</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong>Ngày tạo:</strong><br>
                                    {{ $product->created_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                            <div class="col-md-6">
                                <p class="mb-2">
                                    <strong>Cập nhật lần cuối:</strong><br>
                                    {{ $product->updated_at->format('d/m/Y H:i') }}
                                </p>
                            </div>
                        </div>
                        
                        @if($product->inventory)
                        <hr>
                        <div class="row text-center">
                            <div class="col-md-3">
                                <div class="border rounded p-2">
                                    <h6 class="text-muted mb-1">Tồn kho</h6>
                                    <h4 class="mb-0 text-primary">{{ $product->inventory->quantity_in_stock }}</h4>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-2">
                                    <h6 class="text-muted mb-1">Đã bán</h6>
                                    <h4 class="mb-0 text-success">{{ $product->inventory->quantity_sold }}</h4>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-2">
                                    <h6 class="text-muted mb-1">Hỏng/Lỗi</h6>
                                    <h4 class="mb-0 text-danger">{{ $product->inventory->quantity_damaged }}</h4>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="border rounded p-2">
                                    <h6 class="text-muted mb-1">Trả hàng</h6>
                                    <h4 class="mb-0 text-warning">{{ $product->inventory->quantity_returned }}</h4>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-3">
                            <a href="{{ route('admin.inventory.transactions', $product->id) }}" 
                               class="btn btn-sm btn-outline-primary w-100">
                                <i class="fas fa-list"></i> Xem lịch sử nhập/xuất kho
                            </a>
                        </div>
                        @endif
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
                                   value="{{ old('purchase_price', $product->purchase_price) }}" 
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
                                   value="{{ old('selling_price', $product->selling_price) }}" 
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
                                   value="{{ old('discount_percent', $product->discount_percent) }}" 
                                   step="0.01" min="0" max="100"
                                   onchange="calculateProfit()">
                            @error('discount_percent')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Nhập 0 để tắt giảm giá</small>
                        </div>

                        <hr>

                        {{-- Hiển thị tính toán --}}
                        <div class="bg-light p-3 rounded">
                            <h6 class="mb-3">Tính toán lợi nhuận</h6>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Giá cuối:</span>
                                <strong class="text-primary" id="final_price">{{ number_format($product->final_price) }}₫</strong>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Số tiền giảm:</span>
                                <span class="text-danger" id="discount_amount">{{ number_format($product->discount_amount) }}₫</span>
                            </div>
                            
                            <div class="d-flex justify-content-between mb-2">
                                <span>Lợi nhuận/sp:</span>
                                <strong class="text-success" id="profit_per_unit">{{ number_format($product->profit_per_unit) }}₫</strong>
                            </div>
                            
                            <div class="d-flex justify-content-between">
                                <span>Tỷ suất LN:</span>
                                <strong class="text-info" id="profit_margin">
                                    {{ $product->final_price > 0 ? number_format(($product->profit_per_unit / $product->final_price) * 100, 2) : 0 }}%
                                </strong>
                            </div>
                        </div>

                        {{-- Cảnh báo nếu lỗ --}}
                        <div id="loss_warning" class="alert alert-danger mt-3" style="display: none;">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Cảnh báo:</strong> Giá bán sau giảm thấp hơn giá nhập!
                        </div>
                    </div>
                </div>

                {{-- Kho hàng --}}
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-warehouse"></i> Quản lý kho</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            Để nhập/xuất kho, vui lòng sử dụng chức năng 
                            <a href="{{ route('admin.inventory.index') }}">Quản lý kho</a>
                        </div>
                        
                        @if($product->inventory)
                        <div class="text-center p-3 bg-light rounded">
                            <h6 class="text-muted mb-2">Tồn kho hiện tại</h6>
                            <h2 class="mb-0 
                                {{ $product->inventory->quantity_in_stock == 0 ? 'text-danger' : 
                                   ($product->inventory->quantity_in_stock <= 10 ? 'text-warning' : 'text-success') }}">
                                {{ $product->inventory->quantity_in_stock }}
                            </h2>
                            <small class="text-muted">sản phẩm</small>
                        </div>
                        
                        <div class="d-grid gap-2 mt-3">
                            <a href="{{ route('admin.inventory.stockIn', $product->id) }}" 
                               class="btn btn-success btn-sm">
                                <i class="fas fa-plus"></i> Nhập hàng
                            </a>
                            <button type="button" class="btn btn-danger btn-sm" 
                                    data-bs-toggle="modal" data-bs-target="#damagedModal">
                                <i class="fas fa-exclamation-triangle"></i> Đánh dấu hỏng
                            </button>
                        </div>
                        @endif
                    </div>
                </div>

                {{-- Trạng thái --}}
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-toggle-on"></i> Trạng thái</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" 
                                   id="status_active" name="status_checkbox" value="active"
                                   {{ old('status', $product->status) === 'active' ? 'checked' : '' }}
                                   onchange="updateStatus(this)">
                            <label class="form-check-label" for="status_active">
                                <strong>Kích hoạt bán hàng</strong>
                            </label>
                        </div>
                        <input type="hidden" name="status" id="status_hidden" 
                               value="{{ old('status', $product->status) }}">
                        
                        <div class="alert alert-warning mb-0">
                            <small>
                                <i class="fas fa-info-circle"></i>
                                Tắt trạng thái sẽ ẩn sản phẩm khỏi danh sách bán hàng
                            </small>
                        </div>
                    </div>
                </div>

                {{-- Nút Submit --}}
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save"></i> Cập nhật sản phẩm
                    </button>
                    <a href="{{ route('admin.products.show', $product->id) }}" 
                       class="btn btn-info">
                        <i class="fas fa-eye"></i> Xem chi tiết
                    </a>
                    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Quay lại
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

{{-- Modal đánh dấu hàng hỏng --}}
<div class="modal fade" id="damagedModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.inventory.markDamaged', $product->id) }}" method="POST">
                @csrf
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle"></i> Đánh dấu hàng hỏng
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Số lượng hỏng <span class="text-danger">*</span></label>
                        <input type="number" name="quantity" class="form-control" 
                               min="1" max="{{ $product->inventory->quantity_in_stock ?? 0 }}" required>
                        <small class="text-muted">
                            Tồn kho hiện tại: {{ $product->inventory->quantity_in_stock ?? 0 }}
                        </small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Lý do <span class="text-danger">*</span></label>
                        <textarea name="note" class="form-control" rows="3" required
                                  placeholder="VD: Vỡ màn hình, lỗi nguồn, ..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-check"></i> Xác nhận
                    </button>
                </div>
            </form>
        </div>
    </div>
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
    
    // Số tiền giảm
    const discountAmount = sellingPrice - finalPrice;
    
    // Lợi nhuận
    const profitPerUnit = finalPrice - purchasePrice;
    
    // Tỷ suất lợi nhuận
    const profitMargin = finalPrice > 0 ? (profitPerUnit / finalPrice) * 100 : 0;
    
    // Hiển thị
    document.getElementById('final_price').textContent = finalPrice.toLocaleString('vi-VN') + '₫';
    document.getElementById('discount_amount').textContent = discountAmount.toLocaleString('vi-VN') + '₫';
    document.getElementById('profit_per_unit').textContent = profitPerUnit.toLocaleString('vi-VN') + '₫';
    document.getElementById('profit_margin').textContent = profitMargin.toFixed(2) + '%';
    
    // Đổi màu nếu lỗ
    const profitElement = document.getElementById('profit_per_unit');
    const warningElement = document.getElementById('loss_warning');
    
    if (profitPerUnit < 0) {
        profitElement.classList.remove('text-success');
        profitElement.classList.add('text-danger');
        warningElement.style.display = 'block';
    } else {
        profitElement.classList.remove('text-danger');
        profitElement.classList.add('text-success');
        warningElement.style.display = 'none';
    }
}

// Cập nhật trạng thái
function updateStatus(checkbox) {
    const hiddenInput = document.getElementById('status_hidden');
    hiddenInput.value = checkbox.checked ? 'active' : 'inactive';
}

// Tính toán khi load trang
document.addEventListener('DOMContentLoaded', calculateProfit);
</script>
@endpush
@endsection
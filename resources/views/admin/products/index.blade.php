{{-- resources/views/admin/products/index.blade.php --}}
@extends('admin.layouts.admin')

@section('title', 'Quản lý sản phẩm')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-box"></i> Quản lý sản phẩm</h2>
        <div>
            <a href="{{ route('admin.products.export') }}" class="btn btn-success">
                <i class="fas fa-file-excel"></i> Xuất Excel
            </a>
            <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Thêm sản phẩm
            </a>
        </div>
    </div>

    {{-- Bộ lọc --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.products.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" 
                               placeholder="Tìm kiếm sản phẩm..."
                               value="{{ request('search') }}">
                    </div>
                    
                    <div class="col-md-2">
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
                    
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">Tất cả trạng thái</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>
                                Hoạt động
                            </option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>
                                Ngừng bán
                            </option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <select name="sort_by" class="form-select">
                            <option value="created_at">Mới nhất</option>
                            <option value="name" {{ request('sort_by') == 'name' ? 'selected' : '' }}>
                                Tên A-Z
                            </option>
                            <option value="selling_price" {{ request('sort_by') == 'selling_price' ? 'selected' : '' }}>
                                Giá bán
                            </option>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-search"></i> Tìm kiếm
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Bảng sản phẩm --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="50">ID</th>
                            <th>Sản phẩm</th>
                            <th>Danh mục</th>
                            <th width="120">Giá nhập</th>
                            <th width="120">Giá bán</th>
                            <th width="80">Giảm giá</th>
                            <th width="120">Giá cuối</th>
                            <th width="100">Lợi nhuận</th>
                            <th width="80">Tồn kho</th>
                            <th width="80">Đã bán</th>
                            <th width="100">Trạng thái</th>
                            <th width="150">Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($products as $product)
                        <tr>
                            <td>{{ $product->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($product->image)
                                        <img src="{{ asset($product->image) }}" 
                                             alt="{{ $product->name }}"
                                             style="width: 50px; height: 50px; object-fit: cover; margin-right: 10px;">
                                    @endif
                                    <strong>{{ $product->name }}</strong>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info">{{ $product->category->name }}</span>
                            </td>
                            <td class="text-end">
                                <span class="text-muted">{{ number_format($product->purchase_price) }}₫</span>
                            </td>
                            <td class="text-end">
                                <strong>{{ number_format($product->selling_price) }}₫</strong>
                            </td>
                            <td class="text-center">
                                @if($product->discount_percent > 0)
                                    <span class="badge bg-danger">-{{ $product->discount_percent }}%</span>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <strong class="text-primary">{{ number_format($product->final_price) }}₫</strong>
                            </td>
                            <td class="text-end">
                                <span class="text-success">
                                    +{{ number_format($product->profit_per_unit) }}₫
                                </span>
                            </td>
                            <td class="text-center">
                                @php
                                    $stock = $product->stock;
                                    $badgeClass = $stock == 0 ? 'danger' : ($stock <= 10 ? 'warning' : 'success');
                                @endphp
                                <span class="badge bg-{{ $badgeClass }}">{{ $stock }}</span>
                            </td>
                            <td class="text-center">
                                {{ $product->inventory->quantity_sold ?? 0 }}
                            </td>
                            <td>
                                @if($product->status === 'active')
                                    <span class="badge bg-success">Hoạt động</span>
                                @else
                                    <span class="badge bg-secondary">Ngừng bán</span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.products.show', $product->id) }}" 
                                       class="btn btn-info" title="Chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.products.edit', $product->id) }}" 
                                       class="btn btn-warning" title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-danger" 
                                            onclick="confirmDelete({{ $product->id }})" title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                                
                                <form id="delete-form-{{ $product->id }}" 
                                      action="{{ route('admin.products.destroy', $product->id) }}" 
                                      method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="12" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Chưa có sản phẩm nào</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $products->links() }}
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(id) {
    if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush
@endsection
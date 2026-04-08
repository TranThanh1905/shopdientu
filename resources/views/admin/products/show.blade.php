{{-- resources/views/admin/products/show.blade.php --}}
@extends('admin.layouts.admin')

@section('title', 'Chi tiết sản phẩm')

@section('content')
<div class="container-fluid">
    {{-- Header --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h2><i class="fas fa-box-open"></i> Chi tiết sản phẩm #{{ $product->id }}</h2>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.products.index') }}">Sản phẩm</a></li>
                    <li class="breadcrumb-item active">Chi tiết</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> Sửa sản phẩm
            </a>
            <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Quay lại
            </a>
        </div>
    </div>

    <div class="row">
        {{-- Cột trái - Thông tin sản phẩm --}}
        <div class="col-md-8">
            {{-- Thông tin cơ bản --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> Thông tin sản phẩm</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 text-center">
                            @if($product->image)
                                <img src="{{ asset($product->image) }}" 
                                     alt="{{ $product->name }}"
                                     class="img-fluid rounded border"
                                     style="max-height: 300px;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center"
                                     style="height: 300px;">
                                    <i class="fas fa-image fa-5x text-muted"></i>
                                </div>
                            @endif
                        </div>
                        
                        <div class="col-md-8">
                            <h3 class="mb-3">{{ $product->name }}</h3>
                            
                            <table class="table table-borderless">
                                <tr>
                                    <td width="150"><strong>Danh mục:</strong></td>
                                    <td>
                                        <span class="badge bg-info">{{ $product->category->name }}</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Trạng thái:</strong></td>
                                    <td>
                                        @if($product->status === 'active')
                                            <span class="badge bg-success">Đang bán</span>
                                        @else
                                            <span class="badge bg-secondary">Ngừng bán</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Ngày tạo:</strong></td>
                                    <td>{{ $product->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Cập nhật:</strong></td>
                                    <td>{{ $product->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </table>

                            @if($product->description)
                            <div class="mt-3">
                                <h6><strong>Mô tả:</strong></h6>
                                <p class="text-muted">{{ $product->description }}</p>
                            </div>
                            @endif

                            @if($product->specifications)
                            <div class="mt-3">
                                <h6><strong>Thông số kỹ thuật:</strong></h6>
                                <p class="text-muted">{{ $product->specifications }}</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- Thống kê bán hàng --}}
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> Thống kê bán hàng</h5>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-md-3">
                            <div class="border rounded p-3 mb-3">
                                <i class="fas fa-shopping-cart fa-2x text-primary mb-2"></i>
                                <h6 class="text-muted mb-1">Đã bán</h6>
                                <h3 class="mb-0 text-primary">{{ $stats['total_sold'] }}</h3>
                                <small class="text-muted">sản phẩm</small>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="border rounded p-3 mb-3">
                                <i class="fas fa-warehouse fa-2x text-warning mb-2"></i>
                                <h6 class="text-muted mb-1">Tồn kho</h6>
                                <h3 class="mb-0 text-warning">{{ $product->stock }}</h3>
                                <small class="text-muted">sản phẩm</small>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="border rounded p-3 mb-3">
                                <i class="fas fa-dollar-sign fa-2x text-success mb-2"></i>
                                <h6 class="text-muted mb-1">Doanh thu</h6>
                                <h3 class="mb-0 text-success">{{ number_format($stats['total_revenue']) }}</h3>
                                <small class="text-muted">VNĐ</small>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <div class="border rounded p-3 mb-3">
                                <i class="fas fa-chart-line fa-2x text-info mb-2"></i>
                                <h6 class="text-muted mb-1">Lợi nhuận</h6>
                                <h3 class="mb-0 text-info">{{ number_format($stats['total_profit']) }}</h3>
                                <small class="text-muted">VNĐ</small>
                            </div>
                        </div>
                    </div>

                    @if($product->inventory)
                    <hr>
                    <div class="row text-center">
                        <div class="col-md-4">
                            <h6 class="text-muted">Hàng hỏng/lỗi</h6>
                            <h4 class="text-danger">{{ $product->inventory->quantity_damaged }}</h4>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted">Trả hàng</h6>
                            <h4 class="text-warning">{{ $product->inventory->quantity_returned }}</h4>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-muted">Tỷ lệ hỏng</h6>
                            <h4 class="text-secondary">
                                @php
                                    $total = $product->inventory->quantity_sold + $product->inventory->quantity_damaged;
                                    $damageRate = $total > 0 ? ($product->inventory->quantity_damaged / $total) * 100 : 0;
                                @endphp
                                {{ number_format($damageRate, 2) }}%
                            </h4>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Lịch sử giao dịch kho --}}
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-history"></i> Lịch sử nhập/xuất kho</h5>
                    <a href="{{ route('admin.inventory.transactions', $product->id) }}" 
                       class="btn btn-light btn-sm">
                        Xem tất cả
                    </a>
                </div>
                <div class="card-body">
                    @if($product->inventoryTransactions->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Thời gian</th>
                                        <th>Loại</th>
                                        <th>Số lượng</th>
                                        <th>Đơn giá</th>
                                        <th>Người thực hiện</th>
                                        <th>Ghi chú</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($product->inventoryTransactions->take(5) as $trans)
                                    <tr>
                                        <td>{{ $trans->created_at->format('d/m/Y H:i') }}</td>
                                        <td>
                                            @php
                                                $badges = [
                                                    'in' => 'success',
                                                    'out' => 'primary',
                                                    'damaged' => 'danger',
                                                    'returned' => 'warning'
                                                ];
                                                $labels = \App\Models\InventoryTransaction::getTypeLabels();
                                            @endphp
                                            <span class="badge bg-{{ $badges[$trans->type] ?? 'secondary' }}">
                                                {{ $labels[$trans->type] ?? $trans->type }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($trans->type === 'in' || $trans->type === 'returned')
                                                <span class="text-success">+{{ $trans->quantity }}</span>
                                            @else
                                                <span class="text-danger">-{{ $trans->quantity }}</span>
                                            @endif
                                        </td>
                                        <td>{{ number_format($trans->unit_price) }}₫</td>
                                        <td>{{ $trans->user->name ?? 'Hệ thống' }}</td>
                                        <td>
                                            <small class="text-muted">{{ $trans->note ?? '-' }}</small>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-4">
                            <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                            <p class="text-muted">Chưa có giao dịch nào</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Cột phải - Giá & Hành động --}}
        <div class="col-md-4">
            {{-- Giá bán --}}
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-tags"></i> Thông tin giá</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless mb-0">
                        <tr>
                            <td><strong>Giá nhập:</strong></td>
                            <td class="text-end">
                                <span class="text-muted">{{ number_format($product->purchase_price) }}₫</span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Giá bán:</strong></td>
                            <td class="text-end">
                                <strong>{{ number_format($product->selling_price) }}₫</strong>
                            </td>
                        </tr>
                        @if($product->discount_percent > 0)
                        <tr>
                            <td><strong>Giảm giá:</strong></td>
                            <td class="text-end">
                                <span class="badge bg-danger">-{{ $product->discount_percent }}%</span>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Số tiền giảm:</strong></td>
                            <td class="text-end text-danger">
                                -{{ number_format($product->discount_amount) }}₫
                            </td>
                        </tr>
                        @endif
                        <tr class="border-top">
                            <td><strong>Giá cuối:</strong></td>
                            <td class="text-end">
                                <h4 class="mb-0 text-primary">{{ number_format($product->final_price) }}₫</h4>
                            </td>
                        </tr>
                        <tr class="border-top">
                            <td><strong>Lợi nhuận/sp:</strong></td>
                            <td class="text-end">
                                <h5 class="mb-0 {{ $product->profit_per_unit >= 0 ? 'text-success' : 'text-danger' }}">
                                    {{ $product->profit_per_unit >= 0 ? '+' : '' }}{{ number_format($product->profit_per_unit) }}₫
                                </h5>
                            </td>
                        </tr>
                        <tr>
                            <td><strong>Tỷ suất LN:</strong></td>
                            <td class="text-end">
                                @php
                                    $margin = $product->final_price > 0 ? ($product->profit_per_unit / $product->final_price) * 100 : 0;
                                @endphp
                                <strong class="text-info">{{ number_format($margin, 2) }}%</strong>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            {{-- Hành động nhanh --}}
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-bolt"></i> Hành động nhanh</h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('admin.products.edit', $product->id) }}" 
                           class="btn btn-warning">
                            <i class="fas fa-edit"></i> Sửa sản phẩm
                        </a>
                        
                        <a href="{{ route('admin.inventory.stockIn', $product->id) }}" 
                           class="btn btn-success">
                            <i class="fas fa-plus"></i> Nhập hàng vào kho
                        </a>
                        
                        <button type="button" class="btn btn-danger" 
                                data-bs-toggle="modal" data-bs-target="#damagedModal">
                            <i class="fas fa-exclamation-triangle"></i> Đánh dấu hỏng
                        </button>
                        
                        <hr>
                        
                        <button type="button" class="btn btn-outline-danger" 
                                onclick="confirmDelete()">
                            <i class="fas fa-trash"></i> Xóa sản phẩm
                        </button>
                    </div>
                </div>
            </div>

            {{-- Link liên quan --}}
            <div class="card">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-link"></i> Liên kết</h5>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <a href="{{ route('products.show', $product->id) }}" 
                           class="list-group-item list-group-item-action" target="_blank">
                            <i class="fas fa-external-link-alt"></i> Xem trên website
                        </a>
                        <a href="{{ route('admin.inventory.transactions', $product->id) }}" 
                           class="list-group-item list-group-item-action">
                            <i class="fas fa-list"></i> Lịch sử kho đầy đủ
                        </a>
                        <a href="{{ route('admin.orders.index', ['search' => $product->name]) }}" 
                           class="list-group-item list-group-item-action">
                            <i class="fas fa-shopping-cart"></i> Đơn hàng có sản phẩm này
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
                               min="1" max="{{ $product->stock }}" required>
                        <small class="text-muted">Tồn kho hiện tại: {{ $product->stock }}</small>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Lý do <span class="text-danger">*</span></label>
                        <textarea name="note" class="form-control" rows="3" required
                                  placeholder="VD: Vỡ màn hình, lỗi nguồn, hết hạn sử dụng..."></textarea>
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

{{-- Form xóa sản phẩm (ẩn) --}}
<form id="delete-form" action="{{ route('admin.products.destroy', $product->id) }}" 
      method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script>
function confirmDelete() {
    if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này?\n\nLưu ý: Không thể xóa nếu sản phẩm đã có trong đơn hàng!')) {
        document.getElementById('delete-form').submit();
    }
}
</script>
@endpush
@endsection
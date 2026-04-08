{{-- resources/views/admin/orders/index.blade.php --}}
@extends('admin.layouts.admin')

@section('title', 'Quản lý đơn hàng - Admin')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fa-solid fa-cart-shopping"></i> Quản lý đơn hàng</h2>
    <div>
        <a href="{{ route('admin.orders.export', request()->query()) }}" class="btn btn-success">
            <i class="fas fa-file-excel"></i> Xuất Excel
        </a>
    </div>
</div>

{{-- Thống kê nhanh --}}
<div class="row mb-4">
    <div class="col-md-3">
        <div class="stats-card stats-card--primary">
            <div class="stats-card__content">
                <div>
                    <div class="stats-card__value">{{ $stats['total'] }}</div>
                    <div class="stats-card__label">Tổng đơn</div>
                </div>
                <div class="stats-card__icon">
                    <i class="fa-solid fa-clipboard-list"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase mb-1">Chờ xử lý</div>
                        <div class="h5 mb-0 font-weight-bold text-warning">{{ $stats['pending'] }}</div>
                    </div>
                    <div>
                        <i class="fa-solid fa-clock fa-2x text-muted"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase mb-1">Đang giao</div>
                        <div class="h5 mb-0 font-weight-bold text-info">{{ $stats['shipping'] }}</div>
                    </div>
                    <div>
                        <i class="fas fa-shipping-fast fa-2x text-muted"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card shadow border-0">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-muted small text-uppercase mb-1">Hoàn thành</div>
                        <div class="h5 mb-0 font-weight-bold text-success">{{ $stats['completed'] }}</div>
                    </div>
                    <div>
                        <i class="fas fa-check-circle fa-2x text-muted"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Bộ lọc --}}
<div class="card shadow border-0 mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.orders.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" 
                           placeholder="Mã đơn, Tên, SĐT, Email..."
                           value="{{ request('search') }}">
                </div>
                
                <div class="col-md-2">
                    <select name="status" class="form-control">
                        <option value="">Tất cả trạng thái</option>
                        @foreach(\App\Models\Order::getStatusLabels() as $key => $label)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <div class="col-md-2">
                    <input type="date" name="date_from" class="form-control" 
                           value="{{ request('date_from') }}" placeholder="Từ ngày">
                </div>
                
                <div class="col-md-2">
                    <input type="date" name="date_to" class="form-control" 
                           value="{{ request('date_to') }}" placeholder="Đến ngày">
                </div>
                
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Tìm kiếm
                    </button>
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
                        <i class="fas fa-redo"></i> Reset
                    </a>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Bảng đơn hàng --}}
<div class="card shadow border-0">
    <div class="card-body">
        @if($orders->isEmpty())
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i> Chưa có đơn hàng nào
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Mã đơn</th>
                            <th>Khách hàng</th>
                            <th>Liên hệ</th>
                            <th>SL sản phẩm</th>
                            <th>Tổng tiền</th>
                            <th>Thanh toán</th>
                            <th>Trạng thái</th>
                            <th>Ngày đặt</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                        <tr>
                            <td>
                                <strong>{{ $order->order_code }}</strong>
                                @if($order->customer_code)
                                    <br><small class="text-muted">{{ $order->customer_code }}</small>
                                @endif
                            </td>
                            <td>
                                <div>
                                    <strong>{{ $order->fullname }}</strong>
                                    @if($order->user)
                                        <br><small class="text-info">
                                            <i class="fas fa-user"></i> Member
                                        </small>
                                    @else
                                        <br><small class="text-muted">
                                            <i class="fas fa-user-slash"></i> Guest
                                        </small>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <small>
                                    <i class="fas fa-phone"></i> {{ $order->phone }}<br>
                                    <i class="fas fa-envelope"></i> {{ $order->email }}
                                </small>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-secondary">{{ $order->orderDetails->sum('quantity') }}</span>
                            </td>
                            <td>
                                <strong class="text-danger">{{ number_format($order->total_amount) }}₫</strong>
                                @if($order->discount_amount > 0)
                                    <br><small class="text-danger">-{{ number_format($order->discount_amount) }}₫</small>
                                @endif
                            </td>
                            <td>
                                <strong class="text-primary">{{ number_format($order->final_amount) }}₫</strong>
                            </td>
                            <td>
                                <span class="badge bg-{{ $order->status_color }}">
                                    {{ $order->status_label }}
                                </span>
                            </td>
                            <td><small>{{ $order->created_at->format('d/m/Y H:i') }}</small></td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.orders.show', $order->id) }}" 
                                       class="btn btn-sm btn-primary" title="Chi tiết">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('admin.orders.print', $order->id) }}" 
                                       class="btn btn-sm btn-success" title="In hóa đơn">
                                        <i class="fas fa-print"></i>
                                    </a>
                                    @if(in_array($order->status, ['pending', 'cancelled']))
                                    <button type="button" class="btn btn-sm btn-danger" 
                                            onclick="confirmDelete({{ $order->id }})" title="Xóa">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </div>
                                
                                @if(in_array($order->status, ['pending', 'cancelled']))
                                <form id="delete-form-{{ $order->id }}" 
                                      action="{{ route('admin.orders.destroy', $order->id) }}" 
                                      method="POST" style="display: none;">
                                    @csrf
                                    @method('DELETE')
                                </form>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            
            <div class="mt-3">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
function confirmDelete(id) {
    if (confirm('Bạn có chắc chắn muốn xóa đơn hàng này?')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush
@endsection
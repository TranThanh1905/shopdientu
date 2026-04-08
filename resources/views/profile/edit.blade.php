{{-- resources/views/profile/edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Thông tin cá nhân - ElectroShop')

@section('content')
<div class="container my-5">
    <nav class="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb__item"><a href="{{ route('home') }}">Trang chủ</a></li>
            <li class="breadcrumb__item breadcrumb__item--active">Thông tin cá nhân</li>
        </ol>
    </nav>

    <div class="row g-4">
        <!-- Sidebar -->
        <div class="col-lg-3">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-user-circle fa-5x text-primary"></i>
                    </div>
                    <h5 class="card-title">{{ $user->name }}</h5>
                    <p class="text-muted small mb-2">{{ $user->email }}</p>
                    
                    @if($user->role === 'user' && $user->customer_code)
                        <span class="badge bg-info">{{ $user->customer_code }}</span>
                    @endif
                    
                    @if($user->role === 'admin')
                        <span class="badge bg-danger">Admin</span>
                    @endif
                </div>
                <div class="list-group list-group-flush">
                    <a href="#profile-info" class="list-group-item list-group-item-action smooth-scroll">
                        <i class="fas fa-user me-2"></i> Thông tin cá nhân
                    </a>
                    @if($user->role === 'user')
                    <a href="#order-history" class="list-group-item list-group-item-action smooth-scroll">
                        <i class="fas fa-shopping-bag me-2"></i> Đơn hàng của tôi
                    </a>
                    @endif
                    <a href="#change-password" class="list-group-item list-group-item-action smooth-scroll">
                        <i class="fas fa-key me-2"></i> Đổi mật khẩu
                    </a>
                    <a href="#delete-account" class="list-group-item list-group-item-action text-danger smooth-scroll">
                        <i class="fas fa-trash me-2"></i> Xóa tài khoản
                    </a>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-lg-9">
            <!-- Thống kê -->
            @if($user->role === 'user')
                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card text-center shadow-sm">
                            <div class="card-body">
                                <i class="fas fa-shopping-cart fa-2x text-primary mb-2"></i>
                                <h3 class="mb-0">{{ $stats['total_orders'] }}</h3>
                                <small class="text-muted">Tổng đơn hàng</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center shadow-sm">
                            <div class="card-body">
                                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                                <h3 class="mb-0">{{ $stats['pending_orders'] }}</h3>
                                <small class="text-muted">Đang xử lý</small>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card text-center shadow-sm">
                            <div class="card-body">
                                <i class="fas fa-money-bill-wave fa-2x text-success mb-2"></i>
                                <h3 class="mb-0">{{ number_format($stats['total_spent']) }}₫</h3>
                                <small class="text-muted">Tổng chi tiêu</small>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Profile Information -->
            <div class="card shadow-sm mb-4" id="profile-info">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i> Thông tin cá nhân</h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            <!-- Order History -->
            @if($user->role === 'user')
                <div class="card shadow-sm mb-4" id="order-history">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-shopping-bag me-2"></i> Đơn hàng gần đây</h5>
                    </div>
                    <div class="card-body">
                        @if($recentOrders->isEmpty())
                            <div class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">Bạn chưa có đơn hàng nào</p>
                                <a href="{{ route('products.index') }}" class="btn btn-primary">
                                    <i class="fas fa-shopping-cart"></i> Mua sắm ngay
                                </a>
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>Mã đơn</th>
                                            <th>Ngày đặt</th>
                                            <th>Tổng tiền</th>
                                            <th>Trạng thái</th>
                                            <th>Thao tác</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($recentOrders as $order)
                                            <tr>
                                                <td><strong>{{ $order->order_code }}</strong></td>
                                                <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                                                <td><strong class="text-primary">{{ number_format($order->final_amount) }}₫</strong></td>
                                                <td>
                                                    <span class="badge bg-{{ $order->status_color }}">
                                                        {{ $order->status_label }}
                                                    </span>
                                                </td>
                                                <td>
                                                    <a href="{{ route('orders.show', $order->id) }}" 
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="fas fa-eye"></i> Chi tiết
                                                    </a>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-3">
                                <a href="{{ route('orders.index') }}" class="btn btn-outline-primary">
                                    Xem tất cả đơn hàng <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Change Password -->
            <div class="card shadow-sm mb-4" id="change-password">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-key me-2"></i> Đổi mật khẩu</h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            <!-- Delete Account -->
            <div class="card shadow-sm border-danger mb-4" id="delete-account">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle me-2"></i> Xóa tài khoản</h5>
                </div>
                <div class="card-body">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .list-group-item-action:hover {
        background-color: #f8f9fa;
    }
    
    .card {
        border-radius: 0.5rem;
    }
    
    .card-header {
        border-radius: 0.5rem 0.5rem 0 0 !important;
    }

    .smooth-scroll {
        cursor: pointer;
    }
</style>
@endpush

@push('scripts')
<script>
    // Smooth scroll to sections
    document.querySelectorAll('.smooth-scroll').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const targetId = this.getAttribute('href');
            const target = document.querySelector(targetId);
            if (target) {
                const offset = 80; // Offset for navbar
                const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - offset;
                window.scrollTo({
                    top: targetPosition,
                    behavior: 'smooth'
                });
            }
        });
    });
</script>
@endpush
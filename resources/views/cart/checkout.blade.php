{{-- resources/views/cart/checkout.blade.php --}}
@extends('layouts.app')

@section('title', 'Thanh toán')

@section('content')
<div class="container my-5">
    <h2 class="mb-4"><i class="fas fa-credit-card"></i> Thanh toán</h2>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible">
            <button class="btn-close" onclick="this.parentElement.remove()">&times;</button>
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('order.place') }}">
        @csrf
        
        <div class="d-flex" style="gap: 1rem; align-items: flex-start;">
            <!-- Customer Info -->
            <div style="flex: 2;">
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Thông tin giao hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label class="form-label form-label--required">Họ và tên</label>
                            <input type="text" name="fullname" 
                                   class="form-control @error('fullname') is-invalid @enderror" 
                                   value="{{ old('fullname', Auth::user()->name ?? '') }}" required>
                            @error('fullname')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label form-label--required">Email</label>
                                    <input type="email" name="email" 
                                           class="form-control @error('email') is-invalid @enderror" 
                                           value="{{ old('email', Auth::user()->email ?? '') }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="form-label form-label--required">Số điện thoại</label>
                                    <input type="tel" name="phone" 
                                           class="form-control @error('phone') is-invalid @enderror" 
                                           value="{{ old('phone') }}" 
                                           placeholder="Điền điện thoại liên hệ"
                                           required>
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label form-label--required">Địa chỉ giao hàng</label>
                            <textarea name="address" class="form-control @error('address') is-invalid @enderror" 
                                      rows="3" required>{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group mb-0">
                            <label class="form-label">Ghi chú</label>
                            <textarea name="note" class="form-control" rows="3" 
                                      placeholder="Ghi chú về đơn hàng...">{{ old('note') }}</textarea>
                        </div>

                        @guest
                        <div class="alert alert-info mt-3">
                            <i class="fas fa-info-circle"></i>
                            <a href="{{ route('login') }}">Đăng nhập</a> để lưu thông tin và theo dõi đơn hàng dễ dàng hơn!
                        </div>
                        @endguest
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Phương thức thanh toán</h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment" 
                                   id="cod" value="cod" checked>
                            <label class="form-check-label" for="cod">
                                <i class="fas fa-money-bill-wave text-success"></i>
                                <strong>Thanh toán khi nhận hàng (COD)</strong>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment" 
                                   id="bank" value="bank">
                            <label class="form-check-label" for="bank">
                                <i class="fas fa-university text-primary"></i>
                                Chuyển khoản ngân hàng
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div style="flex: 1;">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Đơn hàng của bạn</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <tbody>
                                    @foreach($cart as $item)
                                    <tr>
                                        <td>
                                            <strong>{{ $item['name'] }}</strong>
                                            <br><small class="text-muted">x{{ $item['quantity'] }}</small>
                                        </td>
                                        <td class="text-end">
                                            {{ number_format($item['subtotal']) }}₫
                                            @if($item['discount_amount'] > 0)
                                                <br><small class="text-danger">-{{ number_format($item['discount_amount']) }}₫</small>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="border-top">
                                    <tr>
                                        <td><strong>Tạm tính:</strong></td>
                                        <td class="text-end">{{ number_format($total) }}₫</td>
                                    </tr>
                                    @if($discount > 0)
                                    <tr>
                                        <td><strong>Giảm giá:</strong></td>
                                        <td class="text-end text-danger">-{{ number_format($discount) }}₫</td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td><strong>Phí vận chuyển:</strong></td>
                                        <td class="text-end">
                                            @if($final_total >= 500000)
                                                <span class="text-success">Miễn phí</span>
                                            @else
                                                30,000₫
                                            @endif
                                        </td>
                                    </tr>
                                    <tr class="table-primary">
                                        <td><strong>Tổng cộng:</strong></td>
                                        <td class="text-end">
                                            <h4 class="mb-0">{{ number_format($final_total >= 500000 ? $final_total : $final_total + 30000) }}₫</h4>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            <i class="fas fa-check"></i> Đặt hàng
                        </button>

                        <a href="{{ route('cart.index') }}" class="btn btn-outline-secondary btn-block mt-2">
                            <i class="fas fa-arrow-left"></i> Quay lại giỏ hàng
                        </a>
                    </div>
                </div>

                <div class="alert alert-info mt-3">
                    <small>
                        <i class="fas fa-info-circle"></i>
                        Bằng việc đặt hàng, bạn đã đồng ý với 
                        <a href="#">điều khoản sử dụng</a> của chúng tôi
                    </small>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection
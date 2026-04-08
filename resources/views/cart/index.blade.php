{{-- resources/views/cart/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Giỏ hàng - ElectroShop')

@section('content')
<div class="cart">
    <div class="container">
        <h2 class="mb-4"><i class="fas fa-shopping-cart"></i> Giỏ hàng của bạn</h2>

        @if(!empty($warnings))
            @foreach($warnings as $warning)
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i> {{ $warning }}
                </div>
            @endforeach
        @endif

        @if(empty($cart))
            <!-- Empty Cart -->
            <div class="cart__empty">
                <i class="fas fa-shopping-cart cart__empty-icon"></i>
                <h4 class="cart__empty-title">Giỏ hàng trống</h4>
                <p class="cart__empty-text">Hãy thêm sản phẩm vào giỏ hàng để tiếp tục mua sắm</p>
                <a href="{{ route('products.index') }}" class="btn btn-primary">
                    <i class="fas fa-shopping-bag"></i> Tiếp tục mua sắm
                </a>
            </div>
        @else
            <div class="d-flex" style="gap: 1rem; align-items: flex-start;">
                <!-- Cart Items -->
                <div style="flex: 3;">
                    <div class="cart__table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Sản phẩm</th>
                                    <th>Giá</th>
                                    <th>Giảm</th>
                                    <th>Đơn giá</th>
                                    <th>Số lượng</th>
                                    <th>Tạm tính</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($cart as $item)
                                <tr class="{{ $item['out_of_stock'] ? 'table-warning' : '' }}">
                                    <td>
                                        <div class="cart__item-info">
                                            @if($item['image'])
                                                <img src="{{ asset($item['image']) }}" 
                                                     class="cart__item-image" 
                                                     alt="{{ $item['name'] }}">
                                            @endif
                                            <div class="cart__item-details">
                                                <h6 class="cart__item-name">{{ $item['name'] }}</h6>
                                                @if($item['out_of_stock'])
                                                    <small class="text-danger">
                                                        <i class="fas fa-exclamation-triangle"></i> 
                                                        Chỉ còn {{ $item['stock'] }} sản phẩm
                                                    </small>
                                                @endif
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="cart__item-price">{{ number_format($item['selling_price']) }}₫</span>
                                    </td>
                                    <td>
                                        @if($item['discount_percent'] > 0)
                                            <span class="cart__item-discount">-{{ $item['discount_percent'] }}%</span>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <strong class="cart__item-price">{{ number_format($item['final_price']) }}₫</strong>
                                    </td>
                                    <td>
                                        <div class="cart__item-quantity">
                                            <form method="POST" action="{{ route('cart.update') }}">
                                                @csrf
                                                <input type="hidden" name="product_id" value="{{ $item['id'] }}">
                                                <input type="number" 
                                                       name="quantity" 
                                                       value="{{ $item['quantity'] }}" 
                                                       min="0" 
                                                       max="{{ $item['stock'] }}"
                                                       onchange="this.form.submit()">
                                            </form>
                                        </div>
                                    </td>
                                    <td>
                                        <strong class="cart__item-price">{{ number_format($item['subtotal']) }}₫</strong>
                                        @if($item['discount_amount'] > 0)
                                            <br><small class="cart__item-old-price">
                                                {{ number_format($item['selling_price'] * $item['quantity']) }}₫
                                            </small>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('cart.remove', $item['id']) }}" 
                                           class="cart__item-remove"
                                           onclick="return confirm('Xóa sản phẩm này?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="mt-3 d-flex justify-content-between">
                        <a href="{{ route('products.index') }}" class="btn btn-outline-primary">
                            <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                        </a>
                        <form method="POST" action="{{ route('cart.clear') }}" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-outline-danger"
                                    onclick="return confirm('Xóa toàn bộ giỏ hàng?')">
                                <i class="fas fa-trash"></i> Xóa giỏ hàng
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Cart Summary -->
                <div style="flex: 1;">
                    <div class="cart__summary">
                        <h5 class="cart__summary-title">Tổng đơn hàng</h5>
                        
                        <div class="cart__summary-row">
                            <span class="cart__summary-label">Tạm tính:</span>
                            <strong class="cart__summary-value">{{ number_format($total) }}₫</strong>
                        </div>
                        
                        @if($discount > 0)
                        <div class="cart__summary-row">
                            <span class="cart__summary-label">Giảm giá:</span>
                            <strong class="cart__summary-value cart__summary-value--discount">-{{ number_format($discount) }}₫</strong>
                        </div>
                        @endif
                        
                        <div class="cart__summary-row cart__summary-row--total">
                            <h5 class="cart__summary-label">Tổng cộng:</h5>
                            <h5 class="cart__summary-value">{{ number_format($final_total) }}₫</h5>
                        </div>

                        @php
                            $hasOutOfStock = collect($cart)->contains('out_of_stock', true);
                        @endphp

                        @if($hasOutOfStock)
                            <div class="alert alert-warning mb-3">
                                <small>
                                    <i class="fas fa-exclamation-triangle"></i>
                                    Vui lòng điều chỉnh số lượng sản phẩm trước khi thanh toán
                                </small>
                            </div>
                            <button class="cart__summary-checkout" disabled style="opacity: 0.6; cursor: not-allowed;">
                                <i class="fas fa-lock"></i> Thanh toán
                            </button>
                        @else
                            <a href="{{ route('checkout') }}" class="cart__summary-checkout">
                                <i class="fas fa-credit-card"></i> Thanh toán
                            </a>
                        @endif
                    </div>

                    <div class="card shadow mt-3">
                        <div class="card-body">
                            <h6><i class="fas fa-info-circle"></i> Thông tin</h6>
                            <ul class="list-unstyled small mb-0">
                                <li><i class="fas fa-check text-success"></i> Miễn phí vận chuyển đơn từ 500k</li>
                                <li><i class="fas fa-check text-success"></i> Đổi trả trong 7 ngày</li>
                                <li><i class="fas fa-check text-success"></i> Bảo hành chính hãng</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
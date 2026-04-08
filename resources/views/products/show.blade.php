@extends('layouts.app')

@section('title', $product->name . ' - ElectroShop')

@section('content')
<div class="container my-5">
    <!-- Breadcrumb -->
    <nav class="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb__item"><a href="{{ route('home') }}">Trang chủ</a></li>
            <li class="breadcrumb__item"><a href="{{ route('products.index') }}">Sản phẩm</a></li>
            <li class="breadcrumb__item breadcrumb__item--active">{{ $product->name }}</li>
        </ol>
    </nav>

    <!-- Product Detail -->
    <div class="d-flex" style="gap: 3rem;">
        <!-- Product Image -->
        <div style="flex: 1;">
            <div class="card border-0 shadow">
                <img src="{{ asset($product->image) }}" 
                     class="card-img-top" 
                     alt="{{ $product->name }}">
            </div>
        </div>

        <!-- Product Info -->
        <div style="flex: 1;">
            <div class="card border-0 shadow p-4">
                <span class="badge badge-primary mb-2" style="align-self: flex-start;">
                    {{ $product->category->name }}
                </span>
                
                <h2 class="mb-3">{{ $product->name }}</h2>
                
                <!-- Price -->
                <div class="mb-4">
                    @if($product->sale_price)
                        <h4 class="text-muted text-decoration-line-through mb-2">
                            {{ number_format($product->selling_price) }}₫
                        </h4>
                        <h3 class="text-danger fw-bold mb-0">
                            {{ number_format($product->selling_price) }}₫
                        </h3>
                        <span class="badge badge-danger mt-2">
                            Giảm {{ $product->discount_percent }}%
                        </span>
                    @else
                        <h3 class="text-danger fw-bold mb-0">
                            {{ number_format($product->selling_price) }}₫
                        </h3>
                    @endif
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <h5>Mô tả:</h5>
                    <p class="text-muted">{{ $product->description }}</p>
                </div>

                <!-- Specifications -->
                @if($product->specifications)
                    <div class="mb-4">
                        <h5>Thông số kỹ thuật:</h5>
                        <p class="text-muted">{{ $product->specifications }}</p>
                    </div>
                @endif

                <!-- Stock -->
                <div class="mb-4">
                    <h5>Tình trạng:</h5>
                    @if($product->stock > 0)
                        <span class="badge badge-success">
                            Còn hàng ({{ $product->stock }} sản phẩm)
                        </span>
                    @else
                        <span class="badge badge-danger">Hết hàng</span>
                    @endif
                </div>

                <!-- Add to Cart -->
                @if($product->stock > 0)
                    <form method="POST" action="{{ route('cart.add') }}" class="add-to-cart-form">
                        @csrf
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        
                        <div class="mb-3">
                            <label class="form-label">Số lượng:</label>
                            <input type="number" 
                                   name="quantity" 
                                   class="form-control" 
                                   value="1" 
                                   min="1" 
                                   max="{{ $product->stock }}">
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-lg btn-block">
                            <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                        </button>
                    </form>
                @endif
            </div>
        </div>
    </div>

    <!-- Related Products -->
    @if($relatedProducts->isNotEmpty())
        <div class="mt-5">
            <h3 class="mb-4">Sản phẩm liên quan</h3>
            <div class="product-card-grid">
                @foreach($relatedProducts as $related)
                    @if($related->id != $product->id)
                        <div class="product-card">
                            @if($related->sale_price)
                                <span class="product-card__badge">-{{ $related->discount_percent }}%</span>
                            @endif
                            
                            <div class="product-card__image">
                                <img src="{{ asset($product->image) }}" 
                                     alt="{{ $related->name }}">
                            </div>
                            
                            <div class="product-card__body">
                                <span class="product-card__category">{{ $related->category->name }}</span>
                                <h3 class="product-card__title">{{ $related->name }}</h3>
                                
                                <div class="product-card__price">
                                    @if($related->selling_price)
                                        <span class="product-card__price-old">{{ number_format($related->selling_price) }}₫</span>
                                        <span class="product-card__price-current">{{ number_format($related->purchase_price) }}₫</span>
                                    @else
                                        <span class="product-card__price-current">{{ number_format($related->purchase_price) }}₫</span>
                                    @endif
                                </div>
                                
                                <a href="{{ route('products.show', $related->id) }}" 
                                   class="btn btn-outline-primary btn-sm btn-block">
                                    Xem chi tiết
                                </a>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    @endif
</div>
@endsection
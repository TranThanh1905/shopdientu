@extends('layouts.app')

@section('title', 'Trang chủ - ElectroShop')

@section('content')

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="d-flex align-items-center" style="gap: 3rem; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 300px;">
                <h1 class="hero__title">Chào mừng đến ElectroShop</h1>
                <p class="hero__subtitle">Khám phá các sản phẩm điện tử chính hãng với giá tốt nhất</p>
                <a href="{{ route('products.index') }}" class="btn btn-light btn-lg">
                    <i class="fas fa-shopping-bag"></i> Mua sắm ngay
                </a>
            </div>
            <div style="flex: 1; min-width: 300px; text-align: center;">
                <img src="{{ asset('images/gemini.jpg') }}"
                     alt="Banner" 
                     class="hero__image"
                     style="max-width: 100%; height: auto;">
            </div>
        </div>
    </div>
</section>

<!-- Categories Section -->
<section class="categories">
    <div class="container">
        <h2 class="categories__title">Danh mục sản phẩm</h2>
        <div class="categories__grid">
            @foreach($categories as $category)
                <a href="{{ route('products.index', ['category' => $category->id]) }}" 
                   class="categories__card">
                    <i class="fa-solid fa-bolt categories__card-icon"></i>
                    <h5 class="categories__card-title">{{ $category->name }}</h5>
                    <p class="categories__card-description">{{ $category->description }}</p>
                </a>
            @endforeach
        </div>
    </div>
</section>

<!-- Products Section -->
<div class="container my-5">
    <h2 class="text-center mb-4">Sản phẩm mới nhất</h2>
    
    <div class="product-card-grid">
        @foreach($products as $product)
            <div class="product-card">
                <!-- Sale Badge -->
                @if($product->selling_price)
                    <span class="product-card__badge">-{{ $product->discount_percent }}%</span>
                @endif
                
                <!-- Product Image -->
                <div class="product-card__image">
                    <img src="{{ asset($product->image) }}" 
                         alt="{{ $product->name }}">
                </div>
                
                <!-- Product Body -->
                <div class="product-card__body">
                    <!-- Category Badge -->
                    <span class="product-card__category">{{ $product->category->name }}</span>
                    
                    <!-- Product Title -->
                    <h3 class="product-card__title">{{ $product->name }}</h3>
                    
                    <!-- Product Description -->
                    <p class="product-card__description">
                        {{ Str::limit($product->description, 80) }}
                    </p>
                    
                    <!-- Product Price -->
                    <div class="product-card__price">
                        @if($product->selling_price)
                            <span class="product-card__price-old">{{ number_format($product->selling_price) }}₫</span>
                            <span class="product-card__price-current">{{ number_format($product->purchase_price) }}₫</span>
                        @else
                            <span class="product-card__price-current">{{ number_format($product->selling_price) }}₫</span>
                        @endif
                    </div>
                    
                    <!-- Product Actions -->
                    <div class="product-card__actions">
                        <a href="{{ route('products.show', $product->id) }}" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-eye"></i> Xem chi tiết
                        </a>
                        <form method="POST" action="{{ route('cart.add') }}" class="add-to-cart-form">
                            @csrf
                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- View All Button -->
    <div class="text-center mt-4">
        <a href="{{ route('products.index') }}" class="btn btn-primary btn-lg">
            Xem tất cả sản phẩm <i class="fas fa-arrow-right"></i>
        </a>
    </div>
</div>

<!-- Features Section -->
<section class="features">
    <div class="container">
        <div class="features__grid">
            <div class="features__item">
                <i class="fas fa-shipping-fast features__item-icon"></i>
                <h5 class="features__item-title">Miễn phí vận chuyển</h5>
                <p class="features__item-text">Đơn hàng từ 500.000₫</p>
            </div>
            
            <div class="features__item">
                <i class="fas fa-shield-alt features__item-icon"></i>
                <h5 class="features__item-title">Bảo hành chính hãng</h5>
                <p class="features__item-text">12-24 tháng</p>
            </div>
            
            <div class="features__item">
                <i class="fas fa-undo features__item-icon"></i>
                <h5 class="features__item-title">Đổi trả dễ dàng</h5>
                <p class="features__item-text">Trong vòng 7 ngày</p>
            </div>
            
            <div class="features__item">
                <i class="fas fa-headset features__item-icon"></i>
                <h5 class="features__item-title">Hỗ trợ 24/7</h5>
                <p class="features__item-text">Hotline: 1900xxxx</p>
            </div>
        </div>
    </div>
</section>

@endsection
@extends('layouts.app')

@section('title', 'Sản phẩm - ElectroShop')

@section('content')
<div class="container my-5">
    <nav class="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb__item"><a href="{{ route('home') }}">Trang chủ</a></li>
            <li class="breadcrumb__item breadcrumb__item--active">Sản phẩm</li>
        </ol>
    </nav>

    <div class="d-flex" style="gap: 2rem;">
        <!-- Sidebar -->
        <div style="width: 250px; flex-shrink: 0;">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-filter"></i> Danh mục</h5>
                </div>
                <div style="padding: 0;">
                    <a href="{{ route('products.index') }}" 
                       class="d-block p-3 text-decoration-none {{ !request('category') ? 'bg-light fw-bold' : '' }}"
                       style="border-bottom: 1px solid #dee2e6; color: inherit;">
                        Tất cả sản phẩm
                    </a>
                    @foreach($categories as $cat)
                        <a href="{{ route('products.index', ['category' => $cat->id]) }}" 
                           class="d-block p-3 text-decoration-none {{ request('category') == $cat->id ? 'bg-light fw-bold' : '' }}"
                           style="border-bottom: 1px solid #dee2e6; color: inherit;">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Products -->
        <div style="flex: 1;">
            @if(request('search'))
                <div class="alert alert-info">
                    <i class="fas fa-search"></i> Kết quả tìm kiếm cho: <strong>{{ request('search') }}</strong>
                    ({{ $products->total() }} sản phẩm)
                </div>
            @endif

            @if($products->isEmpty())
                <div class="alert alert-warning text-center">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <h4>Không tìm thấy sản phẩm nào!</h4>
                </div>
            @else
                <div class="product-card-grid">
                    @foreach($products as $product)
                        <div class="product-card">
                            @if($product->sale_price)
                                <span class="product-card__badge">-{{ $product->discount_percent }}%</span>
                            @endif
                            
                            <div class="product-card__image">
                                <img src="{{ asset($product->image) }}" 
                                     alt="{{ $product->name }}">
                            </div>
                            
                            <div class="product-card__body">
                                <span class="product-card__category">{{ $product->category->name }}</span>
                                <h3 class="product-card__title">{{ $product->name }}</h3>
                                <p class="product-card__description">{{ Str::limit($product->description, 80) }}</p>
                                
                                <div class="product-card__price">
                                    @if($product->sale_price)
                                        <span class="product-card__price-old">{{ number_format($product->selling_price) }}₫</span>
                                        <span class="product-card__price-current">{{ number_format($product->purchase_price) }}₫</span>
                                    @else
                                        <span class="product-card__price-current">{{ number_format($product->purchase_price) }}₫</span>
                                    @endif
                                </div>
                                
                                <div class="product-card__actions">
                                    <a href="{{ route('products.show', $product->id) }}" class="btn btn-outline-primary btn-sm">
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

                <!-- Pagination -->
                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@endsection
<?php
// routes/web.php

use App\Http\Controllers\Admin\AdminCategoryController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminInventoryController;
use App\Http\Controllers\Admin\AdminOrderController;
use App\Http\Controllers\Admin\AdminProductController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Mob\MobOrderController;
/*
|--------------------------------------------------------------------------
| PUBLIC ROUTES (Không cần đăng nhập)
|--------------------------------------------------------------------------
*/

// Trang chủ
Route::get('/', [HomeController::class, 'index'])->name('home');

// Sản phẩm
Route::controller(ProductController::class)->prefix('products')->name('products.')->group(function () {
    Route::get('/', 'index')->name('index');
    Route::get('/{id}', 'show')->name('show');
});

// Giỏ hàng - Xem giỏ hàng (Public)
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');

// Giỏ hàng - Thao tác (Public - session-based cart)
Route::controller(CartController::class)->prefix('cart')->name('cart.')->group(function () {
    Route::post('/add', 'add')->name('add');
    Route::post('/update', 'update')->name('update');
    Route::get('/remove/{id}', 'remove')->name('remove');
    Route::get('/clear', 'clear')->name('clear');
});

/*
|--------------------------------------------------------------------------
| USER AUTHENTICATED ROUTES (Cần đăng nhập)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard (Laravel Breeze mặc định)
    Route::get('/dashboard', function () {
        return view('dashboard');
    })->name('dashboard');

    // Profile (Laravel Breeze)
    Route::prefix('profile')->name('profile.')->group(function () {
        Route::get('/', [ProfileController::class, 'edit'])->name('edit');
        Route::patch('/', [ProfileController::class, 'update'])->name('update');
        Route::delete('/', [ProfileController::class, 'destroy'])->name('destroy');
    });

    // Checkout & Đặt hàng (Yêu cầu đăng nhập)
    Route::controller(OrderController::class)->group(function () {
        Route::get('/checkout', 'checkout')->name('checkout');
        Route::post('/order/place', 'placeOrder')->name('order.place');
        Route::get('/order/success', 'success')->name('order.success');
    });

    // Quản lý đơn hàng của user
    Route::controller(OrderController::class)->prefix('orders')->name('orders.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
        Route::post('/{id}/cancel', 'cancel')->name('cancel');
    });
});

/*
|--------------------------------------------------------------------------
| ADMIN ROUTES (Cần đăng nhập và role admin)
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {

    // ==================== DASHBOARD ====================
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::get('/revenue', [AdminController::class, 'revenue'])->name('revenue');

    // ==================== QUẢN LÝ SẢN PHẨM ====================
    Route::prefix('products')->name('products.')->group(function () {
        // Export (đặt TRƯỚC để tránh conflict với {id})
        Route::get('/export', [AdminProductController::class, 'export'])->name('export');
        
        // Resource routes
        Route::get('/', [AdminProductController::class, 'index'])->name('index');
        Route::get('/create', [AdminProductController::class, 'create'])->name('create');
        Route::post('/', [AdminProductController::class, 'store'])->name('store');
        Route::get('/{id}', [AdminProductController::class, 'show'])->name('show');
        Route::get('/{id}/edit', [AdminProductController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AdminProductController::class, 'update'])->name('update');
        Route::delete('/{id}', [AdminProductController::class, 'destroy'])->name('destroy');
        
        // AJAX & Bulk operations
        Route::post('/{id}/quick-update-price', [AdminProductController::class, 'quickUpdatePrice'])
            ->name('quickUpdatePrice');
        Route::post('/bulk-discount', [AdminProductController::class, 'bulkDiscount'])
            ->name('bulkDiscount');
        Route::post('/calculate-profit', [AdminProductController::class, 'calculateProfit'])
            ->name('calculateProfit');
    });

    // ==================== QUẢN LÝ DANH MỤC ====================
    Route::resource('categories', AdminCategoryController::class)->except(['show']);

    // ==================== QUẢN LÝ NGƯỜI DÙNG ====================
    Route::resource('users', AdminUserController::class);
    Route::post('/users/{id}/change-role', [AdminUserController::class, 'changeRole'])
        ->name('users.changeRole');

    // ==================== QUẢN LÝ ĐƠN HÀNG ====================
    Route::controller(AdminOrderController::class)->prefix('orders')->name('orders.')->group(function () {
        // Export (đặt TRƯỚC để tránh conflict)
        Route::get('/export', 'export')->name('export');
        
        // Danh sách và chi tiết
        Route::get('/', 'index')->name('index');
        Route::get('/{id}', 'show')->name('show');
        
        // Cập nhật trạng thái và xử lý
        Route::post('/{id}/update-status', 'updateStatus')->name('updateStatus');
        Route::post('/{id}/process-return', 'processReturn')->name('processReturn');
        Route::post('/{id}/mark-damaged', 'markDamaged')->name('markDamaged');
        Route::delete('/{id}', 'destroy')->name('destroy');
        
        // In hóa đơn
        Route::get('/{id}/print', 'printInvoice')->name('print');
        Route::get('/{id}/preview', 'previewInvoice')->name('preview');
    });


    Route::prefix('inventory')->name('inventory.')->group(function () {
        Route::get('/', [AdminInventoryController::class, 'index'])->name('index');

        // Stock check
        Route::get('/stock-check', [AdminInventoryController::class, 'stockCheck'])->name('stock-check');

        // Import
        Route::get('/import', [AdminInventoryController::class, 'importList'])->name('import.list');
        Route::get('/import/create', [AdminInventoryController::class, 'importCreate'])->name('import.create');
        Route::post('/import', [AdminInventoryController::class, 'importStore'])->name('import.store');
        Route::get('/import/{id}', [AdminInventoryController::class, 'importShow'])->name('import.show');
        Route::post('/import/{id}/confirm', [AdminInventoryController::class, 'importConfirm'])->name('import.confirm');
        Route::post('/import/{id}/cancel', [AdminInventoryController::class, 'importCancel'])->name('import.cancel');

        // Stock in
        Route::get('/{productId}/stock-in', [AdminInventoryController::class, 'stockIn'])->name('stockIn');
        Route::post('/{productId}/stock-in', [AdminInventoryController::class, 'processStockIn'])->name('processStockIn');

        // Transactions
        Route::get('/{productId}/transactions', [AdminInventoryController::class, 'transactions'])->name('transactions');

        // Damaged
        Route::post('/{productId}/mark-damaged', [AdminInventoryController::class, 'markDamaged'])->name('markDamaged');
    });
});

// ========== ĐĂNG KÝ VỚI BƯỚC XÁC NHẬN ==========
Route::middleware('auth')->group(function () {
    Route::get('/register/confirm', [RegisteredUserController::class, 'showConfirm'])
         ->name('register.confirm');
    Route::post('/register/confirm', [RegisteredUserController::class, 'finishConfirm'])
         ->name('register.confirm.finish');
});

// ========== MOB ROUTES ==========
Route::middleware(['auth', 'mob'])->prefix('mob')->name('mob.')->group(function () {
    Route::get('/orders', [MobOrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{id}', [MobOrderController::class, 'show'])->name('orders.show');
    Route::post('/orders/{id}/confirm', [MobOrderController::class, 'confirm'])->name('orders.confirm');
});



/*
|--------------------------------------------------------------------------
| AUTHENTICATION ROUTES (Laravel Breeze)
|--------------------------------------------------------------------------
*/

require __DIR__.'/auth.php';
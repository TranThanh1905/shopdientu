{{-- resources/views/layouts/app.blade.php --}}
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'ElectroShop - Cửa hàng điện tử')</title>

    <!-- Vite CSS -->
    @vite(['resources/scss/main.scss', 'resources/js/app.js'])

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    @stack('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar">
        <div class="navbar__container">
            <a href="{{ route('home') }}" class="navbar__brand">
                <i class="fa-solid fa-laptop"></i>
                ElectroShop
            </a>

            <button class="navbar__toggle" id="navbarToggle">
                <i class="fa-solid fa-bars"></i>
            </button>

            <ul class="navbar__nav" id="navbarNav">
                <li><a href="{{ route('home') }}" class="navbar__link {{ request()->routeIs('home') ? 'navbar__link--active' : '' }}">Trang chủ</a></li>
                <li><a href="{{ route('products.index') }}" class="navbar__link {{ request()->routeIs('products.*') ? 'navbar__link--active' : '' }}">Sản phẩm</a></li>
            </ul>

            <div class="navbar__search">
                <form method="GET" action="{{ route('products.index') }}">
                    <input type="search" name="search" placeholder="Tìm sản phẩm..." value="{{ request('search') }}">
                    <button type="submit">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>
                </form>
            </div>

            <div class="navbar__actions">
                @guest
                    <a href="{{ route('login') }}" class="btn btn-light btn-sm">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="btn btn-light btn-sm">Đăng ký</a>

                @else
                    <div class="navbar__user">
                        <button class="navbar__user-toggle" id="userDropdown">
                            <i class="fa-solid fa-user"></i>
                            {{ Auth::user()->name }}
                            <i class="fa-solid fa-chevron-down"></i>
                        </button>
                        <div class="navbar__user-dropdown" id="userMenu">
                            @if(Auth::user()->role === 'admin')
                                <a href="{{ route('admin.dashboard') }}">
                                    <i class="fa-solid fa-gear"></i> Admin Panel
                                </a>
                                <hr>
                            @elseif(Auth::user()->role === 'mob')
                                <a href="{{ route('mob.orders.index') }}">
                                    <i class="fa-solid fa-user-shield"></i> Mob Panel
                                </a>
                                <hr>
                            @endif


                            <a href="{{ route('orders.index') }}">
                                <i class="fa-solid fa-shopping-bag"></i> Đơn hàng của tôi
                                @if(Auth::user()->orders()->where('status', 'pending')->count() > 0)
                                    <span class="badge bg-warning text-dark">
                                        {{ Auth::user()->orders()->where('status', 'pending')->count() }}
                                    </span>
                                @endif
                            </a>
                            <a href="{{ route('profile.edit') }}">
                                <i class="fa-solid fa-user-edit"></i> Thông tin cá nhân
                            </a>
                            <hr>

                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" style="width: 100%; text-align: left; background: none; border: none; cursor: pointer; padding: 0.75rem 1.25rem;">
                                    <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
                                </button>
                            </form>
                        </div>
                    </div>
                @endguest

                <a href="{{ route('cart.index') }}" class="navbar__cart">
                    <i class="fa-solid fa-cart-shopping"></i>
                    Giỏ hàng
                    @if(session('cart') && count(session('cart')) > 0)
                        <span class="navbar__cart-badge">{{ count(session('cart')) }}</span>
                    @endif
                </a>
            </div>
        </div>
    </nav>

    <!-- ========== THAY THẾ PHẦN NÀY ========== -->
    <!-- Toast Notifications -->
    @if(session('success'))
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 10100; margin-top: 70px;">
            <div class="alert alert-success alert-dismissible fade show shadow-lg" role="alert" style="min-width: 300px;">
                <i class="fa-solid fa-circle-check me-2"></i>
                <strong>{{ session('success') }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        
        <script>
            setTimeout(function() {
                const alert = document.querySelector('.alert-success');
                if (alert) {
                    alert.classList.remove('show');
                    setTimeout(() => alert.remove(), 150);
                }
            }, 3000);
        </script>
    @endif

    @if(session('error'))
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 9999; margin-top: 70px;">
            <div class="alert alert-danger alert-dismissible fade show shadow-lg" role="alert" style="min-width: 300px;">
                <i class="fa-solid fa-circle-exclamation me-2"></i>
                <strong>{{ session('error') }}</strong>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
        
        <script>
            setTimeout(function() {
                const alert = document.querySelector('.alert-danger');
                if (alert) {
                    alert.classList.remove('show');
                    setTimeout(() => alert.remove(), 150);
                }
            }, 3000);
        </script>
    @endif
    <!-- ========== HẾT PHẦN THAY THẾ ========== -->

    <!-- Main Content -->
    <main>
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="footer__grid">
                <div class="footer__section">
                    <h5><i class="fa-solid fa-laptop"></i> ElectroShop</h5>
                    <p>Cửa hàng đồ điện tử uy tín, chất lượng cao với giá cả phải chăng.</p>
                    <div class="footer__social">
                        <a href="#"><i class="fa-brands fa-facebook fa-2x"></i></a>
                        <a href="#"><i class="fa-brands fa-instagram fa-2x"></i></a>
                        <a href="#"><i class="fa-brands fa-youtube fa-2x"></i></a>
                    </div>
                </div>
                <div class="footer__section">
                    <h5>Liên kết</h5>
                    <ul>
                        <li><a href="{{ route('home') }}">Trang chủ</a></li>
                        <li><a href="{{ route('products.index') }}">Sản phẩm</a></li>
                        <li><a href="#">Về chúng tôi</a></li>
                        <li><a href="#">Liên hệ</a></li>
                    </ul>
                </div>
                <div class="footer__section">
                    <h5>Thông tin liên hệ</h5>
                    <p><i class="fa-solid fa-location-dot"></i> Trường ĐH Phenikaa</p>
                    <p><i class="fa-solid fa-phone"></i> 0912345678</p>
                    <p><i class="fa-solid fa-envelope"></i> info@electroshop.vn</p>
                </div>
            </div>
            <div class="footer__bottom">
                <p>&copy; {{ date('Y') }} ElectroShop. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        // Mobile menu toggle
        document.getElementById('navbarToggle')?.addEventListener('click', function() {
            document.getElementById('navbarNav').classList.toggle('active');
        });

        // User dropdown
        document.getElementById('userDropdown')?.addEventListener('click', function() {
            document.getElementById('userMenu').classList.toggle('active');
        });

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            if (!event.target.closest('.navbar__user')) {
                document.getElementById('userMenu')?.classList.remove('active');
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')
</body>
</html>
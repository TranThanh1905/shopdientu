<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin') — ElectroShop</title>

    @vite(['resources/scss/main.scss', 'resources/js/app.js'])

    @stack('styles')
</head>
<body>

<div class="admin-layout">

    {{-- ============================================================
         SIDEBAR
         ============================================================ --}}
    <aside class="admin-sidebar" id="adminSidebar">

        {{-- Header / Brand --}}
        <div class="admin-sidebar__header">
            <a href="{{ route('admin.dashboard') }}" class="admin-sidebar__brand">
                <i class="fa-solid fa-laptop"></i>
                ElectroShop
            </a>
        </div>

        {{-- Nav --}}
        <nav class="admin-sidebar__nav">

            {{-- ── Tổng quan ── --}}
            <div style="padding: 0.75rem 1.5rem 0.25rem;
                        font-size: 0.65rem;
                        font-weight: 700;
                        letter-spacing: 0.1em;
                        text-transform: uppercase;
                        color: rgba(255,255,255,0.35);">
                Tổng quan
            </div>

            <a href="{{ route('admin.dashboard') }}"
               class="admin-sidebar__link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-pie"></i>
                Dashboard
            </a>

            <a href="{{ route('admin.revenue') }}"
               class="admin-sidebar__link {{ request()->routeIs('admin.revenue') ? 'active' : '' }}">
                <i class="fa-solid fa-chart-line"></i>
                Doanh thu
            </a>

            {{-- ── Đơn hàng ── --}}
            <div style="padding: 1rem 1.5rem 0.25rem;
                        font-size: 0.65rem;
                        font-weight: 700;
                        letter-spacing: 0.1em;
                        text-transform: uppercase;
                        color: rgba(255,255,255,0.35);">
                Đơn hàng
            </div>

            <a href="{{ route('admin.orders.index') }}"
               class="admin-sidebar__link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                <i class="fa-solid fa-bag-shopping"></i>
                Quản lý đơn hàng
                @php $pendingCount = \App\Models\Order::where('status','pending')->count(); @endphp
                @if($pendingCount > 0)
                    <span class="badge bg-danger ms-auto" style="font-size: 0.65rem;">
                        {{ $pendingCount }}
                    </span>
                @endif
            </a>

            {{-- ── Kho hàng ── --}}
            <div style="padding: 1rem 1.5rem 0.25rem;
                        font-size: 0.65rem;
                        font-weight: 700;
                        letter-spacing: 0.1em;
                        text-transform: uppercase;
                        color: rgba(255,255,255,0.35);">
                Kho hàng
            </div>

            <a href="{{ route('admin.inventory.index') }}"
               class="admin-sidebar__link {{ request()->routeIs('admin.inventory.index') ? 'active' : '' }}">
                <i class="fa-solid fa-warehouse"></i>
                Tổng quan kho
            </a>

            <a href="{{ route('admin.inventory.stock-check') }}"
               class="admin-sidebar__link {{ request()->routeIs('admin.inventory.stock-check') ? 'active' : '' }}">
                <i class="fa-solid fa-clipboard-check"></i>
                Kiểm hàng
            </a>

            <a href="{{ route('admin.inventory.import.list') }}"
               class="admin-sidebar__link {{ request()->routeIs('admin.inventory.import.*') ? 'active' : '' }}">
                <i class="fa-solid fa-file-import"></i>
                Phiếu nhập kho
            </a>

            {{-- ── Catalogue ── --}}
            <div style="padding: 1rem 1.5rem 0.25rem;
                        font-size: 0.65rem;
                        font-weight: 700;
                        letter-spacing: 0.1em;
                        text-transform: uppercase;
                        color: rgba(255,255,255,0.35);">
                Catalogue
            </div>

            <a href="{{ route('admin.products.index') }}"
               class="admin-sidebar__link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}">
                <i class="fa-solid fa-box"></i>
                Sản phẩm
            </a>

            <a href="{{ route('admin.categories.index') }}"
               class="admin-sidebar__link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}">
                <i class="fa-solid fa-tag"></i>
                Danh mục
            </a>

            {{-- ── Người dùng ── --}}
            <div style="padding: 1rem 1.5rem 0.25rem;
                        font-size: 0.65rem;
                        font-weight: 700;
                        letter-spacing: 0.1em;
                        text-transform: uppercase;
                        color: rgba(255,255,255,0.35);">
                Người dùng
            </div>

            <a href="{{ route('admin.users.index') }}"
               class="admin-sidebar__link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                <i class="fa-solid fa-users"></i>
                Tài khoản
            </a>

            {{-- ── Separator ── --}}
            <div style="margin: 1rem 1.5rem;
                        border-top: 1px solid rgba(255,255,255,0.1);"></div>

            <a href="{{ route('home') }}" target="_blank"
               class="admin-sidebar__link">
                <i class="fa-solid fa-store"></i>
                Về trang shop
            </a>

        </nav>

        {{-- User block ở đáy --}}
        <div style="padding: 1rem 1.5rem;
                    border-top: 1px solid rgba(255,255,255,0.1);
                    margin-top: auto;
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;">
            <div style="width: 36px; height: 36px;
                        border-radius: 50%;
                        background: rgba(255,255,255,0.15);
                        display: flex; align-items: center; justify-content: center;
                        font-size: 0.8rem; font-weight: 700; color: #fff;
                        flex-shrink: 0;">
                {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
            </div>
            <div style="overflow: hidden;">
                <div style="color: #fff; font-size: 0.82rem; font-weight: 600;
                            white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                    {{ Auth::user()->name }}
                </div>
                <div style="color: rgba(255,255,255,0.45); font-size: 0.7rem;">
                    {{ Auth::user()->role === 'admin' ? 'Quản trị viên' : 'Trung gian' }}
                </div>
            </div>
        </div>

    </aside>

    {{-- ============================================================
         MAIN
         ============================================================ --}}
    <div class="admin-main" id="adminMain">

        {{-- TOPBAR --}}
        <div class="admin-topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="admin-sidebar-toggle" id="sidebarToggle">
                    <i class="fa-solid fa-bars"></i>
                </button>

                {{-- Breadcrumb --}}
                <nav style="font-size: 0.82rem; color: #64748b; display: flex; align-items: center; gap: 0.375rem;">
                    <a href="{{ route('admin.dashboard') }}"
                       style="color: #64748b; text-decoration: none;">Admin</a>
                    @hasSection('breadcrumb')
                        <i class="fa-solid fa-chevron-right" style="font-size: 0.6rem; color: #cbd5e1;"></i>
                        @yield('breadcrumb')
                    @endif
                </nav>
            </div>

            <div class="admin-topbar__user">
                {{-- Thông báo đơn chờ --}}
                <a href="{{ route('admin.orders.index', ['status' => 'pending']) }}"
                class="btn btn-light btn-sm me-1"
                title="{{ $pendingCount ?? 0 }} đơn chờ">

                    <span class="position-relative d-inline-block">
                        <i class="fa-solid fa-bell"></i>

                        @if(($pendingCount ?? 0) > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                style="font-size: 0.6rem;">
                                {{ $pendingCount }}
                            </span>
                        @endif
                    </span>
                </a>

                {{-- User dropdown --}}
                <div class="dropdown">
                    <button class="btn btn-light btn-sm dropdown-toggle d-flex align-items-center gap-2"
                            data-bs-toggle="dropdown">
                        <div style="width: 28px; height: 28px; border-radius: 50%;
                                    background: linear-gradient(135deg, #2563eb, #4f46e5);
                                    color: #fff; font-size: 0.7rem; font-weight: 700;
                                    display: flex; align-items: center; justify-content: center;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 2)) }}
                        </div>
                        <span style="font-size: 0.85rem;">{{ Auth::user()->name }}</span>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm" style="min-width: 180px; border: 1px solid #e2e8f0;">
                        <li>
                            <a class="dropdown-item d-flex align-items-center gap-2"
                               href="{{ route('profile.edit') }}">
                                <i class="fa-solid fa-user" style="width: 16px; color: #2563eb;"></i>
                                Hồ sơ cá nhân
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="dropdown-item d-flex align-items-center gap-2 text-danger">
                                    <i class="fa-solid fa-right-from-bracket" style="width: 16px;"></i>
                                    Đăng xuất
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- CONTENT --}}
        <div class="admin-content">

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-3"
                     style="border-radius: 10px; border: none;
                            background: linear-gradient(135deg, rgba(17,153,142,0.12), rgba(56,239,125,0.12));
                            color: #065f46; border-left: 4px solid #11998e;">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('success') }}</span>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-3"
                     style="border-radius: 10px; border: none;
                            background: linear-gradient(135deg, rgba(255,107,107,0.12), rgba(238,90,111,0.12));
                            color: #991b1b; border-left: 4px solid #ff6b6b;">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ session('error') }}</span>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center gap-2 mb-3"
                     style="border-radius: 10px; border: none;
                            background: linear-gradient(135deg, rgba(251,191,36,0.12), rgba(249,115,22,0.12));
                            color: #92400e; border-left: 4px solid #fbbf24;">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <span>{{ session('warning') }}</span>
                    <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @yield('content')

        </div>

    </div>

</div>

{{-- ============================================================
     SCRIPTS
     ============================================================ --}}
<script>
(function () {
    // Mobile sidebar toggle
    const sidebar   = document.getElementById('adminSidebar');
    const toggleBtn = document.getElementById('sidebarToggle');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('active');
        });

        // Đóng sidebar khi click ngoài (mobile)
        document.addEventListener('click', (e) => {
            if (window.innerWidth <= 768
                && !sidebar.contains(e.target)
                && !toggleBtn.contains(e.target)) {
                sidebar.classList.remove('active');
            }
        });
    }

    // Tự đóng flash messages sau 4 giây
    document.querySelectorAll('.alert').forEach(el => {
        setTimeout(() => {
            const bsAlert = bootstrap?.Alert?.getOrCreateInstance(el);
            bsAlert?.close();
        }, 4000);
    });
})();
</script>

@stack('scripts')
</body>
</html>
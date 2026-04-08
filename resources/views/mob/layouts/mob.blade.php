<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Mob Panel - ElectroShop')</title>
    @vite(['resources/scss/main.scss', 'resources/js/app.js'])
    <style>
        :root {
            --mob-primary:   #205aa7;
            --mob-warning:   #fbbf24;
            --mob-sidebar-w: 240px;
        }

        /* Sidebar layout */
        .mob-wrapper {
            display: flex;
            min-height: 100vh;
        }

        .mob-sidebar {
            width: var(--mob-sidebar-w);
            background: var(--mob-primary);
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0;
            left: 0;
            bottom: 0;
            z-index: 100;
            box-shadow: 4px 0 12px rgba(32, 90, 167, 0.2);
        }

        .mob-sidebar__brand {
            padding: 1.5rem 1.25rem;
            border-bottom: 1px solid rgba(255,255,255,0.15);
        }

        .mob-sidebar__brand-title {
            color: #fff;
            font-size: 1.125rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .mob-sidebar__brand-title:hover {
            color: var(--mob-warning);
            text-decoration: none;
        }

        .mob-sidebar__role-badge {
            display: inline-block;
            background: var(--mob-warning);
            color: #0f172a;
            font-size: 0.65rem;
            font-weight: 700;
            padding: 0.15rem 0.5rem;
            border-radius: 9999px;
            margin-top: 0.25rem;
            letter-spacing: 0.05em;
            text-transform: uppercase;
        }

        .mob-sidebar__nav {
            flex: 1;
            padding: 1rem 0;
        }

        .mob-nav__link {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            padding: 0.75rem 1.25rem;
            color: rgba(255,255,255,0.75);
            text-decoration: none;
            font-size: 0.9375rem;
            font-weight: 500;
            transition: all 0.2s ease;
            border-left: 3px solid transparent;
        }

        .mob-nav__link:hover,
        .mob-nav__link--active {
            color: #fff;
            background: rgba(255,255,255,0.1);
            border-left-color: var(--mob-warning);
            text-decoration: none;
        }

        .mob-nav__link i {
            width: 18px;
            text-align: center;
        }

        .mob-sidebar__footer {
            padding: 1rem 1.25rem;
            border-top: 1px solid rgba(255,255,255,0.15);
        }

        .mob-sidebar__user {
            color: rgba(255,255,255,0.9);
            font-size: 0.875rem;
            font-weight: 500;
            margin-bottom: 0.75rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Main content */
        .mob-main {
            margin-left: var(--mob-sidebar-w);
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .mob-topbar {
            height: 56px;
            background: #fff;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1.5rem;
            position: sticky;
            top: 0;
            z-index: 50;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
        }

        .mob-topbar__breadcrumb {
            font-size: 0.875rem;
            color: #64748b;
        }

        .mob-topbar__breadcrumb strong {
            color: var(--mob-primary);
        }

        .mob-content {
            padding: 1.75rem;
            flex: 1;
            background: #f8fafc;
        }

        /* Alert toast */
        .mob-toast {
            position: fixed;
            top: 70px;
            right: 1.5rem;
            z-index: 9999;
            min-width: 320px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .mob-sidebar { display: none; }
            .mob-main { margin-left: 0; }
        }
    </style>
</head>
<body>
<div class="mob-wrapper">

    {{-- Sidebar --}}
    <aside class="mob-sidebar">
        <div class="mob-sidebar__brand">
            <a href="{{ route('mob.orders.index') }}" class="mob-sidebar__brand-title">
                <i class="fa-solid fa-shield-halved"></i>
                Mob Panel
            </a>
            <span class="mob-sidebar__role-badge">Trung gian</span>
        </div>

        <nav class="mob-sidebar__nav">
            <a href="{{ route('mob.orders.index') }}"
               class="mob-nav__link {{ request()->routeIs('mob.orders.*') ? 'mob-nav__link--active' : '' }}">
                <i class="fa-solid fa-list-check"></i>
                Đơn hàng
            </a>
        </nav>

        <div class="mob-sidebar__footer">
            <div class="mob-sidebar__user">
                <i class="fa-solid fa-circle-user"></i>
                {{ Auth::user()->name }}
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit"
                        class="btn btn-sm w-100"
                        style="background:rgba(255,255,255,0.15);color:#fff;border-color:rgba(255,255,255,0.3);">
                    <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
                </button>
            </form>
        </div>
    </aside>

    {{-- Main --}}
    <div class="mob-main">

        {{-- Topbar --}}
        <header class="mob-topbar">
            <div class="mob-topbar__breadcrumb">
                <i class="fa-solid fa-house"></i>
                &rsaquo;
                <strong>@yield('breadcrumb', 'Dashboard')</strong>
            </div>
            <div style="font-size:0.8125rem;color:#64748b;">
                <i class="fa-solid fa-clock"></i>
                {{ now()->format('d/m/Y H:i') }}
            </div>
        </header>

        {{-- Toast Notifications --}}
        @if(session('success'))
            <div class="mob-toast">
                <div class="alert alert-success alert-dismissible fade show shadow"
                     role="alert">
                    <i class="fa-solid fa-circle-check me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div class="mob-toast">
                <div class="alert alert-danger alert-dismissible fade show shadow"
                     role="alert">
                    <i class="fa-solid fa-circle-exclamation me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
        @endif

        {{-- Content --}}
        <main class="mob-content">
            @yield('content')
        </main>

    </div>
</div>

<script>
    // Auto-dismiss toasts
    setTimeout(() => {
        document.querySelectorAll('.mob-toast .alert').forEach(el => {
            el.classList.remove('show');
            setTimeout(() => el.closest('.mob-toast')?.remove(), 200);
        });
    }, 4000);
</script>

@stack('scripts')
</body>
</html>
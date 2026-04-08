<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký thành công - ElectroShop</title>
    @vite(['resources/scss/main.scss', 'resources/js/app.js'])
    <style>
        body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #205aa7 0%, #1e3a6e 100%);
            padding: 1.5rem;
        }

        .confirm-wrapper {
            width: 100%;
            max-width: 480px;
        }

        /* Card chính */
        .confirm-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 25px 50px rgba(0,0,0,0.25);
            animation: slideUp 0.45s cubic-bezier(0.4, 0, 0.2, 1);
        }

        @keyframes slideUp {
            from { opacity: 0; transform: translateY(24px); }
            to   { opacity: 1; transform: translateY(0); }
        }

        /* Header xanh */
        .confirm-card__header {
            background: linear-gradient(135deg, #205aa7, #fb923c);
            padding: 2.5rem 2rem;
            text-align: center;
        }

        .confirm-card__icon {
            width: 72px;
            height: 72px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            animation: pop 0.5s 0.3s cubic-bezier(0.68,-0.55,0.265,1.55) both;
        }

        @keyframes pop {
            from { transform: scale(0); opacity: 0; }
            to   { transform: scale(1); opacity: 1; }
        }

        .confirm-card__icon i {
            font-size: 2rem;
            color: #fff;
        }

        .confirm-card__title {
            color: #fff;
            font-size: 1.5rem;
            font-weight: 800;
            margin-bottom: 0.35rem;
        }

        .confirm-card__subtitle {
            color: rgba(255,255,255,0.85);
            font-size: 0.9375rem;
        }

        /* Body */
        .confirm-card__body {
            padding: 2rem;
        }

        /* Info rows */
        .info-row {
            display: flex;
            align-items: center;
            gap: 0.875rem;
            padding: 0.75rem 0;
            border-bottom: 1px solid #f1f5f9;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-row__icon {
            width: 36px;
            height: 36px;
            background: #eff6ff;
            border-radius: 9px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .info-row__icon i {
            font-size: 0.9rem;
            color: #205aa7;
        }

        .info-row__label {
            font-size: 0.75rem;
            color: #94a3b8;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .info-row__value {
            font-size: 0.9375rem;
            color: #1e293b;
            font-weight: 600;
        }

        /* Mã khách hàng */
        .customer-code-box {
            background: linear-gradient(135deg, #205aa7, #fb923c);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            margin: 1.25rem 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .customer-code-box__label {
            color: rgba(255,255,255,0.8);
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .customer-code-box__code {
            color: #fff;
            font-size: 1.375rem;
            font-weight: 800;
            letter-spacing: 0.05em;
        }

        .customer-code-box__badge {
            background: rgba(255,255,255,0.2);
            color: #fff;
            font-size: 0.75rem;
            padding: 0.2rem 0.6rem;
            border-radius: 9999px;
            font-weight: 600;
        }

        /* Note */
        .confirm-note {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 10px;
            padding: 0.875rem 1rem;
            margin-bottom: 1.25rem;
            font-size: 0.8125rem;
            color: #92400e;
            line-height: 1.6;
        }

        /* Button --*/
        .btn-start {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, #205aa7, #fb923c);
            color: #fff;
            border: none;
            border-radius: 12px;
            font-size: 1rem;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            text-decoration: none;
        }

        .btn-start:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(32,90,167,0.35);
            color: #fff;
            text-decoration: none;
        }
    </style>
</head>
<body>

<div class="confirm-wrapper">
    <div class="confirm-card">

        {{-- Header --}}
        <div class="confirm-card__header">
            <div class="confirm-card__icon">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <h1 class="confirm-card__title">Đăng ký thành công!</h1>
            <p class="confirm-card__subtitle">
                Chào mừng bạn đến với ElectroShop 🎉
            </p>
        </div>

        {{-- Body --}}
        <div class="confirm-card__body">

            {{-- Mã khách hàng nổi bật --}}
            @if(Auth::user()->customer_code)
                <div class="customer-code-box">
                    <div>
                        <div class="customer-code-box__label">Mã khách hàng của bạn</div>
                        <div class="customer-code-box__code">
                            {{ Auth::user()->customer_code }}
                        </div>
                    </div>
                    <span class="customer-code-box__badge">
                        <i class="fa-solid fa-star me-1"></i> Thành viên
                    </span>
                </div>
            @endif

            {{-- Thông tin tài khoản --}}
            <div class="info-row">
                <div class="info-row__icon">
                    <i class="fa-solid fa-user"></i>
                </div>
                <div>
                    <div class="info-row__label">Họ tên</div>
                    <div class="info-row__value">{{ Auth::user()->name }}</div>
                </div>
            </div>

            <div class="info-row">
                <div class="info-row__icon">
                    <i class="fa-solid fa-envelope"></i>
                </div>
                <div>
                    <div class="info-row__label">Email</div>
                    <div class="info-row__value">{{ Auth::user()->email }}</div>
                </div>
            </div>

            <div class="info-row">
                <div class="info-row__icon">
                    <i class="fa-solid fa-calendar"></i>
                </div>
                <div>
                    <div class="info-row__label">Ngày tham gia</div>
                    <div class="info-row__value">{{ now()->format('d/m/Y') }}</div>
                </div>
            </div>

            {{-- Lưu ý --}}
            <div class="confirm-note mt-4">
                <i class="fa-solid fa-lightbulb me-1"></i>
                <strong>Mẹo:</strong> Lưu lại mã khách hàng để tra cứu đơn hàng
                nhanh hơn khi cần hỗ trợ.
            </div>

            {{-- Button --}}
            <form method="POST" action="{{ route('register.confirm.finish') }}">
                @csrf
                <button type="submit" class="btn-start">
                    <i class="fa-solid fa-rocket"></i>
                    Bắt đầu mua sắm ngay
                </button>
            </form>

        </div>
    </div>
</div>

</body>
</html>
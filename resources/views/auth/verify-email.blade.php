@extends('layouts.app')

@section('title', 'Xác thực Email - ElectroShop')

@push('styles')
<style>
    .verify-email-container {
        max-width: 500px;
        margin: 4rem auto;
        padding: 2rem;
    }

    .verify-email-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 2.5rem;
    }

    .verify-email-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .verify-email-header i {
        font-size: 4rem;
        color: #ffc107;
        margin-bottom: 1rem;
    }

    .verify-email-header h2 {
        color: #333;
        font-size: 1.75rem;
        margin-bottom: 0.5rem;
    }

    .verify-notice {
        background: #fff3cd;
        border-left: 4px solid #ffc107;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border-radius: 4px;
        color: #856404;
        font-size: 0.95rem;
        line-height: 1.6;
    }

    .verify-notice i {
        color: #ffc107;
        margin-right: 0.5rem;
    }

    .success-message {
        background: #d4edda;
        border-left: 4px solid #28a745;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border-radius: 4px;
        color: #155724;
        font-size: 0.95rem;
    }

    .success-message i {
        color: #28a745;
        margin-right: 0.5rem;
    }

    .action-buttons {
        display: flex;
        justify-content: space-between;
        align-items: center;
        gap: 1rem;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #eee;
    }

    .btn-resend {
        flex: 1;
        background: #007bff;
        color: white;
        padding: 0.75rem 1.5rem;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.3s;
    }

    .btn-resend:hover {
        background: #0056b3;
    }

    .btn-logout {
        color: #dc3545;
        text-decoration: none;
        font-size: 0.95rem;
        padding: 0.75rem 1rem;
        transition: color 0.3s;
        background: none;
        border: none;
        cursor: pointer;
    }

    .btn-logout:hover {
        color: #c82333;
        text-decoration: underline;
    }

    @media (max-width: 576px) {
        .action-buttons {
            flex-direction: column;
        }

        .btn-resend {
            width: 100%;
        }
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="verify-email-container">
        <div class="verify-email-card">
            <div class="verify-email-header">
                <i class="fa-solid fa-envelope-circle-check"></i>
                <h2>Xác thực Email</h2>
            </div>

            <div class="verify-notice">
                <i class="fa-solid fa-info-circle"></i>
                Cảm ơn bạn đã đăng ký! Trước khi bắt đầu, vui lòng xác thực địa chỉ email của bạn bằng cách nhấp vào liên kết chúng tôi vừa gửi qua email. Nếu bạn không nhận được email, chúng tôi sẽ vui lòng gửi lại cho bạn.
            </div>

            @if (session('status') == 'verification-link-sent')
                <div class="success-message">
                    <i class="fa-solid fa-circle-check"></i>
                    Một liên kết xác thực mới đã được gửi đến địa chỉ email bạn đã cung cấp khi đăng ký.
                </div>
            @endif

            <div class="action-buttons">
                <form method="POST" action="{{ route('verification.send') }}" style="flex: 1;">
                    @csrf
                    <button type="submit" class="btn-resend">
                        <i class="fa-solid fa-paper-plane"></i> Gửi lại Email xác thực
                    </button>
                </form>

                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn-logout">
                        <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
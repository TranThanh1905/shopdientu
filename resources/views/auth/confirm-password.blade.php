@extends('layouts.app')

@section('title', 'Xác nhận mật khẩu - ElectroShop')

@push('styles')
<style>
    .confirm-password-container {
        max-width: 450px;
        margin: 4rem auto;
        padding: 2rem;
    }

    .confirm-password-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 2.5rem;
    }

    .confirm-password-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .confirm-password-header h2 {
        color: #333;
        font-size: 1.75rem;
        margin-bottom: 0.5rem;
    }

    .security-notice {
        background: #f8f9fa;
        border-left: 4px solid #007bff;
        padding: 1rem;
        margin-bottom: 1.5rem;
        border-radius: 4px;
        color: #666;
        font-size: 0.95rem;
        line-height: 1.5;
    }

    .security-notice i {
        color: #007bff;
        margin-right: 0.5rem;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        margin-bottom: 0.5rem;
        color: #333;
        font-weight: 500;
        font-size: 0.95rem;
    }

    .form-input {
        width: 100%;
        padding: 0.75rem 1rem;
        border: 1px solid #ddd;
        border-radius: 8px;
        font-size: 1rem;
        transition: border-color 0.3s;
    }

    .form-input:focus {
        outline: none;
        border-color: #007bff;
        box-shadow: 0 0 0 3px rgba(0, 123, 255, 0.1);
    }

    .form-error {
        color: #dc3545;
        font-size: 0.85rem;
        margin-top: 0.5rem;
    }

    .btn-confirm {
        width: 100%;
        background: #007bff;
        color: white;
        padding: 0.75rem;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.3s;
        margin-top: 1rem;
    }

    .btn-confirm:hover {
        background: #0056b3;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="confirm-password-container">
        <div class="confirm-password-card">
            <div class="confirm-password-header">
                <h2><i class="fa-solid fa-shield-halved"></i> Xác nhận mật khẩu</h2>
            </div>

            <div class="security-notice">
                <i class="fa-solid fa-lock"></i>
                Đây là khu vực bảo mật của ứng dụng. Vui lòng xác nhận mật khẩu của bạn trước khi tiếp tục.
            </div>

            <form method="POST" action="{{ route('password.confirm') }}">
                @csrf

                <!-- Password -->
                <div class="form-group">
                    <label for="password" class="form-label">
                        <i class="fa-solid fa-lock"></i> Mật khẩu
                    </label>
                    <input 
                        id="password" 
                        class="form-input"
                        type="password"
                        name="password"
                        required 
                        autocomplete="current-password"
                        placeholder="Nhập mật khẩu của bạn"
                        autofocus
                    />
                    @error('password')
                        <div class="form-error">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <button type="submit" class="btn-confirm">
                    <i class="fa-solid fa-check"></i> Xác nhận
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
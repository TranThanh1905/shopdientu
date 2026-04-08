@extends('layouts.app')

@section('title', 'Quên mật khẩu - ElectroShop')

@push('styles')
<style>
    .forgot-password-container {
        max-width: 450px;
        margin: 4rem auto;
        padding: 2rem;
    }

    .forgot-password-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 2.5rem;
    }

    .forgot-password-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .forgot-password-header h2 {
        color: #333;
        font-size: 1.75rem;
        margin-bottom: 0.5rem;
    }

    .forgot-password-header p {
        color: #666;
        font-size: 0.95rem;
        line-height: 1.5;
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

    .btn-submit {
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

    .btn-submit:hover {
        background: #0056b3;
    }

    .back-to-login {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #eee;
    }

    .back-to-login a {
        color: #007bff;
        text-decoration: none;
        font-size: 0.95rem;
    }

    .back-to-login a:hover {
        text-decoration: underline;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="forgot-password-container">
        <div class="forgot-password-card">
            <div class="forgot-password-header">
                <h2><i class="fa-solid fa-key"></i> Quên mật khẩu</h2>
                <p>Nhập địa chỉ email của bạn và chúng tôi sẽ gửi link để đặt lại mật khẩu</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="alert alert-success" style="margin-bottom: 1.5rem;">
                    <i class="fa-solid fa-circle-check"></i> {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf

                <!-- Email Address -->
                <div class="form-group">
                    <label for="email" class="form-label">
                        <i class="fa-solid fa-envelope"></i> Email
                    </label>
                    <input 
                        id="email" 
                        class="form-input" 
                        type="email" 
                        name="email" 
                        value="{{ old('email') }}" 
                        required 
                        autofocus
                        placeholder="Nhập địa chỉ email của bạn"
                    />
                    @error('email')
                        <div class="form-error">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-paper-plane"></i> Gửi link đặt lại mật khẩu
                </button>
            </form>

            <div class="back-to-login">
                <a href="{{ route('login') }}">
                    <i class="fa-solid fa-arrow-left"></i> Quay lại đăng nhập
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
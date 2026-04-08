@extends('layouts.app')

@section('title', 'Đăng nhập - ElectroShop')

@push('styles')
<style>
    .login-container {
        max-width: 450px;
        margin: 4rem auto;
        padding: 2rem;
    }

    .login-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 2.5rem;
    }

    .login-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .login-header h2 {
        color: #333;
        font-size: 1.75rem;
        margin-bottom: 0.5rem;
    }

    .login-header p {
        color: #666;
        font-size: 0.95rem;
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

    .form-checkbox {
        display: flex;
        align-items: center;
        margin: 1rem 0;
    }

    .form-checkbox input {
        margin-right: 0.5rem;
    }

    .form-checkbox label {
        color: #666;
        font-size: 0.9rem;
        cursor: pointer;
    }

    .form-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 1.5rem;
    }

    .forgot-password {
        color: #007bff;
        text-decoration: none;
        font-size: 0.9rem;
        transition: color 0.3s;
    }

    .forgot-password:hover {
        color: #0056b3;
        text-decoration: underline;
    }

    .btn-login {
        background: #007bff;
        color: white;
        padding: 0.75rem 2rem;
        border: none;
        border-radius: 8px;
        font-size: 1rem;
        font-weight: 500;
        cursor: pointer;
        transition: background 0.3s;
    }

    .btn-login:hover {
        background: #0056b3;
    }

    .register-link {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #eee;
        color: #666;
        font-size: 0.95rem;
    }

    .register-link a {
        color: #007bff;
        text-decoration: none;
        font-weight: 500;
    }

    .register-link a:hover {
        text-decoration: underline;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h2><i class="fa-solid fa-user-circle"></i> Đăng nhập</h2>
                <p>Chào mừng bạn trở lại ElectroShop</p>
            </div>

            <!-- Session Status -->
            @if (session('status'))
                <div class="alert alert-success" style="margin-bottom: 1.5rem;">
                    <i class="fa-solid fa-circle-check"></i> {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
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
                        autocomplete="username"
                        placeholder="Nhập địa chỉ email của bạn"
                    />
                    @error('email')
                        <div class="form-error">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

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
                    />
                    @error('password')
                        <div class="form-error">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Remember Me -->
                <div class="form-checkbox">
                    <input id="remember_me" type="checkbox" name="remember">
                    <label for="remember_me">Ghi nhớ đăng nhập</label>
                </div>

                <div class="form-actions">
                    @if (Route::has('password.request'))
                        <a class="forgot-password" href="{{ route('password.request') }}">
                            Quên mật khẩu?
                        </a>
                    @endif

                    <button type="submit" class="btn-login">
                        <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập
                    </button>
                </div>
            </form>

            @if (Route::has('register'))
                <div class="register-link">
                    Chưa có tài khoản? <a href="{{ route('register') }}">Đăng ký ngay</a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
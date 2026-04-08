@extends('layouts.app')

@section('title', 'Đăng ký - ElectroShop')

@push('styles')
<style>
    .register-container {
        max-width: 450px;
        margin: 4rem auto;
        padding: 2rem;
    }

    .register-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 2.5rem;
    }

    .register-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .register-header h2 {
        color: #333;
        font-size: 1.75rem;
        margin-bottom: 0.5rem;
    }

    .register-header p {
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

    .btn-register {
        width: 100%;
        background: linear-gradient(90deg,rgba(37, 186, 99, 1) 45%, rgba(151, 204, 16, 1) 94%);
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

    .btn-register:hover {
        background: linear-gradient(90deg,rgba(71, 204, 127, 1) 14%, rgba(46, 140, 11, 1) 94%);
    }

    .login-link {
        text-align: center;
        margin-top: 1.5rem;
        padding-top: 1.5rem;
        border-top: 1px solid #eee;
        color: #666;
        font-size: 0.95rem;
    }

    .login-link a {
        color: #007bff;
        text-decoration: none;
        font-weight: 500;
    }

    .login-link a:hover {
        text-decoration: underline;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <h2><i class="fa-solid fa-user-plus"></i> Đăng ký</h2>
                <p>Tạo tài khoản mới tại ElectroShop</p>
            </div>

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Name -->
                <div class="form-group">
                    <label for="name" class="form-label">
                        <i class="fa-solid fa-user"></i> Họ và tên
                    </label>
                    <input 
                        id="name" 
                        class="form-input" 
                        type="text" 
                        name="name" 
                        value="{{ old('name') }}" 
                        required 
                        autofocus 
                        autocomplete="name"
                        placeholder="Nhập họ và tên của bạn"
                    />
                    @error('name')
                        <div class="form-error">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

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
                        autocomplete="new-password"
                        placeholder="Nhập mật khẩu (tối thiểu 8 ký tự)"
                    />
                    @error('password')
                        <div class="form-error">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <!-- Confirm Password -->
                <div class="form-group">
                    <label for="password_confirmation" class="form-label">
                        <i class="fa-solid fa-lock"></i> Xác nhận mật khẩu
                    </label>
                    <input 
                        id="password_confirmation" 
                        class="form-input"
                        type="password"
                        name="password_confirmation"
                        required 
                        autocomplete="new-password"
                        placeholder="Nhập lại mật khẩu"
                    />
                    @error('password_confirmation')
                        <div class="form-error">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <button type="submit" class="btn-register">
                    <i class="fa-solid fa-user-plus"></i> Đăng ký
                </button>
            </form>

            <div class="login-link">
                Đã có tài khoản? <a href="{{ route('login') }}">Đăng nhập ngay</a>
            </div>
        </div>
    </div>
</div>
@endsection
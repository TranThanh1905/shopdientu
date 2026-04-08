@extends('layouts.app')

@section('title', 'Đặt lại mật khẩu - ElectroShop')

@push('styles')
<style>
    .reset-password-container {
        max-width: 450px;
        margin: 4rem auto;
        padding: 2rem;
    }

    .reset-password-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        padding: 2.5rem;
    }

    .reset-password-header {
        text-align: center;
        margin-bottom: 2rem;
    }

    .reset-password-header h2 {
        color: #333;
        font-size: 1.75rem;
        margin-bottom: 0.5rem;
    }

    .reset-password-header p {
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

    .btn-submit {
        width: 100%;
        background: #28a745;
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
        background: #218838;
    }
</style>
@endpush

@section('content')
<div class="container">
    <div class="reset-password-container">
        <div class="reset-password-card">
            <div class="reset-password-header">
                <h2><i class="fa-solid fa-lock-open"></i> Đặt lại mật khẩu</h2>
                <p>Nhập mật khẩu mới cho tài khoản của bạn</p>
            </div>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf

                <!-- Password Reset Token -->
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

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
                        value="{{ old('email', $request->email) }}" 
                        required 
                        autofocus 
                        autocomplete="username"
                        placeholder="Địa chỉ email của bạn"
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
                        <i class="fa-solid fa-lock"></i> Mật khẩu mới
                    </label>
                    <input 
                        id="password" 
                        class="form-input" 
                        type="password" 
                        name="password" 
                        required 
                        autocomplete="new-password"
                        placeholder="Nhập mật khẩu mới (tối thiểu 8 ký tự)"
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
                        placeholder="Nhập lại mật khẩu mới"
                    />
                    @error('password_confirmation')
                        <div class="form-error">
                            <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                        </div>
                    @enderror
                </div>

                <button type="submit" class="btn-submit">
                    <i class="fa-solid fa-check"></i> Đặt lại mật khẩu
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
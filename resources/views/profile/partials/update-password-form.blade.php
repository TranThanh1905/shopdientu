{{-- resources/views/profile/partials/update-password-form.blade.php --}}
<section>
    <header class="mb-4">
        <p class="text-muted mb-0">
            Đảm bảo tài khoản của bạn sử dụng mật khẩu mạnh để bảo mật.
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}">
        @csrf
        @method('put')

        <!-- Current Password -->
        <div class="mb-3">
            <label for="update_password_current_password" class="form-label">
                Mật khẩu hiện tại <span class="text-danger">*</span>
            </label>
            <input type="password" 
                   class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                   id="update_password_current_password" 
                   name="current_password" 
                   autocomplete="current-password">
            @error('current_password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- New Password -->
        <div class="mb-3">
            <label for="update_password_password" class="form-label">
                Mật khẩu mới <span class="text-danger">*</span>
            </label>
            <input type="password" 
                   class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                   id="update_password_password" 
                   name="password" 
                   autocomplete="new-password">
            @error('password', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="form-text text-muted">
                Mật khẩu phải có ít nhất 8 ký tự.
            </small>
        </div>

        <!-- Confirm Password -->
        <div class="mb-3">
            <label for="update_password_password_confirmation" class="form-label">
                Xác nhận mật khẩu mới <span class="text-danger">*</span>
            </label>
            <input type="password" 
                   class="form-control @error('password_confirmation', 'updatePassword') is-invalid @enderror" 
                   id="update_password_password_confirmation" 
                   name="password_confirmation" 
                   autocomplete="new-password">
            @error('password_confirmation', 'updatePassword')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-key me-2"></i> Đổi mật khẩu
            </button>

            @if (session('status') === 'password-updated')
                <span class="text-success">
                    <i class="fas fa-check-circle me-1"></i> Đã cập nhật!
                </span>
            @endif
        </div>
    </form>
</section>
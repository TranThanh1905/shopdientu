{{-- resources/views/profile/partials/update-profile-information-form.blade.php --}}
<section>
    <header class="mb-4">
        <p class="text-muted mb-0">
            Cập nhật thông tin tài khoản của bạn.
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}">
        @csrf
        @method('patch')

        <!-- Name -->
        <div class="mb-3">
            <label for="name" class="form-label">
                Họ và tên <span class="text-danger">*</span>
            </label>
            <input type="text" 
                   class="form-control @error('name') is-invalid @enderror" 
                   id="name" 
                   name="name" 
                   value="{{ old('name', $user->name) }}" 
                   required 
                   autofocus>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <!-- Email -->
        <div class="mb-3">
            <label for="email" class="form-label">
                Email <span class="text-danger">*</span>
            </label>
            <input type="email" 
                   class="form-control @error('email') is-invalid @enderror" 
                   id="email" 
                   name="email" 
                   value="{{ old('email', $user->email) }}" 
                   required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="alert alert-warning mt-2">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Email của bạn chưa được xác thực.
                    
                    <form method="post" action="{{ route('verification.send') }}" class="d-inline">
                        @csrf
                        <button type="submit" class="btn btn-link p-0 align-baseline">
                            Gửi lại email xác thực
                        </button>
                    </form>

                    @if (session('status') === 'verification-link-sent')
                        <div class="alert alert-success mt-2">
                            Email xác thực mới đã được gửi đến địa chỉ của bạn.
                        </div>
                    @endif
                </div>
            @endif
        </div>

        <!-- Customer Code (Read-only for users) -->
        @if($user->role === 'user' && $user->customer_code)
            <div class="mb-3">
                <label class="form-label">Mã khách hàng</label>
                <input type="text" 
                       class="form-control bg-light" 
                       value="{{ $user->customer_code }}" 
                       readonly>
                <small class="form-text text-muted">
                    Mã khách hàng được tạo tự động và không thể thay đổi.
                </small>
            </div>
        @endif

        <div class="d-flex align-items-center gap-3">
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-2"></i> Lưu thay đổi
            </button>

            @if (session('status') === 'profile-updated')
                <span class="text-success">
                    <i class="fas fa-check-circle me-1"></i> Đã lưu!
                </span>
            @endif
        </div>
    </form>
</section>
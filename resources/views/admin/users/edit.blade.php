@extends('admin.layouts.admin')

@section('title', 'Sửa người dùng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-user-edit"></i> Sửa thông tin người dùng</h2>
    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Quay lại
    </a>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body">
        <form method="POST" action="{{ route('admin.users.update', $user->id) }}">
            @csrf
            @method('PUT')

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Họ tên <span class="text-danger">*</span></label>
                    <input type="text"
                           name="name"
                           class="form-control @error('name') is-invalid @enderror"
                           value="{{ old('name', $user->name) }}"
                           required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Email <span class="text-danger">*</span></label>
                    <input type="email"
                           name="email"
                           class="form-control @error('email') is-invalid @enderror"
                           value="{{ old('email', $user->email) }}"
                           required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Mật khẩu mới</label>
                    <input type="password"
                           name="password"
                           class="form-control @error('password') is-invalid @enderror"
                           placeholder="Để trống nếu không đổi mật khẩu"
                           autocomplete="new-password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="text-muted">
                        <i class="fas fa-info-circle"></i>
                        Chỉ nhập nếu muốn thay đổi mật khẩu
                    </small>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Xác nhận mật khẩu mới</label>
                    <input type="password"
                           name="password_confirmation"
                           class="form-control"
                           placeholder="Nhập lại mật khẩu mới"
                           autocomplete="new-password">
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                <select name="role"
                        class="form-select @error('role') is-invalid @enderror"
                        required
                        {{ $user->id === auth()->id() ? 'disabled' : '' }}>
                    <option value="user" {{ old('role', $user->role) == 'user' ? 'selected' : '' }}>
                        User (Người dùng)
                    </option>
                    <option value="admin" {{ old('role', $user->role) == 'admin' ? 'selected' : '' }}>
                        Admin (Quản trị viên)
                    </option>
                </select>
                @if($user->id === auth()->id())
                    <input type="hidden" name="role" value="{{ $user->role }}">
                    <small class="text-warning d-block mt-1">
                        <i class="fas fa-exclamation-triangle"></i>
                        Bạn không thể thay đổi vai trò của chính mình
                    </small>
                @endif
                @error('role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Thông tin bổ sung -->
            <div class="alert alert-info">
                <strong><i class="fas fa-info-circle"></i> Thông tin tài khoản:</strong>
                <ul class="mb-0 mt-2">
                    <li>ID: <strong>#{{ $user->id }}</strong></li>
                    <li>Ngày tạo: <strong>{{ $user->created_at->format('d/m/Y H:i') }}</strong></li>
                    <li>Cập nhật lần cuối: <strong>{{ $user->updated_at->format('d/m/Y H:i') }}</strong></li>
                </ul>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Cập nhật
                </button>
                <a href="{{ route('admin.users.index') }}" class="btn btn-secondary">
                    <i class="fas fa-times"></i> Hủy
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
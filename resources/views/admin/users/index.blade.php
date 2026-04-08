@extends('admin.layouts.admin')

@section('title', 'Quản lý người dùng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-users"></i> Quản lý người dùng</h2>
    <a href="{{ route('admin.users.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Thêm người dùng
    </a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th width="50">ID</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Vai trò</th>
                        <th>Ngày tạo</th>
                        <th width="200" class="text-center">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>
                                <strong>{{ $user->name }}</strong>
                                @if($user->id === auth()->id())
                                    <span class="badge bg-info">Bạn</span>
                                @endif
                            </td>
                            <td>{{ $user->email }}</td>
                            <td>
                                @if($user->role === 'admin')
                                    <span class="badge bg-danger">
                                        <i class="fas fa-shield-alt"></i> Admin
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-user"></i> User
                                    </span>
                                @endif
                            </td>
                            <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <!-- Nút SỬA -->
                                    <a href="{{ route('admin.users.edit', $user->id) }}"
                                       class="btn btn-sm btn-warning"
                                       title="Sửa">
                                        <i class="fas fa-edit"></i>
                                    </a>

                                    @if($user->id !== auth()->id())
                                        <!-- Nút thay đổi vai trò -->
                                        <form method="POST"
                                              action="{{ route('admin.users.changeRole', $user->id) }}"
                                              class="d-inline">
                                            @csrf
                                            <input type="hidden"
                                                   name="role"
                                                   value="{{ $user->role === 'admin' ? 'user' : 'admin' }}">
                                            <button type="submit"
                                                    class="btn btn-sm btn-info"
                                                    title="{{ $user->role === 'admin' ? 'Chuyển thành User' : 'Chuyển thành Admin' }}"
                                                    onclick="return confirm('Bạn có chắc muốn thay đổi vai trò?')">
                                                <i class="fas fa-exchange-alt"></i>
                                            </button>
                                        </form>

                                        <!-- Nút XÓA với Modal -->
                                        <button type="button"
                                                class="btn btn-sm btn-danger"
                                                data-bs-toggle="modal"
                                                data-bs-target="#deleteModal-{{ $user->id }}"
                                                title="Xóa">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted">
                                <i class="fas fa-inbox fa-3x mb-3"></i>
                                <p>Không có người dùng nào</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="mt-3">
            {{ $users->links() }}
        </div>
    </div>
</div>

<!-- Delete Modals -->
@foreach($users as $user)
    @if($user->id !== auth()->id())
    <div class="modal fade" id="deleteModal-{{ $user->id }}" tabindex="-1" aria-labelledby="deleteModalLabel-{{ $user->id }}" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel-{{ $user->id }}">
                        <i class="fas fa-exclamation-triangle"></i> Xác nhận xóa
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Bạn có chắc chắn muốn xóa người dùng <strong>{{ $user->name }}</strong>?</p>
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-circle"></i>
                        <strong>Lưu ý:</strong> Hành động này không thể hoàn tác!
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Hủy
                    </button>
                    <form method="POST" action="{{ route('admin.users.destroy', $user->id) }}" class="d-inline">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">
                            <i class="fas fa-trash"></i> Xóa người dùng
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
@endforeach
@endsection
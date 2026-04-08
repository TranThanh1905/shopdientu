{{-- resources/views/profile/partials/delete-user-form.blade.php --}}
<section>
    <header class="mb-4">
        <p class="text-muted mb-0">
            Sau khi xóa tài khoản, tất cả dữ liệu và thông tin sẽ bị xóa vĩnh viễn. 
            Trước khi xóa, vui lòng tải xuống bất kỳ dữ liệu nào bạn muốn giữ lại.
        </p>
    </header>

    <!-- Toggle Delete Form Button -->
    <button type="button" 
            class="btn btn-danger" 
            onclick="toggleDeleteForm()">
        <i class="fas fa-trash me-2"></i> Xóa tài khoản
    </button>

    <!-- Delete Form (Hidden by default) -->
    <div id="deleteAccountForm" style="display: none;" class="mt-4">
        <div class="alert alert-danger">
            <h6 class="alert-heading">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Xác nhận xóa tài khoản
            </h6>
            <p class="mb-0">
                Bạn có chắc chắn muốn xóa tài khoản của mình? 
                Hành động này không thể hoàn tác.
            </p>
        </div>

        <form method="post" action="{{ route('profile.destroy') }}" onsubmit="return confirmDelete()">
            @csrf
            @method('delete')

            <div class="mb-3">
                <label for="delete_password" class="form-label">
                    Nhập mật khẩu để xác nhận <span class="text-danger">*</span>
                </label>
                <input type="password" 
                       class="form-control @error('password', 'userDeletion') is-invalid @enderror" 
                       id="delete_password" 
                       name="password" 
                       placeholder="Nhập mật khẩu của bạn"
                       autocomplete="current-password"
                       required>
                @error('password', 'userDeletion')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-trash me-2"></i> Xác nhận xóa tài khoản
                </button>
                <button type="button" class="btn btn-secondary" onclick="toggleDeleteForm()">
                    <i class="fas fa-times me-2"></i> Hủy
                </button>
            </div>
        </form>
    </div>
</section>

<script>
    function toggleDeleteForm() {
        const form = document.getElementById('deleteAccountForm');
        if (form.style.display === 'none') {
            form.style.display = 'block';
            // Scroll to form
            form.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        } else {
            form.style.display = 'none';
        }
    }

    function confirmDelete() {
        return confirm('⚠️ BẠN CHẮC CHẮN MUỐN XÓA TÀI KHOẢN?\n\nHành động này sẽ:\n- Xóa vĩnh viễn tất cả dữ liệu\n- Xóa tất cả đơn hàng\n- Không thể khôi phục\n\nNhấn OK để tiếp tục xóa tài khoản.');
    }

    // Show form if there are validation errors
    @if ($errors->userDeletion->any())
        document.addEventListener('DOMContentLoaded', function() {
            toggleDeleteForm();
            document.getElementById('delete_password').focus();
        });
    @endif
</script>
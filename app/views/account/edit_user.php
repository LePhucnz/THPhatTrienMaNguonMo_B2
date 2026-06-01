<?php include 'app/views/shares/header.php'; ?>

<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-7">
            <div class="card shadow">
                <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-user-edit"></i> Sửa người dùng
                    </h5>
                    <a href="/Account/manageUsers" class="btn btn-sm btn-secondary">
                        ← Quay lại
                    </a>
                </div>
                <div class="card-body p-4">

                    <?php if(!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach($errors as $e): ?>
                                    <li><?= htmlspecialchars($e) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="/Account/updateUser">
                        <input type="hidden" name="id" value="<?= $account->id ?>">

                       
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-info-circle"></i> Thông tin cơ bản
                        </h6>

                        <div class="mb-3">
                            <label>Username</label>
                            <input type="text" class="form-control bg-light"
                                   value="<?= htmlspecialchars($account->username) ?>" 
                                   disabled>
                            <small class="text-muted">Không thể thay đổi username</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="fullname" class="form-control"
                                       value="<?= htmlspecialchars($account->fullname) ?>" 
                                       required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Số điện thoại</label>
                                <input type="text" name="phone" class="form-control"
                                       value="<?= htmlspecialchars($account->phone ?? '') ?>">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label>Địa chỉ</label>
                            <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($account->address ?? '') ?></textarea>
                        </div>

                        <hr>

                     
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-user-shield"></i> Phân quyền
                        </h6>

                        <div class="mb-3">
                            <label>Role <span class="text-danger">*</span></label>
                            <select name="role" class="form-select"
                                    <?= ($account->id == $_SESSION['user_id']) ? 'disabled' : '' ?>>
                                <option value="user"  <?= $account->role === 'user'  ? 'selected' : '' ?>>
                                    👤 User
                                </option>
                                <option value="admin" <?= $account->role === 'admin' ? 'selected' : '' ?>>
                                    👑 Admin
                                </option>
                            </select>
                            <?php if($account->id == $_SESSION['user_id']): ?>
                                <small class="text-warning">
                                    ⚠️ Không thể tự đổi role của chính mình
                                </small>
                                
                                <input type="hidden" name="role" value="<?= $account->role ?>">
                            <?php endif; ?>
                        </div>

                        <hr>

                        
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-key"></i> Đặt lại mật khẩu
                            <small class="text-muted fw-normal">(để trống nếu không đổi)</small>
                        </h6>

                        <div class="mb-4">
                            <label>Mật khẩu mới</label>
                            <input type="password" name="new_password" 
                                   class="form-control" minlength="6"
                                   placeholder="Nhập mật khẩu mới...">
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success flex-grow-1">
                                <i class="fas fa-save"></i> Lưu thay đổi
                            </button>
                            <a href="/Account/manageUsers" class="btn btn-secondary">
                                Hủy
                            </a>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>
<?php include 'app/views/shares/header.php'; ?>

<div class="container mt-4">
    <div class="row">
        <!-- Cột trái: Avatar -->
        <div class="col-md-3 text-center mb-4">
            <div class="card shadow p-3">
                <?php 
                $avatarSrc = !empty($account->avatar) 
                    ? '/public/' . $account->avatar 
                    : 'https://ui-avatars.com/api/?name=' . urlencode($account->fullname) . '&size=150&background=28a745&color=fff';
                ?>
                <img src="<?= $avatarSrc ?>" class="rounded-circle mb-3" 
                     style="width:150px;height:150px;object-fit:cover;">
                <h5><?= htmlspecialchars($account->fullname) ?></h5>
                <span class="badge <?= $account->role == 'admin' ? 'badge-danger' : 'badge-info' ?>">
                    <?= ucfirst($account->role) ?>
                </span>
                <hr>
                <a href="/Account/changePassword" class="btn btn-outline-warning btn-sm btn-block w-100">
                    <i class="fas fa-key"></i> Đổi mật khẩu
                </a>
                <a href="/Account/setupSecurity" class="btn btn-outline-primary btn-sm btn-block w-100 mt-2">
                    <i class="fas fa-shield-alt"></i> Câu hỏi bảo mật
                </a>
            </div>
        </div>

        <!-- Cột phải: Thông tin -->
        <div class="col-md-9">
            <?php if(isset($success)): ?>
                <div class="alert alert-success"><?= $success ?></div>
            <?php endif; ?>
            <?php if(!empty($errors)): ?>
                <div class="alert alert-danger">
                    <ul class="mb-0"><?php foreach($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?></ul>
                </div>
            <?php endif; ?>

            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-user-edit"></i> Hồ sơ cá nhân</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="/Account/updateProfile" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Username</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($account->username) ?>" disabled>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="fullname" class="form-control" 
                                       value="<?= htmlspecialchars($account->fullname) ?>" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label>Email</label>
                            <input type="email" name="email" class="form-control"
                                value="<?= htmlspecialchars($account->email ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label>Số điện thoại</label>
                            <input type="text" name="phone" class="form-control"
                                   value="<?= htmlspecialchars($account->phone ?? '') ?>">
                        </div>
                        <div class="mb-3">
                            <label>Địa chỉ</label>
                            <textarea name="address" class="form-control" rows="2"><?= htmlspecialchars($account->address ?? '') ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label>Ảnh đại diện</label>
                            <input type="file" name="avatar" class="form-control" accept="image/*">
                            <small class="text-muted">JPG, PNG, WebP — tối đa 2MB</small>
                        </div>
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Lưu thay đổi
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>
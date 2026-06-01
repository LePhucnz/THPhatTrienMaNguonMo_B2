<?php include 'app/views/shares/header.php'; ?>

<h3><i class="fas fa-users-cog"></i> Quản lý người dùng</h3>

<div class="table-responsive mt-3">
    <table class="table table-bordered table-hover">
        <thead class="thead-dark">
            <tr>
                <th>#</th>
                <th>Avatar</th>
                <th>Username</th>
                <th>Họ tên</th>
                <th>Phone</th>
                <th>Role</th>
                <th>Trạng thái</th>
                <th>Thao tác</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach($accounts as $acc): ?>
            <tr class="<?= $acc->is_locked ? 'table-danger' : '' ?>">
                <td><?= $acc->id ?></td>
                <td>
                    <?php $src = !empty($acc->avatar) ? '/public/'.$acc->avatar : 'https://ui-avatars.com/api/?name='.urlencode($acc->fullname).'&size=40&background=28a745&color=fff'; ?>
                    <img src="<?= $src ?>" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;">
                </td>
                <td><code><?= htmlspecialchars($acc->username) ?></code></td>
                <td><?= htmlspecialchars($acc->fullname) ?></td>
                <td><?= htmlspecialchars($acc->phone ?? '-') ?></td>
                <td>
                    <span class="badge <?= $acc->role == 'admin' ? 'badge-danger' : 'badge-info' ?>">
                        <?= ucfirst($acc->role) ?>
                    </span>
                </td>
                <td>
                    <?php if($acc->is_locked): ?>
                        <span class="badge badge-danger">🔒 Đã khóa</span>
                    <?php else: ?>
                        <span class="badge badge-success">✅ Hoạt động</span>
                    <?php endif; ?>
                </td>
                <td>
                    <?php if($acc->id != $_SESSION['user_id']): ?>
                      
                        <a href="/Account/editUser/<?= $acc->id ?>" 
                        class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> Sửa
                        </a>

                      
                        <a href="/Account/toggleLock/<?= $acc->id ?>" 
                        class="btn btn-sm <?= $acc->is_locked ? 'btn-success' : 'btn-warning' ?>"
                        onclick="return confirm('<?= $acc->is_locked ? 'Mở khóa' : 'Khóa' ?> tài khoản này?')">
                            <i class="fas fa-<?= $acc->is_locked ? 'lock-open' : 'lock' ?>"></i>
                            <?= $acc->is_locked ? 'Mở khóa' : 'Khóa' ?>
                        </a>

                      
                        <a href="/Account/deleteUser/<?= $acc->id ?>" 
                        class="btn btn-sm btn-danger"
                        onclick="return confirm('Xóa tài khoản này?')">
                            <i class="fas fa-trash"></i>
                        </a>
                    <?php else: ?>
                        
                        <a href="/Account/editUser/<?= $acc->id ?>" 
                        class="btn btn-sm btn-primary">
                            <i class="fas fa-edit"></i> Sửa
                        </a>
                        <span class="text-muted ms-1">(Tài khoản của bạn)</span>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?php include 'app/views/shares/footer.php'; ?>
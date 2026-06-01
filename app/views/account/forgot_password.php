<?php include 'app/views/shares/header.php'; ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-info text-white">
                    <h5><i class="fas fa-unlock-alt"></i> Quên mật khẩu</h5>
                </div>
                <div class="card-body">
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <form method="POST" action="/Account/verifyUsername">
                        <div class="mb-3">
                            <label>Nhập Username của bạn</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-info w-100 text-white">
                            Tiếp theo →
                        </button>
                        <a href="/Account/login" class="btn btn-secondary w-100 mt-2">← Quay lại</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'app/views/shares/footer.php'; ?>
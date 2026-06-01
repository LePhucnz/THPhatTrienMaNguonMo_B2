<?php include 'app/views/shares/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-dark text-white text-center py-4">
                    <h3><i class="fas fa-sign-in-alt"></i> Đăng nhập</h3>
                </div>
                <div class="card-body p-4">
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?= $error ?></div>
                    <?php endif; ?>

                    <form action="/Account/checklogin" method="post">
                    <div class="form-group mb-3">
                        <label>Username hoặc Email</label>
                        <input type="text" name="username" class="form-control" 
                            placeholder="Nhập username hoặc email..."
                            required
                            value="<?= htmlspecialchars($_POST['username'] ?? '') ?>">
                    </div>
                        <div class="form-group mb-3">
                            <label>Mật khẩu</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>

                        <!-- ✅ Remember Me -->
                        <div class="form-group mb-3 d-flex justify-content-between align-items-center">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" name="remember_me" id="remember_me">
                                <label class="form-check-label" for="remember_me">Ghi nhớ đăng nhập</label>
                            </div>
                            <a href="/Account/forgotPassword" class="text-muted small">Quên mật khẩu?</a>
                        </div>

                        <button type="submit" class="btn btn-primary btn-block w-100">
                            <i class="fas fa-sign-in-alt"></i> Đăng nhập
                        </button>
                    </form>

                    <hr>
                    <p class="text-center mb-0">
                        Chưa có tài khoản? 
                        <a href="/Account/register">Đăng ký ngay</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>
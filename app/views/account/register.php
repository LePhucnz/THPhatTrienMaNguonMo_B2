<?php include 'app/views/shares/header.php'; ?>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white text-center py-3">
                    <h4><i class="fas fa-user-plus"></i> Đăng ký tài khoản</h4>
                </div>
                <div class="card-body p-4">

                    <?php if(isset($errors['account'])): ?>
                        <div class="alert alert-danger"><?= $errors['account'] ?></div>
                    <?php endif; ?>

                    <form action="/Account/save" method="POST">

                        
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-user"></i> Thông tin cơ bản
                        </h6>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Username <span class="text-danger">*</span></label>
                                <input type="text" name="username" class="form-control
                                       <?= isset($errors['username']) ? 'is-invalid' : '' ?>"
                                       value="<?= htmlspecialchars($_POST['username'] ?? '') ?>"
                                       required>
                                <?php if(isset($errors['username'])): ?>
                                    <div class="invalid-feedback"><?= $errors['username'] ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" name="fullname" class="form-control
                                       <?= isset($errors['fullname']) ? 'is-invalid' : '' ?>"
                                       value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>"
                                       required>
                                <?php if(isset($errors['fullname'])): ?>
                                    <div class="invalid-feedback"><?= $errors['fullname'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label>Mật khẩu <span class="text-danger">*</span></label>
                                <input type="password" name="password" class="form-control
                                       <?= isset($errors['password']) ? 'is-invalid' : '' ?>"
                                       required minlength="6">
                                <?php if(isset($errors['password'])): ?>
                                    <div class="invalid-feedback"><?= $errors['password'] ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label>Xác nhận mật khẩu <span class="text-danger">*</span></label>
                                <input type="password" name="confirmpassword" class="form-control
                                       <?= isset($errors['confirmPass']) ? 'is-invalid' : '' ?>"
                                       required>
                                <?php if(isset($errors['confirmPass'])): ?>
                                    <div class="invalid-feedback"><?= $errors['confirmPass'] ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <hr>

                        
                        <h6 class="text-muted mb-3">
                            <i class="fas fa-shield-alt"></i> Câu hỏi bảo mật
                            <small class="text-muted fw-normal">(dùng khi quên mật khẩu)</small>
                        </h6>

                        <div class="mb-3">
                            <label>Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control
                                <?= isset($errors['email']) ? 'is-invalid' : '' ?>"
                                value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                                placeholder="example@gmail.com"
                                required>
                            <?php if(isset($errors['email'])): ?>
                                <div class="invalid-feedback"><?= $errors['email'] ?></div>
                            <?php endif; ?>
                            <label>Chọn câu hỏi <span class="text-danger">*</span></label>
                            <select name="security_question" class="form-select
                                    <?= isset($errors['security_question']) ? 'is-invalid' : '' ?>"
                                    required>
                                <option value="">-- Chọn câu hỏi bảo mật --</option>
                                <?php
                                $questions = [
                                    "Tên thú cưng đầu tiên của bạn?",
                                    "Tên trường tiểu học của bạn?",
                                    "Tên người thân mà bạn yêu quý nhất?",
                                    "Món ăn yêu thích của bạn?",
                                    "Tên thành phố bạn sinh ra?",
                                ];
                                foreach ($questions as $q):
                                    $selected = (($_POST['security_question'] ?? '') === $q) ? 'selected' : '';
                                ?>
                                    <option value="<?= $q ?>" <?= $selected ?>><?= $q ?></option>
                                <?php endforeach; ?>
                            </select>
                            <?php if(isset($errors['security_question'])): ?>
                                <div class="invalid-feedback"><?= $errors['security_question'] ?></div>
                            <?php endif; ?>
                        </div>

                        <div class="mb-4">
                            <label>Câu trả lời <span class="text-danger">*</span></label>
                            <input type="text" name="security_answer" class="form-control
                                   <?= isset($errors['security_answer']) ? 'is-invalid' : '' ?>"
                                   value="<?= htmlspecialchars($_POST['security_answer'] ?? '') ?>"
                                   required autocomplete="off">
                            <small class="text-muted">Không phân biệt hoa/thường</small>
                            <?php if(isset($errors['security_answer'])): ?>
                                <div class="invalid-feedback"><?= $errors['security_answer'] ?></div>
                            <?php endif; ?>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-user-plus"></i> Đăng ký
                        </button>

                        <p class="text-center mt-3 mb-0">
                            Đã có tài khoản? 
                            <a href="/Account/login">Đăng nhập ngay</a>
                        </p>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>
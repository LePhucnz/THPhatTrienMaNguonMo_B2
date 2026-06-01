<?php include 'app/views/shares/header.php'; ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h5><i class="fas fa-shield-alt"></i> Thiết lập câu hỏi bảo mật</h5>
                </div>
                <div class="card-body">
                    <?php if(isset($success)): ?>
                        <div class="alert alert-success"><?= $success ?></div>
                    <?php endif; ?>
                    <?php if(!empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach($errors as $e): ?>
                                    <li><?= htmlspecialchars($e) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="/Account/saveSecurity">
                        <div class="mb-3">
                            <label>Chọn câu hỏi bảo mật</label>
                            <select name="security_question" class="form-select" required>
                                <option value="">-- Chọn câu hỏi --</option>
                                <option>Tên thú cưng đầu tiên của bạn?</option>
                                <option>Tên trường tiểu học của bạn?</option>
                                <option>Tên người thân mà bạn yêu quý nhất?</option>
                                <option>Món ăn yêu thích của bạn?</option>
                                <option>Tên thành phố bạn sinh ra?</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label>Câu trả lời</label>
                            <input type="text" name="security_answer" 
                                   class="form-control" required>
                            <small class="text-muted">Không phân biệt hoa/thường</small>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save"></i> Lưu
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'app/views/shares/footer.php'; ?>
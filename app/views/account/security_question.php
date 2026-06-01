<?php include 'app/views/shares/header.php'; ?>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow">
                <div class="card-header bg-warning text-dark">
                    <h5><i class="fas fa-shield-alt"></i> Xác minh bảo mật</h5>
                </div>
                <div class="card-body">
                    <?php if(isset($error)): ?>
                        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
                    <?php endif; ?>
                    <p class="fw-bold text-secondary"><?= htmlspecialchars($question) ?></p>
                    <form method="POST" action="/Account/verifyAnswer">
                        <div class="mb-3">
                            <label>Câu trả lời của bạn</label>
                            <input type="text" name="answer" class="form-control" 
                                   required autocomplete="off">
                            <small class="text-muted">Không phân biệt hoa/thường</small>
                        </div>
                        <button type="submit" class="btn btn-warning w-100">
                            Xác nhận →
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'app/views/shares/footer.php'; ?>
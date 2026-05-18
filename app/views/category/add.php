<?php include 'app/views/shares/header.php'; ?>

<div class="container mt-4">
    <h1>
        <i class="fas fa-plus-circle"></i> Thêm danh mục mới
    </h1>

    <?php if(!empty($errors)): ?>
    <div class="alert alert-danger">
        <ul class="mb-0">
            <?php foreach($errors as $error): ?>
                <li><?php echo htmlspecialchars($error); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php endif; ?>

    <div class="card">
        <div class="card-body">
            <form method="POST" action="/Category/save">
                <div class="form-group">
                    <label for="name">Tên danh mục:</label>
                    <input type="text" class="form-control" id="name" name="name" 
                           placeholder="Nhập tên danh mục" required>
                </div>

                <div class="form-group">
                    <label for="description">Mô tả:</label>
                    <textarea class="form-control" id="description" name="description" 
                              rows="4" placeholder="Nhập mô tả"></textarea>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Lưu danh mục
                </button>
                <a href="/Category/list" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Quay lại
                </a>
            </form>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>
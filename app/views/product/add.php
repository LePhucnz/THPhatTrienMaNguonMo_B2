<?php include 'app/views/shares/header.php'; ?>

<h1>Thêm sản phẩm mới</h1>

<?php if(!empty($errors)): ?>
<div class="alert alert-danger">
    <ul class="mb-0">
        <?php foreach($errors as $error): ?>
            <li><?php echo htmlspecialchars($error); ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST" action="/Product/save" enctype="multipart/form-data">
    <div class="form-group">
        <label for="name">Tên sản phẩm:</label>
        <input type="text" class="form-control" id="name" name="name" required>
    </div>

    <div class="form-group">
        <label for="description">Mô tả:</label>
        <textarea class="form-control" id="description" name="description" rows="4" required></textarea>
    </div>

    <div class="form-group">
        <label for="price">Giá:</label>
        <input type="number" class="form-control" id="price" name="price" step="0.01" required>
    </div>

    <div class="form-group">
        <label for="category_id">Danh mục:</label>
        <select class="form-control" id="category_id" name="category_id" required>
            <?php foreach($categories as $category): ?>
                <option value="<?php echo $category->id; ?>">
                    <?php echo htmlspecialchars($category->name); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="form-group">
        <label for="image">Hình ảnh:</label>
        <input type="file" class="form-control" id="image" name="image" accept="image/*">
        <small class="form-text text-muted">Chỉ chấp nhận: JPG, JPEG, PNG, GIF (Tối đa 10MB)</small>
    </div>

    <button type="submit" class="btn btn-primary">
        <i class="fa fa-save"></i> Thêm sản phẩm
    </button>
    <a href="/Product" class="btn btn-secondary">
        <i class="fa fa-arrow-left"></i> Quay lại
    </a>
</form>

<?php include 'app/views/shares/footer.php'; ?>
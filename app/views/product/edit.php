<?php include 'app/views/shares/header.php'; ?>

<h1>Sửa sản phẩm</h1>

<?php if(!empty($errors)): ?>
<div class="alert alert-danger">
    <ul>
        <?php foreach($errors as $error): ?>
            <li><?php echo htmlspecialchars($error); ?></li>
        <?php endforeach; ?>
    </ul>
</div>
<?php endif; ?>

<form method="POST" action="/Product/update" enctype="multipart/form-data">
    <input type="hidden" name="id" value="<?php echo $product->id; ?>">
    
    <div class="form-group">
        <label for="name">Tên sản phẩm:</label>
        <input type="text" id="name" name="name" class="form-control" value="<?php echo htmlspecialchars($product->name); ?>" required>
    </div>

    <div class="form-group">
        <label for="description">Mô tả:</label>
        <textarea id="description" name="description" class="form-control" required><?php echo htmlspecialchars($product->description); ?></textarea>
    </div>

    <div class="form-group">
        <label for="price">Giá:</label>
        <input type="number" id="price" name="price" class="form-control" step="0.01" value="<?php echo htmlspecialchars($product->price); ?>" required>
    </div>

    <div class="form-group">
        <label for="category_id">Danh mục:</label>
        <select id="category_id" name="category_id" class="form-control" required>
            <?php foreach($categories as $category): ?>
                <option value="<?php echo $category->id; ?>" <?php echo ($category->id == $product->category_id) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($category->name); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- PHẦN XỬ LÝ HÌNH ẢNH -->
    <div class="form-group">
        <label for="image">Hình ảnh mới:</label>
        <input type="file" id="image" name="image" class="form-control" accept="image/*">
        <input type="hidden" name="existing_image" value="<?php echo $product->image; ?>">
        
        <div class="mt-2">
            <label>Hình ảnh hiện tại:</label><br>
            <?php if($product->image && file_exists('public/' . $product->image)): ?>
                <img id="currentImage" src="/public/<?php echo $product->image; ?>" alt="Product Image" style="max-width: 150px; margin-bottom: 10px;">
            <?php else: ?>
                <div class="alert alert-info d-inline-block">Chưa có hình ảnh</div>
            <?php endif; ?>
            
            <img id="previewImage" src="" alt="Preview" style="max-width: 150px; display: none; border: 2px dashed #ccc; margin-top: 10px;">
        </div>
    </div>

    <button type="submit" class="btn btn-primary">Lưu thay đổi</button>
    <a href="/Product" class="btn btn-secondary mt-2">Quay lại danh sách sản phẩm</a>
</form>

<script>
    document.getElementById('image')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const previewImage = document.getElementById('previewImage');
                const currentImage = document.getElementById('currentImage');
                
                previewImage.src = e.target.result;
                previewImage.style.display = 'block';
                if (currentImage) currentImage.style.display = 'none';
            }
            reader.readAsDataURL(file);
        }
    });
</script>

<?php include 'app/views/shares/footer.php'; ?>
<?php include 'app/views/shares/header.php'; ?>

<!-- Filter Section -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0"><i class="fas fa-filter"></i> Bộ lọc sản phẩm</h5>
    </div>
    <div class="card-body">
        <form method="GET" action="/Product" class="row g-3 align-items-end">
            <!-- Lọc theo danh mục -->
            <div class="col-md-3">
                <label class="form-label">Danh mục</label>
                <select name="category" class="form-select">
                    <option value="">Tất cả</option>
                    <?php 
                    $db = (new Database())->getConnection();
                    $cats = $db->query("SELECT * FROM category ORDER BY name")->fetchAll(PDO::FETCH_OBJ);
                    foreach($cats as $cat): 
                    ?>
                    <option value="<?php echo $cat->id; ?>" 
                            <?php echo (isset($_GET['category']) && $_GET['category'] == $cat->id) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($cat->name); ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Lọc theo giá min -->
            <div class="col-md-2">
                <label class="form-label">Giá từ</label>
                <input type="number" name="min_price" class="form-control" 
                       placeholder="0" min="0" step="1000"
                       value="<?php echo isset($_GET['min_price']) ? htmlspecialchars($_GET['min_price']) : ''; ?>">
            </div>
            
            <!-- Lọc theo giá max -->
            <div class="col-md-2">
                <label class="form-label">Đến</label>
                <input type="number" name="max_price" class="form-control" 
                       placeholder="100.000.000" min="0" step="1000"
                       value="<?php echo isset($_GET['max_price']) ? htmlspecialchars($_GET['max_price']) : ''; ?>">
            </div>
            
            <!-- Nút lọc -->
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-search"></i> Lọc
                </button>
            </div>
            
            <!-- Nút xóa lọc -->
            <div class="col-md-2">
                <a href="/Product" class="btn btn-outline-secondary w-100">
                    <i class="fas fa-times"></i> Reset
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Hiển thị thông báo đang lọc -->
<?php 
$hasFilter = isset($_GET['category']) || isset($_GET['min_price']) || isset($_GET['max_price']) || isset($_GET['keyword']);
if ($hasFilter): 
?>
<div class="alert alert-info d-flex justify-content-between align-items-center">
    <div>
        <i class="fas fa-info-circle"></i> 
        <?php if(isset($_GET['keyword'])): ?>
            Đang tìm kiếm: <strong>"<?php echo htmlspecialchars($_GET['keyword']); ?>"</strong>
        <?php elseif(isset($_GET['category'])): ?>
            Danh mục: <strong><?php 
                $catId = (int)$_GET['category'];
                $db = (new Database())->getConnection();
                $cat = $db->prepare("SELECT name FROM category WHERE id = :id");
                $cat->bindParam(':id', $catId, PDO::PARAM_INT);
                $cat->execute();
                $c = $cat->fetch(PDO::FETCH_OBJ);
                echo $c ? htmlspecialchars($c->name) : 'Tất cả';
            ?></strong>
        <?php endif; ?>
        
        <?php if(isset($_GET['min_price']) || isset($_GET['max_price'])): ?>
            <?php if(isset($_GET['keyword']) || isset($_GET['category'])) echo ' - '; ?>
            Giá: 
            <strong><?php echo !empty($_GET['min_price']) ? number_format((float)$_GET['min_price'], 0, ',', '.') : '0'; ?></strong>
            - 
            <strong><?php echo !empty($_GET['max_price']) ? number_format((float)$_GET['max_price'], 0, ',', '.') : '∞'; ?></strong> ₫
        <?php endif; ?>
    </div>
    <a href="/Product" class="btn btn-sm btn-outline-danger">
        <i class="fas fa-times"></i> Xóa lọc
    </a>
</div>
<?php endif; ?>

<!-- Danh sách sản phẩm -->
<?php if(empty($products)): ?>
<div class="alert alert-warning text-center py-5">
    <i class="fas fa-exclamation-circle fa-3x mb-3 text-muted"></i>
    <h4>Không tìm thấy sản phẩm nào</h4>
    <p class="text-muted">Hãy thử điều chỉnh bộ lọc hoặc tìm kiếm với từ khóa khác.</p>
    <a href="/Product" class="btn btn-primary">Xem tất cả sản phẩm</a>
</div>
<?php else: ?>

<div class="row">
    <?php foreach($products as $product): ?>
    <div class="col-md-4 mb-4">
        <div class="card h-100 shadow-sm">
            <?php if($product->image): ?>
                <img src="/public/<?php echo htmlspecialchars($product->image); ?>" 
                     class="card-img-top" 
                     alt="<?php echo htmlspecialchars($product->name); ?>"
                     style="height: 200px; object-fit: cover;">
            <?php else: ?>
                <div class="card-img-top bg-secondary text-white d-flex align-items-center justify-content-center" 
                     style="height: 200px;">
                    <i class="fas fa-image fa-3x"></i>
                </div>
            <?php endif; ?>
            
            <div class="card-body">
                <h5 class="card-title text-truncate" title="<?php echo htmlspecialchars($product->name); ?>">
                    <a href="/Product/show/<?php echo $product->id; ?>" class="text-dark text-decoration-none">
                        <?php echo htmlspecialchars($product->name); ?>
                    </a>
                </h5>
                <p class="card-text text-muted small" style="height: 40px; overflow: hidden;">
                    <?php echo htmlspecialchars(substr($product->description, 0, 80)); ?>...
                </p>
                <h6 class="text-danger fw-bold mb-2">
                    <?php echo number_format((float)($product->price ?? 0), 0, ',', '.'); ?> ₫
                </h6>
                <span class="badge bg-info text-dark">
                    <i class="fas fa-tag"></i> <?php echo htmlspecialchars($product->category_name); ?>
                </span>
            </div>
            
            <div class="card-footer bg-white border-top-0">
                <div class="d-flex gap-2">
                    <button onclick="addToCart(<?php echo $product->id; ?>)" 
                            class="btn btn-success btn-sm flex-grow-1" 
                            title="Thêm vào giỏ hàng">
                        <i class="fas fa-cart-plus"></i> Thêm vào giỏ
                    </button>
                    <a href="/Product/edit/<?php echo $product->id; ?>" 
                       class="btn btn-warning btn-sm flex-grow-1" title="Sửa">
                        <i class="fas fa-edit"></i>
                    </a>
                    <a href="/Product/delete/<?php echo $product->id; ?>" 
                       class="btn btn-danger btn-sm flex-grow-1" 
                       onclick="return confirm('Bạn có chắc muốn xóa?');" title="Xóa">
                        <i class="fas fa-trash"></i>
                    </a>
                    <a href="/Product/show/<?php echo $product->id; ?>" 
                       class="btn btn-primary btn-sm flex-grow-1" title="Xem chi tiết">
                        <i class="fas fa-eye"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<?php if(count($products) > 0): ?>
<div class="text-center mt-4">
    <small class="text-muted">
        Hiển thị <?php echo count($products); ?> sản phẩm
    </small>
</div>
<?php endif; ?>

<?php endif; ?>

<!-- JavaScript for AJAX Add to Cart -->
<script>
// Function to add product to cart via AJAX
function addToCart(productId) {
    const formData = new FormData();
    formData.append('product_id', productId);
    
    fetch('/Product/addToCartAjax', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Update cart badge
            updateCartBadge(data.totalItems);
            
            // Show success notification
            showNotification('success', data.message);
        } else {
            showNotification('error', data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', 'Có lỗi xảy ra. Vui lòng thử lại!');
    });
}

// Function to update cart badge
function updateCartBadge(count) {
    const badge = document.getElementById('cart-badge');
    if (badge) {
        badge.textContent = count;
        if (count > 0) {
            badge.classList.remove('d-none');
        } else {
            badge.classList.add('d-none');
        }
    }
}

// Function to show notification
function showNotification(type, message) {
    // Create notification element
    const notification = document.createElement('div');
    notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show`;
    notification.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
    notification.innerHTML = `
        ${message}
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto dismiss after 3 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
</script>

<?php include 'app/views/shares/footer.php'; ?>
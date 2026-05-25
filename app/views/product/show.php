<?php include 'app/views/shares/header.php'; ?>

<div class="container mt-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white text-center">
            <h2 class="mb-0">Chi tiết sản phẩm</h2>
        </div>
        <div class="card-body">
            <?php if($product): ?>
            <div class="row">
                <!-- Hình ảnh sản phẩm -->
                <div class="col-md-6">
                    <?php if($product->image): ?>
                        <img src="/public/<?php echo htmlspecialchars($product->image); ?>" 
                             class="img-fluid rounded" 
                             alt="<?php echo htmlspecialchars($product->name); ?>"
                             style="max-height: 500px; width: 100%; object-fit: cover;">
                    <?php else: ?>
                        <div class="bg-light text-center py-5 rounded">
                            <i class="fas fa-image fa-3x text-muted"></i>
                            <p class="mt-2">Không có ảnh</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Thông tin sản phẩm -->
                <div class="col-md-6">
                    <h3 class="text-dark font-weight-bold mb-3">
                        <?php echo htmlspecialchars($product->name); ?>
                    </h3>
                    
                    <p class="text-muted">
                        <?php echo nl2br(htmlspecialchars($product->description)); ?>
                    </p>
                    
                    <h4 class="text-danger font-weight-bold my-3">
                        <?php echo number_format((float)($product->price ?? 0), 0, ',', '.'); ?> ₫
                    </h4>
                    
                    <p class="mb-3">
                        <strong>Danh mục:</strong> 
                        <span class="badge bg-info text-dark">
                            <?php echo htmlspecialchars($product->category_name ?? 'Chưa phân loại'); ?>
                        </span>
                    </p>
                    
                    <!-- ✅ NÚT THÊM VÀO GIỎ HÀNG -->
                    <div class="mt-4 d-flex flex-wrap gap-2">
                        <button onclick="addToCart(<?php echo $product->id; ?>)" 
                                class="btn btn-success btn-lg px-4"
                                id="btn-add-to-cart">
                            <i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng
                        </button>
                        
                        <a href="/Product" class="btn btn-secondary btn-lg px-4">
                            <i class="fas fa-arrow-left"></i> Quay lại danh sách
                        </a>
                    </div>
                    
                    <!-- Đánh giá trung bình (nếu có) -->
                    <?php if(isset($averageRating) && $averageRating['total'] > 0): ?>
                    <div class="mt-4 p-3 bg-light rounded">
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                <h2 class="mb-0 text-primary font-weight-bold">
                                    <?php echo $averageRating['average']; ?>/5
                                </h2>
                                <div class="text-warning">
                                    <?php 
                                    $avg = round($averageRating['average']);
                                    for($i = 0; $i < 5; $i++): 
                                    ?>
                                        <i class="fas fa-star<?php echo $i < $avg ? '' : '-o'; ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <div>
                                <p class="mb-1 text-muted">
                                    <?php echo $averageRating['total']; ?> đánh giá
                                </p>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <hr class="my-5">

            <!-- PHẦN BÌNH LUẬN / ĐÁNH GIÁ -->
            <div class="review-section">
                <h4 class="mb-4"><i class="fas fa-comments"></i> Đánh giá & Bình luận</h4>

                <!-- Form Thêm Bình Luận -->
                <div class="card bg-light mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">Viết đánh giá của bạn</h5>
                        <form method="POST" action="/Product/saveReview">
                            <input type="hidden" name="product_id" value="<?php echo $product->id; ?>">
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tên của bạn:</label>
                                    <input type="text" name="username" class="form-control" 
                                           placeholder="Nhập tên hoặc để trống" 
                                           value="<?php 
                                           if (session_status() === PHP_SESSION_NONE) {
                                               session_start();
                                           }
                                           echo htmlspecialchars($_SESSION['username'] ?? ''); 
                                           ?>">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Số sao đánh giá:</label>
                                    <select name="rating" class="form-select">
                                        <option value="5">⭐⭐⭐⭐⭐ (5 sao - Tuyệt vời)</option>
                                        <option value="4">⭐⭐⭐⭐ (4 sao - Tốt)</option>
                                        <option value="3" selected>⭐⭐⭐ (3 sao - Trung bình)</option>
                                        <option value="2">⭐⭐ (2 sao - Kém)</option>
                                        <option value="1">⭐ (1 sao - Rất kém)</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Nội dung đánh giá:</label>
                                <textarea name="content" class="form-control" rows="4" 
                                          placeholder="Chia sẻ cảm nhận của bạn về sản phẩm..." required></textarea>
                            </div>
                            
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Gửi đánh giá
                            </button>
                        </form>
                    </div>
                </div>

                <!-- Danh sách Bình Luận -->
                <?php if (!empty($reviews)): ?>
                    <h5 class="mb-3">
                        <i class="fas fa-list"></i> 
                        <?php echo count($reviews); ?> bình luận
                    </h5>
                    <?php foreach ($reviews as $review): ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <h6 class="card-subtitle mb-1 text-primary">
                                        <i class="fas fa-user"></i> 
                                        <?php echo htmlspecialchars($review->username ?? 'Ẩn danh'); ?>
                                    </h6>
                                    <div class="text-warning mb-2">
                                        <?php for($i = 0; $i < ($review->rating ?? 0); $i++): ?>
                                            <i class="fas fa-star"></i>
                                        <?php endfor; ?>
                                    </div>
                                </div>
                                <small class="text-muted">
                                    <i class="far fa-clock"></i> 
                                    <?php echo date('d/m/Y H:i', strtotime($review->created_at ?? 'now')); ?>
                                </small>
                            </div>
                            <p class="card-text mb-0"><?php echo nl2br(htmlspecialchars($review->content)); ?></p>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="alert alert-info text-center">
                        <i class="fas fa-comment-alt fa-2x mb-2"></i>
                        <p class="mb-0">Chưa có bình luận nào. Hãy là người đầu tiên đánh giá sản phẩm này!</p>
                    </div>
                <?php endif; ?>
            </div>

            <?php else: ?>
            <div class="alert alert-danger text-center py-5">
                <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                <h4>Không tìm thấy sản phẩm!</h4>
                <a href="/Product" class="btn btn-primary mt-3">Quay lại danh sách</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- JavaScript for AJAX Add to Cart -->
<script>
// Function to add product to cart via AJAX
function addToCart(productId) {
    const btn = document.getElementById('btn-add-to-cart');
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang thêm...';
    }
    
    const formData = new FormData();
    formData.append('product_id', productId);
    
    fetch('/Product/addToCartAjax', {
        method: 'POST',
        body: formData,
        credentials: 'same-origin'
    })
    .then(response => {
        if (!response.ok) throw new Error('HTTP error: ' + response.status);
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Update cart badge
            updateCartBadge(data.totalItems);
            
            // Show success notification
            showNotification('success', data.message);
            
            // Reset button
            if (btn) {
                btn.innerHTML = '<i class="fas fa-check"></i> Đã thêm!';
                setTimeout(() => {
                    btn.innerHTML = '<i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng';
                    btn.disabled = false;
                }, 1500);
            }
        } else {
            showNotification('error', data.message || 'Có lỗi xảy ra');
            if (btn) {
                btn.innerHTML = '<i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng';
                btn.disabled = false;
            }
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('error', '❌ Lỗi kết nối. Vui lòng thử lại!');
        if (btn) {
            btn.innerHTML = '<i class="fas fa-cart-plus"></i> Thêm vào giỏ hàng';
            btn.disabled = false;
        }
    });
}

// Function to update cart badge (from header.php)
function updateCartBadge(count) {
    const badge = document.getElementById('cart-badge');
    if (badge) {
        badge.textContent = count;
        if (count > 0) {
            badge.classList.remove('d-none');
            badge.style.animation = 'none';
            setTimeout(() => badge.style.animation = 'pulse 0.3s ease', 10);
        } else {
            badge.classList.add('d-none');
        }
    }
}

// Function to show notification
function showNotification(type, message) {
    // Remove old notification
    const oldNotif = document.getElementById('ajax-notification');
    if (oldNotif) oldNotif.remove();
    
    const notification = document.createElement('div');
    notification.id = 'ajax-notification';
    notification.className = `alert alert-${type === 'success' ? 'success' : 'danger'} alert-dismissible fade show shadow`;
    notification.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 320px; max-width: 400px;';
    notification.innerHTML = `
        <strong>${type === 'success' ? '✅ Thành công!' : '❌ Lỗi!'}</strong><br>
        ${message}
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto hide after 3 seconds
    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}
</script>

<?php include 'app/views/shares/footer.php'; ?>
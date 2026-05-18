<?php include 'app/views/shares/header.php'; ?>

<div class="container mt-4">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white text-center">
            <h2 class="mb-0">Chi tiết sản phẩm</h2>
        </div>
        <div class="card-body">
            <?php if($product): ?>
            <div class="row">
                <div class="col-md-6">
                    <?php if($product->image): ?>
                        <img src="/public/<?php echo htmlspecialchars($product->image); ?>" 
                             class="img-fluid rounded" 
                             alt="<?php echo htmlspecialchars($product->name); ?>">
                    <?php else: ?>
                        <div class="bg-light text-center py-5 rounded">
                            <i class="fas fa-image fa-3x text-muted"></i>
                            <p class="mt-2">Không có ảnh</p>
                        </div>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <h3 class="text-dark font-weight-bold"><?php echo htmlspecialchars($product->name); ?></h3>
                    <p class="text-muted"><?php echo nl2br(htmlspecialchars($product->description)); ?></p>
                    <h4 class="text-danger font-weight-bold">
                        <?php echo number_format((float)($product->price ?? 0), 0, ',', '.'); ?> ₫
                    </h4>
                    <p><strong>Danh mục:</strong> 
                        <span class="badge bg-info text-dark">
                            <?php echo htmlspecialchars($product->category_name ?? 'Chưa phân loại'); ?>
                        </span>
                    </p>
                    
                    <!-- ✅ HIỂN THỊ ĐIỂM TRUNG BÌNH -->
                    <?php if(isset($averageRating)): ?>
                    <div class="mt-3 p-3 bg-light rounded">
                        <div class="d-flex align-items-center">
                            <div class="mr-3">
                                <h2 class="mb-0 text-primary font-weight-bold">
                                    <?php echo $averageRating['average']; ?>
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
                                <small class="text-muted">
                                    <?php 
                                    // Phân loại đánh giá
                                    if($averageRating['average'] >= 4.5) {
                                        echo '<span class="text-success"><i class="fas fa-check-circle"></i> Xuất sắc</span>';
                                    } elseif($averageRating['average'] >= 4) {
                                        echo '<span class="text-primary"><i class="fas fa-thumbs-up"></i> Rất tốt</span>';
                                    } elseif($averageRating['average'] >= 3) {
                                        echo '<span class="text-warning"><i class="fas fa-minus-circle"></i> Trung bình</span>';
                                    } else {
                                        echo '<span class="text-danger"><i class="fas fa-times-circle"></i> Chưa tốt</span>';
                                    }
                                    ?>
                                </small>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                    <!-- ✅ KẾT THÚC HIỂN THỊ ĐIỂM TRUNG BÌNH -->
                    
                    <div class="mt-4">
                        <a href="/Product" class="btn btn-secondary">
                            <i class="fas fa-arrow-left"></i> Quay lại danh sách
                        </a>
                    </div>
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
                                           value="<?php echo SessionHelper::isLoggedIn() ? htmlspecialchars($_SESSION['username'] ?? '') : ''; ?>">
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
            <!-- ✅ KẾT THÚC PHẦN BÌNH LUẬN -->

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

<?php include 'app/views/shares/footer.php'; ?>
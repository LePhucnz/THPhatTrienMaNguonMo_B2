<?php include 'app/views/shares/header.php'; ?>

<div class="container mt-4">
    <h2><i class="fas fa-shopping-cart"></i> Giỏ hàng của bạn</h2>
    
    <?php if (empty($cartItems)): ?>
        <div class="alert alert-info mt-3">
            <i class="fas fa-info-circle"></i> Giỏ hàng của bạn đang trống.
            <a href="/Product" class="alert-link">Tiếp tục mua sắm</a>
        </div>
    <?php else: ?>
        <form method="POST" action="/Product/updateCart">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="bg-light">
                        <tr>
                            <th>Sản phẩm</th>
                            <th>Giá</th>
                            <th>Số lượng</th>
                            <th>Thành tiền</th>
                            <th>Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($cartItems as $item): ?>
                        <tr>
                            <td>
                                <?php if ($item['product']->image): ?>
                                    <img src="/public/<?php echo htmlspecialchars($item['product']->image); ?>" 
                                         alt="<?php echo htmlspecialchars($item['name']); ?>" 
                                         style="width: 60px; height: 60px; object-fit: cover;" 
                                         class="rounded mr-2">
                                <?php endif; ?>
                                <strong><?php echo htmlspecialchars($item['name']); ?></strong>
                            </td>
                            <td><?php echo number_format($item['price'], 0, ',', '.'); ?> ₫</td>
                            <td>
                                <input type="number" name="quantities[<?php echo $item['product']->id; ?>]" 
                                       value="<?php echo $item['quantity']; ?>" min="1" 
                                       class="form-control" style="width: 70px;">
                            </td>
                            <td class="text-danger font-weight-bold">
                                <?php echo number_format($item['subtotal'], 0, ',', '.'); ?> ₫
                            </td>
                            <td>
                                <a href="/Product/removeFromCart/<?php echo $item['product']->id; ?>" 
                                   class="btn btn-danger btn-sm"
                                   onclick="return confirm('Xóa sản phẩm này khỏi giỏ hàng?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mt-3">
                <a href="/Product" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Tiếp tục mua sắm
                </a>
                <div class="text-right">
                    <h4>Tổng cộng: <span class="text-danger"><?php echo number_format($total, 0, ',', '.'); ?> ₫</span></h4>
                    <button type="submit" class="btn btn-warning mt-2">
                        <i class="fas fa-sync"></i> Cập nhật giỏ hàng
                    </button>
                    <a href="/Product/checkout" class="btn btn-success mt-2 ml-2">
                        <i class="fas fa-credit-card"></i> Thanh toán
                    </a>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php include 'app/views/shares/footer.php'; ?>
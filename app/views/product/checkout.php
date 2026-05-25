<?php include 'app/views/shares/header.php'; ?>

<style>
.checkout-container { max-width: 1200px; margin: 0 auto; }
.product-item { display: flex; align-items: center; padding: 15px; background: white; border-radius: 8px; margin-bottom: 10px; border: 1px solid #e9ecef; }
.product-item img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; margin-right: 15px; }
.product-info { flex: 1; }
.price-summary { background: white; padding: 20px; border-radius: 8px; margin-top: 15px; }
.price-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e9ecef; }
.price-row.total { font-size: 18px; font-weight: bold; color: #e74c3c; border-top: 2px solid #e74c3c; border-bottom: none; margin-top: 10px; padding-top: 15px; }
.voucher-box { border: 2px dashed #3498db; padding: 15px; border-radius: 8px; background: #f8f9fa; margin-bottom: 15px; }
.voucher-applied { background: #d4edda; color: #155724; padding: 12px; border-radius: 8px; margin-bottom: 10px; display: flex; justify-content: space-between; align-items: center; border-left: 4px solid #28a745; }
.payment-option { border: 2px solid #e9ecef; border-radius: 10px; padding: 15px; margin-bottom: 10px; cursor: pointer; transition: all 0.3s; }
.payment-option.active { border-color: #2ecc71; background: #f0fff4; }
.qr-code-section { display: none; text-align: center; padding: 20px; background: white; border-radius: 10px; margin-top: 15px; }
.qr-code-section.show { display: block; }
#voucher-message { margin-top: 10px; }
.copy-btn { background: #3498db; color: white; border: none; padding: 2px 8px; border-radius: 4px; cursor: pointer; font-size: 12px; }
.copy-btn:hover { background: #2980b9; }
.bank-info { background: #f8f9fa; padding: 15px; border-radius: 8px; margin: 15px 0; text-align: left; }
.bank-info-row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid #dee2e6; }
.bank-info-row:last-child { border-bottom: none; }
</style>

<div class="container mt-4 checkout-container">
    <h2 class="mb-4"><i class="fas fa-credit-card"></i> Thanh toán</h2>
    
    <!-- ✅ FORM THANH TOÁN CHÍNH - CHỈ SUBMIT KHI NHẤN "XÁC NHẬN ĐẶT HÀNG" -->
    <form method="POST" action="/Product/processCheckout" id="checkout-form">
        <div class="row">
            <!-- Cột trái: Thông tin giao hàng & Voucher -->
            <div class="col-md-7">
                <!-- Thông tin giao hàng -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-shipping-fast"></i> Thông tin giao hàng</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" name="fullname" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Số điện thoại <span class="text-danger">*</span></label>
                                <input type="tel" class="form-control" name="phone" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Địa chỉ <span class="text-danger">*</span></label>
                            <textarea class="form-control" name="address" rows="3" required></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Tỉnh/Thành phố</label>
                                <select class="form-control" name="city">
                                    <option value="">Chọn thành phố</option>
                                    <option value="hcm">TP. Hồ Chí Minh</option>
                                    <option value="hanoi">Hà Nội</option>
                                    <option value="danang">Đà Nẵng</option>
                                    <option value="other">Tỉnh khác</option>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Quận/Huyện</label>
                                <input type="text" class="form-control" name="district">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ghi chú đơn hàng (Tùy chọn)</label>
                            <textarea class="form-control" name="notes" rows="2" placeholder="Ví dụ: Giao hàng giờ hành chính..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- ✅ PHẦN VOUCHER - FORM RIÊNG, KHÔNG SUBMIT FORM CHÍNH -->
                <div class="card mb-4">
                    <div class="card-header bg-warning text-dark">
                        <h5 class="mb-0"><i class="fas fa-ticket-alt"></i> Mã giảm giá / Voucher</h5>
                    </div>
                    <div class="card-body">
                        <div id="voucher-message"></div>

                        <?php if (isset($appliedVoucher)): ?>
                            <!-- Hiển thị voucher đã áp dụng -->
                            <div class="voucher-applied">
                                <div>
                                    <strong>✅ Đã áp dụng:</strong> 
                                    <code><?php echo htmlspecialchars($appliedVoucher['code']); ?></code>
                                    <span class="text-muted small ml-2">
                                        (<?php 
                                            if ($appliedVoucher['type'] == 'freeship') {
                                                echo 'Miễn phí vận chuyển';
                                            } elseif ($appliedVoucher['type'] == 'percent') {
                                                echo "Giảm {$appliedVoucher['value']}%";
                                                if ($appliedVoucher['max_discount'] > 0) {
                                                    echo " (tối đa " . number_format($appliedVoucher['max_discount'], 0, ',', '.') . " đ)";
                                                }
                                            } else {
                                                echo "Giảm " . number_format($appliedVoucher['value'], 0, ',', '.') . " đ";
                                            }
                                        ?>)
                                    </span>
                                </div>
                                <a href="/Product/removeVoucher" class="btn btn-sm btn-outline-danger">
                                    <i class="fas fa-times"></i> Hủy
                                </a>
                            </div>
                        <?php else: ?>
                            <!-- ✅ FORM ÁP DỤNG VOUCHER RIÊNG - TYPE="button" ĐỂ KHÔNG SUBMIT -->
                            <div class="voucher-box">
                                <div class="input-group">
                                    <input type="text" id="voucher-code" class="form-control" 
                                           placeholder="Nhập mã voucher (VD: SALE10, FREESHIP)">
                                    <button type="button" class="btn btn-primary" onclick="applyVoucher()">
                                        <i class="fas fa-check"></i> Áp dụng
                                    </button>
                                </div>
                                
                                <?php if (!empty($vouchers)): ?>
                                <small class="text-muted mt-2 d-block">
                                    <i class="fas fa-lightbulb"></i> Voucher có sẵn:
                                    <?php foreach($vouchers as $v): ?>
                                        <span class="badge badge-info ml-1" style="cursor:pointer;" 
                                              onclick="document.getElementById('voucher-code').value='<?php echo $v->code; ?>'">
                                            <?php echo $v->code; ?>
                                        </span>
                                    <?php endforeach; ?>
                                </small>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Phương thức thanh toán -->
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-wallet"></i> Phương thức thanh toán</h5>
                    </div>
                    <div class="card-body">
                        <div class="payment-option active" onclick="selectPayment('cod')">
                            <label class="d-flex align-items-center" style="cursor: pointer;">
                                <input type="radio" name="payment_method" value="cod" checked>
                                <div>
                                    <strong><i class="fas fa-money-bill-wave"></i> Thanh toán khi nhận hàng (COD)</strong>
                                </div>
                            </label>
                        </div>
                        <div class="payment-option" onclick="selectPayment('qr')">
                            <label class="d-flex align-items-center" style="cursor: pointer;">
                                <input type="radio" name="payment_method" value="qr">
                                <div>
                                    <strong><i class="fas fa-qrcode"></i> Chuyển khoản QR Code</strong>
                                </div>
                            </label>
                        </div>
                        
                        <div id="qr-section" class="qr-code-section">
                            <h6 class="text-primary mb-3"><i class="fas fa-qrcode"></i> Quét mã QR để thanh toán</h6>
                            <div class="qr-code">
                                <img src="https://api.qrserver.com/v1/create-qr-code/?size=250x250&data=<?php 
                                    echo urlencode(json_encode([
                                        'accountNo' => '1234567890',
                                        'accountName' => 'NGUYEN VAN A',
                                        'acqId' => '970400',
                                        'amount' => number_format($finalTotal ?? 0, 0, '', ''),
                                        'addInfo' => urlencode($orderCode ?? ''),
                                        'template' => 'compact'
                                    ])); 
                                ?>" alt="QR Code" style="width: 100%;">
                            </div>
                            <div class="bank-info">
                                <div class="bank-info-row">
                                    <span><i class="fas fa-building"></i> Ngân hàng:</span>
                                    <strong>Vietcombank</strong>
                                </div>
                                <div class="bank-info-row">
                                    <span><i class="fas fa-user"></i> Tên tài khoản:</span>
                                    <strong>NGUYEN VAN A</strong>
                                </div>
                                <div class="bank-info-row">
                                    <span><i class="fas fa-credit-card"></i> Số tài khoản:</span>
                                    <strong>1234567890</strong>
                                    <button type="button" class="copy-btn" onclick="copyToClipboard('1234567890')"><i class="fas fa-copy"></i> Copy</button>
                                </div>
                                <div class="bank-info-row">
                                    <span><i class="fas fa-money-bill"></i> Số tiền:</span>
                                    <strong class="text-danger"><?php echo number_format($finalTotal ?? 0, 0, ',', '.'); ?> đ</strong>
                                    <button type="button" class="copy-btn" onclick="copyToClipboard('<?php echo number_format($finalTotal ?? 0, 0, '', ''); ?>')"><i class="fas fa-copy"></i> Copy</button>
                                </div>
                                <div class="bank-info-row">
                                    <span><i class="fas fa-hashtag"></i> Nội dung:</span>
                                    <strong><?php echo $orderCode ?? ''; ?></strong>
                                    <button type="button" class="copy-btn" onclick="copyToClipboard('<?php echo $orderCode ?? ''; ?>')"><i class="fas fa-copy"></i> Copy</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Cột phải: Tóm tắt đơn hàng -->
            <div class="col-md-5">
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-shopping-cart"></i> 
                            Đơn hàng của bạn (<?php echo isset($cartItems) ? count($cartItems) : 0; ?> sản phẩm)
                        </h5>
                    </div>
                    <div class="card-body">
                        <!-- Danh sách sản phẩm -->
                        <?php 
                        $subtotal = 0;
                        if (!empty($cartItems)):
                            foreach ($cartItems as $item): 
                                $subtotal += $item['subtotal'];
                        ?>
                        <div class="product-item">
                            <?php if (!empty($item['product']->image)): ?>
                                <img src="/public/<?php echo htmlspecialchars($item['product']->image); ?>" alt="img">
                            <?php else: ?>
                                <div style="width: 80px; height: 80px; background: #e9ecef; border-radius: 8px; display: flex; align-items: center; justify-content: center; margin-right: 15px;">
                                    <i class="fas fa-image fa-2x text-muted"></i>
                                </div>
                            <?php endif; ?>
                            <div class="product-info">
                                <div class="font-weight-bold"><?php echo htmlspecialchars($item['product']->name); ?></div>
                                <small class="text-muted">x<?php echo $item['quantity']; ?></small>
                            </div>
                            <div class="text-right font-weight-bold text-danger">
                                <?php echo number_format($item['subtotal'], 0, ',', '.'); ?> đ
                            </div>
                        </div>
                        <?php endforeach; endif; ?>

                        <!-- ✅ TỔNG KẾT GIÁ - CÓ HIỂN THỊ GIẢM GIÁ -->
                        <div class="price-summary">
                            <div class="price-row">
                                <span>Tạm tính:</span>
                                <span><?php echo number_format($subtotal ?? 0, 0, ',', '.'); ?> đ</span>
                            </div>
                            <div class="price-row">
                                <span>Phí vận chuyển:</span>
                                <span><?php echo number_format($shippingFee ?? 30000, 0, ',', '.'); ?> đ</span>
                            </div>
                            <div class="price-row">
                                <span>Thuế VAT (10%):</span>
                                <span><?php echo number_format(($subtotal ?? 0) * 0.10, 0, ',', '.'); ?> đ</span>
                            </div>
                            
                            <!-- ✅ HIỂN THỊ GIẢM GIÁ NẾU CÓ VOUCHER -->
                            <?php if (isset($discountAmount) && $discountAmount > 0 && isset($appliedVoucher)): ?>
                            <div class="price-row text-success" id="discount-row">
                                <span>
                                    <i class="fas fa-tag"></i> 
                                    Giảm giá (<?php echo htmlspecialchars($appliedVoucher['code']); ?>):
                                </span>
                                <span id="discount-amount">- <?php echo number_format($discountAmount, 0, ',', '.'); ?> đ</span>
                            </div>
                            <?php else: ?>
                            <div class="price-row text-success" id="discount-row" style="display: none;">
                                <span><i class="fas fa-tag"></i> Giảm giá:</span>
                                <span id="discount-amount">- 0 đ</span>
                            </div>
                            <?php endif; ?>
                            
                            <div class="price-row total">
                                <span>TỔNG THANH TOÁN:</span>
                                <span id="final-total"><?php echo number_format($finalTotal ?? ($subtotal + ($shippingFee ?? 30000) + (($subtotal ?? 0) * 0.10) - ($discountAmount ?? 0)), 0, ',', '.'); ?> đ</span>
                            </div>
                        </div>

                        <!-- Mã đơn hàng -->
                        <div class="alert alert-light mt-3">
                            <strong><i class="fas fa-receipt"></i> Mã đơn hàng:</strong> 
                            <span class="text-primary"><?php echo $orderCode ?? ''; ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Nút xác nhận -->
        <div class="row mt-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center">
                    <a href="/Product/cart" class="btn btn-secondary btn-lg">
                        <i class="fas fa-arrow-left"></i> Quay lại giỏ hàng
                    </a>
                    <!-- ✅ CHỈ NÚT NÀY MỚI SUBMIT FORM -->
                    <button type="submit" class="btn btn-success btn-lg">
                        <i class="fas fa-check-circle"></i> Xác nhận đặt hàng
                    </button>
                </div>
            </div>
        </div>
        
        <input type="hidden" name="order_code" value="<?php echo $orderCode ?? ''; ?>">
        <input type="hidden" name="total_amount" value="<?php echo $finalTotal ?? ($subtotal + ($shippingFee ?? 30000) + (($subtotal ?? 0) * 0.10) - ($discountAmount ?? 0)); ?>">
        <?php if (isset($appliedVoucher)): ?>
            <input type="hidden" name="voucher_id" value="<?php echo $appliedVoucher['id']; ?>">
            <input type="hidden" name="voucher_code" value="<?php echo $appliedVoucher['code']; ?>">
            <input type="hidden" name="discount_amount" value="<?php echo $discountAmount; ?>">
        <?php endif; ?>
    </form>
</div>

<script>
// ✅ ÁP DỤNG VOUCHER QUA AJAX - KHÔNG SUBMIT FORM CHÍNH
function applyVoucher() {
    const voucherCode = document.getElementById('voucher-code').value.trim();
    const messageDiv = document.getElementById('voucher-message');
    
    if (!voucherCode) {
        messageDiv.innerHTML = '<div class="alert alert-warning">Vui lòng nhập mã voucher!</div>';
        return;
    }
    
    const formData = new FormData();
    formData.append('code', voucherCode);
    formData.append('subtotal', <?php echo $subtotal ?? 0; ?>);
    
    fetch('/Product/applyVoucher', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Reload trang để hiển thị voucher đã áp dụng
            location.reload();
        } else {
            messageDiv.innerHTML = '<div class="alert alert-danger">' + data.message + '</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        messageDiv.innerHTML = '<div class="alert alert-danger">Có lỗi xảy ra. Vui lòng thử lại!</div>';
    });
}

// Enter key để áp dụng voucher
document.getElementById('voucher-code')?.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        applyVoucher();
    }
});

// Select payment method
function selectPayment(method) {
    document.querySelectorAll('.payment-option').forEach(option => {
        option.classList.remove('active');
    });
    event.currentTarget.classList.add('active');
    document.querySelector(`input[name="payment_method"][value="${method}"]`).checked = true;
    
    document.getElementById('qr-section').classList.remove('show');
    if (method === 'qr') {
        document.getElementById('qr-section').classList.add('show');
    }
}

// Copy to clipboard
function copyToClipboard(text) {
    navigator.clipboard.writeText(text).then(() => {
        showCopyNotification();
    }).catch(() => {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.select();
        document.execCommand('copy');
        document.body.removeChild(textArea);
        showCopyNotification();
    });
}

function showCopyNotification() {
    const notification = document.createElement('div');
    notification.className = 'alert alert-success';
    notification.style.cssText = 'position: fixed; top: 20px; right: 20px; z-index: 9999; min-width: 200px;';
    notification.innerHTML = '<i class="fas fa-check-circle"></i> Đã copy!';
    document.body.appendChild(notification);
    setTimeout(() => notification.remove(), 2000);
}

// Form validation
document.getElementById('checkout-form').addEventListener('submit', function(e) {
    const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
    if (paymentMethod === 'qr' || paymentMethod === 'bank') {
        const confirmed = confirm('Bạn đã thanh toán xong chưa? Nhấn OK nếu đã thanh toán.');
        if (!confirmed) {
            e.preventDefault();
        }
    }
});
</script>

<?php include 'app/views/shares/footer.php'; ?>
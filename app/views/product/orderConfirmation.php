<?php include 'app/views/shares/header.php'; ?>

<div class="container mt-5">
    <div class="card border-success shadow">
        <div class="card-body text-center py-5">
            <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
            <h2 class="mt-4 text-success">Đặt hàng thành công!</h2>
            <p class="lead text-muted mt-3">
                Cảm ơn bạn đã mua sắm tại WebBanHang.<br>
                Đơn hàng của bạn đang được xử lý và sẽ được giao trong thời gian sớm nhất.
            </p>
            <div class="mt-4">
                <a href="/Product" class="btn btn-primary btn-lg">
                    <i class="fas fa-shopping-bag"></i> Tiếp tục mua sắm
                </a>
                <a href="/Product/cart" class="btn btn-outline-secondary btn-lg ml-2">
                    <i class="fas fa-list"></i> Xem đơn hàng
                </a>
            </div>
        </div>
    </div>
</div>

<?php include 'app/views/shares/footer.php'; ?>
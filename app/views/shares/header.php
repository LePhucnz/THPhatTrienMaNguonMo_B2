<?php
// Khởi tạo session an toàn nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .header-top { background: #28a745; padding: 10px 0; }
        .search-box { max-width: 600px; margin: 0 auto; }
        .search-box input { border-radius: 25px 0 0 25px; border: none; padding: 10px 20px; }
        .search-box button { border-radius: 0 25px 25px 0; background: #ffc107; border: none; padding: 10px 25px; }
        .category-nav { background: #f8f9fa; padding: 10px 0; border-bottom: 1px solid #dee2e6; }
        .category-nav .nav-link { color: #333; font-weight: 500; }
        .category-nav .nav-link:hover { color: #28a745; }
        .category-nav .nav-link.active { color: #28a745; font-weight: bold; border-bottom: 2px solid #28a745; }
        .admin-link { color: #dc3545 !important; font-weight: bold; }
        
        /* Badge giỏ hàng */
        .cart-badge {
            position: absolute;
            top: -8px;
            right: -10px;
            background: #dc3545;
            color: white;
            border-radius: 50%;
            padding: 2px 7px;
            font-size: 11px;
            font-weight: bold;
            min-width: 20px;
            text-align: center;
            line-height: 1;
            animation: pulse 0.3s ease;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.3); }
            100% { transform: scale(1); }
        }
        
        .cart-icon-wrapper {
            position: relative;
            display: inline-block;
        }
        
        /* Banner Slider Styles */
        .banner-slider {
            position: relative;
            margin-bottom: 30px;
        }
        
        .banner-slider .carousel-item {
            height: 400px;
        }
        
        .banner-slider .carousel-item img {
            height: 100%;
            object-fit: cover;
            filter: brightness(0.7);
        }
        
        .banner-slider .carousel-caption {
            bottom: 20%;
        }
        
        .banner-slider .carousel-caption h3 {
            font-size: 3rem;
            font-weight: bold;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
            animation: fadeInDown 1s ease;
        }
        
        .banner-slider .carousel-caption p {
            font-size: 1.5rem;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
            animation: fadeInUp 1s ease;
        }
        
        .banner-slider .carousel-control-prev-icon,
        .banner-slider .carousel-control-next-icon {
            background-color: rgba(0,0,0,0.5);
            border-radius: 50%;
            padding: 20px;
        }
        
        .banner-slider .carousel-indicators li {
            width: 15px;
            height: 15px;
            border-radius: 50%;
            margin: 0 5px;
        }
        
        @keyframes fadeInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .btn-shop-now {
            background: #ffc107;
            color: #000;
            padding: 12px 30px;
            border-radius: 25px;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
            margin-top: 15px;
            transition: all 0.3s;
        }
        
        .btn-shop-now:hover {
            background: #e0a800;
            transform: translateY(-2px);
            color: #000;
        }
        
        @media (max-width: 768px) {
            .banner-slider .carousel-item {
                height: 250px;
            }
            .banner-slider .carousel-caption h3 {
                font-size: 1.5rem;
            }
            .banner-slider .carousel-caption p {
                font-size: 1rem;
            }
        }
    </style>
</head>
<body>
    <!-- Header Top -->
    <div class="header-top">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-3">
                    <h2 class="text-white mb-0">
                        <!-- ✅ Yêu cầu 3: WebBanHang có link dẫn về trang list -->
                        <a href="/Product" style="color: white; text-decoration: none; transition: opacity 0.2s;" 
                           onmouseover="this.style.opacity='0.8'" onmouseout="this.style.opacity='1'">
                            <i class="fas fa-store"></i> WebBanHang
                        </a>
                    </h2>
                </div>
                <div class="col-md-6">
                    <form action="/Product/search" method="GET" class="search-box d-flex">
                        <input type="text" name="keyword" class="form-control" placeholder="Tìm kiếm sản phẩm..." 
                               value="<?php echo htmlspecialchars($_GET['keyword'] ?? ''); ?>">
                        <button type="submit" class="btn btn-warning"><i class="fas fa-search"></i></button>
                    </form>
                </div>
                <div class="col-md-3 text-white text-right">
                    <!-- ✅ Yêu cầu 2: Icon giỏ hàng hiển thị số lượng -->
                    <a href="/Product/cart" class="text-white mr-3 position-relative" style="text-decoration: none;">
                        <span class="cart-icon-wrapper">
                            <i class="fas fa-shopping-cart fa-lg"></i>
                            <?php
                            // Tính số lượng sản phẩm trong giỏ (an toàn với session)
                            $cartCount = 0;
                            if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
                                $cartCount = array_sum(array_column($_SESSION['cart'], 'quantity'));
                            }
                            ?>
                            <span id="cart-badge" class="cart-badge <?php echo $cartCount > 0 ? '' : 'd-none'; ?>">
                                <?php echo $cartCount; ?>
                            </span>
                        </span>
                        <span class="ml-1 d-none d-md-inline">Giỏ hàng</span>
                    </a>
                    <span><i class="fas fa-phone"></i> 1900.1234</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Category Navigation -->
    <div class="category-nav">
        <div class="container">
            <ul class="nav">
                <li class="nav-item">
                    <a class="nav-link <?php echo (!isset($_GET['category'])) ? 'active' : ''; ?>" 
                       href="/Product">
                        <i class="fas fa-th-large"></i> Tất cả
                    </a>
                </li>
                <?php 
                $db = (new Database())->getConnection();
                $stmt = $db->query("SELECT * FROM category ORDER BY name ASC");
                $categories = $stmt->fetchAll(PDO::FETCH_OBJ);
                foreach($categories as $cat): 
                    $isActive = (isset($_GET['category']) && $_GET['category'] == $cat->id) ? 'active' : '';
                ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $isActive; ?>" 
                       href="/Product?category=<?php echo $cat->id; ?>">
                        <?php echo htmlspecialchars($cat->name); ?>
                    </a>
                </li>
                <?php endforeach; ?>
                
                <li class="nav-item ml-auto">
                    <a class="nav-link admin-link" href="/Category/list">
                        <i class="fas fa-cogs"></i> Quản lý danh mục
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Banner Slider -->
    <div class="banner-slider">
        <div id="mainSlider" class="carousel slide" data-ride="carousel" data-interval="4000">
            <!-- Indicators -->
            <ol class="carousel-indicators">
                <li data-target="#mainSlider" data-slide-to="0" class="active"></li>
                <li data-target="#mainSlider" data-slide-to="1"></li>
                <li data-target="#mainSlider" data-slide-to="2"></li>
            </ol>

            <!-- Slides -->
            <div class="carousel-inner">
                <!-- Slide 1 -->
                <div class="carousel-item active">
                    <img src="https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=1200" alt="Slide 1">
                    <div class="carousel-caption">
                        <h3>🔥 SIÊU KHUYẾN MÃI</h3>
                        <p>Giảm giá đến 50% cho sản phẩm công nghệ</p>
                        <a href="/Product" class="btn-shop-now">MUA NGAY <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>

                <!-- Slide 2 -->
                <div class="carousel-item">
                    <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1200" alt="Slide 2">
                    <div class="carousel-caption">
                        <h3>📱 ĐIỆN THOẠI MỚI NHẤT</h3>
                        <p>iPhone 17 Pro - Trải nghiệm đột phá</p>
                        <a href="/Product?category=1" class="btn-shop-now">XEM NGAY <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>

                <!-- Slide 3 -->
                <div class="carousel-item">
                    <img src="https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=1200" alt="Slide 3">
                    <div class="carousel-caption">
                        <h3>💻 LAPTOP CAO CẤP</h3>
                        <p>Hiệu năng mạnh mẽ - Giá cực tốt</p>
                        <a href="/Product?category=2" class="btn-shop-now">KHÁM PHÁ <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- Controls -->
            <a class="carousel-control-prev" href="#mainSlider" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="sr-only">Previous</span>
            </a>
            <a class="carousel-control-next" href="#mainSlider" role="button" data-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="sr-only">Next</span>
            </a>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="container mt-4">
        <!-- Thanh công cụ quản lý -->
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
            <h4><i class="fas fa-box-open"></i> Quản lý sản phẩm</h4>
            <div>
                <a href="/Product/add" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Thêm sản phẩm mới
                </a>
                <a href="/Product" class="btn btn-secondary btn-sm">
                    <i class="fas fa-list"></i> Danh sách
                </a>
            </div>
        </div>

        <!-- ✅ JavaScript cập nhật badge giỏ hàng khi thêm sản phẩm qua AJAX -->
        <script>
        // Function để cập nhật badge giỏ hàng
        function updateCartBadge(count) {
            const badge = document.getElementById('cart-badge');
            if (badge) {
                badge.textContent = count;
                if (count > 0) {
                    badge.classList.remove('d-none');
                    // Hiệu ứng pulse khi có sản phẩm mới
                    badge.style.animation = 'none';
                    setTimeout(() => badge.style.animation = 'pulse 0.3s ease', 10);
                } else {
                    badge.classList.add('d-none');
                }
            }
        }
        </script>
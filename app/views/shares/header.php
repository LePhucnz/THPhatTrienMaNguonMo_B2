<?php
// Khởi tạo session an toàn nếu chưa có
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'app/helpers/SessionHelper.php';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý sản phẩm</title>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .product-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border: none;
    border-radius: 10px;
    overflow: hidden;
}

.product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1);
}

.product-image-wrapper {
    position: relative;
    overflow: hidden;
    padding-top: 75%; /* 4:3 Aspect Ratio */
}

.product-image-wrapper img {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.product-title {
    font-size: 0.95rem;
    font-weight: 600;
    line-height: 1.4;
    height: 42px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    margin-bottom: 0.5rem;
}

.product-description {
    height: 40px;
    overflow: hidden;
    text-overflow: ellipsis;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    font-size: 0.85rem;
}

.card-footer {
    padding: 0.75rem;
}

.btn-group .btn {
    font-size: 0.85rem;
    padding: 0.4rem 0.6rem;
}

/* Loading Animation */
#loading {
    padding: 50px 0;
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .product-title {
        font-size: 0.9rem;
        height: 38px;
    }
    
    .product-description {
        height: 35px;
        font-size: 0.8rem;
    }
    
    .btn-group .btn {
        font-size: 0.8rem;
        padding: 0.35rem 0.5rem;
    }
}

/* Badge styling */
.badge-info {
    background-color: #17a2b8 !important;
    color: white;
    font-size: 0.75rem;
    padding: 0.35rem 0.5rem;
}

/* Price styling */
.text-danger {
    font-size: 1.1rem;
}
        .header-top { background: #28a745; padding: 10px 0; }
        .search-box { max-width: 600px; margin: 0 auto; }
        .search-box input { border-radius: 25px 0 0 25px; border: none; padding: 10px 20px; }
        .search-box button { border-radius: 0 25px 25px 0; background: #ffc107; border: none; padding: 10px 25px; }
        .category-nav { background: #f8f9fa; padding: 10px 0; border-bottom: 1px solid #dee2e6; }
        .category-nav .nav-link { color: #333; font-weight: 500; }
        .category-nav .nav-link:hover { color: #28a745; }
        .category-nav .nav-link.active { color: #28a745; font-weight: bold; border-bottom: 2px solid #28a745; }
        .admin-link { color: #dc3545 !important; font-weight: bold; }
        .cart-badge {
            position: absolute; top: -8px; right: -10px;
            background: #dc3545; color: white; border-radius: 50%;
            padding: 2px 7px; font-size: 11px; font-weight: bold;
            min-width: 20px; text-align: center; line-height: 1;
            animation: pulse 0.3s ease;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.3); }
            100% { transform: scale(1); }
        }
        .cart-icon-wrapper { position: relative; display: inline-block; }
        .banner-slider { position: relative; margin-bottom: 30px; }
        .banner-slider .carousel-item { height: 400px; }
        .banner-slider .carousel-item img { height: 100%; object-fit: cover; filter: brightness(0.7); }
        .banner-slider .carousel-caption { bottom: 20%; }
        .banner-slider .carousel-caption h3 { font-size: 3rem; font-weight: bold; text-shadow: 2px 2px 4px rgba(0,0,0,0.5); }
        .banner-slider .carousel-caption p { font-size: 1.5rem; text-shadow: 1px 1px 2px rgba(0,0,0,0.5); }
        .btn-shop-now {
            background: #ffc107; color: #000; padding: 12px 30px;
            border-radius: 25px; font-weight: bold; text-decoration: none;
            display: inline-block; margin-top: 15px; transition: all 0.3s;
        }
        .btn-shop-now:hover { background: #e0a800; transform: translateY(-2px); color: #000; }
        @media (max-width: 768px) {
            .banner-slider .carousel-item { height: 250px; }
            .banner-slider .carousel-caption h3 { font-size: 1.5rem; }
            .banner-slider .carousel-caption p { font-size: 1rem; }
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
                        <a href="/Product" style="color: white; text-decoration: none;">
                            <i class="fas fa-store"></i> WebBanHang
                        </a>
                    </h2>
                </div>
                <div class="col-md-5">
                    <form action="/Product/search" method="GET" class="search-box d-flex">
                        <input type="text" name="keyword" class="form-control"
                               placeholder="Tìm kiếm sản phẩm..."
                               value="<?php echo htmlspecialchars($_GET['keyword'] ?? ''); ?>">
                        <button type="submit" class="btn btn-warning">
                            <i class="fas fa-search"></i>
                        </button>
                    </form>
                </div>
                <div class="col-md-4 text-white text-right d-flex align-items-center justify-content-end">
                    <!-- Giỏ hàng -->
                    <a href="/Product/cart" class="text-white mr-3 position-relative" style="text-decoration: none;">
                        <span class="cart-icon-wrapper">
                            <i class="fas fa-shopping-cart fa-lg"></i>
                            <?php
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

                    <!-- ✅ PHẦN ĐĂNG NHẬP / ĐĂNG XUẤT -->
                    <?php if (SessionHelper::isLoggedIn()): ?>
                        <!-- Avatar + tên user -->
                        <a href="/Account/profile" class="text-white mr-2" style="text-decoration:none;">
                            <?php 
                            $avatarSrc = !empty($_SESSION['avatar']) 
                                ? '/public/' . $_SESSION['avatar'] 
                                : 'https://ui-avatars.com/api/?name=' . urlencode($_SESSION['fullname'] ?? 'U') . '&size=32&background=fff&color=28a745';
                            ?>
                            <img src="<?= $avatarSrc ?>" class="rounded-circle mr-1" style="width:32px;height:32px;object-fit:cover;">
                            <strong><?= htmlspecialchars($_SESSION['fullname'] ?? $_SESSION['username']) ?></strong>
                        </a>
                        <?php if (SessionHelper::isAdmin()): ?>
                            <a href="/Account/manageUsers" class="btn btn-outline-light btn-sm mr-1">
                                <i class="fas fa-users-cog"></i> Quản lý user
                            </a>
                        <?php endif; ?>
                        <a href="/Account/logout" class="btn btn-outline-light btn-sm">
                            <i class="fas fa-sign-out-alt"></i> Đăng xuất
                        </a>
                    <?php else: ?>
                        <a href="/Account/login" class="btn btn-outline-light btn-sm mr-1">
                            <i class="fas fa-sign-in-alt"></i> Đăng nhập
                        </a>
                        <a href="/Account/register" class="btn btn-warning btn-sm">
                            <i class="fas fa-user-plus"></i> Đăng ký
                        </a>
                    <?php endif; ?>
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
                $cats = $stmt->fetchAll(PDO::FETCH_OBJ);
                foreach ($cats as $cat):
                    $isActive = (isset($_GET['category']) && $_GET['category'] == $cat->id) ? 'active' : '';
                ?>
                <li class="nav-item">
                    <a class="nav-link <?php echo $isActive; ?>"
                       href="/Product?category=<?php echo $cat->id; ?>">
                        <?php echo htmlspecialchars($cat->name); ?>
                    </a>
                </li>
                <?php endforeach; ?>

                <!-- Chỉ hiện "Quản lý danh mục" nếu là Admin -->
                <?php if (SessionHelper::isAdmin()): ?>
                <li class="nav-item ml-auto">
                    <a class="nav-link admin-link" href="/Category/list">
                        <i class="fas fa-cogs"></i> Danh mục
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link admin-link" href="/Voucher">
                        <i class="fas fa-ticket-alt"></i> Voucher
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>

    <!-- Banner Slider -->
    <div class="banner-slider">
        <div id="mainSlider" class="carousel slide" data-ride="carousel" data-interval="4000">
            <ol class="carousel-indicators">
                <li data-target="#mainSlider" data-slide-to="0" class="active"></li>
                <li data-target="#mainSlider" data-slide-to="1"></li>
                <li data-target="#mainSlider" data-slide-to="2"></li>
            </ol>
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="https://images.unsplash.com/photo-1607082348824-0a96f2a4b9da?w=1200" alt="Slide 1">
                    <div class="carousel-caption">
                        <h3>🔥 SIÊU KHUYẾN MÃI</h3>
                        <p>Giảm giá đến 50% cho sản phẩm công nghệ</p>
                        <a href="/Product" class="btn-shop-now">MUA NGAY <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1200" alt="Slide 2">
                    <div class="carousel-caption">
                        <h3>📱 ĐIỆN THOẠI MỚI NHẤT</h3>
                        <p>iPhone 17 Pro - Trải nghiệm đột phá</p>
                        <a href="/Product?category=1" class="btn-shop-now">XEM NGAY <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
                <div class="carousel-item">
                    <img src="https://images.unsplash.com/photo-1496181133206-80ce9b88a853?w=1200" alt="Slide 3">
                    <div class="carousel-caption">
                        <h3>💻 LAPTOP CAO CẤP</h3>
                        <p>Hiệu năng mạnh mẽ - Giá cực tốt</p>
                        <a href="/Product?category=2" class="btn-shop-now">KHÁM PHÁ <i class="fas fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
            <a class="carousel-control-prev" href="#mainSlider" role="button" data-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </a>
            <a class="carousel-control-next" href="#mainSlider" role="button" data-slide="next">
                <span class="carousel-control-next-icon"></span>
            </a>
        </div>
    </div>

    <!-- Main Content Container -->
    <div class="container mt-4">
        <div class="d-flex justify-content-between align-items-center mb-3 border-bottom pb-2">
            <h4><i class="fas fa-box-open"></i> Quản lý sản phẩm</h4>
            <?php if (SessionHelper::isAdmin()): ?>
            <div>
                <a href="/Product/add" class="btn btn-success btn-sm">
                    <i class="fas fa-plus"></i> Thêm sản phẩm mới
                </a>
                <a href="/Product" class="btn btn-secondary btn-sm">
                    <i class="fas fa-list"></i> Danh sách
                </a>
            </div>
            <?php endif; ?>
        </div>

        <script>
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
        </script>

    
<?php
ini_set('display_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/models/CategoryModel.php');
require_once('app/models/ReviewModel.php');
require_once('app/models/VoucherModel.php');
require_once('app/helpers/SessionHelper.php');

class ProductController {
    private $productModel;
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
    }

    // ✅ Hàm kiểm tra quyền admin - dùng chung
    private function requireAdmin() {
        if (!SessionHelper::isLoggedIn()) {
            header('Location: /Account/login');
            exit;
        }
        if (!SessionHelper::isAdmin()) {
            http_response_code(403);
            die('
                <div style="text-align:center; margin-top:100px; font-family:sans-serif;">
                    <h2>⛔ Truy cập bị từ chối</h2>
                    <p>Bạn không có quyền thực hiện chức năng này.</p>
                    <a href="/Product" style="color:#28a745;">← Quay về trang chủ</a>
                </div>
            ');
        }
    }

    // ==================== CRUD SẢN PHẨM ====================

    public function index() {
        $categoryId = $_GET['category'] ?? null;
        $keyword    = $_GET['keyword'] ?? null;
        $minPrice   = $_GET['min_price'] ?? null;
        $maxPrice   = $_GET['max_price'] ?? null;

        if ($minPrice !== null || $maxPrice !== null) {
            $products = $this->productModel->getProductsByPriceRange($minPrice, $maxPrice, $categoryId);
        } elseif ($keyword) {
            $products = $this->productModel->searchProducts($keyword);
        } elseif ($categoryId) {
            $products = $this->productModel->getProductsByCategory($categoryId);
        } else {
            $products = $this->productModel->getProducts();
        }

        include 'app/views/product/list.php';
    }

    public function search() {
        $keyword  = $_GET['keyword'] ?? '';
        $products = $this->productModel->searchProducts($keyword);
        include 'app/views/product/list.php';
    }

    public function show($id) {
        $product = $this->productModel->getProductById($id);
        if ($product) {
            $reviewModel   = new ReviewModel($this->db);
            $reviews       = $reviewModel->getReviewsByProductId($id);
            $averageRating = $reviewModel->getAverageRating($id);
            include 'app/views/product/show.php';
        } else {
            echo "Không tìm thấy sản phẩm.";
        }
    }

    public function add() {
        $this->requireAdmin(); // ✅ Chỉ admin
        $categories = (new CategoryModel($this->db))->getCategories();
        include 'app/views/product/add.php';
    }

    public function save() {
        $this->requireAdmin(); // ✅ Chỉ admin
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name        = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price       = $_POST['price'] ?? '';
            $category_id = $_POST['category_id'] ?? null;
            $image       = '';

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                try {
                    $image = $this->uploadImage($_FILES['image']);
                } catch (Exception $e) {
                    $errors     = ['image' => $e->getMessage()];
                    $categories = (new CategoryModel($this->db))->getCategories();
                    include 'app/views/product/add.php';
                    return;
                }
            }

            $result = $this->productModel->addProduct($name, $description, $price, $category_id, $image);

            if (is_array($result)) {
                $errors     = $result;
                $categories = (new CategoryModel($this->db))->getCategories();
                include 'app/views/product/add.php';
            } else {
                header('Location: /Product');
                exit;
            }
        }
    }

    public function edit($id) {
        $this->requireAdmin(); // ✅ Chỉ admin
        $product    = $this->productModel->getProductById($id);
        $categories = (new CategoryModel($this->db))->getCategories();
        if ($product) {
            include 'app/views/product/edit.php';
        } else {
            echo "Không tìm thấy sản phẩm.";
        }
    }

    public function update() {
        $this->requireAdmin(); // ✅ Chỉ admin
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id          = $_POST['id'];
            $name        = $_POST['name'];
            $description = $_POST['description'];
            $price       = $_POST['price'];
            $category_id = $_POST['category_id'];
            $image       = $_POST['existing_image'] ?? '';

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                try {
                    $image = $this->uploadImage($_FILES['image']);
                } catch (Exception $e) {
                    echo "Lỗi upload: " . $e->getMessage();
                    return;
                }
            }

            if ($this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image)) {
                header('Location: /Product');
                exit;
            } else {
                echo "Lỗi cập nhật!";
            }
        }
    }

    public function delete($id) {
        $this->requireAdmin(); // ✅ Chỉ admin
        $this->productModel->deleteProduct($id);
        header('Location: /Product');
        exit;
    }

    // ==================== UPLOAD HÌNH ẢNH ====================

    private function uploadImage($file) {
        $target_dir = "public/uploads/";
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        $extension   = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
        $newFileName = uniqid('img_', true) . '.' . $extension;
        $target_file = $target_dir . $newFileName;

        if (getimagesize($file["tmp_name"]) === false) {
            throw new Exception("File không phải là hình ảnh.");
        }
        if ($file["size"] > 10 * 1024 * 1024) {
            throw new Exception("Hình ảnh có kích thước quá lớn (>10MB).");
        }
        if (!in_array($extension, ["jpg", "jpeg", "png", "gif", "webp"])) {
            throw new Exception("Chỉ cho phép các định dạng JPG, JPEG, PNG, GIF và WebP.");
        }
        if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            throw new Exception("Có lỗi xảy ra khi tải lên hình ảnh.");
        }
        return str_replace('public/', '', $target_file);
    }

    // ==================== ĐÁNH GIÁ SẢN PHẨM ====================

    public function saveReview() {
        // Tất cả user đã đăng nhập đều được review
        if (!SessionHelper::isLoggedIn()) {
            header('Location: /Account/login');
            exit;
        }
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $product_id = $_POST['product_id'] ?? null;
            $rating     = $_POST['rating'] ?? 5;
            $content    = $_POST['content'] ?? '';
            $username   = $_SESSION['username'] ?? 'Khách';

            if (!empty($content) && $product_id) {
                $reviewModel = new ReviewModel($this->db);
                $reviewModel->addReview($product_id, $username, $rating, $content);
            }
            header("Location: /Product/show/" . $product_id);
            exit;
        }
    }

    // ==================== GIỎ HÀNG (tất cả user) ====================

    public function cart() {
        SessionHelper::start();
        $cart      = $_SESSION['cart'] ?? [];
        $cartItems = [];
        $total     = 0;

        foreach ($cart as $id => $item) {
            $product = $this->productModel->getProductById($id);
            if ($product) {
                $item['product']  = $product;
                $item['subtotal'] = (float)$product->price * $item['quantity'];
                $total           += $item['subtotal'];
                $cartItems[]      = $item;
            }
        }
        include 'app/views/product/cart.php';
    }

    public function addToCart($id) {
        SessionHelper::start();
    
        // Bắt đăng nhập trước
        if (!SessionHelper::isLoggedIn()) {
            // Lưu lại trang muốn đến để sau khi đăng nhập redirect về
            $_SESSION['redirect_after_login'] = '/Product/addToCart/' . $id;
            header('Location: /Account/login');
            exit;
        }
    
        $product = $this->productModel->getProductById($id);
        if (!$product) {
            echo "Không tìm thấy sản phẩm.";
            return;
        }
    
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['quantity']++;
        } else {
            $_SESSION['cart'][$id] = [
                'name'     => $product->name,
                'price'    => (float)$product->price,
                'image'    => $product->image,
                'quantity' => 1
            ];
        }
    
        header('Location: /Product/cart');
        exit;
    }

    public function addToCartAjax() {
        if (ob_get_level()) ob_clean();
        header('Content-Type: application/json');
        if (session_status() === PHP_SESSION_NONE) session_start();
    
        // Bắt đăng nhập
        if (!SessionHelper::isLoggedIn()) {
            echo json_encode([
                'success'  => false,
                'redirect' => '/Account/login',
                'message'  => 'Vui lòng đăng nhập để thêm vào giỏ hàng!'
            ]);
            return;
        }
    
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['product_id'] ?? null;
            if (!$id || !is_numeric($id)) {
                echo json_encode(['success' => false, 'message' => 'ID sản phẩm không hợp lệ']);
                return;
            }
    
            $product = $this->productModel->getProductById($id);
            if (!$product) {
                echo json_encode(['success' => false, 'message' => 'Không tìm thấy sản phẩm']);
                return;
            }
    
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
    
            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['quantity']++;
            } else {
                $_SESSION['cart'][$id] = [
                    'name'     => $product->name,
                    'price'    => (float)$product->price,
                    'image'    => $product->image,
                    'quantity' => 1
                ];
            }
    
            $totalItems = array_sum(array_column($_SESSION['cart'], 'quantity'));
            echo json_encode([
                'success'    => true,
                'message'    => '✅ Đã thêm "' . htmlspecialchars($product->name) . '" vào giỏ hàng',
                'totalItems' => $totalItems
            ]); // ✅ Không có dấu phẩy thừa sau phần tử cuối
        } // ✅ Đóng if POST
    }

    public function updateCart() {
        SessionHelper::start();
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['quantities'])) {
            foreach ($_POST['quantities'] as $id => $quantity) {
                $quantity = (int)$quantity;
                if ($quantity <= 0) {
                    unset($_SESSION['cart'][$id]);
                } else {
                    $_SESSION['cart'][$id]['quantity'] = $quantity;
                }
            }
        }
        header('Location: /Product/cart');
        exit;
    }

    public function removeFromCart($id) {
        SessionHelper::start();
        if (isset($_SESSION['cart'][$id])) {
            unset($_SESSION['cart'][$id]);
        }
        header('Location: /Product/cart');
        exit;
    }

    // ==================== VOUCHER ====================

    public function applyVoucher() {
        if (ob_get_level()) ob_clean();
        if (session_status() === PHP_SESSION_NONE) session_start();
        header('Content-Type: application/json');

        try {
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                echo json_encode(['success' => false, 'message' => 'Method không được hỗ trợ']);
                return;
            }
            $code         = strtoupper(trim($_POST['code'] ?? ''));
            $cartSubtotal = $_POST['subtotal'] ?? 0;

            if (empty($code)) {
                echo json_encode(['success' => false, 'message' => 'Vui lòng nhập mã voucher']);
                return;
            }

            $voucherModel = new VoucherModel($this->db);
            $voucher      = $voucherModel->getVoucherByCode($code);

            if (!$voucher) {
                echo json_encode(['success' => false, 'message' => 'Mã voucher không tồn tại']);
                return;
            }

            $validation = $voucherModel->validateVoucher($voucher, $cartSubtotal);
            if (!$validation['valid']) {
                echo json_encode(['success' => false, 'message' => $validation['message']]);
                return;
            }

            $_SESSION['applied_voucher'] = [
                'id'           => $voucher->id,
                'code'         => $voucher->code,
                'type'         => $voucher->type,
                'value'        => (float)$voucher->value,
                'max_discount' => $voucher->max_discount ? (float)$voucher->max_discount : null
            ];

            echo json_encode(['success' => true, 'message' => '✅ Áp dụng voucher thành công']);

        } catch (Exception $e) {
            error_log("Voucher Error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'Lỗi server. Vui lòng thử lại sau.']);
        }
    }

    public function removeVoucher() {
        SessionHelper::start();
        unset($_SESSION['applied_voucher']);
        header('Location: /Product/checkout');
        exit;
    }

    // ==================== THANH TOÁN ====================

    public function checkout() {
        SessionHelper::start();
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            header('Location: /Product');
            exit;
        }

        $orderCode = 'DH' . date('YmdHis') . strtoupper(substr(uniqid(), -6));
        $_SESSION['current_order_code'] = $orderCode;

        $userInfo = null;
        if (SessionHelper::isLoggedIn()) {
            require_once 'app/models/AccountModel.php';
            $accModel = new AccountModel($this->db);
            $userInfo = $accModel->getAccountById($_SESSION['user_id']);
        }
        
        $cart       = $_SESSION['cart'];
        $cartItems  = [];
        $subtotal   = 0;
        $shippingFee = 30000;

        foreach ($cart as $id => $item) {
            $product = $this->productModel->getProductById($id);
            if ($product) {
                $itemTotal  = (float)$product->price * $item['quantity'];
                $subtotal  += $itemTotal;
                $cartItems[] = [
                    'product'  => $product,
                    'quantity' => $item['quantity'],
                    'price'    => (float)$product->price,
                    'subtotal' => $itemTotal
                ];
            }
        }

        $tax            = $subtotal * 0.10;
        $discountAmount = 0;
        $appliedVoucher = $_SESSION['applied_voucher'] ?? null;

        if ($appliedVoucher) {
            if ($appliedVoucher['type'] === 'percent') {
                $discountAmount = $subtotal * ($appliedVoucher['value'] / 100);
                if ($appliedVoucher['max_discount'] && $discountAmount > $appliedVoucher['max_discount']) {
                    $discountAmount = $appliedVoucher['max_discount'];
                }
            } elseif ($appliedVoucher['type'] === 'fixed') {
                $discountAmount = $appliedVoucher['value'];
            } elseif ($appliedVoucher['type'] === 'freeship') {
                $discountAmount = $shippingFee;
            }
            $discountAmount = min($discountAmount, $subtotal + $shippingFee);
        }

        $finalTotal   = ($subtotal + $shippingFee + $tax) - $discountAmount;
        $voucherModel = new VoucherModel($this->db);
        $vouchers     = $voucherModel->getActiveVouchers();
        $voucherError = $_SESSION['voucher_error'] ?? '';
        unset($_SESSION['voucher_error']);

        include 'app/views/product/checkout.php';
    }

    public function processCheckout() {
        SessionHelper::start();
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /Product');
            exit;
        }

        $fullname      = $_POST['fullname'] ?? '';
        $phone         = $_POST['phone'] ?? '';
        $email         = $_POST['email'] ?? '';
        $address       = $_POST['address'] ?? '';
        $city          = $_POST['city'] ?? '';
        $district      = $_POST['district'] ?? '';
        $notes         = $_POST['notes'] ?? '';
        $paymentMethod = $_POST['payment_method'] ?? 'cod';
        $orderCode     = $_POST['order_code'] ?? $_SESSION['current_order_code'] ?? null;

        if (empty($orderCode)) {
            $orderCode = 'DH' . date('YmdHis') . strtoupper(substr(uniqid(), -6));
        }
        unset($_SESSION['current_order_code']);

        if (empty($fullname) || empty($phone) || empty($address)) {
            echo "Vui lòng điền đầy đủ thông tin.";
            return;
        }
        if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
            echo "Giỏ hàng trống.";
            return;
        }

        $cart        = $_SESSION['cart'];
        $subtotal    = 0;
        $shippingFee = 30000;

        foreach ($cart as $id => $item) {
            $product = $this->productModel->getProductById($id);
            if ($product) {
                $subtotal += (float)$product->price * $item['quantity'];
            }
        }

        $tax            = $subtotal * 0.10;
        $discountAmount = 0;
        $voucherId      = null;
        $voucherCode    = null;

        if (isset($_SESSION['applied_voucher'])) {
            $v = $_SESSION['applied_voucher'];
            if ($v['type'] === 'percent') {
                $discountAmount = $subtotal * ($v['value'] / 100);
                if ($v['max_discount'] && $discountAmount > $v['max_discount']) {
                    $discountAmount = $v['max_discount'];
                }
            } elseif ($v['type'] === 'fixed') {
                $discountAmount = $v['value'];
            } elseif ($v['type'] === 'freeship') {
                $discountAmount = $shippingFee;
            }
            $discountAmount = min($discountAmount, $subtotal + $shippingFee);
            $voucherId      = $v['id'];
            $voucherCode    = $v['code'];
        }

        $finalTotal = ($subtotal + $shippingFee + $tax) - $discountAmount;
        $this->db->beginTransaction();

        try {
            $query = "INSERT INTO orders 
                      (name, phone, email, address, city, district, notes, payment_method, order_code, 
                       subtotal, shipping_fee, tax, voucher_id, voucher_code, discount_amount, final_total, status, created_at) 
                      VALUES 
                      (:name, :phone, :email, :address, :city, :district, :notes, :payment_method, :order_code,
                       :subtotal, :shipping_fee, :tax, :voucher_id, :voucher_code, :discount_amount, :final_total, :status, NOW())";
            $stmt = $this->db->prepare($query);
            $status = ($paymentMethod === 'cod') ? 'pending' : 'waiting_payment';
            $stmt->bindParam(':name', $fullname);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':address', $address);
            $stmt->bindParam(':city', $city);
            $stmt->bindParam(':district', $district);
            $stmt->bindParam(':notes', $notes);
            $stmt->bindParam(':payment_method', $paymentMethod);
            $stmt->bindParam(':order_code', $orderCode);
            $stmt->bindParam(':subtotal', $subtotal);
            $stmt->bindParam(':shipping_fee', $shippingFee);
            $stmt->bindParam(':tax', $tax);
            $stmt->bindParam(':voucher_id', $voucherId, PDO::PARAM_INT);
            $stmt->bindParam(':voucher_code', $voucherCode);
            $stmt->bindParam(':discount_amount', $discountAmount);
            $stmt->bindParam(':final_total', $finalTotal);
            $stmt->bindParam(':status', $status);
            $stmt->execute();

            $orderId = $this->db->lastInsertId();

            foreach ($_SESSION['cart'] as $productId => $item) {
                $product = $this->productModel->getProductById($productId);
                if ($product) {
                    $query = "INSERT INTO order_details (order_id, product_id, quantity, price) 
                              VALUES (:order_id, :product_id, :quantity, :price)";
                    $stmt  = $this->db->prepare($query);
                    $stmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
                    $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
                    $stmt->bindParam(':quantity', $item['quantity'], PDO::PARAM_INT);
                    $stmt->bindParam(':price', $product->price, PDO::PARAM_STR);
                    $stmt->execute();
                }
            }

            unset($_SESSION['cart']);
            unset($_SESSION['applied_voucher']);

            if ($voucherId) {
                $voucherModel = new VoucherModel($this->db);
                $voucherModel->incrementUsage($voucherId);
            }

            $this->db->commit();
            header('Location: /Product/orderConfirmation/' . $orderId);
            exit;

        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Order Error: " . $e->getMessage());
            echo "Lỗi khi xử lý đơn hàng: " . $e->getMessage();
        }
    }

    public function orderConfirmation($orderId = null) {
        $order = null;
        if ($orderId && is_numeric($orderId)) {
            $query = "SELECT * FROM orders WHERE id = :id";
            $stmt  = $this->db->prepare($query);
            $stmt->bindParam(':id', $orderId, PDO::PARAM_INT);
            $stmt->execute();
            $order = $stmt->fetch(PDO::FETCH_OBJ);
        }
        include 'app/views/product/orderConfirmation.php';
    }
}
?>
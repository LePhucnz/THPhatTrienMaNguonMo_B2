<?php
require_once __DIR__ . '/../models/CartModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class CartApiController {
    private $cartModel;
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->cartModel = new CartModel($this->db);
    }

    // GET /api/cart - Xem giỏ hàng
    public function index() {
        header('Content-Type: application/json');
        $payload = AuthMiddleware::requireAuth();
        
        $items = $this->cartModel->getCart($payload['id']);
        $total = $this->cartModel->getCartTotal($payload['id']);
        
        echo json_encode([
            'items' => $items,
            'total' => $total,
            'count' => count($items)
        ]);
    }

    // POST /api/cart/add - Thêm vào giỏ
    public function addToCart() {
        header('Content-Type: application/json');
        $payload = AuthMiddleware::requireAuth();
        $data = json_decode(file_get_contents("php://input"), true);
        
        $productId = $data['product_id'] ?? null;
        $quantity = $data['quantity'] ?? 1;
        
        if (!$productId || $quantity < 1) {
            http_response_code(400);
            echo json_encode(['message' => 'Thông tin không hợp lệ']);
            return;
        }
        
        $result = $this->cartModel->addToCart($payload['id'], $productId, $quantity);
        
        if ($result['success']) {
            echo json_encode($result);
        } else {
            http_response_code(400);
            echo json_encode($result);
        }
    }

    // PUT /api/cart/update - Cập nhật số lượng
    public function updateQuantity() {
        header('Content-Type: application/json');
        $payload = AuthMiddleware::requireAuth();
        $data = json_decode(file_get_contents("php://input"), true);
        
        $productId = $data['product_id'] ?? null;
        $quantity = $data['quantity'] ?? 1;
        
        if (!$productId) {
            http_response_code(400);
            echo json_encode(['message' => 'Thiếu product_id']);
            return;
        }
        
        $this->cartModel->updateQuantity($payload['id'], $productId, $quantity);
        echo json_encode(['message' => 'Cập nhật thành công']);
    }

    // DELETE /api/cart/remove/{productId} - Xóa sản phẩm
    public function removeFromCart($productId) {
        header('Content-Type: application/json');
        $payload = AuthMiddleware::requireAuth();
        
        $this->cartModel->removeFromCart($payload['id'], $productId);
        echo json_encode(['message' => 'Đã xóa khỏi giỏ hàng']);
    }

    // DELETE /api/cart/clear - Xóa toàn bộ giỏ
    public function clearCart() {
        header('Content-Type: application/json');
        $payload = AuthMiddleware::requireAuth();
        
        $this->cartModel->clearCart($payload['id']);
        echo json_encode(['message' => 'Đã xóa giỏ hàng']);
    }
}
?>
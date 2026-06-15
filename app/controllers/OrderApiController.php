<?php
require_once __DIR__ . '/../models/OrderModel.php';
require_once __DIR__ . '/../models/CartModel.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class OrderApiController {
    private $orderModel;
    private $cartModel;
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->orderModel = new OrderModel($this->db);
        $this->cartModel = new CartModel($this->db);
    }

    // POST /api/order/create - Tạo đơn hàng
    public function createOrder() {
        header('Content-Type: application/json');
        $payload = AuthMiddleware::requireAuth();
        $data = json_decode(file_get_contents("php://input"), true);
        
        $paymentMethod = $data['payment_method'] ?? 'cod';
        $shippingAddress = $data['shipping_address'] ?? '';
        $voucherCode = $data['voucher_code'] ?? null;
        
        if (empty($shippingAddress)) {
            http_response_code(400);
            echo json_encode(['message' => 'Vui lòng nhập địa chỉ giao hàng']);
            return;
        }
        
        $cartItems = $this->cartModel->getCart($payload['id']);
        
        if (empty($cartItems)) {
            http_response_code(400);
            echo json_encode(['message' => 'Giỏ hàng trống']);
            return;
        }
        
        $result = $this->orderModel->createOrder(
            $payload['id'],
            $cartItems,
            $paymentMethod,
            $shippingAddress,
            $voucherCode
        );
        
        if ($result['success']) {
            // Clear cart after successful order
            $this->cartModel->clearCart($payload['id']);
            echo json_encode($result);
        } else {
            http_response_code(400);
            echo json_encode($result);
        }
    }

    // GET /api/order/my-orders - Xem đơn hàng của tôi
    public function myOrders() {
        header('Content-Type: application/json');
        $payload = AuthMiddleware::requireAuth();
        
        $orders = $this->orderModel->getOrdersByUser($payload['id']);
        echo json_encode($orders);
    }

    // GET /api/order/{id} - Chi tiết đơn hàng
    public function show($id) {
        header('Content-Type: application/json');
        $payload = AuthMiddleware::requireAuth();
        
        $order = $this->orderModel->getOrderById($id);
        
        if (!$order) {
            http_response_code(404);
            echo json_encode(['message' => 'Không tìm thấy đơn hàng']);
            return;
        }
        
        // Check permission (only owner or admin)
        if ($order->user_id != $payload['id'] && $payload['role'] !== 'admin') {
            http_response_code(403);
            echo json_encode(['message' => 'Không có quyền xem đơn hàng này']);
            return;
        }
        
        echo json_encode($order);
    }

    // PUT /api/order/{id}/cancel - Hủy đơn hàng
    public function cancelOrder($id) {
        header('Content-Type: application/json');
        $payload = AuthMiddleware::requireAuth();
        
        $result = $this->orderModel->cancelOrder($id, $payload['id']);
        
        if ($result) {
            echo json_encode(['message' => 'Đã hủy đơn hàng']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Không thể hủy đơn hàng này']);
        }
    }

    // PUT /api/order/{id}/status - Cập nhật trạng thái (Admin only)
    public function updateStatus($id) {
        header('Content-Type: application/json');
        AuthMiddleware::requireAdmin();
        
        $data = json_decode(file_get_contents("php://input"), true);
        $status = $data['status'] ?? '';
        
        $validStatuses = ['pending', 'processing', 'shipped', 'completed', 'cancelled'];
        if (!in_array($status, $validStatuses)) {
            http_response_code(400);
            echo json_encode(['message' => 'Trạng thái không hợp lệ']);
            return;
        }
        
        $this->orderModel->updateOrderStatus($id, $status);
        echo json_encode(['message' => 'Đã cập nhật trạng thái']);
    }

    // PUT /api/order/{id}/payment - Cập nhật thanh toán (Admin only)
    public function updatePayment($id) {
        header('Content-Type: application/json');
        AuthMiddleware::requireAdmin();
        
        $data = json_decode(file_get_contents("php://input"), true);
        $status = $data['payment_status'] ?? 'paid';
        
        $this->orderModel->updatePaymentStatus($id, $status);
        echo json_encode(['message' => 'Đã cập nhật trạng thái thanh toán']);
    }

    // GET /api/order/admin/all - Xem tất cả đơn hàng (Admin)
    public function getAllOrders() {
        header('Content-Type: application/json');
        AuthMiddleware::requireAdmin();
        
        $orders = $this->orderModel->getAllOrders();
        echo json_encode($orders);
    }
}
?>
<?php
class OrderModel {
    private $conn;
    private $table = 'orders';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function createOrder($userId, $cartItems, $paymentMethod, $shippingAddress, $voucherCode = null) {
        if (empty($cartItems)) {
            return ['success' => false, 'message' => 'Giỏ hàng trống'];
        }

        $this->conn->beginTransaction();
        
        try {
            // Tính subtotal
            $subtotal = 0;
            foreach ($cartItems as $item) {
                $subtotal += $item->price * $item->quantity;
            }

            // Xử lý voucher
            $discount = 0;
            $voucherId = null;
            $finalVoucherCode = null;
            
            if ($voucherCode) {
                $voucherQuery = "SELECT * FROM vouchers WHERE code = :code AND status = 1 AND NOW() BETWEEN start_date AND end_date";
                $voucherStmt = $this->conn->prepare($voucherQuery);
                $voucherStmt->bindParam(':code', $voucherCode);
                $voucherStmt->execute();
                
                if ($voucher = $voucherStmt->fetch(PDO::FETCH_OBJ)) {
                    if ($subtotal >= $voucher->min_order_value) {
                        if ($voucher->type === 'percent') {
                            $discount = ($subtotal * $voucher->value) / 100;
                            if ($voucher->max_discount && $discount > $voucher->max_discount) {
                                $discount = $voucher->max_discount;
                            }
                        } elseif ($voucher->type === 'fixed') {
                            $discount = $voucher->value;
                        } elseif ($voucher->type === 'freeship') {
                            $discount = $voucher->value;
                        }
                        $voucherId = $voucher->id;
                        $finalVoucherCode = $voucher->code;
                    }
                }
            }

            $finalTotal = $subtotal - $discount;
            $orderCode = 'ORD-' . strtoupper(substr(md5(uniqid()), 0, 8));

            // Khai báo biến trước khi bindParam
            $name = $shippingAddress;
            $phone = '';
            $email = '';

            // INSERT vào bảng orders
            $query = "INSERT INTO " . $this->table . " 
                      (user_id, name, phone, email, address, payment_method, order_code, 
                       subtotal, shipping_fee, tax, total_amount, status, 
                       voucher_id, voucher_code, discount_amount, final_total) 
                      VALUES 
                      (:user_id, :name, :phone, :email, :address, :payment_method, :order_code,
                       :subtotal, 0, 0, :total_amount, 'pending',
                       :voucher_id, :voucher_code, :discount_amount, :final_total)";
            
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':name', $name);
            $stmt->bindParam(':phone', $phone);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':address', $shippingAddress);
            $stmt->bindParam(':payment_method', $paymentMethod);
            $stmt->bindParam(':order_code', $orderCode);
            $stmt->bindParam(':subtotal', $subtotal);
            $stmt->bindParam(':total_amount', $finalTotal);
            $stmt->bindParam(':voucher_id', $voucherId, PDO::PARAM_INT);
            $stmt->bindParam(':voucher_code', $finalVoucherCode);
            $stmt->bindParam(':discount_amount', $discount);
            $stmt->bindParam(':final_total', $finalTotal);
            $stmt->execute();
            
            $orderId = $this->conn->lastInsertId();

            // INSERT vào order_details
            $detailQuery = "INSERT INTO order_details (order_id, product_id, quantity, price) 
                           VALUES (:order_id, :product_id, :quantity, :price)";
            $detailStmt = $this->conn->prepare($detailQuery);
            
            foreach ($cartItems as $item) {
                $detailStmt->bindParam(':order_id', $orderId, PDO::PARAM_INT);
                $detailStmt->bindParam(':product_id', $item->product_id, PDO::PARAM_INT);
                $detailStmt->bindParam(':quantity', $item->quantity, PDO::PARAM_INT);
                $detailStmt->bindParam(':price', $item->price);
                $detailStmt->execute();
            }

            // Cập nhật số lần sử dụng voucher
            if ($voucherId) {
                $updateVoucher = "UPDATE vouchers SET used_count = used_count + 1 WHERE id = :id";
                $updateStmt = $this->conn->prepare($updateVoucher);
                $updateStmt->bindParam(':id', $voucherId, PDO::PARAM_INT);
                $updateStmt->execute();
            }

            $this->conn->commit();
            return ['success' => true, 'order_id' => $orderId, 'order_code' => $orderCode, 'message' => 'Đặt hàng thành công'];
            
        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => 'Lỗi: ' . $e->getMessage()];
        }
    }

    public function getOrdersByUser($userId) {
        $query = "SELECT * FROM " . $this->table . " WHERE user_id = :user_id ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getAllOrders() {
        $query = "SELECT o.*, a.username, a.fullname 
                  FROM " . $this->table . " o 
                  LEFT JOIN account a ON o.user_id = a.id 
                  ORDER BY o.created_at DESC";
        return $this->conn->query($query)->fetchAll(PDO::FETCH_OBJ);
    }

    public function getOrderById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        
        if (!$order = $stmt->fetch(PDO::FETCH_OBJ)) {
            return null;
        }

        $itemsQuery = "SELECT od.*, p.name as product_name, p.image 
                       FROM order_details od 
                       JOIN product p ON od.product_id = p.id 
                       WHERE od.order_id = :order_id";
        $itemsStmt = $this->conn->prepare($itemsQuery);
        $itemsStmt->bindParam(':order_id', $id, PDO::PARAM_INT);
        $itemsStmt->execute();
        
        $order->items = $itemsStmt->fetchAll(PDO::FETCH_OBJ);
        return $order;
    }

    public function updateOrderStatus($id, $status) {
        $query = "UPDATE " . $this->table . " SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function updatePaymentStatus($id, $status) {
        $query = "UPDATE " . $this->table . " SET payment_status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function cancelOrder($id, $userId) {
        $query = "UPDATE " . $this->table . " SET status = 'cancelled' WHERE id = :id AND user_id = :user_id AND status = 'pending'";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
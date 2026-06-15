<?php
class CartModel {
    private $conn;
    private $table = 'cart';

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getCart($userId) {
        $query = "SELECT c.*, p.name, p.price, p.image, p.category_id 
                  FROM " . $this->table . " c 
                  JOIN product p ON c.product_id = p.id 
                  WHERE c.user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function addToCart($userId, $productId, $quantity = 1) {
        // Check if product exists
        $checkProduct = $this->conn->prepare("SELECT id, price FROM product WHERE id = :id");
        $checkProduct->bindParam(':id', $productId, PDO::PARAM_INT);
        $checkProduct->execute();
        
        if (!$checkProduct->fetch()) {
            return ['success' => false, 'message' => 'Sản phẩm không tồn tại'];
        }

        // Check if already in cart
        $check = $this->conn->prepare("SELECT id, quantity FROM " . $this->table . " WHERE user_id = :user_id AND product_id = :product_id");
        $check->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $check->bindParam(':product_id', $productId, PDO::PARAM_INT);
        $check->execute();
        
        if ($item = $check->fetch(PDO::FETCH_OBJ)) {
            // Update quantity
            $newQty = $item->quantity + $quantity;
            $query = "UPDATE " . $this->table . " SET quantity = :quantity WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':quantity', $newQty, PDO::PARAM_INT);
            $stmt->bindParam(':id', $item->id, PDO::PARAM_INT);
        } else {
            // Insert new
            $query = "INSERT INTO " . $this->table . " (user_id, product_id, quantity) VALUES (:user_id, :product_id, :quantity)";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        }
        
        return $stmt->execute() ? ['success' => true, 'message' => 'Thêm vào giỏ hàng thành công'] : ['success' => false, 'message' => 'Thêm thất bại'];
    }

    public function updateQuantity($userId, $productId, $quantity) {
        if ($quantity <= 0) {
            return $this->removeFromCart($userId, $productId);
        }
        
        $query = "UPDATE " . $this->table . " SET quantity = :quantity WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        
        return $stmt->execute();
    }

    public function removeFromCart($userId, $productId) {
        $query = "DELETE FROM " . $this->table . " WHERE user_id = :user_id AND product_id = :product_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function clearCart($userId) {
        $query = "DELETE FROM " . $this->table . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function getCartTotal($userId) {
        $query = "SELECT SUM(p.price * c.quantity) as total 
                  FROM " . $this->table . " c 
                  JOIN product p ON c.product_id = p.id 
                  WHERE c.user_id = :user_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->total ?? 0;
    }

    public function getCartItemCount($userId) {
        $query = "SELECT COUNT(*) as count FROM " . $this->table . " WHERE user_id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        return $result->count;
    }
}
?>
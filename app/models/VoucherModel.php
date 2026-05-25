<?php
class VoucherModel {
    private $conn;
    private $table = "vouchers";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy danh sách voucher đang hoạt động
    public function getActiveVouchers() {
        $query = "SELECT * FROM " . $this->table . " 
                  WHERE status = 1 AND NOW() BETWEEN start_date AND end_date 
                  ORDER BY id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // Tìm voucher theo mã
    public function getVoucherByCode($code) {
        $query = "SELECT * FROM " . $this->table . " WHERE code = :code LIMIT 1";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':code', strtoupper(trim($code)));
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    // Kiểm tra điều kiện sử dụng voucher
    public function validateVoucher($voucher, $cartSubtotal) {
        if (!$voucher) return ['valid' => false, 'message' => 'Mã voucher không tồn tại.'];
        if ($voucher->status != 1) return ['valid' => false, 'message' => 'Voucher đã ngừng hoạt động.'];
        if (strtotime($voucher->start_date) > time() || strtotime($voucher->end_date) < time()) {
            return ['valid' => false, 'message' => 'Voucher đã hết hạn hoặc chưa đến thời gian sử dụng.'];
        }
        if ($voucher->usage_limit > 0 && $voucher->used_count >= $voucher->usage_limit) {
            return ['valid' => false, 'message' => 'Voucher đã hết lượt sử dụng.'];
        }
        if ($cartSubtotal < $voucher->min_order_value) {
            return ['valid' => false, 'message' => 'Đơn hàng chưa đạt giá trị tối thiểu (' . number_format($voucher->min_order_value, 0, ',', '.') . ' đ).'];
        }
        return ['valid' => true, 'message' => ''];
    }

    // Tăng số lần sử dụng
    public function incrementUsage($id) {
        $query = "UPDATE " . $this->table . " SET used_count = used_count + 1 WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}
?>
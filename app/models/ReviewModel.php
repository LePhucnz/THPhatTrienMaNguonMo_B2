<?php
class ReviewModel {
    private $conn;
    private $table_name = "reviews";

    public function __construct($db) {
        $this->conn = $db;
    }

    // Lấy danh sách bình luận của một sản phẩm
    public function getReviewsByProductId($product_id) {
        $query = "SELECT * FROM " . $this->table_name . " 
                  WHERE product_id = :product_id 
                  ORDER BY created_at DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    // ✅ TÍNH ĐIỂM TRUNG BÌNH
    public function getAverageRating($product_id) {
        $query = "SELECT AVG(rating) as avg_rating, COUNT(*) as total_reviews 
                  FROM " . $this->table_name . " 
                  WHERE product_id = :product_id";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_OBJ);
        
        return [
            'average' => $result->avg_rating ? round($result->avg_rating, 1) : 0,
            'total' => $result->total_reviews
        ];
    }

    // Thêm bình luận mới
    public function addReview($product_id, $username, $rating, $content) {
        $query = "INSERT INTO " . $this->table_name . " 
                  (product_id, username, rating, content) 
                  VALUES(:product_id, :username, :rating, :content)";
        
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':product_id', $product_id, PDO::PARAM_INT);
        $stmt->bindParam(':username', htmlspecialchars(strip_tags($username)));
        $stmt->bindParam(':rating', $rating, PDO::PARAM_INT);
        $stmt->bindParam(':content', htmlspecialchars(strip_tags($content)));

        return $stmt->execute();
    }
}
?>
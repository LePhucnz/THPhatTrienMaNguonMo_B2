<?php
class CategoryModel {
    private $conn;
    private $table_name = "category";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getCategories() {
        $query = "SELECT * FROM " . $this->table_name . " ORDER BY id ASC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getCategoryById($id) {
        $query = "SELECT * FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function addCategory($name, $description) {
        $query = "INSERT INTO " . $this->table_name . " (name, description) 
                  VALUES(:name, :description)";
        $stmt = $this->conn->prepare($query);
        
        // ✅ Gán vào biến trước để tránh lỗi "Only variables should be passed by reference"
        $cleanName = htmlspecialchars(strip_tags($name));
        $cleanDesc = htmlspecialchars(strip_tags($description));
        
        $stmt->bindParam(':name', $cleanName);
        $stmt->bindParam(':description', $cleanDesc);
        
        return $stmt->execute();
    }

    public function updateCategory($id, $name, $description) {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, description = :description 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        
        // ✅ Gán vào biến trước
        $cleanName = htmlspecialchars(strip_tags($name));
        $cleanDesc = htmlspecialchars(strip_tags($description));
        
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $cleanName);
        $stmt->bindParam(':description', $cleanDesc);
        
        return $stmt->execute();
    }

    public function deleteCategory($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
}
?>
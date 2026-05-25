<?php
class ProductModel {
    private $conn;
    private $table_name = "product";

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getProducts() {
        $query = "SELECT p.id, p.name, p.description, p.price, p.image, c.name as category_name 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN category c ON p.category_id = c.id
                  ORDER BY p.id DESC";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getProductsByCategory($categoryId = null) {
        if ($categoryId) {
            $query = "SELECT p.id, p.name, p.description, p.price, p.image, c.name as category_name 
                      FROM " . $this->table_name . " p 
                      LEFT JOIN category c ON p.category_id = c.id
                      WHERE p.category_id = :category_id
                      ORDER BY p.id DESC";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':category_id', $categoryId, PDO::PARAM_INT);
        } else {
            $query = "SELECT p.id, p.name, p.description, p.price, p.image, c.name as category_name 
                      FROM " . $this->table_name . " p 
                      LEFT JOIN category c ON p.category_id = c.id
                      ORDER BY p.id DESC";
            $stmt = $this->conn->prepare($query);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function searchProducts($keyword) {
        $query = "SELECT p.id, p.name, p.description, p.price, p.image, c.name as category_name 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN category c ON p.category_id = c.id
                  WHERE p.name LIKE :keyword OR p.description LIKE :keyword
                  ORDER BY p.id DESC";
        $stmt = $this->conn->prepare($query);
        $searchTerm = "%{$keyword}%";
        $stmt->bindParam(':keyword', $searchTerm);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }

    public function getProductById($id) {
        $query = "SELECT p.*, c.name as category_name 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN category c ON p.category_id = c.id 
                  WHERE p.id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function addProduct($name, $description, $price, $category_id, $image) {
        $errors = [];
        if(empty($name)) $errors['name'] = 'Tên sản phẩm không được để trống';
        if(empty($description)) $errors['description'] = 'Mô tả không được để trống';
        if(!is_numeric($price) || $price < 0) $errors['price'] = 'Giá sản phẩm không hợp lệ';
        if(count($errors) > 0) return $errors;

        $query = "INSERT INTO " . $this->table_name . " 
                  (name, description, price, category_id, image) 
                  VALUES(:name, :description, :price, :category_id, :image)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':name', htmlspecialchars(strip_tags($name)));
        $stmt->bindParam(':description', htmlspecialchars(strip_tags($description)));
        $stmt->bindParam(':price', htmlspecialchars(strip_tags($price)), PDO::PARAM_STR);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->bindParam(':image', htmlspecialchars(strip_tags($image)));

        return $stmt->execute();
    }

    public function updateProduct($id, $name, $description, $price, $category_id, $image) {
        $query = "UPDATE " . $this->table_name . " 
                  SET name = :name, description = :description, price = :price, 
                      category_id = :category_id, image = :image 
                  WHERE id = :id";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->bindParam(':name', htmlspecialchars(strip_tags($name)));
        $stmt->bindParam(':description', htmlspecialchars(strip_tags($description)));
        $stmt->bindParam(':price', htmlspecialchars(strip_tags($price)), PDO::PARAM_STR);
        $stmt->bindParam(':category_id', $category_id, PDO::PARAM_INT);
        $stmt->bindParam(':image', htmlspecialchars(strip_tags($image)));

        return $stmt->execute();
    }

    public function deleteProduct($id) {
        $query = "DELETE FROM " . $this->table_name . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    // ✅ LỌC THEO KHOẢNG GIÁ
    public function getProductsByPriceRange($minPrice = null, $maxPrice = null, $categoryId = null) {
        $query = "SELECT p.id, p.name, p.description, p.price, p.image, c.name as category_name 
                  FROM " . $this->table_name . " p 
                  LEFT JOIN category c ON p.category_id = c.id 
                  WHERE 1=1";
        
        $params = [];
        
        if ($minPrice !== null && $minPrice !== '') {
            $query .= " AND p.price >= :min_price";
            $params[':min_price'] = (float)$minPrice;
        }
        
        if ($maxPrice !== null && $maxPrice !== '') {
            $query .= " AND p.price <= :max_price";
            $params[':max_price'] = (float)$maxPrice;
        }
        
        if ($categoryId !== null && $categoryId !== '') {
            $query .= " AND p.category_id = :category_id";
            $params[':category_id'] = (int)$categoryId;
        }
        
        $query .= " ORDER BY p.id DESC";
        
        $stmt = $this->conn->prepare($query);
        
        foreach ($params as $key => $value) {
            if (is_float($value)) {
                $stmt->bindValue($key, $value, PDO::PARAM_STR);
            } elseif (is_int($value)) {
                $stmt->bindValue($key, $value, PDO::PARAM_INT);
            } else {
                $stmt->bindValue($key, $value);
            }
        }
        
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_OBJ);
    }
    
    // ✅ LẤY KHOẢNG GIÁ MIN/MAX
    public function getPriceRange() {
        $query = "SELECT MIN(price) as min_price, MAX(price) as max_price FROM " . $this->table_name;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_OBJ);
    }

    public function addToCartAjax() {
        header('Content-Type: application/json');
        SessionHelper::start();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['product_id'] ?? null;
            
            if (!$id) {
                echo json_encode(['success' => false, 'message' => 'Invalid product ID']);
                return;
            }
            
            $product = $this->productModel->getProductById($id);
            if (!$product) {
                echo json_encode(['success' => false, 'message' => 'Product not found']);
                return;
            }
            
            // Khởi tạo giỏ hàng nếu chưa có
            if (!isset($_SESSION['cart'])) {
                $_SESSION['cart'] = [];
            }
            
            // Nếu sản phẩm đã có trong giỏ, tăng số lượng
            if (isset($_SESSION['cart'][$id])) {
                $_SESSION['cart'][$id]['quantity']++;
            } else {
                // Thêm sản phẩm mới vào giỏ
                $_SESSION['cart'][$id] = [
                    'name' => $product->name,
                    'price' => $product->price,
                    'image' => $product->image,
                    'quantity' => 1
                ];
            }
            
            // Tính tổng số lượng sản phẩm trong giỏ
            $totalItems = array_sum(array_column($_SESSION['cart'], 'quantity'));
            
            echo json_encode([
                'success' => true, 
                'message' => 'Đã thêm vào giỏ hàng',
                'totalItems' => $totalItems
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Invalid request method']);
        }
    }
}
?>
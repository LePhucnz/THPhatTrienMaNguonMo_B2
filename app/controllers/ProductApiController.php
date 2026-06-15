<?php
<<<<<<< HEAD
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/ProductModel.php';
require_once __DIR__ . '/../helpers/JwtHelper.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
=======
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
>>>>>>> bb8e51174b687910ab3573f6eedac9644ec186a6

class ProductApiController {
    private $productModel;
    private $db;

    public function __construct() {
<<<<<<< HEAD
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
    }

    // GET /api/product - Công khai
=======
        $this->db           = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
    }

    // GET /api/product
>>>>>>> bb8e51174b687910ab3573f6eedac9644ec186a6
    public function index() {
        header('Content-Type: application/json');
        $products = $this->productModel->getProducts();
        echo json_encode($products);
    }

<<<<<<< HEAD
    // GET /api/product/{id} - Công khai
=======
    // GET /api/product/{id}
>>>>>>> bb8e51174b687910ab3573f6eedac9644ec186a6
    public function show($id) {
        header('Content-Type: application/json');
        $product = $this->productModel->getProductById($id);
        if ($product) {
            echo json_encode($product);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Product not found']);
        }
    }

<<<<<<< HEAD
    // GET /api/product?search=keyword - Công khai
    public function search($keyword) {
        header('Content-Type: application/json');
        $products = $this->productModel->searchProducts($keyword);
        echo json_encode($products);
    }

    // GET /api/product?category=id - Công khai
    public function getByCategory($categoryId) {
        header('Content-Type: application/json');
        $products = $this->productModel->getProductsByCategory($categoryId);
        echo json_encode($products);
    }

    // GET /api/product?sort=ASC|DESC - Công khai
    public function sortByPrice($order = 'ASC') {
        header('Content-Type: application/json');
        $products = $this->productModel->getProductsSortedByPrice($order);
        echo json_encode($products);
    }

    // ✅ POST /api/product - CHỈ ADMIN
    public function store() {
        header('Content-Type: application/json');
        AuthMiddleware::requireAdmin(); // ← THÊM DÒNG NÀY
        
        $data = json_decode(file_get_contents("php://input"), true);
        $name = $data['name'] ?? '';
        $description = $data['description'] ?? '';
        $price = $data['price'] ?? '';
        $category_id = $data['category_id'] ?? null;
        $image = $data['image'] ?? '';
=======
    // POST /api/product
    public function store() {
        header('Content-Type: application/json');
        $data        = json_decode(file_get_contents("php://input"), true);
        $name        = $data['name']        ?? '';
        $description = $data['description'] ?? '';
        $price       = $data['price']       ?? '';
        $category_id = $data['category_id'] ?? null;
        $image       = $data['image']       ?? ''; // ✅ thêm image
>>>>>>> bb8e51174b687910ab3573f6eedac9644ec186a6

        $result = $this->productModel->addProduct($name, $description, $price, $category_id, $image);
        if (is_array($result)) {
            http_response_code(400);
            echo json_encode(['errors' => $result]);
        } else {
            http_response_code(201);
            echo json_encode(['message' => 'Product created successfully']);
        }
    }

<<<<<<< HEAD
    // ✅ PUT /api/product/{id} - CHỈ ADMIN
    public function update($id) {
        header('Content-Type: application/json');
        AuthMiddleware::requireAdmin(); // ← THÊM DÒNG NÀY
        
        $data = json_decode(file_get_contents("php://input"), true);
        $name = $data['name'] ?? '';
        $description = $data['description'] ?? '';
        $price = $data['price'] ?? '';
        $category_id = $data['category_id'] ?? null;
        $image = $data['image'] ?? '';

        if (empty($image)) {
            $existing = $this->productModel->getProductById($id);
            $image = $existing ? $existing->image : '';
=======
    // PUT /api/product/{id}
    public function update($id) {
        header('Content-Type: application/json');
        $data        = json_decode(file_get_contents("php://input"), true);
        $name        = $data['name']        ?? '';
        $description = $data['description'] ?? '';
        $price       = $data['price']       ?? '';
        $category_id = $data['category_id'] ?? null;
        $image       = $data['image']       ?? ''; 

        // Nếu không gửi image lên thì giữ nguyên ảnh cũ
        if (empty($image)) {
            $existing = $this->productModel->getProductById($id);
            $image    = $existing ? $existing->image : '';
>>>>>>> bb8e51174b687910ab3573f6eedac9644ec186a6
        }

        $result = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image);
        if ($result) {
            echo json_encode(['message' => 'Product updated successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Product update failed']);
        }
    }

<<<<<<< HEAD
    // ✅ DELETE /api/product/{id} - CHỈ ADMIN
    public function destroy($id) {
        header('Content-Type: application/json');
        AuthMiddleware::requireAdmin(); // ← THÊM DÒNG NÀY
        
=======
    // DELETE /api/product/{id}
    public function destroy($id) {
        header('Content-Type: application/json');
>>>>>>> bb8e51174b687910ab3573f6eedac9644ec186a6
        $result = $this->productModel->deleteProduct($id);
        if ($result) {
            echo json_encode(['message' => 'Product deleted successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Product deletion failed']);
        }
    }
<<<<<<< HEAD
=======

    // Hiển thị danh sách sản phẩm với jQuery
public function list_jquery() {
    include 'app/views/product/list_jquery.php';
}

// Form thêm sản phẩm với jQuery
public function add_jquery() {
    include 'app/views/product/add_jquery.php';
}

// Form sửa sản phẩm với jQuery
public function edit_jquery($id) {
    $editId = $id;
    include 'app/views/product/edit_jquery.php';
}

>>>>>>> bb8e51174b687910ab3573f6eedac9644ec186a6
}
?>
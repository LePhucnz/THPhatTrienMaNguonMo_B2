<?php
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');

class ProductApiController {
    private $productModel;
    private $db;

    public function __construct() {
        $this->db           = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
    }

    // GET /api/product
    public function index() {
        header('Content-Type: application/json');
        $products = $this->productModel->getProducts();
        echo json_encode($products);
    }

    // GET /api/product/{id}
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

    // POST /api/product
    public function store() {
        header('Content-Type: application/json');
        $data        = json_decode(file_get_contents("php://input"), true);
        $name        = $data['name']        ?? '';
        $description = $data['description'] ?? '';
        $price       = $data['price']       ?? '';
        $category_id = $data['category_id'] ?? null;
        $image       = $data['image']       ?? ''; // ✅ thêm image

        $result = $this->productModel->addProduct($name, $description, $price, $category_id, $image);
        if (is_array($result)) {
            http_response_code(400);
            echo json_encode(['errors' => $result]);
        } else {
            http_response_code(201);
            echo json_encode(['message' => 'Product created successfully']);
        }
    }

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
        }

        $result = $this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image);
        if ($result) {
            echo json_encode(['message' => 'Product updated successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Product update failed']);
        }
    }

    // DELETE /api/product/{id}
    public function destroy($id) {
        header('Content-Type: application/json');
        $result = $this->productModel->deleteProduct($id);
        if ($result) {
            echo json_encode(['message' => 'Product deleted successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Product deletion failed']);
        }
    }

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

}
?>
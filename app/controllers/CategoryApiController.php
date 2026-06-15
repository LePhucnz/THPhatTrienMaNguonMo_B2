<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../helpers/JwtHelper.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';

class CategoryApiController {
    private $categoryModel;
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->categoryModel = new CategoryModel($this->db);
    }

    // GET /api/category - Công khai
    public function index() {
        header('Content-Type: application/json');
        $categories = $this->categoryModel->getCategories();
        echo json_encode($categories);
    }

    // GET /api/category/{id} - Công khai
    public function show($id) {
        header('Content-Type: application/json');
        $category = $this->categoryModel->getCategoryById($id);
        if ($category) {
            echo json_encode($category);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Category not found']);
        }
    }

    // ✅ POST /api/category - CHỈ ADMIN
    public function store() {
        header('Content-Type: application/json');
        AuthMiddleware::requireAdmin(); // ← THÊM DÒNG NÀY
        
        $data = json_decode(file_get_contents("php://input"), true);
        $name = $data['name'] ?? '';
        $description = $data['description'] ?? '';

        if (empty($name)) {
            http_response_code(400);
            echo json_encode(['errors' => ['name' => 'Tên danh mục không được để trống']]);
            return;
        }

        $result = $this->categoryModel->addCategory($name, $description);
        if ($result) {
            http_response_code(201);
            echo json_encode(['message' => 'Category created successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Category creation failed']);
        }
    }

    // ✅ PUT /api/category/{id} - CHỈ ADMIN
    public function update($id) {
        header('Content-Type: application/json');
        AuthMiddleware::requireAdmin(); // ← THÊM DÒNG NÀY
        
        $data = json_decode(file_get_contents("php://input"), true);
        $name = $data['name'] ?? '';
        $description = $data['description'] ?? '';

        if (empty($name)) {
            http_response_code(400);
            echo json_encode(['errors' => ['name' => 'Tên danh mục không được để trống']]);
            return;
        }

        $result = $this->categoryModel->updateCategory($id, $name, $description);
        if ($result) {
            echo json_encode(['message' => 'Category updated successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Category update failed']);
        }
    }

    // ✅ DELETE /api/category/{id} - CHỈ ADMIN
    public function destroy($id) {
        header('Content-Type: application/json');
        AuthMiddleware::requireAdmin(); // ← THÊM DÒNG NÀY
        
        // Kiểm tra danh mục có sản phẩm không
        if ($this->categoryModel->hasProducts($id)) {
            http_response_code(400);
            echo json_encode(['message' => 'Không thể xóa danh mục vẫn còn sản phẩm']);
            return;
        }
        
        $result = $this->categoryModel->deleteCategory($id);
        if ($result) {
            echo json_encode(['message' => 'Category deleted successfully']);
        } else {
            http_response_code(400);
            echo json_encode(['message' => 'Category deletion failed']);
        }
    }
}
?>
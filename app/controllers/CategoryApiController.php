<?php
<<<<<<< HEAD
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/CategoryModel.php';
require_once __DIR__ . '/../helpers/JwtHelper.php';
require_once __DIR__ . '/../middleware/AuthMiddleware.php';
=======
require_once(__DIR__ . '/../config/database.php');
require_once(__DIR__ . '/../models/CategoryModel.php');
>>>>>>> bb8e51174b687910ab3573f6eedac9644ec186a6

class CategoryApiController {
    private $categoryModel;
    private $db;

    public function __construct() {
<<<<<<< HEAD
        $this->db = (new Database())->getConnection();
        $this->categoryModel = new CategoryModel($this->db);
    }

    // GET /api/category - Công khai
=======
        $this->db            = (new Database())->getConnection();
        $this->categoryModel = new CategoryModel($this->db);
    }

    // GET /api/category - Lấy danh sách danh mục
>>>>>>> bb8e51174b687910ab3573f6eedac9644ec186a6
    public function index() {
        header('Content-Type: application/json');
        $categories = $this->categoryModel->getCategories();
        echo json_encode($categories);
    }

<<<<<<< HEAD
    // GET /api/category/{id} - Công khai
=======
    // GET /api/category/{id} - Lấy danh mục theo ID
>>>>>>> bb8e51174b687910ab3573f6eedac9644ec186a6
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

<<<<<<< HEAD
    // ✅ POST /api/category - CHỈ ADMIN
    public function store() {
        header('Content-Type: application/json');
        AuthMiddleware::requireAdmin(); // ← THÊM DÒNG NÀY
        
        $data = json_decode(file_get_contents("php://input"), true);
        $name = $data['name'] ?? '';
=======
    // POST /api/category - Tạo danh mục mới
    public function store() {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        
        $name        = $data['name']        ?? '';
>>>>>>> bb8e51174b687910ab3573f6eedac9644ec186a6
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

<<<<<<< HEAD
    // ✅ PUT /api/category/{id} - CHỈ ADMIN
    public function update($id) {
        header('Content-Type: application/json');
        AuthMiddleware::requireAdmin(); // ← THÊM DÒNG NÀY
        
        $data = json_decode(file_get_contents("php://input"), true);
        $name = $data['name'] ?? '';
=======
    // PUT /api/category/{id} - Cập nhật danh mục
    public function update($id) {
        header('Content-Type: application/json');
        $data = json_decode(file_get_contents("php://input"), true);
        
        $name        = $data['name']        ?? '';
>>>>>>> bb8e51174b687910ab3573f6eedac9644ec186a6
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

<<<<<<< HEAD
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
        
=======
    // DELETE /api/category/{id} - Xóa danh mục
    public function destroy($id) {
        header('Content-Type: application/json');
>>>>>>> bb8e51174b687910ab3573f6eedac9644ec186a6
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
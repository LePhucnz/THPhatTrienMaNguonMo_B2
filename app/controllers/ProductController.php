<?php
require_once('app/config/database.php');
require_once('app/models/ProductModel.php');
require_once('app/models/CategoryModel.php');
require_once('app/models/ReviewModel.php');
require_once('app/helpers/SessionHelper.php');

class ProductController {
    private $productModel;
    private $db;

    public function __construct() {
        $this->db = (new Database())->getConnection();
        $this->productModel = new ProductModel($this->db);
    }

    public function index() {
        $categoryId = $_GET['category'] ?? null;
        $keyword = $_GET['keyword'] ?? null;
        $minPrice = $_GET['min_price'] ?? null;
        $maxPrice = $_GET['max_price'] ?? null;
        
        if ($minPrice !== null || $maxPrice !== null) {
            $products = $this->productModel->getProductsByPriceRange($minPrice, $maxPrice, $categoryId);
        } elseif ($keyword) {
            $products = $this->productModel->searchProducts($keyword);
        } elseif ($categoryId) {
            $products = $this->productModel->getProductsByCategory($categoryId);
        } else {
            $products = $this->productModel->getProducts();
        }
        
        include 'app/views/product/list.php';
    }

    public function search() {
        $keyword = $_GET['keyword'] ?? '';
        $products = $this->productModel->searchProducts($keyword);
        include 'app/views/product/list.php';
    }

    public function show($id) {
        $product = $this->productModel->getProductById($id);
        
        if ($product) {
            $reviewModel = new ReviewModel($this->db);
            $reviews = $reviewModel->getReviewsByProductId($id);
            $averageRating = $reviewModel->getAverageRating($id);
            include 'app/views/product/show.php';
        } else {
            echo "Không tìm thấy sản phẩm.";
        }
    }

    // ✅ METHOD MỚI: Xử lý lưu bình luận
    public function saveReview() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $product_id = $_POST['product_id'] ?? null;
            $rating = $_POST['rating'] ?? 5;
            $content = $_POST['content'] ?? '';
            $username = $_POST['username'] ?? 'Khách';

            if (!empty($content) && $product_id) {
                $reviewModel = new ReviewModel($this->db);
                $reviewModel->addReview($product_id, $username, $rating, $content);
            }

            header("Location: /Product/show/" . $product_id);
            exit;
        }
    }

    public function add() {
        $categories = (new CategoryModel($this->db))->getCategories();
        include 'app/views/product/add.php';
    }

    public function save() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $name = $_POST['name'] ?? '';
            $description = $_POST['description'] ?? '';
            $price = $_POST['price'] ?? '';
            $category_id = $_POST['category_id'] ?? null;
            $image = '';

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                try {
                    $image = $this->uploadImage($_FILES['image']);
                } catch (Exception $e) {
                    $errors = ['image' => $e->getMessage()];
                    $categories = (new CategoryModel($this->db))->getCategories();
                    include 'app/views/product/add.php';
                    return;
                }
            }

            $result = $this->productModel->addProduct($name, $description, $price, $category_id, $image);
            
            if (is_array($result)) {
                $errors = $result;
                $categories = (new CategoryModel($this->db))->getCategories();
                include 'app/views/product/add.php';
            } else {
                header('Location: /Product');
                exit;
            }
        }
    }

    public function edit($id) {
        $product = $this->productModel->getProductById($id);
        $categories = (new CategoryModel($this->db))->getCategories();
        if ($product) {
            include 'app/views/product/edit.php';
        } else {
            echo "Không tìm thấy sản phẩm.";
        }
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $name = $_POST['name'];
            $description = $_POST['description'];
            $price = $_POST['price'];
            $category_id = $_POST['category_id'];
            $image = $_POST['existing_image'] ?? '';

            if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
                try {
                    $image = $this->uploadImage($_FILES['image']);
                } catch (Exception $e) {
                    echo "Lỗi upload: " . $e->getMessage();
                    return;
                }
            }

            if ($this->productModel->updateProduct($id, $name, $description, $price, $category_id, $image)) {
                header('Location: /Product');
                exit;
            } else {
                echo "Lỗi cập nhật!";
            }
        }
    }

    public function delete($id) {
        $this->productModel->deleteProduct($id);
        header('Location: /Product');
        exit;
    }

    private function uploadImage($file) {
        $target_dir = "public/uploads/";
        
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $target_file = $target_dir . basename($file["name"]);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        $check = getimagesize($file["tmp_name"]);
        if ($check === false) {
            throw new Exception("File không phải là hình ảnh.");
        }

        if ($file["size"] > 10 * 1024 * 1024) {
            throw new Exception("Hình ảnh có kích thước quá lớn (>10MB).");
        }

        if (!in_array($imageFileType, ["jpg", "jpeg", "png", "gif"])) {
            throw new Exception("Chỉ cho phép các định dạng JPG, JPEG, PNG và GIF.");
        }

        if (!move_uploaded_file($file["tmp_name"], $target_file)) {
            throw new Exception("Có lỗi xảy ra khi tải lên hình ảnh.");
        }

        return str_replace('public/', '', $target_file);
    }
}
?>
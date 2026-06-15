<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

<<<<<<< HEAD
=======
// Handle preflight requests
>>>>>>> bb8e51174b687910ab3573f6eedac9644ec186a6
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

<<<<<<< HEAD
chdir(__DIR__ . '/..');

require_once(__DIR__ . '/../app/config/database.php');
require_once(__DIR__ . '/../app/helpers/JwtHelper.php');
require_once(__DIR__ . '/../app/middleware/AuthMiddleware.php');

require_once(__DIR__ . '/../app/controllers/AccountApiController.php');
require_once(__DIR__ . '/../app/controllers/CategoryApiController.php');
require_once(__DIR__ . '/../app/controllers/ProductApiController.php');
require_once(__DIR__ . '/../app/controllers/CartApiController.php');
require_once(__DIR__ . '/../app/controllers/OrderApiController.php');

=======
// Đổi thư mục làm việc về thư mục gốc của project
chdir(__DIR__ . '/..');

require_once(__DIR__ . '/../app/config/database.php');
require_once(__DIR__ . '/../app/models/CategoryModel.php');
require_once(__DIR__ . '/../app/models/ProductModel.php');
require_once(__DIR__ . '/../app/models/AccountModel.php');
require_once(__DIR__ . '/../app/controllers/CategoryApiController.php');
require_once(__DIR__ . '/../app/controllers/ProductApiController.php');
require_once(__DIR__ . '/../app/controllers/AccountApiController.php'); // ✅ Thêm dòng này

// Lấy URL và method
>>>>>>> bb8e51174b687910ab3573f6eedac9644ec186a6
$requestUri = $_SERVER['REQUEST_URI'];
$basePath = '/api';
$path = str_replace($basePath, '', parse_url($requestUri, PHP_URL_PATH));
$path = trim($path, '/');
$method = $_SERVER['REQUEST_METHOD'];

// Parse path segments
<<<<<<< HEAD
$segments = array_values(array_filter(explode('/', $path), function($v) { return $v !== ''; }));

$resource = $segments[0] ?? '';
$seg1 = $segments[1] ?? null;  // Segment thứ 1 (sau resource)
$seg2 = $segments[2] ?? null;  // Segment thứ 2
$seg3 = $segments[3] ?? null;  // Segment thứ 3

try {
    switch ($resource) {
        // ==================== ACCOUNT ====================
        // /api/account/register         → seg1='register'
        // /api/account/login            → seg1='login'
        // /api/account/me               → seg1='me'
        // /api/account/profile          → seg1='profile'
        // /api/account/change-password  → seg1='change-password'
        // /api/account/forgot-password  → seg1='forgot-password'
        // /api/account/reset-password   → seg1='reset-password'
        // /api/account                  → seg1=null (Admin: list all)
        // /api/account/{id}/toggle-lock → seg1=id, seg2='toggle-lock'
        // /api/account/{id}             → seg1=id (DELETE)
        case 'account':
            $controller = new AccountApiController();
            
            if ($method === 'POST' && $seg1 === 'register') {
                $controller->register();
            } elseif ($method === 'POST' && $seg1 === 'login') {
                $controller->login();
            } elseif ($method === 'GET' && $seg1 === 'me') {
                $controller->me();
            } elseif ($method === 'PUT' && $seg1 === 'profile') {
                $controller->updateProfile();
            } elseif ($method === 'PUT' && $seg1 === 'change-password') {
                $controller->changePassword();
            } elseif ($method === 'POST' && $seg1 === 'forgot-password') {
                $controller->forgotPassword();
            } elseif ($method === 'POST' && $seg1 === 'reset-password') {
                $controller->resetPassword();
            } elseif ($method === 'GET' && $seg1 === null) {
                $controller->index(); // Admin only
            } elseif ($method === 'PUT' && $seg2 === 'toggle-lock' && is_numeric($seg1)) {
                $controller->toggleLock($seg1);
            } elseif ($method === 'DELETE' && is_numeric($seg1)) {
                $controller->destroy($seg1);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Account endpoint not found: ' . $requestUri]);
            }
            break;

        // ==================== CATEGORY ====================
        // /api/category       → seg1=null (GET list, POST create)
        // /api/category/{id}  → seg1=id (GET, PUT, DELETE)
        case 'category':
            $controller = new CategoryApiController();
            
            if ($method === 'GET' && $seg1 === null) {
                $controller->index();
            } elseif ($method === 'GET' && is_numeric($seg1)) {
                $controller->show($seg1);
            } elseif ($method === 'POST' && $seg1 === null) {
                $controller->store();
            } elseif ($method === 'PUT' && is_numeric($seg1)) {
                $controller->update($seg1);
            } elseif ($method === 'DELETE' && is_numeric($seg1)) {
                $controller->destroy($seg1);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Category endpoint not found']);
            }
            break;

        // ==================== PRODUCT ====================
        // /api/product              → seg1=null (GET list, POST create)
        // /api/product?search=...   → query params
        // /api/product/{id}         → seg1=id
        case 'product':
            $controller = new ProductApiController();
            
            if ($method === 'GET' && $seg1 === null) {
                if (isset($_GET['search'])) {
                    $controller->search($_GET['search']);
                } elseif (isset($_GET['category'])) {
                    $controller->getByCategory($_GET['category']);
                } elseif (isset($_GET['sort'])) {
                    $controller->sortByPrice($_GET['sort']);
                } else {
                    $controller->index();
                }
            } elseif ($method === 'GET' && is_numeric($seg1)) {
                $controller->show($seg1);
            } elseif ($method === 'POST' && $seg1 === null) {
                $controller->store();
            } elseif ($method === 'PUT' && is_numeric($seg1)) {
                $controller->update($seg1);
            } elseif ($method === 'DELETE' && is_numeric($seg1)) {
                $controller->destroy($seg1);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Product endpoint not found']);
            }
            break;

        // ==================== CART ====================
        // /api/cart              → seg1=null (GET list)
        // /api/cart/add          → seg1='add' (POST)
        // /api/cart/update       → seg1='update' (PUT)
        // /api/cart/clear        → seg1='clear' (DELETE)
        // /api/cart/{productId}  → seg1=productId (DELETE)
        case 'cart':
            $controller = new CartApiController();
            
            if ($method === 'GET' && $seg1 === null) {
                $controller->index();
            } elseif ($method === 'POST' && $seg1 === 'add') {
                $controller->addToCart();
            } elseif ($method === 'PUT' && $seg1 === 'update') {
                $controller->updateQuantity();
            } elseif ($method === 'DELETE' && $seg1 === 'clear') {
                $controller->clearCart();
            } elseif ($method === 'DELETE' && is_numeric($seg1)) {
                $controller->removeFromCart($seg1);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Cart endpoint not found: ' . $requestUri]);
            }
            break;

        // ==================== ORDER ====================
        // /api/order/create           → seg1='create' (POST)
        // /api/order/my-orders        → seg1='my-orders' (GET)
        // /api/order/admin/all        → seg1='admin', seg2='all' (GET)
        // /api/order/{id}             → seg1=id (GET)
        // /api/order/{id}/cancel      → seg1=id, seg2='cancel' (PUT)
        // /api/order/{id}/status      → seg1=id, seg2='status' (PUT)
        // /api/order/{id}/payment     → seg1=id, seg2='payment' (PUT)
        case 'order':
            $controller = new OrderApiController();
            
            if ($method === 'POST' && $seg1 === 'create') {
                $controller->createOrder();
            } elseif ($method === 'GET' && $seg1 === 'my-orders') {
                $controller->myOrders();
            } elseif ($method === 'GET' && $seg1 === 'admin' && $seg2 === 'all') {
                $controller->getAllOrders();
            } elseif ($method === 'GET' && is_numeric($seg1)) {
                $controller->show($seg1);
            } elseif ($method === 'PUT' && $seg2 === 'cancel' && is_numeric($seg1)) {
                $controller->cancelOrder($seg1);
            } elseif ($method === 'PUT' && $seg2 === 'status' && is_numeric($seg1)) {
                $controller->updateStatus($seg1);
            } elseif ($method === 'PUT' && $seg2 === 'payment' && is_numeric($seg1)) {
                $controller->updatePayment($seg1);
            } else {
                http_response_code(404);
                echo json_encode(['message' => 'Order endpoint not found: ' . $requestUri]);
=======
$segments = explode('/', $path);
$resource = $segments[0] ?? '';
$id = $segments[1] ?? null;

try {
    switch ($resource) {
        case 'product':
            $controller = new ProductApiController();
            
            if ($method === 'GET') {
                if ($id) {
                    $controller->show($id);
                } else {
                    $controller->index();
                }
            } elseif ($method === 'POST') {
                $controller->store();
            } elseif ($method === 'PUT') {
                if ($id) {
                    $controller->update($id);
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'Product ID is required']);
                }
            } elseif ($method === 'DELETE') {
                if ($id) {
                    $controller->destroy($id);
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'Product ID is required']);
                }
            }
            break;

            case 'category':
                $controller = new CategoryApiController();
                
                if ($method === 'GET') {
                    if ($id) {
                        $controller->show($id);
                    } else {
                        $controller->index();
                    }
                } elseif ($method === 'POST') {
                    $controller->store();
                } elseif ($method === 'PUT') {
                    if ($id) {
                        $controller->update($id);
                    } else {
                        http_response_code(400);
                        echo json_encode(['message' => 'Category ID is required']);
                    }
                } elseif ($method === 'DELETE') {
                    if ($id) {
                        $controller->destroy($id);
                    } else {
                        http_response_code(400);
                        echo json_encode(['message' => 'Category ID is required']);
                    }
                } else {
                    http_response_code(405);
                    echo json_encode(['message' => 'Method not allowed']);
                }
                break;

        case 'account': // ✅ Cập nhật phần này
            $controller = new AccountApiController();
            
            if ($method === 'GET') {
                if ($id) {
                    $controller->show($id);
                } else {
                    $controller->index();
                }
            } elseif ($method === 'POST') {
                $controller->store();
            } elseif ($method === 'PUT') {
                if ($id) {
                    $controller->update($id);
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'Account ID is required']);
                }
            } elseif ($method === 'DELETE') {
                if ($id) {
                    $controller->destroy($id);
                } else {
                    http_response_code(400);
                    echo json_encode(['message' => 'Account ID is required']);
                }
>>>>>>> bb8e51174b687910ab3573f6eedac9644ec186a6
            }
            break;

        default:
            http_response_code(404);
<<<<<<< HEAD
            echo json_encode(['message' => 'Endpoint not found: ' . $requestUri]);
=======
            echo json_encode(['message' => 'Endpoint not found']);
>>>>>>> bb8e51174b687910ab3573f6eedac9644ec186a6
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'message' => 'Server error',
<<<<<<< HEAD
        'error' => $e->getMessage(),
        'file' => $e->getFile(),
        'line' => $e->getLine()
=======
        'error' => $e->getMessage()
>>>>>>> bb8e51174b687910ab3573f6eedac9644ec186a6
    ]);
}
?>
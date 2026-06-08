<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

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
$requestUri = $_SERVER['REQUEST_URI'];
$basePath = '/api';
$path = str_replace($basePath, '', parse_url($requestUri, PHP_URL_PATH));
$path = trim($path, '/');
$method = $_SERVER['REQUEST_METHOD'];

// Parse path segments
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
            }
            break;

        default:
            http_response_code(404);
            echo json_encode(['message' => 'Endpoint not found']);
            break;
    }
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'message' => 'Server error',
        'error' => $e->getMessage()
    ]);
}
?>
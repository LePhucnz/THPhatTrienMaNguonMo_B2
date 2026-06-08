<?php
session_start();
require_once 'app/helpers/SessionHelper.php';
require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';

$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// ✅ Thêm Voucher vào danh sách
$knownControllers = ['Product', 'Category', 'Account', 'Voucher', 'Api'];
if (isset($url[0]) && !in_array(ucfirst($url[0]), $knownControllers)) {
    array_shift($url);
}
// ====== XỬ LÝ API ======
if (isset($url[0]) && strtolower($url[0]) === 'api' && isset($url[1])) {
    $apiControllerName = ucfirst($url[1]) . 'ApiController';
    $apiPath           = 'app/controllers/' . $apiControllerName . '.php';

    if (file_exists($apiPath)) {
        require_once $apiPath;
        $controller = new $apiControllerName();
        $method     = $_SERVER['REQUEST_METHOD'];
        $id         = $url[2] ?? null;

        switch ($method) {
            case 'GET':    $action = $id ? 'show'    : 'index'; break;
            case 'POST':   $action = 'store';                   break;
            case 'PUT':    $action = $id ? 'update'  : null;    break;
            case 'DELETE': $action = $id ? 'destroy' : null;    break;
            default:
                http_response_code(405);
                echo json_encode(['message' => 'Method Not Allowed']);
                exit;
        }

        if ($action && method_exists($controller, $action)) {
            $id ? call_user_func([$controller, $action], $id)
                : call_user_func([$controller, $action]);
        } else {
            http_response_code(404);
            echo json_encode(['message' => 'Action not found']);
        }
    } else {
        http_response_code(404);
        echo json_encode(['message' => 'API Controller not found']);
    }
    exit;
}
// ====== HẾT XỬ LÝ API ======

$controllerName = isset($url[0]) && $url[0] != ''
    ? ucfirst($url[0]) . 'Controller'
    : 'ProductController';

$action = isset($url[1]) && $url[1] != '' ? $url[1] : 'index';

$controllerPath = 'app/controllers/' . $controllerName . '.php';

if (!file_exists($controllerPath)) {
    die('Controller not found: ' . $controllerPath);
}

require_once $controllerPath;
$controller = new $controllerName();

if (!method_exists($controller, $action)) {
    die('Action not found: ' . $action);
}

call_user_func_array([$controller, $action], array_slice($url, 2));
?>
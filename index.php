<?php
session_start();
require_once 'app/helpers/SessionHelper.php';

require_once 'app/config/database.php';
require_once 'app/models/ProductModel.php';

// Lấy URL
$url = $_GET['url'] ?? '';
$url = rtrim($url, '/');
$url = filter_var($url, FILTER_SANITIZE_URL);
$url = explode('/', $url);

// Nếu URL trống, mặc định là Product controller
$controllerName = isset($url[0]) && $url[0] != '' 
    ? ucfirst($url[0]) . 'Controller' 
    : 'ProductController';

$action = isset($url[1]) && $url[1] != '' ? $url[1] : 'index';

// Kiểm tra file controller tồn tại
$controllerPath = 'app/controllers/' . $controllerName . '.php';

if (!file_exists($controllerPath)) {
    die('Controller not found: ' . $controllerPath);
}

require_once $controllerPath;
$controller = new $controllerName();

if (!method_exists($controller, $action)) {
    die('Action not found: ' . $action);
}

// Gọi action với parameters
call_user_func_array([$controller, $action], array_slice($url, 2));
?>
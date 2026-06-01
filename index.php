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
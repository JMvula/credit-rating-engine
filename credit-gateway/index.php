<?php
// index.php — The gateway's router

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');   // tighten this once you have a real frontend domain
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle the browser's CORS pre-flight check
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

require_once 'config.php';
require_once 'ApiClient.php';

$requestUri    = $_SERVER['REQUEST_URI'];
$requestMethod = $_SERVER['REQUEST_METHOD'];

$path = parse_url($requestUri, PHP_URL_PATH);
$path = trim($path, '/');
$segments = explode('/', $path);

if ($segments[0] === 'credit-gateway') {
    array_shift($segments);
}

if ($segments[0] === 'api') {
    array_shift($segments);
}

$resource = $segments[0] ?? null;

function respond($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data, JSON_PRETTY_PRINT);
    exit();
}

switch ($resource) {

    case 'score':
        require_once 'handlers/score.php';
        handleScore($requestMethod);
        break;

    case null:
        respond(['message' => 'Welcome to the ' . APP_NAME, 'version' => '1.0']);
        break;

    default:
        respond(['error' => 'Resource not found: ' . $resource], 404);
}
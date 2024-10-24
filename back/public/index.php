<?php

// Add CORS headers globally
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Handle preflight request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header("HTTP/1.1 200 OK");
    exit;
}

require_once '../autoload.php';

use App\Controllers\UsersController;
use App\Core\Response;
use App\Core\Router;
use App\Core\DIContainer;

$router = new Router();
$container = new DIContainer();

// add allowed routes
$router->register('GET', 'users/form', UsersController::class, 'getRegForm');
$router->register('POST', 'users', UsersController::class, 'addUser');
$router->register('GET', 'users', UsersController::class, 'usersList');
$router->register('GET', 'users/{id}', UsersController::class, 'getUser');
$router->register('DELETE', 'users/{id}', UsersController::class, 'deleteUser');

// set PDO connection for DI container
$container->set(
    PDO::class,
    new PDO("mysql:host=db;port=3306;dbname=cms", 'user', 'pass')
);

/**
 * Handle incoming request
 */
function handle(Router $router, DIContainer $container): Response {
    $uri = $_SERVER['REQUEST_URI'];
    $method = $_SERVER['REQUEST_METHOD'];

    // get route info
    $routeInfo = $router->dispatch($method, $uri);

    if (!$routeInfo) {
        return new Response(404, 'error', 'Not found');
    }

    // create instance in DI container
    $controllerInstance = $container->get($routeInfo['controller']);

    // call to method
    return call_user_func_array([$controllerInstance, $routeInfo['action']], $routeInfo['vars']);
}

// get the response
$response = handle($router, $container);

// set header
header('Content-Type: application/json; charset=utf-8');

// set http code
http_response_code($response->getHttpCode());

// response with json
echo json_encode($response->getData());
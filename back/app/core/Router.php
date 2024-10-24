<?php

namespace App\Core;

use App\Core\JsonResponse;

/**
 * Routes handler
 */
class Router {
    public array $routes = [];

    public function register($method, $path, $controller, $action) {
        $regex = str_replace('/', '\/', preg_replace('/{([^}]+)}/', '([^/]+)', $path));
        $this->routes[] = [
            'method'    => $method,
            'path'      => "#^$regex$#",
            'controller' => $controller,
            'action'    => $action
        ];
    }

    public function dispatch(string $method, string $uri) :?array{
        $uri = trim(parse_url($uri, PHP_URL_PATH), '/');

        foreach ($this->routes as $r) {
            if ($method === $r['method'] && preg_match($r['path'], $uri, $vars)) {
                array_shift($vars);
                $r['vars'] = $vars;
                return $r;
            }
        }


        return null;
    }
}

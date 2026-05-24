<?php
/**
 * Simple Router class
 */

class Router {
    protected $routes = [];
    protected $notFoundCallback;

    public function add($method, $path, $callback) {
        $method = strtoupper($method);
        $this->routes[] = [
            'method' => $method,
            'path'   => $this->formatPath($path),
            'callback' => $callback
        ];
    }

    public function setNotFound($callback) {
        $this->notFoundCallback = $callback;
    }

    public function dispatch() {
        $requestMethod = $_SERVER['REQUEST_METHOD'];

        // Support ?url= fallback when mod_rewrite is not available
        if (!empty($_GET['url'])) {
            $requestUri = '/' . ltrim(trim($_GET['url']), '/');
        } else {
            $requestUri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
        }

        $scriptName = $_SERVER['SCRIPT_NAME'];

        // Remove the script's base directory from the URI so routes are relative
        $baseDir = dirname($scriptName);
        if ($baseDir !== '/' && $baseDir !== '\\') {
            // Normalise directory separators
            $baseDir = str_replace('\\', '/', $baseDir);
            if (strpos($requestUri, $baseDir) === 0) {
                $requestUri = substr($requestUri, strlen($baseDir));
            }
        }

        // Remove index.php if it appears verbatim in the URI
        if (strpos($requestUri, '/index.php') === 0) {
            $requestUri = substr($requestUri, strlen('/index.php'));
        }

        // Trim slashes
        $requestUri = '/' . trim($requestUri, '/');

        foreach ($this->routes as $route) {
            if ($route['method'] !== $requestMethod) {
                continue;
            }
            $pattern = $this->convertPathToRegex($route['path']);
            if (preg_match($pattern, $requestUri, $matches)) {
                // Extract named parameters
                $params = [];
                foreach ($matches as $key => $value) {
                    if (!is_int($key)) {
                        $params[$key] = $value;
                    }
                }
                // Call the route callback with params
                if (is_callable($route['callback'])) {
                    call_user_func_array($route['callback'], $params);
                } else {
                    // Assume string like "Controller@method"
                    $parts = explode('@', $route['callback']);
                    $controllerName = $parts[0];
                    $method = $parts[1] ?? 'index';
                    if (class_exists($controllerName) && method_exists($controllerName, $method)) {
                        $controller = new $controllerName();
                        call_user_func_array([$controller, $method], $params);
                    } else {
                        $this->handleNotFound();
                    }
                }
                return;
            }
        }
        $this->handleNotFound();
    }

    protected function handleNotFound() {
        if ($this->notFoundCallback) {
            call_user_func($this->notFoundCallback);
        } else {
            header("HTTP/1.0 404 Not Found");
            echo "404 Not Found";
        }
    }

    protected function formatPath($path) {
        // Ensure leading slash and trim trailing slash
        $path = '/' . trim($path, '/');
        return $path;
    }

    protected function convertPathToRegex($path) {
        // Convert /user/{id} to regex with named capture group
        $regex = preg_replace('#\{([^}]+)\}#', '(?P<$1>[^/]+)', $path);
        // Add start and end delimiters
        $regex = '#^' . $regex . '$#';
        return $regex;
    }
}
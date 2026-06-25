<?php
/**
 * Simple Request Router
 * Compatible with PHP 5.2.3
 */
class Router {
    private $routes = array();

    /**
     * Add a route
     * @param string $method GET, POST, etc.
     * @param string $path Route path
     * @param string $handler Controller@method format
     */
    public function add($method, $path, $handler) {
        $this->routes[] = array(
            'method' => strtoupper($method),
            'path' => $path,
            'handler' => $handler
        );
    }

    /**
     * Resolve and dispatch the request
     * @param string $requestMethod GET, POST, etc.
     * @param string $requestPath URL path
     * @return bool
     */
    public function resolve($requestMethod, $requestPath) {
        $requestMethod = strtoupper($requestMethod);
        $requestPath = '/' . ltrim($requestPath, '/');
        
        foreach ($this->routes as $route) {
            if ($route['method'] === $requestMethod) {
                // Compile path to regular expression for simple param matching (if needed)
                // e.g. /clients/edit -> exact match. Or /clients/edit/:id
                $pattern = $route['path'];
                $pattern = preg_replace('/:[a-zA-Z0-9_]+/', '([a-zA-Z0-9_\-]+)', $pattern);
                $pattern = '@^' . $pattern . '$@';

                if (preg_match($pattern, $requestPath, $matches)) {
                    array_shift($matches); // Remove first match
                    
                    // Handler structure: "ControllerName@methodName"
                    $parts = explode('@', $route['handler']);
                    $controllerName = $parts[0];
                    $methodName = $parts[1];

                    if (class_exists($controllerName)) {
                        $controller = new $controllerName();
                        
                        // Check if method exists
                        if (method_exists($controller, $methodName)) {
                            call_user_func_array(array($controller, $methodName), $matches);
                            return true;
                        }
                    }
                }
            }
        }
        
        // 404 Route
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
        echo "<p>La ruta especificada no existe en el sistema.</p>";
        return false;
    }
}

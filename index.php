<?php
/**
 * Front Controller and Autoloader for Call Center Application
 * Compatible with PHP 5.2.3
 */

// Start session
session_start();

// Display errors for development (can be disabled in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Register Custom Autoloader
spl_autoload_register(function ($className) {
    $paths = array(
        'src/Domain/Model/',
        'src/Domain/Repository/',
        'src/Application/UseCase/',
        'src/Infrastructure/Persistence/',
        'src/Infrastructure/Database/',
        'src/Infrastructure/Controller/',
        'src/Infrastructure/'
    );
    
    foreach ($paths as $path) {
        $file = dirname(__FILE__) . '/' . $path . $className . '.php';
        if (file_exists($file)) {
            require_once $file;
            return;
        }
    }
});

// Auto-login from GET parameters (Paso 1 y Paso 13)
if (isset($_GET['email']) && isset($_GET['pass']) && isset($_GET['token']) && $_GET['token'] === 'identificador_unico') {
    $email = trim($_GET['email']);
    $pass = trim($_GET['pass']);
    
    try {
        $db = DatabaseConnection::getInstance();
        $email_escaped = mysqli_real_escape_string($db, $email);
        $sql = "SELECT `id`, `username`, `password` FROM `biartet_users` WHERE `username` = '$email_escaped' LIMIT 1";
        $result = mysqli_query($db, $sql);
        $userRow = $result ? mysqli_fetch_assoc($result) : null;
        
        if (!$userRow) {
            $hashedPass = sha1($pass);
            $insert_sql = "INSERT INTO `biartet_users` (`username`, `password`, `fecha_creacion`, `fecha_actualizacion`) VALUES ('$email_escaped', '$hashedPass', NOW(), NOW())";
            mysqli_query($db, $insert_sql);
            
            // Retrieve again
            $result = mysqli_query($db, $sql);
            $userRow = $result ? mysqli_fetch_assoc($result) : null;
        }
        
        if ($userRow && sha1($pass) === $userRow['password']) {
            $_SESSION['user'] = array(
                'id' => (int)$userRow['id'],
                'username' => $userRow['username']
            );
            $_SESSION['last_activity'] = time();
            
            // Send email notification on login (Paso 12)
            AuthController::sendAuthEmail('login', $email);
            
            header('Location: ./');
            exit();
        }
    } catch (Exception $e) {
        // Silent fallback
    }
}

// Parse Friendly URL Route
$requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
// Remove query string from route matching
if ($pos = strpos($requestUri, '?')) {
    $requestUri = substr($requestUri, 0, $pos);
}

// Remove project base directory (if running in a subfolder like /pendientes/)
$scriptName = isset($_SERVER['SCRIPT_NAME']) ? $_SERVER['SCRIPT_NAME'] : '';
$basePath = dirname($scriptName);
if ($basePath !== '/' && $basePath !== '\\') {
    $basePath = rtrim(str_replace('\\', '/', $basePath), '/');
    if (strpos($requestUri, $basePath) === 0) {
        $requestUri = substr($requestUri, strlen($basePath));
    }
}

// Normalize route
$route = '/' . ltrim($requestUri, '/');

// Initialize Router and define routes
$router = new Router();

// Auth Routes
$router->add('GET', '/login', 'AuthController@showLogin');
$router->add('POST', '/login', 'AuthController@login');
$router->add('GET', '/logout', 'AuthController@logout');
$router->add('POST', '/session/keep-alive', 'AuthController@keepAlive');

// Client Routes
$router->add('GET', '/', 'ClientController@index');
$router->add('GET', '/clients', 'ClientController@index');
$router->add('GET', '/clients/create', 'ClientController@showCreate');
$router->add('GET', '/clients/edit', 'ClientController@showEdit');
$router->add('POST', '/clients/save', 'ClientController@save');
$router->add('GET', '/clients/delete', 'ClientController@delete');

// Order & API Routes
$router->add('POST', '/orders/save', 'ClientController@saveOrder');
$router->add('GET', '/api/locations', 'ClientController@getLocations');

// Report Routes
$router->add('GET', '/report', 'ReportController@download');

// Resolve the route
$requestMethod = isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
$router->resolve($requestMethod, $route);

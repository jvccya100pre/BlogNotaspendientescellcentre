<?php
/**
 * AuthController
 * Compatible with PHP 5.2.3
 */
class AuthController {
    private $authUseCase;

    public function __construct() {
        $userRepository = new MysqlUserRepository();
        $this->authUseCase = new AuthenticateUser($userRepository);
    }

    /**
     * Display login form
     */
    public function showLogin() {
        if (isset($_SESSION['user'])) {
            header('Location: ./');
            exit();
        }
        
        $this->render('login', array(
            'title' => 'Iniciar Sesión',
            'error' => isset($_SESSION['login_error']) ? $_SESSION['login_error'] : null
        ));
        
        unset($_SESSION['login_error']);
    }

    /**
     * Process login request
     */
    public function login() {
        $username = isset($_POST['username']) ? trim($_POST['username']) : '';
        $password = isset($_POST['password']) ? trim($_POST['password']) : '';

        $user = $this->authUseCase->execute($username, $password);
        if ($user !== null) {
            $_SESSION['user'] = array(
                'id' => $user->id,
                'username' => $user->username
            );
            $_SESSION['last_activity'] = time();
            
            // Send login email
            self::sendAuthEmail('login', $username);
            
            header('Location: ./');
            exit();
        } else {
            $_SESSION['login_error'] = 'Credenciales inválidas. Por favor verifique e intente de nuevo.';
            header('Location: ./login');
            exit();
        }
    }

    /**
     * Process logout
     */
    public function logout() {
        if (isset($_SESSION['user'])) {
            self::sendAuthEmail('logout', $_SESSION['user']['username']);
        }
        unset($_SESSION['user']);
        session_destroy();
        
        $inactive = isset($_GET['inactive']) ? '?inactive=1' : '';
        header('Location: ./login' . $inactive);
        exit();
    }

    /**
     * Keep alive session endpoint for AJAX heartbeat (Paso 2)
     */
    public function keepAlive() {
        if (isset($_SESSION['user'])) {
            $_SESSION['last_activity'] = time();
            header('Content-Type: application/json');
            echo json_encode(array('status' => 'success', 'last_activity' => $_SESSION['last_activity']));
            exit();
        }
        header('HTTP/1.0 401 Unauthorized');
        echo json_encode(array('status' => 'error', 'message' => 'Unauthorized'));
        exit();
    }

    /**
     * Send email on login/logout (Paso 12)
     */
    public static function sendAuthEmail($action, $username) {
        $subject = ($action === 'login') ? 'Inicio de Sesión - Call Center' : 'Cierre de Sesión - Call Center';
        $time = date('Y-m-d H:i:s');
        $message = "Hola,\n\nSe ha registrado un " . ($action === 'login' ? 'inicio' : 'cierre') . " de sesión en la cuenta del sistema de Call Center.\n\nUsuario: $username\nFecha y Hora: $time\n\nSaludos,\nCall Center System";
        $headers = 'From: no-reply@createsoftw.com' . "\r\n" .
            'Reply-To: no-reply@createsoftw.com' . "\r\n" .
            'X-Mailer: PHP/' . phpversion();
            
        @mail($username, $subject, $message, $headers);
    }

    /**
     * Render view with layout
     */
    private function render($viewName, $data = array()) {
        extract($data);
        $viewFile = dirname(__FILE__) . '/../Views/' . $viewName . '.php';
        $layoutFile = dirname(__FILE__) . '/../Views/layout.php';
        
        ob_start();
        if (file_exists($viewFile)) {
            include $viewFile;
        } else {
            echo "Vista no encontrada: " . $viewName;
        }
        $content = ob_get_clean();
        
        if (file_exists($layoutFile)) {
            include $layoutFile;
        } else {
            echo $content;
        }
    }
}

<?php
/**
 * MemorandumController for general Announcements / Memorándums
 * Compatible with PHP 5.2.3
 */
require_once dirname(__FILE__) . '/../Database/DatabaseConnection.php';
require_once dirname(__FILE__) . '/../Database/SystemLog.php';

class MemorandumController {

    private function checkAuth() {
        if (!isset($_SESSION['user'])) {
            header('Location: login');
            exit();
        }
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 600)) {
            unset($_SESSION['user']);
            header('Location: login?inactive=1');
            exit();
        }
        $_SESSION['last_activity'] = time();
    }

    private function checkAdmin() {
        $this->checkAuth();
        $db = DatabaseConnection::getInstance();
        $username = mysqli_real_escape_string($db, $_SESSION['user']['username']);
        $res = mysqli_query($db, "SELECT `is_admin` FROM `biartet_users` WHERE `username` = '$username' LIMIT 1");
        $row = $res ? mysqli_fetch_assoc($res) : null;
        if (!$row || (int)$row['is_admin'] !== 1) {
            $_SESSION['error_message'] = 'Acceso denegado. Solo administradores pueden ingresar a esta sección.';
            header('Location: ./');
            exit();
        }
    }

    public function index() {
        $this->checkAuth();
        $db = DatabaseConnection::getInstance();

        $result = mysqli_query($db, "SELECT * FROM `biartet_memorandums` ORDER BY `fecha_creacion` DESC");
        $memos = array();
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $memos[] = $row;
            }
        }

        // Determine if user is admin to show Create button
        $username = mysqli_real_escape_string($db, $_SESSION['user']['username']);
        $userRes = mysqli_query($db, "SELECT `is_admin` FROM `biartet_users` WHERE `username` = '$username' LIMIT 1");
        $userRow = $userRes ? mysqli_fetch_assoc($userRes) : null;
        $isAdmin = ($userRow && (int)$userRow['is_admin'] === 1);

        $this->render('memorandum_list', array(
            'title' => 'Memorándums',
            'memos' => $memos,
            'isAdmin' => $isAdmin,
            'success' => isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null,
            'error' => isset($_SESSION['error_message']) ? $_SESSION['error_message'] : null
        ));
        unset($_SESSION['success_message']);
        unset($_SESSION['error_message']);
    }

    public function showCreate() {
        $this->checkAdmin();
        $this->render('memorandum_form', array(
            'title' => 'Crear Memorándum',
            'memo' => null,
            'errors' => array()
        ));
    }

    public function save() {
        $this->checkAdmin();
        $db = DatabaseConnection::getInstance();

        $titulo = isset($_POST['titulo']) ? trim($_POST['titulo']) : '';
        $contenido = isset($_POST['contenido']) ? trim($_POST['contenido']) : '';
        
        $errors = array();
        if (empty($titulo)) {
            $errors['titulo'] = 'El título del memorándum es obligatorio.';
        }
        if (empty($contenido)) {
            $errors['contenido'] = 'El contenido del memorándum es obligatorio.';
        }

        // Handle image upload
        $imagen = null;
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['imagen']['tmp_name'];
            $fileName = $_FILES['imagen']['name'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = dirname(__FILE__) . '/../../../uploads/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }
            
            if (move_uploaded_file($fileTmpPath, $uploadFileDir . $newFileName)) {
                $imagen = 'uploads/' . $newFileName;
            }
        }

        $memoData = array(
            'titulo' => $titulo,
            'contenido' => $contenido,
            'imagen' => $imagen
        );

        if (!empty($errors)) {
            $this->render('memorandum_form', array(
                'title' => 'Crear Memorándum',
                'memo' => $memoData,
                'errors' => $errors
            ));
            return;
        }

        $titulo_escaped = mysqli_real_escape_string($db, $titulo);
        $content_escaped = mysqli_real_escape_string($db, $contenido);
        $img_escaped = $imagen ? "'" . mysqli_real_escape_string($db, $imagen) . "'" : "NULL";

        $sql = "INSERT INTO `biartet_memorandums` (`titulo`, `contenido`, `imagen`, `fecha_creacion`) VALUES ('$titulo_escaped', '$content_escaped', $img_escaped, NOW())";
        if (mysqli_query($db, $sql)) {
            SystemLog::write("Envió memorándum: " . $titulo);
            $_SESSION['success_message'] = 'Memorándum enviado con éxito.';
            header('Location: memorandums');
            exit();
        } else {
            $this->render('memorandum_form', array(
                'title' => 'Crear Memorándum',
                'memo' => $memoData,
                'errors' => array('global' => 'Error al guardar el memorándum en la base de datos.')
            ));
        }
    }

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

<?php
/**
 * ProductController for Managing Product Catalog
 * Compatible with PHP 5.2.3
 */
require_once dirname(__FILE__) . '/../Database/DatabaseConnection.php';
require_once dirname(__FILE__) . '/../Database/SystemLog.php';

class ProductController {
    
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
        
        $userId = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : 0;
        $result = mysqli_query($db, "SELECT * FROM `biartet_productos` WHERE `usuario_id` IS NULL OR `usuario_id` = $userId ORDER BY `nombre` ASC");
        $products = array();
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $products[] = $row;
            }
        }

        $this->render('product_list', array(
            'title' => 'Catálogo de Productos',
            'products' => $products,
            'success' => isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null,
            'error' => isset($_SESSION['error_message']) ? $_SESSION['error_message'] : null
        ));
        unset($_SESSION['success_message']);
        unset($_SESSION['error_message']);
    }

    public function showCreate() {
        $this->checkAuth();
        $this->render('product_form', array(
            'title' => 'Crear Producto',
            'product' => null,
            'errors' => array()
        ));
    }

    public function showEdit() {
        $this->checkAuth();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $db = DatabaseConnection::getInstance();
        $res = mysqli_query($db, "SELECT * FROM `biartet_productos` WHERE `id` = $id LIMIT 1");
        $product = $res ? mysqli_fetch_assoc($res) : null;

        if (!$product) {
            $_SESSION['error_message'] = 'El producto especificado no existe.';
            header('Location: products');
            exit();
        }

        // Check ownership
        $userId = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : 0;
        $isAdmin = isset($_SESSION['user']['is_admin']) ? (int)$_SESSION['user']['is_admin'] : 0;
        $ownerId = $product['usuario_id'] !== null ? (int)$product['usuario_id'] : null;
        if ($ownerId !== null && $ownerId !== $userId) {
            $_SESSION['error_message'] = 'Acceso denegado a este producto.';
            header('Location: ./products');
            exit();
        }
        if ($ownerId === null && $isAdmin !== 1) {
            $_SESSION['error_message'] = 'Solo los administradores pueden editar productos globales.';
            header('Location: ./products');
            exit();
        }

        $this->render('product_form', array(
            'title' => 'Editar Producto',
            'product' => $product,
            'errors' => array()
        ));
    }

    public function save() {
        $this->checkAuth();
        $db = DatabaseConnection::getInstance();

        $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $descripcion = isset($_POST['descripcion']) ? trim($_POST['descripcion']) : '';
        $precio_moneda_local = isset($_POST['precio_moneda_local']) ? (double)$_POST['precio_moneda_local'] : 0.00;
        $existing_imagen = isset($_POST['existing_imagen']) ? trim($_POST['existing_imagen']) : '';

        $userId = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : 0;
        $isAdmin = isset($_SESSION['user']['is_admin']) ? (int)$_SESSION['user']['is_admin'] : 0;

        if ($id) {
            // Check ownership
            $resOwner = mysqli_query($db, "SELECT `usuario_id` FROM `biartet_productos` WHERE `id` = $id LIMIT 1");
            $rowOwner = $resOwner ? mysqli_fetch_assoc($resOwner) : null;
            if ($rowOwner) {
                $ownerId = $rowOwner['usuario_id'] !== null ? (int)$rowOwner['usuario_id'] : null;
                if ($ownerId !== null && $ownerId !== $userId) {
                    $_SESSION['error_message'] = 'Acceso denegado.';
                    header('Location: products');
                    exit();
                }
                if ($ownerId === null && $isAdmin !== 1) {
                    $_SESSION['error_message'] = 'Solo los administradores pueden editar productos globales.';
                    header('Location: products');
                    exit();
                }
            }
        }

        $errors = array();
        if (empty($nombre)) {
            $errors['nombre'] = 'El nombre del producto es obligatorio.';
        }
        if ($precio_moneda_local < 0) {
            $errors['precio_moneda_local'] = 'El precio no puede ser negativo.';
        }

        // Handle image upload
        $imagen = $existing_imagen;
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

        $productData = array(
            'id' => $id,
            'nombre' => $nombre,
            'descripcion' => $descripcion,
            'precio_moneda_local' => $precio_moneda_local,
            'imagen' => $imagen
        );

        if (!empty($errors)) {
            $this->render('product_form', array(
                'title' => $id ? 'Editar Producto' : 'Crear Producto',
                'product' => $productData,
                'errors' => $errors
            ));
            return;
        }

        $nombre_escaped = mysqli_real_escape_string($db, $nombre);
        $desc_escaped = mysqli_real_escape_string($db, $descripcion);
        $img_escaped = mysqli_real_escape_string($db, $imagen);

        if ($id) {
            $sql = "UPDATE `biartet_productos` SET 
                `nombre` = '$nombre_escaped', 
                `descripcion` = '$desc_escaped', 
                `imagen` = '$img_escaped', 
                `precio_moneda_local` = $precio_moneda_local, 
                `fecha_actualizacion` = NOW() 
                WHERE `id` = $id";
            $success = mysqli_query($db, $sql);
            if ($success) {
                SystemLog::write("Actualizó producto: " . $nombre);
                $_SESSION['success_message'] = 'Producto actualizado con éxito.';
            } else {
                $_SESSION['error_message'] = 'Error al actualizar el producto en la base de datos.';
            }
        } else {
            $usuario_id = ($isAdmin === 1) ? "NULL" : $userId;
            $sql = "INSERT INTO `biartet_productos` 
                (`nombre`, `descripcion`, `imagen`, `precio_moneda_local`, `fecha_creacion`, `fecha_actualizacion`, `usuario_id`) 
                VALUES ('$nombre_escaped', '$desc_escaped', '$img_escaped', $precio_moneda_local, NOW(), NOW(), $usuario_id)";
            $success = mysqli_query($db, $sql);
            if ($success) {
                SystemLog::write("Creó producto: " . $nombre);
                $_SESSION['success_message'] = 'Producto creado con éxito.';
            } else {
                $_SESSION['error_message'] = 'Error al crear el producto en la base de datos.';
            }
        }

        header('Location: products');
        exit();
    }

    public function delete() {
        $this->checkAuth();
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $db = DatabaseConnection::getInstance();

        // Check ownership
        $resProduct = mysqli_query($db, "SELECT * FROM `biartet_productos` WHERE `id` = $id LIMIT 1");
        $product = $resProduct ? mysqli_fetch_assoc($resProduct) : null;
        if (!$product) {
            $_SESSION['error_message'] = 'El producto no existe.';
            header('Location: products');
            exit();
        }
        $userId = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : 0;
        $isAdmin = isset($_SESSION['user']['is_admin']) ? (int)$_SESSION['user']['is_admin'] : 0;
        $ownerId = $product['usuario_id'] !== null ? (int)$product['usuario_id'] : null;
        if ($ownerId !== null && $ownerId !== $userId) {
            $_SESSION['error_message'] = 'Acceso denegado.';
            header('Location: products');
            exit();
        }
        if ($ownerId === null && $isAdmin !== 1) {
            $_SESSION['error_message'] = 'Solo los administradores pueden eliminar productos globales.';
            header('Location: products');
            exit();
        }

        $prodName = $product['nombre'];
        $sql = "DELETE FROM `biartet_productos` WHERE `id` = $id";
        if (mysqli_query($db, $sql)) {
            SystemLog::write("Eliminó producto: " . $prodName);
            $_SESSION['success_message'] = 'Producto eliminado con éxito.';
        } else {
            $_SESSION['error_message'] = 'Error al eliminar el producto de la base de datos.';
        }

        header('Location: products');
        exit();
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

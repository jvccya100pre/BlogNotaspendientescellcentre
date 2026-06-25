<?php
/**
 * ClientController
 * Compatible with PHP 5.2.3
 */
class ClientController {
    private $getClientsUseCase;
    private $getClientByIdUseCase;
    private $saveClientUseCase;
    private $deleteClientUseCase;

    public function __construct() {
        $clientRepository = new MysqlClientRepository();
        $this->getClientsUseCase = new GetClients($clientRepository);
        $this->getClientByIdUseCase = new GetClientById($clientRepository);
        $this->saveClientUseCase = new SaveClient($clientRepository);
        $this->deleteClientUseCase = new DeleteClient($clientRepository);
    }

    /**
     * Enforce user authentication and check inactivity timeout (Paso 2)
     */
    private function checkAuth() {
        if (!isset($_SESSION['user'])) {
            header('Location: ./login');
            exit();
        }

        // Inactivity timeout: 10 minutes (600 seconds)
        if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 600)) {
            header('Location: ./logout?inactive=1');
            exit();
        }

        // Update last activity timestamp
        $_SESSION['last_activity'] = time();
    }

    /**
     * Display list of active clients and orders (Dashboard)
     */
    public function index() {
        $this->checkAuth();
        
        $clients = $this->getClientsUseCase->execute();
        
        // Fetch all orders from biartet_pedido
        $orders = array();
        try {
            $db = DatabaseConnection::getInstance();
            $result = mysqli_query($db, "SELECT * FROM `biartet_pedido` ORDER BY `fecha_creacion` DESC");
            if ($result) {
                while ($row = mysqli_fetch_assoc($result)) {
                    $orders[] = $row;
                }
            }
        } catch (Exception $e) {
            // Suppress error or handle
        }
        
        $repo = new MysqlClientRepository();
        
        $this->render('dashboard', array(
            'title' => 'Panel de Control',
            'clients' => $clients,
            'orders' => $orders,
            'repo' => $repo,
            'success' => isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null,
            'error' => isset($_SESSION['error_message']) ? $_SESSION['error_message'] : null
        ));
        
        unset($_SESSION['success_message']);
        unset($_SESSION['error_message']);
    }

    /**
     * Show client creation form
     */
    public function showCreate() {
        $this->checkAuth();
        
        $repo = new MysqlClientRepository();
        $estadosList = $repo->getEstados();
        
        $this->render('client_form', array(
            'title' => 'Registrar Cliente',
            'client' => null,
            'estadosList' => $estadosList,
            'municipiosList' => array(),
            'ciudadesList' => array(),
            'errors' => array()
        ));
    }

    /**
     * Show client editing form
     */
    public function showEdit() {
        $this->checkAuth();
        
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $client = $this->getClientByIdUseCase->execute($id);
        
        if ($client === null) {
            $_SESSION['error_message'] = 'El cliente especificado no existe.';
            header('Location: ./');
            exit();
        }
        
        $repo = new MysqlClientRepository();
        $estadosList = $repo->getEstados();
        $municipiosList = $client->estado_id ? $repo->getMunicipiosByEstado($client->estado_id) : array();
        $ciudadesList = $client->municipio_id ? $repo->getCiudadesByMunicipio($client->municipio_id) : array();
        
        $this->render('client_form', array(
            'title' => 'Modificar Cliente',
            'client' => $client,
            'estadosList' => $estadosList,
            'municipiosList' => $municipiosList,
            'ciudadesList' => $ciudadesList,
            'errors' => array()
        ));
    }

    /**
     * Process creation or update submission
     */
    public function save() {
        $this->checkAuth();

        $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
        $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $direccion = isset($_POST['direccion']) ? trim($_POST['direccion']) : '';
        $estado_id = isset($_POST['estado_id']) && $_POST['estado_id'] !== '' ? (int)$_POST['estado_id'] : null;
        $municipio_id = isset($_POST['municipio_id']) && $_POST['municipio_id'] !== '' ? (int)$_POST['municipio_id'] : null;
        $ciudad_id = isset($_POST['ciudad_id']) && $_POST['ciudad_id'] !== '' ? (int)$_POST['ciudad_id'] : null;
        $estado_llamada = isset($_POST['estado_llamada']) ? trim($_POST['estado_llamada']) : '';
        $observacion = isset($_POST['observacion']) ? trim($_POST['observacion']) : '';
        $lapso_tiempo = isset($_POST['lapso_tiempo']) ? trim($_POST['lapso_tiempo']) : '';
        $lapso_dias = isset($_POST['lapso_dias']) ? trim($_POST['lapso_dias']) : '';

        // Handle file upload
        $archivo_adjunto = isset($_POST['existing_archivo_adjunto']) ? trim($_POST['existing_archivo_adjunto']) : null;
        if (empty($archivo_adjunto) && !empty($id)) {
            $existing = $this->getClientByIdUseCase->execute($id);
            if ($existing !== null) {
                $archivo_adjunto = $existing->archivo_adjunto;
            }
        }

        if (isset($_FILES['archivo_adjunto']) && $_FILES['archivo_adjunto']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['archivo_adjunto']['tmp_name'];
            $fileName = $_FILES['archivo_adjunto']['name'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));
            
            // Generate clean unique filename
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = dirname(__FILE__) . '/../../../uploads/';
            
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0777, true);
            }
            
            $dest_path = $uploadFileDir . $newFileName;
            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $archivo_adjunto = 'uploads/' . $newFileName;
            }
        }

        // Create client model instance
        $client = new Client(
            $id,
            null, // Autogenerated by Use Case on creation
            $telefono,
            $nombre,
            $direccion,
            $estado_id,
            $municipio_id,
            $ciudad_id,
            $archivo_adjunto,
            $estado_llamada,
            $observacion,
            $lapso_tiempo,
            $lapso_dias,
            1, // Active state
            null, // Autogenerated on creation
            null  // Autogenerated/updated
        );

        $result = $this->saveClientUseCase->execute($client);
        if ($result === true) {
            $_SESSION['success_message'] = empty($id) 
                ? 'Cliente registrado con éxito.' 
                : 'Cliente actualizado con éxito.';
            header('Location: ./');
            exit();
        } else {
            // Re-render form with errors and location context
            $repo = new MysqlClientRepository();
            $estadosList = $repo->getEstados();
            $municipiosList = $client->estado_id ? $repo->getMunicipiosByEstado($client->estado_id) : array();
            $ciudadesList = $client->municipio_id ? $repo->getCiudadesByMunicipio($client->municipio_id) : array();

            $this->render('client_form', array(
                'title' => empty($id) ? 'Registrar Cliente' : 'Modificar Cliente',
                'client' => $client,
                'estadosList' => $estadosList,
                'municipiosList' => $municipiosList,
                'ciudadesList' => $ciudadesList,
                'errors' => $result
            ));
        }
    }

    /**
     * Process order saving (Paso 9 y Paso 11)
     */
    public function saveOrder() {
        $this->checkAuth();

        $cliente = isset($_POST['cliente']) ? trim($_POST['cliente']) : '';
        $telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : '';
        $producto = isset($_POST['producto']) ? trim($_POST['producto']) : '';
        $precio = isset($_POST['precio']) ? trim($_POST['precio']) : '';
        $direccion = isset($_POST['direccion']) ? trim($_POST['direccion']) : '';
        $pago = isset($_POST['pago']) ? trim($_POST['pago']) : '';
        $fecha = isset($_POST['fecha']) ? trim($_POST['fecha']) : '';
        $nota = isset($_POST['nota']) ? trim($_POST['nota']) : 'Llamar antes.';

        // Enforce all fields are required
        if (empty($cliente) || empty($telefono) || empty($producto) || empty($precio) || empty($direccion) || empty($pago) || empty($fecha) || empty($nota)) {
            header('Content-Type: application/json');
            header('HTTP/1.0 400 Bad Request');
            echo json_encode(array('status' => 'error', 'message' => 'Error: Todos los campos son obligatorios en el formulario de Pedido.'));
            exit();
        }

        try {
            $db = DatabaseConnection::getInstance();
            $cliente_escaped = mysqli_real_escape_string($db, $cliente);
            $telefono_escaped = mysqli_real_escape_string($db, $telefono);
            $producto_escaped = mysqli_real_escape_string($db, $producto);
            $precio_escaped = mysqli_real_escape_string($db, $precio);
            $direccion_escaped = mysqli_real_escape_string($db, $direccion);
            $pago_escaped = mysqli_real_escape_string($db, $pago);
            $fecha_escaped = mysqli_real_escape_string($db, $fecha);
            $nota_escaped = mysqli_real_escape_string($db, $nota);

            $sql = "INSERT INTO `biartet_pedido` 
                (`cliente`, `telefono`, `producto`, `precio`, `direccion`, `pago`, `fecha`, `nota`, `fecha_creacion`) 
                VALUES ('$cliente_escaped', '$telefono_escaped', '$producto_escaped', '$precio_escaped', '$direccion_escaped', '$pago_escaped', '$fecha_escaped', '$nota_escaped', NOW())";
            
            if (mysqli_query($db, $sql)) {
                header('Content-Type: application/json');
                echo json_encode(array('status' => 'success', 'message' => '¡Pedido registrado con éxito en la tabla biartet_pedido!'));
                exit();
            } else {
                throw new Exception(mysqli_error($db));
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            header('HTTP/1.0 500 Internal Server Error');
            echo json_encode(array('status' => 'error', 'message' => 'Error al guardar en la base de datos: ' . $e->getMessage()));
            exit();
        }
    }

    /**
     * AJAX endpoint to query Venezuelan geographical locations (Paso 4)
     */
    public function getLocations() {
        $this->checkAuth();

        $type = isset($_GET['type']) ? $_GET['type'] : '';
        $repo = new MysqlClientRepository();
        $data = array();

        if ($type === 'estados') {
            $data = $repo->getEstados();
        } else if ($type === 'municipios') {
            $estado_id = isset($_GET['estado_id']) ? (int)$_GET['estado_id'] : 0;
            $data = $repo->getMunicipiosByEstado($estado_id);
        } else if ($type === 'ciudades') {
            $municipio_id = isset($_GET['municipio_id']) ? (int)$_GET['municipio_id'] : 0;
            $data = $repo->getCiudadesByMunicipio($municipio_id);
        }

        header('Content-Type: application/json');
        echo json_encode($data);
        exit();
    }

    /**
     * Delete (deactivate) a client
     */
    public function delete() {
        $this->checkAuth();
        
        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $result = $this->deleteClientUseCase->execute($id);
        
        if ($result) {
            $_SESSION['success_message'] = 'Cliente eliminado con éxito.';
        } else {
            $_SESSION['error_message'] = 'Error al intentar eliminar el cliente.';
        }
        
        header('Location: ./');
        exit();
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

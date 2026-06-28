<?php
/**
 * CampaignController
 * Compatible with PHP 5.2.3
 */
class CampaignController {
    private $campaignRepository;

    public function __construct() {
        $this->campaignRepository = new MysqlCampaignRepository();
    }

    /**
     * Enforce user authentication and check inactivity timeout
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
     * List all campaigns
     */
    public function index() {
        $this->checkAuth();
        $campaigns = $this->campaignRepository->findAll();

        $this->render('campaign_list', array(
            'title' => 'Campañas de Ventas',
            'campaigns' => $campaigns,
            'success' => isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null,
            'error' => isset($_SESSION['error_message']) ? $_SESSION['error_message'] : null
        ));

        unset($_SESSION['success_message']);
        unset($_SESSION['error_message']);
    }

    /**
     * Show campaign creation form
     */
    public function showCreate() {
        $this->checkAuth();

        $db = DatabaseConnection::getInstance();
        $prodRes = mysqli_query($db, "SELECT * FROM `biartet_productos` ORDER BY `nombre` ASC");
        $productsList = array();
        if ($prodRes) {
            while ($row = mysqli_fetch_assoc($prodRes)) {
                $productsList[] = $row;
            }
        }

        $this->render('campaign_form', array(
            'title' => 'Crear Campaña',
            'campaign' => null,
            'productsList' => $productsList,
            'errors' => array()
        ));
    }

    public function showEdit() {
        $this->checkAuth();

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $campaign = $this->campaignRepository->findById($id);

        if ($campaign === null) {
            $_SESSION['error_message'] = 'La campaña especificada no existe.';
            header('Location: ./campaigns');
            exit();
        }

        // Check ownership
        $userId = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : 0;
        $isAdmin = isset($_SESSION['user']['is_admin']) ? (int)$_SESSION['user']['is_admin'] : 0;
        $ownerId = $campaign->usuario_id !== null ? (int)$campaign->usuario_id : null;
        if ($ownerId !== null && $ownerId !== $userId) {
            $_SESSION['error_message'] = 'Acceso denegado a esta campaña.';
            header('Location: ./campaigns');
            exit();
        }
        if ($ownerId === null && $isAdmin !== 1) {
            $_SESSION['error_message'] = 'Solo los administradores pueden editar campañas globales.';
            header('Location: ./campaigns');
            exit();
        }

        $db = DatabaseConnection::getInstance();
        $prodRes = mysqli_query($db, "SELECT * FROM `biartet_productos` ORDER BY `nombre` ASC");
        $productsList = array();
        if ($prodRes) {
            while ($row = mysqli_fetch_assoc($prodRes)) {
                $productsList[] = $row;
            }
        }

        $this->render('campaign_form', array(
            'title' => 'Editar Campaña',
            'campaign' => $campaign,
            'productsList' => $productsList,
            'errors' => array()
        ));
    }

    public function save() {
        $this->checkAuth();

        $id = isset($_POST['id']) ? (int)$_POST['id'] : null;
        $nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
        $charla_saludo = isset($_POST['charla_saludo']) ? trim($_POST['charla_saludo']) : '';
        $charla_desarrollo = isset($_POST['charla_desarrollo']) ? trim($_POST['charla_desarrollo']) : '';
        $charla_cierre = isset($_POST['charla_cierre']) ? trim($_POST['charla_cierre']) : '';
        $estado = isset($_POST['estado']) ? (int)$_POST['estado'] : 1;

        $userId = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : 0;
        $isAdmin = isset($_SESSION['user']['is_admin']) ? (int)$_SESSION['user']['is_admin'] : 0;

        if ($id) {
            $campaign = $this->campaignRepository->findById($id);
            if ($campaign === null) {
                $_SESSION['error_message'] = 'La campaña especificada no existe.';
                header('Location: ./campaigns');
                exit();
            }
            $ownerId = $campaign->usuario_id !== null ? (int)$campaign->usuario_id : null;
            if ($ownerId !== null && $ownerId !== $userId) {
                $_SESSION['error_message'] = 'Acceso denegado.';
                header('Location: ./campaigns');
                exit();
            }
            if ($ownerId === null && $isAdmin !== 1) {
                $_SESSION['error_message'] = 'Solo los administradores pueden editar campañas globales.';
                header('Location: ./campaigns');
                exit();
            }
        }

        // Process up to 30 items
        $itemsData = isset($_POST['items']) ? $_POST['items'] : array();
        $items = array();
        $itemCount = 0;

        foreach ($itemsData as $itemRow) {
            if ($itemCount >= 30) break;
            $producto_id = isset($itemRow['producto_id']) ? (int)$itemRow['producto_id'] : 0;
            if ($producto_id <= 0) continue;

            $db = DatabaseConnection::getInstance();
            $prodRes = mysqli_query($db, "SELECT `nombre` FROM `biartet_productos` WHERE `id` = $producto_id LIMIT 1");
            $prodRow = $prodRes ? mysqli_fetch_assoc($prodRes) : null;
            $nombre_producto = $prodRow ? $prodRow['nombre'] : 'Producto Desconocido';

            $precio = isset($itemRow['precio']) ? (double)$itemRow['precio'] : 0.0;
            $precio_moneda_local = isset($itemRow['precio_moneda_local']) ? (double)$itemRow['precio_moneda_local'] : 0.0;
            $comision_venta = isset($itemRow['comision_venta']) ? (double)$itemRow['comision_venta'] : 0.0;

            $items[] = new CampaignItem(
                null,
                $id,
                $producto_id,
                $nombre_producto,
                $precio,
                $precio_moneda_local,
                $comision_venta
            );
            $itemCount++;
        }

        $grupo_id = isset($_POST['grupo_id']) && $_POST['grupo_id'] !== '' ? (int)$_POST['grupo_id'] : null;

        $campaign = new Campaign(
            $id,
            $nombre,
            $charla_saludo,
            $charla_desarrollo,
            $charla_cierre,
            $estado,
            null,
            null,
            $items,
            null,
            $grupo_id
        );

        $errors = $campaign->validate();
        if (!empty($errors)) {
            $db = DatabaseConnection::getInstance();
            $prodRes = mysqli_query($db, "SELECT * FROM `biartet_productos` ORDER BY `nombre` ASC");
            $productsList = array();
            if ($prodRes) {
                while ($row = mysqli_fetch_assoc($prodRes)) {
                    $productsList[] = $row;
                }
            }

            $this->render('campaign_form', array(
                'title' => empty($id) ? 'Crear Campaña' : 'Editar Campaña',
                'campaign' => $campaign,
                'productsList' => $productsList,
                'errors' => $errors
            ));
            return;
        }

        $savedId = $this->campaignRepository->save($campaign);
        if ($savedId !== false) {
            SystemLog::write((empty($id) ? "Creó" : "Actualizó") . " campaña: " . $nombre);
            $_SESSION['success_message'] = empty($id) 
                ? 'Campaña creada con éxito.' 
                : 'Campaña actualizada con éxito.';
            header('Location: ./campaigns');
            exit();
        } else {
            $db = DatabaseConnection::getInstance();
            $prodRes = mysqli_query($db, "SELECT * FROM `biartet_productos` ORDER BY `nombre` ASC");
            $productsList = array();
            if ($prodRes) {
                while ($row = mysqli_fetch_assoc($prodRes)) {
                    $productsList[] = $row;
                }
            }

            $this->render('campaign_form', array(
                'title' => empty($id) ? 'Crear Campaña' : 'Editar Campaña',
                'campaign' => $campaign,
                'productsList' => $productsList,
                'errors' => array('global' => 'Error al guardar la campaña en la base de datos.')
            ));
        }
    }

    public function delete() {
        $this->checkAuth();

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $campaign = $this->campaignRepository->findById($id);
        if ($campaign === null) {
            $_SESSION['error_message'] = 'La campaña especificada no existe.';
            header('Location: ./campaigns');
            exit();
        }

        $userId = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : 0;
        $isAdmin = isset($_SESSION['user']['is_admin']) ? (int)$_SESSION['user']['is_admin'] : 0;
        $ownerId = $campaign->usuario_id !== null ? (int)$campaign->usuario_id : null;
        if ($ownerId !== null && $ownerId !== $userId) {
            $_SESSION['error_message'] = 'Acceso denegado.';
            header('Location: ./campaigns');
            exit();
        }
        if ($ownerId === null && $isAdmin !== 1) {
            $_SESSION['error_message'] = 'Solo los administradores pueden eliminar campañas globales.';
            header('Location: ./campaigns');
            exit();
        }

        $result = $this->campaignRepository->delete($id);

        if ($result) {
            SystemLog::write("Eliminó campaña con ID: " . $id);
            $_SESSION['success_message'] = 'Campaña eliminada con éxito.';
        } else {
            $_SESSION['error_message'] = 'Error al intentar eliminar la campaña.';
        }

        header('Location: ./campaigns');
        exit();
    }

    /**
     * AJAX endpoint to fetch items of a campaign (returns JSON)
     */
    public function getItems() {
        $this->checkAuth();

        $campaignId = isset($_GET['campaign_id']) ? (int)$_GET['campaign_id'] : 0;
        $items = $this->campaignRepository->findItemsByCampaignId($campaignId);

        $data = array();
        foreach ($items as $item) {
            $data[] = array(
                'id' => $item->id,
                'nombre_producto' => $item->nombre_producto,
                'precio' => $item->precio,
                'comision_venta' => $item->comision_venta,
                'premio_extra' => $item->premio_extra
            );
        }

        header('Content-Type: application/json');
        echo json_encode($data);
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

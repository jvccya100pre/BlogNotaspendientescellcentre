<?php
/**
 * AdminController for daily targets, daily prizes, and system logs
 * Compatible with PHP 5.2.3
 */
require_once dirname(__FILE__) . '/../Database/DatabaseConnection.php';
require_once dirname(__FILE__) . '/../Database/SystemLog.php';

class AdminController {

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

    public function logs() {
        $this->checkAdmin();
        $db = DatabaseConnection::getInstance();

        $result = mysqli_query($db, "SELECT * FROM `biartet_logs` ORDER BY `fecha_hora` DESC LIMIT 500");
        $logs = array();
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $logs[] = $row;
            }
        }

        $this->render('logs_list', array(
            'title' => 'Logs del Sistema',
            'logs' => $logs
        ));
    }

    public function dailyPrizes() {
        $this->checkAdmin();
        $db = DatabaseConnection::getInstance();

        $fecha = isset($_GET['fecha']) ? trim($_GET['fecha']) : date('Y-m-d');
        $fecha_escaped = mysqli_real_escape_string($db, $fecha);
        
        $grupo_id = isset($_GET['grupo_id']) && $_GET['grupo_id'] !== '' ? (int)$_GET['grupo_id'] : 0;
        $group_cond = ($grupo_id > 0) ? "AND (c.grupo_id IS NULL OR c.grupo_id = $grupo_id)" : "AND c.grupo_id IS NULL";
        $join_cond = ($grupo_id > 0) ? "AND dp.grupo_id = $grupo_id" : "AND dp.grupo_id IS NULL";

        // Fetch active campaign items for the selected group
        $sql = "SELECT ci.id, ci.nombre_producto, c.nombre AS campana_nombre, COALESCE(dp.premio_extra, 0.00) AS premio_hoy
                FROM `biartet_campana_items` ci
                INNER JOIN `biartet_campanas` c ON ci.campana_id = c.id
                LEFT JOIN `biartet_premios_diarios` dp ON ci.id = dp.campana_item_id AND dp.fecha = '$fecha_escaped' $join_cond
                WHERE c.usuario_id IS NULL $group_cond
                ORDER BY c.nombre ASC, ci.nombre_producto ASC";
        
        $result = mysqli_query($db, $sql);
        $items = array();
        if ($result) {
            while ($row = mysqli_fetch_assoc($result)) {
                $items[] = $row;
            }
        }

        $this->render('daily_prizes', array(
            'title' => 'Control de Premios por Día',
            'fecha' => $fecha,
            'grupo_id' => $grupo_id,
            'items' => $items,
            'success' => isset($_SESSION['success_message']) ? $_SESSION['success_message'] : null,
            'error' => isset($_SESSION['error_message']) ? $_SESSION['error_message'] : null
        ));
        unset($_SESSION['success_message']);
        unset($_SESSION['error_message']);
    }

    public function saveDailyPrize() {
        $this->checkAdmin();
        $db = DatabaseConnection::getInstance();

        $fecha = isset($_POST['fecha']) ? trim($_POST['fecha']) : '';
        $grupo_id = isset($_POST['grupo_id']) ? (int)$_POST['grupo_id'] : 0;
        $premios = isset($_POST['premios']) ? $_POST['premios'] : array();

        if (empty($fecha)) {
            $_SESSION['error_message'] = 'Debe especificar una fecha válida.';
            header('Location: daily-prizes');
            exit();
        }

        $fecha_escaped = mysqli_real_escape_string($db, $fecha);
        $grupo_val = ($grupo_id > 0) ? $grupo_id : "NULL";

        foreach ($premios as $itemId => $amount) {
            $itemId = (int)$itemId;
            $amount = (double)$amount;

            // Check if record exists
            $checkSql = "SELECT `id` FROM `biartet_premios_diarios` WHERE `fecha` = '$fecha_escaped' AND `campana_item_id` = $itemId AND " . ($grupo_id > 0 ? "`grupo_id` = $grupo_id" : "`grupo_id` IS NULL") . " LIMIT 1";
            $checkRes = mysqli_query($db, $checkSql);
            $checkRow = $checkRes ? mysqli_fetch_assoc($checkRes) : null;

            if ($checkRow) {
                $sql = "UPDATE `biartet_premios_diarios` SET `premio_extra` = $amount WHERE `id` = " . (int)$checkRow['id'];
            } else {
                $sql = "INSERT INTO `biartet_premios_diarios` (`fecha`, `campana_item_id`, `premio_extra`, `grupo_id`) VALUES ('$fecha_escaped', $itemId, $amount, $grupo_val)";
            }
            mysqli_query($db, $sql);
        }

        SystemLog::write("Actualizó premios diarios para la fecha: " . $fecha . " y grupo ID: " . $grupo_id);
        $_SESSION['success_message'] = 'Premios diarios guardados con éxito.';
        header('Location: daily-prizes?fecha=' . urlencode($fecha) . '&grupo_id=' . urlencode($grupo_id));
        exit();
    }

    /**
     * AJAX endpoint to save daily target
     */
    public function saveDailyTarget() {
        $this->checkAdmin();
        $db = DatabaseConnection::getInstance();

        $fecha = isset($_POST['fecha']) ? trim($_POST['fecha']) : '';
        $vendedor_id = isset($_POST['vendedor_id']) ? (int)$_POST['vendedor_id'] : 0;
        $cantidad_meta = isset($_POST['cantidad_meta']) ? (int)$_POST['cantidad_meta'] : 0;

        header('Content-Type: application/json');

        if (empty($fecha) || $vendedor_id <= 0 || $cantidad_meta < 0) {
            header('HTTP/1.0 400 Bad Request');
            echo json_encode(array('status' => 'error', 'message' => 'Parámetros inválidos o incompletos.'));
            exit();
        }

        $fecha_escaped = mysqli_real_escape_string($db, $fecha);

        // Check if seller exists
        $sellerRes = mysqli_query($db, "SELECT `username` FROM `biartet_users` WHERE `id` = $vendedor_id LIMIT 1");
        $sellerRow = $sellerRes ? mysqli_fetch_assoc($sellerRes) : null;
        if (!$sellerRow) {
            header('HTTP/1.0 400 Bad Request');
            echo json_encode(array('status' => 'error', 'message' => 'El vendedor especificado no existe.'));
            exit();
        }

        $checkSql = "SELECT `id` FROM `biartet_metas_diarias` WHERE `fecha` = '$fecha_escaped' AND `vendedor_id` = $vendedor_id LIMIT 1";
        $checkRes = mysqli_query($db, $checkSql);
        $checkRow = $checkRes ? mysqli_fetch_assoc($checkRes) : null;

        if ($checkRow) {
            $sql = "UPDATE `biartet_metas_diarias` SET `cantidad_meta` = $cantidad_meta WHERE `id` = " . (int)$checkRow['id'];
        } else {
            $sql = "INSERT INTO `biartet_metas_diarias` (`fecha`, `vendedor_id`, `cantidad_meta`) VALUES ('$fecha_escaped', $vendedor_id, $cantidad_meta)";
        }

        if (mysqli_query($db, $sql)) {
            SystemLog::write("Configuró meta de venta para " . $sellerRow['username'] . " en fecha " . $fecha . ": " . $cantidad_meta . " ventas");
            echo json_encode(array('status' => 'success', 'message' => 'Meta diaria guardada con éxito.'));
        } else {
            header('HTTP/1.0 500 Internal Server Error');
            echo json_encode(array('status' => 'error', 'message' => 'Error al guardar la meta en la base de datos.'));
        }
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

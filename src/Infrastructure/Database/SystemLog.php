<?php
/**
 * SystemLog helper to register system actions
 * Compatible with PHP 5.2.3
 */
require_once dirname(__FILE__) . '/DatabaseConnection.php';

class SystemLog {
    /**
     * Write an action to logs database
     * @param string $action
     */
    public static function write($action) {
        try {
            $db = DatabaseConnection::getInstance();
            $usuario = isset($_SESSION['user']['username']) ? $_SESSION['user']['username'] : 'Sistema / Anónimo';
            
            $usuario_escaped = mysqli_real_escape_string($db, $usuario);
            $action_escaped = mysqli_real_escape_string($db, $action);
            
            $sql = "INSERT INTO `biartet_logs` (`fecha_hora`, `usuario`, `accion`) VALUES (NOW(), '$usuario_escaped', '$action_escaped')";
            mysqli_query($db, $sql);
        } catch (Exception $e) {
            // Fail silently
        }
    }
}

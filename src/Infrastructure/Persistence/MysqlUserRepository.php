<?php
/**
 * MysqlUserRepository Implementation
 * Compatible with PHP 5.2.3
 */
class MysqlUserRepository implements UserRepositoryInterface {
    private $db;

    public function __construct() {
        $this->db = DatabaseConnection::getInstance();
    }

    public function findByUsername($username) {
        try {
            $username_escaped = mysqli_real_escape_string($this->db, $username);
            $sql = "SELECT * FROM `biartet_users` WHERE `username` = '$username_escaped' AND `fecha_eliminacion` IS NULL LIMIT 1";
            $result = mysqli_query($this->db, $sql);
            
            if (!$result) {
                return null;
            }
            
            $row = mysqli_fetch_assoc($result);
            if (!$row) {
                return null;
            }
            
            return new User(
                $row['id'],
                $row['username'],
                $row['password'],
                $row['is_admin'],
                $row['nombre_completo'],
                $row['usuario_dialview'],
                $row['contrasena_dialview'],
                $row['fecha_eliminacion'],
                $row['fecha_creacion'],
                $row['fecha_actualizacion'],
                $row['grupo_id'] !== null ? (int)$row['grupo_id'] : null
            );
        } catch (Exception $e) {
            return null;
        }
    }
}

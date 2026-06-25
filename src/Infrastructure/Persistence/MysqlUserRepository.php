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
            $sql = "SELECT `id`, `username`, `password`, `fecha_creacion`, `fecha_actualizacion` FROM `biartet_users` WHERE `username` = '$username_escaped' LIMIT 1";
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
                $row['fecha_creacion'],
                $row['fecha_actualizacion']
            );
        } catch (Exception $e) {
            return null;
        }
    }
}

<?php
/**
 * User Entity Model
 * Compatible with PHP 5.2.3
 */
class User {
    public $id;
    public $username;
    public $password;
    public $fecha_creacion;
    public $fecha_actualizacion;

    public function __construct($id, $username, $password, $fecha_creacion, $fecha_actualizacion) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->fecha_creacion = $fecha_creacion;
        $this->fecha_actualizacion = $fecha_actualizacion;
    }
}

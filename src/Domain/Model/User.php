<?php
/**
 * User Entity Model
 * Compatible with PHP 5.2.3
 */
class User {
    public $id;
    public $username;
    public $password;
    public $is_admin;
    public $nombre_completo;
    public $usuario_dialview;
    public $contrasena_dialview;
    public $fecha_eliminacion;
    public $grupo_id;
    public $fecha_creacion;
    public $fecha_actualizacion;

    public function __construct(
        $id,
        $username,
        $password,
        $is_admin = 0,
        $nombre_completo = null,
        $usuario_dialview = null,
        $contrasena_dialview = null,
        $fecha_eliminacion = null,
        $fecha_creacion = null,
        $fecha_actualizacion = null,
        $grupo_id = null
    ) {
        $this->id = $id;
        $this->username = $username;
        $this->password = $password;
        $this->is_admin = $is_admin;
        $this->nombre_completo = $nombre_completo;
        $this->usuario_dialview = $usuario_dialview;
        $this->contrasena_dialview = $contrasena_dialview;
        $this->fecha_eliminacion = $fecha_eliminacion;
        $this->fecha_creacion = $fecha_creacion;
        $this->fecha_actualizacion = $fecha_actualizacion;
        $this->grupo_id = $grupo_id;
    }
}

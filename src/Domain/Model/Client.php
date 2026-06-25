<?php
/**
 * Client Entity Model
 * Compatible with PHP 5.2.3
 */
class Client {
    public $id;
    public $identificador_unico;
    public $telefono;
    public $nombre;
    public $direccion;
    public $estado_id;
    public $municipio_id;
    public $ciudad_id;
    public $archivo_adjunto;
    public $estado_llamada;
    public $observacion;
    public $lapso_tiempo;
    public $lapso_dias;
    public $estado; // 1 for active, 0 for inactive
    public $fecha_creacion;
    public $fecha_actualizacion;
    public $posponer_hasta;

    public function __construct(
        $id,
        $identificador_unico,
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
        $estado,
        $fecha_creacion,
        $fecha_actualizacion,
        $posponer_hasta = null
    ) {
        $this->id = $id;
        $this->identificador_unico = $identificador_unico;
        $this->telefono = $telefono;
        $this->nombre = $nombre;
        $this->direccion = $direccion;
        $this->estado_id = $estado_id;
        $this->municipio_id = $municipio_id;
        $this->ciudad_id = $ciudad_id;
        $this->archivo_adjunto = $archivo_adjunto;
        $this->estado_llamada = $estado_llamada;
        $this->observacion = $observacion;
        $this->lapso_tiempo = $lapso_tiempo;
        $this->lapso_dias = $lapso_dias;
        $this->estado = $estado;
        $this->fecha_creacion = $fecha_creacion;
        $this->fecha_actualizacion = $fecha_actualizacion;
        $this->posponer_hasta = $posponer_hasta;
    }

    /**
     * Validate client attributes
     * @return array Array of error messages, empty if valid
     */
    public function validate() {
        $errors = array();

        if (empty($this->nombre)) {
            $errors['nombre'] = 'El nombre es obligatorio.';
        }
        
        if (empty($this->telefono)) {
            $errors['telefono'] = 'El número de teléfono es obligatorio.';
        } else if (!preg_match('/^[0-9\+\-\s\(\)]+$/', $this->telefono)) {
            $errors['telefono'] = 'El formato del teléfono es inválido.';
        }

        if (empty($this->direccion)) {
            $errors['direccion'] = 'La dirección es obligatoria.';
        }

        if (empty($this->estado_id)) {
            $errors['estado_id'] = 'El estado de la ubicación es obligatorio.';
        }

        if (empty($this->municipio_id)) {
            $errors['municipio_id'] = 'El municipio es obligatorio.';
        }

        if (empty($this->ciudad_id)) {
            $errors['ciudad_id'] = 'La ciudad es obligatoria.';
        }

        if (empty($this->archivo_adjunto)) {
            $errors['archivo_adjunto'] = 'El archivo adjunto o imagen es obligatorio.';
        }

        if (empty($this->observacion)) {
            $errors['observacion'] = 'La observación es obligatoria.';
        }

        if (empty($this->lapso_tiempo)) {
            $errors['lapso_tiempo'] = 'El lapso de horas es obligatorio.';
        }

        if (empty($this->lapso_dias)) {
            $errors['lapso_dias'] = 'El lapso de días es obligatorio.';
        }

        $validEstados = array(
            'Pendiente',
            'Exito pedido pendiente'
        );
        if (!in_array($this->estado_llamada, $validEstados)) {
            $errors['estado_llamada'] = 'El estado de llamada seleccionado es inválido.';
        }

        return $errors;
    }
}

<?php
/**
 * Campaign Entity Model
 * Compatible with PHP 5.2.3
 */
class Campaign {
    public $id;
    public $nombre;
    public $charla_saludo;
    public $charla_desarrollo;
    public $charla_cierre;
    public $estado; // 1 for active, 0 for hidden
    public $fecha_creacion;
    public $fecha_actualizacion;
    public $items; // Array of CampaignItem objects

    public function __construct(
        $id,
        $nombre,
        $charla_saludo,
        $charla_desarrollo,
        $charla_cierre,
        $estado,
        $fecha_creacion,
        $fecha_actualizacion,
        $items = array()
    ) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->charla_saludo = $charla_saludo;
        $this->charla_desarrollo = $charla_desarrollo;
        $this->charla_cierre = $charla_cierre;
        $this->estado = $estado;
        $this->fecha_creacion = $fecha_creacion;
        $this->fecha_actualizacion = $fecha_actualizacion;
        $this->items = $items;
    }

    /**
     * Validate campaign attributes
     * @return array
     */
    public function validate() {
        $errors = array();

        if (empty($this->nombre)) {
            $errors['nombre'] = 'El nombre de la campaña es obligatorio.';
        }

        if (strlen($this->charla_saludo) > 2000) {
            $errors['charla_saludo'] = 'El saludo no puede exceder los 2000 caracteres.';
        }

        return $errors;
    }
}

<?php
/**
 * CampaignItem Entity Model
 * Compatible with PHP 5.2.3
 */
class CampaignItem {
    public $id;
    public $campana_id;
    public $nombre_producto;
    public $precio;
    public $comision_venta;
    public $premio_extra;

    public function __construct(
        $id,
        $campana_id,
        $nombre_producto,
        $precio,
        $comision_venta,
        $premio_extra
    ) {
        $this->id = $id;
        $this->campana_id = $campana_id;
        $this->nombre_producto = $nombre_producto;
        $this->precio = (double)$precio;
        $this->comision_venta = (double)$comision_venta;
        $this->premio_extra = (double)$premio_extra;
    }

    /**
     * Validate item attributes
     * @return array
     */
    public function validate() {
        $errors = array();

        if (empty($this->nombre_producto)) {
            $errors['nombre_producto'] = 'El nombre del producto es obligatorio.';
        }

        if ($this->precio < 0) {
            $errors['precio'] = 'El precio no puede ser negativo.';
        }

        if ($this->comision_venta < 0) {
            $errors['comision_venta'] = 'La comisión no puede ser negativa.';
        }

        if ($this->premio_extra < 0) {
            $errors['premio_extra'] = 'El premio extra no puede ser negativo.';
        }

        return $errors;
    }
}

<?php
/**
 * CampaignItem Entity Model
 * Compatible with PHP 5.2.3
 */
class CampaignItem {
    public $id;
    public $campana_id;
    public $producto_id;
    public $nombre_producto;
    public $precio;
    public $precio_moneda_local;
    public $comision_venta;

    public function __construct(
        $id,
        $campana_id,
        $producto_id,
        $nombre_producto,
        $precio,
        $precio_moneda_local,
        $comision_venta
    ) {
        $this->id = $id;
        $this->campana_id = $campana_id;
        $this->producto_id = (int)$producto_id;
        $this->nombre_producto = $nombre_producto;
        $this->precio = (double)$precio;
        $this->precio_moneda_local = (double)$precio_moneda_local;
        $this->comision_venta = (double)$comision_venta;
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

        if ($this->precio_moneda_local < 0) {
            $errors['precio_moneda_local'] = 'El precio en moneda local no puede ser negativo.';
        }

        if ($this->comision_venta < 0) {
            $errors['comision_venta'] = 'La comisión no puede ser negativa.';
        }

        return $errors;
    }
}

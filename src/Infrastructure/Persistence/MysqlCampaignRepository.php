<?php
/**
 * MysqlCampaignRepository Implementation
 * Compatible with PHP 5.2.3
 */
class MysqlCampaignRepository {
    private $db;

    public function __construct() {
        $this->db = DatabaseConnection::getInstance();
    }

    /**
     * Find a campaign by ID
     * @param int $id
     * @return Campaign|null
     */
    public function findById($id) {
        try {
            $id_escaped = (int)$id;
            $result = mysqli_query($this->db, "SELECT * FROM `biartet_campanas` WHERE `id` = $id_escaped LIMIT 1");
            if (!$result) {
                return null;
            }
            $row = mysqli_fetch_assoc($result);
            if (!$row) {
                return null;
            }
            $items = $this->findItemsByCampaignId($row['id']);
            return $this->mapRowToCampaign($row, $items);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get all active campaigns (estado = 1)
     * @return array Array of Campaign objects
     */
    public function findAllActive() {
        try {
            $userId = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : 0;
            $isAdmin = isset($_SESSION['user']['is_admin']) ? (int)$_SESSION['user']['is_admin'] : 0;
            $userGroupId = isset($_SESSION['user']['grupo_id']) ? (int)$_SESSION['user']['grupo_id'] : 0;

            if ($isAdmin === 1) {
                $result = mysqli_query($this->db, "SELECT * FROM `biartet_campanas` WHERE `estado` = 1 ORDER BY `nombre` ASC");
            } else {
                $groupCond = ($userGroupId > 0) ? "OR (`usuario_id` IS NULL AND (`grupo_id` IS NULL OR `grupo_id` = $userGroupId))" : "OR (`usuario_id` IS NULL AND `grupo_id` IS NULL)";
                $result = mysqli_query($this->db, "SELECT * FROM `biartet_campanas` WHERE `estado` = 1 AND (`usuario_id` = $userId $groupCond) ORDER BY `nombre` ASC");
            }

            if (!$result) {
                return array();
            }
            $campaigns = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $items = $this->findItemsByCampaignId($row['id']);
                $campaigns[] = $this->mapRowToCampaign($row, $items);
            }
            return $campaigns;
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Get all campaigns regardless of state
     * @return array Array of Campaign objects
     */
    public function findAll() {
        try {
            $userId = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : 0;
            $isAdmin = isset($_SESSION['user']['is_admin']) ? (int)$_SESSION['user']['is_admin'] : 0;
            $userGroupId = isset($_SESSION['user']['grupo_id']) ? (int)$_SESSION['user']['grupo_id'] : 0;

            if ($isAdmin === 1) {
                $result = mysqli_query($this->db, "SELECT * FROM `biartet_campanas` ORDER BY `fecha_creacion` DESC");
            } else {
                $groupCond = ($userGroupId > 0) ? "OR (`usuario_id` IS NULL AND (`grupo_id` IS NULL OR `grupo_id` = $userGroupId))" : "OR (`usuario_id` IS NULL AND `grupo_id` IS NULL)";
                $result = mysqli_query($this->db, "SELECT * FROM `biartet_campanas` WHERE (`usuario_id` = $userId $groupCond) ORDER BY `fecha_creacion` DESC");
            }

            if (!$result) {
                return array();
            }
            $campaigns = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $items = $this->findItemsByCampaignId($row['id']);
                $campaigns[] = $this->mapRowToCampaign($row, $items);
            }
            return $campaigns;
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Save a campaign (create or update) and its items
     * @param Campaign $campaign
     * @return int|bool Returns saved campaign ID or false on failure
     */
    public function save(Campaign $campaign) {
        try {
            $nombre = "'" . mysqli_real_escape_string($this->db, $campaign->nombre) . "'";
            $charla_saludo = "'" . mysqli_real_escape_string($this->db, $campaign->charla_saludo) . "'";
            $charla_desarrollo = "'" . mysqli_real_escape_string($this->db, $campaign->charla_desarrollo) . "'";
            $charla_cierre = "'" . mysqli_real_escape_string($this->db, $campaign->charla_cierre) . "'";
            $estado = (int)$campaign->estado;
            $now = date('Y-m-d H:i:s');

            if (empty($campaign->id)) {
                // INSERT
                $userId = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : 0;
                $isAdmin = isset($_SESSION['user']['is_admin']) ? (int)$_SESSION['user']['is_admin'] : 0;
                $usuario_id = ($isAdmin === 1) ? "NULL" : $userId;
                $grupo_id_val = ($campaign->grupo_id !== null) ? (int)$campaign->grupo_id : "NULL";

                $sql = "INSERT INTO `biartet_campanas` 
                    (`nombre`, `charla_saludo`, `charla_desarrollo`, `charla_cierre`, `estado`, `fecha_creacion`, `fecha_actualizacion`, `usuario_id`, `grupo_id`) 
                    VALUES ($nombre, $charla_saludo, $charla_desarrollo, $charla_cierre, $estado, '$now', '$now', $usuario_id, $grupo_id_val)";
                if (mysqli_query($this->db, $sql)) {
                    $campaignId = mysqli_insert_id($this->db);
                    $this->saveItems($campaignId, $campaign->items);
                    return $campaignId;
                }
                return false;
            } else {
                // UPDATE
                $id = (int)$campaign->id;
                $grupo_id_val = ($campaign->grupo_id !== null) ? (int)$campaign->grupo_id : "NULL";
                $sql = "UPDATE `biartet_campanas` SET 
                    `nombre` = $nombre, 
                    `charla_saludo` = $charla_saludo, 
                    `charla_desarrollo` = $charla_desarrollo, 
                    `charla_cierre` = $charla_cierre, 
                    `estado` = $estado, 
                    `grupo_id` = $grupo_id_val,
                    `fecha_actualizacion` = '$now' 
                    WHERE `id` = $id";
                if (mysqli_query($this->db, $sql)) {
                    $this->saveItems($id, $campaign->items);
                    return $id;
                }
                return false;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Save campaign items (removes old ones and inserts new list)
     * @param int $campaignId
     * @param array $items Array of CampaignItem objects
     */
    private function saveItems($campaignId, array $items) {
        $campaignId = (int)$campaignId;
        // Delete existing items
        mysqli_query($this->db, "DELETE FROM `biartet_campana_items` WHERE `campana_id` = $campaignId");

        // Insert new items
        foreach ($items as $item) {
            if (empty($item->nombre_producto)) continue;
            $productoId = (int)$item->producto_id;
            $nombre = "'" . mysqli_real_escape_string($this->db, $item->nombre_producto) . "'";
            $precio = (double)$item->precio;
            $precioLocal = (double)$item->precio_moneda_local;
            $comision = (double)$item->comision_venta;

            $sql = "INSERT INTO `biartet_campana_items` 
                (`campana_id`, `producto_id`, `nombre_producto`, `precio`, `precio_moneda_local`, `comision_venta`) 
                VALUES ($campaignId, $productoId, $nombre, $precio, $precioLocal, $comision)";
            mysqli_query($this->db, $sql);
        }
    }

    /**
     * Delete/Hide a campaign (or full database delete? We can do delete)
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        try {
            $id_escaped = (int)$id;
            // Since there is a foreign key with ON DELETE CASCADE, items are deleted automatically
            $sql = "DELETE FROM `biartet_campanas` WHERE `id` = $id_escaped";
            return (bool)mysqli_query($this->db, $sql);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Retrieve campaign items by campaign ID
     * @param int $campaignId
     * @return array Array of CampaignItem objects
     */
    public function findItemsByCampaignId($campaignId) {
        try {
            $campaignId = (int)$campaignId;
            $result = mysqli_query($this->db, "SELECT * FROM `biartet_campana_items` WHERE `campana_id` = $campaignId ORDER BY `id` ASC");
            if (!$result) {
                return array();
            }
            $items = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $items[] = new CampaignItem(
                    (int)$row['id'],
                    (int)$row['campana_id'],
                    (int)$row['producto_id'],
                    $row['nombre_producto'],
                    (double)$row['precio'],
                    (double)$row['precio_moneda_local'],
                    (double)$row['comision_venta']
                );
            }
            return $items;
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Retrieve a campaign item by its ID
     * @param int $itemId
     * @return CampaignItem|null
     */
    public function findItemById($itemId) {
        try {
            $itemId = (int)$itemId;
            $result = mysqli_query($this->db, "SELECT * FROM `biartet_campana_items` WHERE `id` = $itemId LIMIT 1");
            if (!$result) {
                return null;
            }
            $row = mysqli_fetch_assoc($result);
            if (!$row) {
                return null;
            }
            return new CampaignItem(
                (int)$row['id'],
                (int)$row['campana_id'],
                (int)$row['producto_id'],
                $row['nombre_producto'],
                (double)$row['precio'],
                (double)$row['precio_moneda_local'],
                (double)$row['comision_venta']
            );
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Map database row to Campaign object
     */
    private function mapRowToCampaign($row, $items) {
        return new Campaign(
            (int)$row['id'],
            $row['nombre'],
            $row['charla_saludo'],
            $row['charla_desarrollo'],
            $row['charla_cierre'],
            (int)$row['estado'],
            $row['fecha_creacion'],
            $row['fecha_actualizacion'],
            $items,
            $row['usuario_id'] !== null ? (int)$row['usuario_id'] : null,
            $row['grupo_id'] !== null ? (int)$row['grupo_id'] : null
        );
    }
}

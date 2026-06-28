<?php
/**
 * MysqlClientRepository Implementation
 * Compatible with PHP 5.2.3
 */
class MysqlClientRepository implements ClientRepositoryInterface {
    private $db;

    public function __construct() {
        $this->db = DatabaseConnection::getInstance();
    }

    /**
     * Find a client by their ID
     * @param int $id
     * @return Client|null
     */
    public function findById($id) {
        try {
            $id_escaped = (int)$id;
            $result = mysqli_query($this->db, "SELECT * FROM `biartet_clientes` WHERE `id` = $id_escaped AND `estado` = 1 LIMIT 1");
            if (!$result) {
                return null;
            }
            $row = mysqli_fetch_assoc($result);
            if (!$row) {
                return null;
            }
            return $this->mapRowToClient($row);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Find a client by their unique identifier
     * @param string $identifier
     * @return Client|null
     */
    public function findByIdentifier($identifier) {
        try {
            $identifier_escaped = mysqli_real_escape_string($this->db, $identifier);
            $result = mysqli_query($this->db, "SELECT * FROM `biartet_clientes` WHERE `identificador_unico` = '$identifier_escaped' AND `estado` = 1 LIMIT 1");
            if (!$result) {
                return null;
            }
            $row = mysqli_fetch_assoc($result);
            if (!$row) {
                return null;
            }
            return $this->mapRowToClient($row);
        } catch (Exception $e) {
            return null;
        }
    }

    /**
     * Get all active clients
     * @return array Array of Client objects
     */
    public function findAllActive() {
        try {
            $vendedor_id = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : 0;
            $result = mysqli_query($this->db, "SELECT * FROM `biartet_clientes` WHERE `estado` = 1 AND `vendedor_id` = $vendedor_id ORDER BY `fecha_creacion` DESC");
            if (!$result) {
                return array();
            }
            $clients = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $clients[] = $this->mapRowToClient($row);
            }
            return $clients;
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Get all active clients created/updated on a specific date (Y-m-d)
     * @param string $dateString
     * @return array Array of Client objects
     */
    public function findAllActiveByDate($dateString) {
        try {
            $date_escaped = mysqli_real_escape_string($this->db, $dateString);
            $vendedor_id = isset($_SESSION['user']['id']) ? (int)$_SESSION['user']['id'] : 0;
            $result = mysqli_query($this->db, "SELECT * FROM `biartet_clientes` WHERE `estado` = 1 AND `vendedor_id` = $vendedor_id AND (DATE(`fecha_creacion`) = '$date_escaped' OR DATE(`fecha_actualizacion`) = '$date_escaped') ORDER BY `fecha_creacion` DESC");
            if (!$result) {
                return array();
            }
            $clients = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $clients[] = $this->mapRowToClient($row);
            }
            return $clients;
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Save a client record (create or update)
     * @param Client $client
     * @return bool
     */
    public function save(Client $client) {
        try {
            $identificador_unico = $client->identificador_unico !== null ? "'" . mysqli_real_escape_string($this->db, $client->identificador_unico) . "'" : "NULL";
            $telefono = $client->telefono !== null ? "'" . mysqli_real_escape_string($this->db, $client->telefono) . "'" : "NULL";
            $nombre = $client->nombre !== null ? "'" . mysqli_real_escape_string($this->db, $client->nombre) . "'" : "NULL";
            $direccion = $client->direccion !== null ? "'" . mysqli_real_escape_string($this->db, $client->direccion) . "'" : "NULL";
            $estado_id = $client->estado_id !== null ? (int)$client->estado_id : "NULL";
            $municipio_id = $client->municipio_id !== null ? (int)$client->municipio_id : "NULL";
            $ciudad_id = $client->ciudad_id !== null ? (int)$client->ciudad_id : "NULL";
            $archivo_adjunto = $client->archivo_adjunto !== null ? "'" . mysqli_real_escape_string($this->db, $client->archivo_adjunto) . "'" : "NULL";
            $estado_llamada = $client->estado_llamada !== null ? "'" . mysqli_real_escape_string($this->db, $client->estado_llamada) . "'" : "NULL";
            $observacion = $client->observacion !== null ? "'" . mysqli_real_escape_string($this->db, $client->observacion) . "'" : "NULL";
            $lapso_tiempo = $client->lapso_tiempo !== null ? "'" . mysqli_real_escape_string($this->db, $client->lapso_tiempo) . "'" : "NULL";
            $lapso_dias = $client->lapso_dias !== null ? "'" . mysqli_real_escape_string($this->db, $client->lapso_dias) . "'" : "NULL";
            $estado = $client->estado !== null ? (int)$client->estado : "NULL";
            $fecha_creacion = $client->fecha_creacion !== null ? "'" . mysqli_real_escape_string($this->db, $client->fecha_creacion) . "'" : "NULL";
            $fecha_actualizacion = $client->fecha_actualizacion !== null ? "'" . mysqli_real_escape_string($this->db, $client->fecha_actualizacion) . "'" : "NULL";
            $posponer_hasta = (isset($client->posponer_hasta) && $client->posponer_hasta !== null) ? "'" . mysqli_real_escape_string($this->db, $client->posponer_hasta) . "'" : "NULL";

            $campana_id = $client->campana_id !== null ? (int)$client->campana_id : "NULL";
            $campana_item_id = $client->campana_item_id !== null ? (int)$client->campana_item_id : "NULL";
            $cantidad_items = $client->cantidad_items !== null ? (int)$client->cantidad_items : 1;
            $comision_aplicada = $client->comision_aplicada !== null ? (double)$client->comision_aplicada : 0.00;
            $premio_aplicado = $client->premio_aplicado !== null ? (double)$client->premio_aplicado : 0.00;
            $precio_aplicado = $client->precio_aplicado !== null ? (double)$client->precio_aplicado : 0.00;
            $vendedor_id = $client->vendedor_id !== null ? (int)$client->vendedor_id : "NULL";

            if (empty($client->id)) {
                // INSERT
                $sql = "INSERT INTO `biartet_clientes` 
                    (`identificador_unico`, `telefono`, `nombre`, `direccion`, `estado_id`, `municipio_id`, `ciudad_id`, `archivo_adjunto`, `estado_llamada`, `observacion`, `lapso_tiempo`, `lapso_dias`, `estado`, `fecha_creacion`, `fecha_actualizacion`, `posponer_hasta`, `campana_id`, `campana_item_id`, `cantidad_items`, `comision_aplicada`, `premio_aplicado`, `precio_aplicado`, `vendedor_id`) 
                    VALUES ($identificador_unico, $telefono, $nombre, $direccion, $estado_id, $municipio_id, $ciudad_id, $archivo_adjunto, $estado_llamada, $observacion, $lapso_tiempo, $lapso_dias, $estado, $fecha_creacion, $fecha_actualizacion, $posponer_hasta, $campana_id, $campana_item_id, $cantidad_items, $comision_aplicada, $premio_aplicado, $precio_aplicado, $vendedor_id)";
                return (bool)mysqli_query($this->db, $sql);
            } else {
                // UPDATE
                $id = (int)$client->id;
                $sql = "UPDATE `biartet_clientes` SET 
                    `telefono` = $telefono, 
                    `nombre` = $nombre, 
                    `direccion` = $direccion, 
                    `estado_id` = $estado_id, 
                    `municipio_id` = $municipio_id, 
                    `ciudad_id` = $ciudad_id, 
                    `archivo_adjunto` = $archivo_adjunto, 
                    `estado_llamada` = $estado_llamada, 
                    `observacion` = $observacion, 
                    `lapso_tiempo` = $lapso_tiempo, 
                    `lapso_dias` = $lapso_dias, 
                    `fecha_actualizacion` = $fecha_actualizacion,
                    `posponer_hasta` = $posponer_hasta,
                    `campana_id` = $campana_id,
                    `campana_item_id` = $campana_item_id,
                    `cantidad_items` = $cantidad_items,
                    `comision_aplicada` = $comision_aplicada,
                    `premio_aplicado` = $premio_aplicado,
                    `precio_aplicado` = $precio_aplicado,
                    `vendedor_id` = $vendedor_id
                    WHERE `id` = $id";
                return (bool)mysqli_query($this->db, $sql);
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Delete (deactivate) a client record
     * @param int $id
     * @return bool
     */
    public function delete($id) {
        try {
            $id_escaped = (int)$id;
            $sql = "UPDATE `biartet_clientes` SET `estado` = 0, `fecha_actualizacion` = NOW() WHERE `id` = $id_escaped";
            return (bool)mysqli_query($this->db, $sql);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Get list of Venezuelan states
     * @return array
     */
    public function getEstados() {
        try {
            $result = mysqli_query($this->db, "SELECT `id`, `nombre` FROM `biartet_venezuela_estados` ORDER BY `nombre` ASC");
            if (!$result) {
                return array();
            }
            $rows = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = $row;
            }
            return $rows;
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Get list of municipalities in a state
     * @param int $estado_id
     * @return array
     */
    public function getMunicipiosByEstado($estado_id) {
        try {
            $estado_id_escaped = (int)$estado_id;
            $result = mysqli_query($this->db, "SELECT `id`, `nombre` FROM `biartet_venezuela_municipios` WHERE `estado_id` = $estado_id_escaped ORDER BY `nombre` ASC");
            if (!$result) {
                return array();
            }
            $rows = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = $row;
            }
            return $rows;
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Get list of cities in a municipality
     * @param int $municipio_id
     * @return array
     */
    public function getCiudadesByMunicipio($municipio_id) {
        try {
            $municipio_id_escaped = (int)$municipio_id;
            $result = mysqli_query($this->db, "SELECT `id`, `nombre` FROM `biartet_venezuela_ciudades` WHERE `municipio_id` = $municipio_id_escaped ORDER BY `nombre` ASC");
            if (!$result) {
                return array();
            }
            $rows = array();
            while ($row = mysqli_fetch_assoc($result)) {
                $rows[] = $row;
            }
            return $rows;
        } catch (Exception $e) {
            return array();
        }
    }

    /**
     * Get state name by ID
     * @param int $id
     * @return string
     */
    public function getEstadoName($id) {
        if (empty($id)) return '';
        try {
            $id_escaped = (int)$id;
            $result = mysqli_query($this->db, "SELECT `nombre` FROM `biartet_venezuela_estados` WHERE `id` = $id_escaped LIMIT 1");
            if ($result && $row = mysqli_fetch_assoc($result)) {
                return $row['nombre'];
            }
            return '';
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * Get municipality name by ID
     * @param int $id
     * @return string
     */
    public function getMunicipioName($id) {
        if (empty($id)) return '';
        try {
            $id_escaped = (int)$id;
            $result = mysqli_query($this->db, "SELECT `nombre` FROM `biartet_venezuela_municipios` WHERE `id` = $id_escaped LIMIT 1");
            if ($result && $row = mysqli_fetch_assoc($result)) {
                return $row['nombre'];
            }
            return '';
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * Get city name by ID
     * @param int $id
     * @return string
     */
    public function getCiudadName($id) {
        if (empty($id)) return '';
        try {
            $id_escaped = (int)$id;
            $result = mysqli_query($this->db, "SELECT `nombre` FROM `biartet_venezuela_ciudades` WHERE `id` = $id_escaped LIMIT 1");
            if ($result && $row = mysqli_fetch_assoc($result)) {
                return $row['nombre'];
            }
            return '';
        } catch (Exception $e) {
            return '';
        }
    }

    /**
     * Map database row to Client model
     * @param array $row
     * @return Client
     */
    private function mapRowToClient($row) {
        return new Client(
            $row['id'],
            $row['identificador_unico'],
            $row['telefono'],
            $row['nombre'],
            $row['direccion'],
            isset($row['estado_id']) ? $row['estado_id'] : null,
            isset($row['municipio_id']) ? $row['municipio_id'] : null,
            isset($row['ciudad_id']) ? $row['ciudad_id'] : null,
            isset($row['archivo_adjunto']) ? $row['archivo_adjunto'] : null,
            $row['estado_llamada'],
            $row['observacion'],
            $row['lapso_tiempo'],
            $row['lapso_dias'],
            $row['estado'],
            $row['fecha_creacion'],
            $row['fecha_actualizacion'],
            isset($row['posponer_hasta']) ? $row['posponer_hasta'] : null,
            isset($row['campana_id']) ? $row['campana_id'] : null,
            isset($row['campana_item_id']) ? $row['campana_item_id'] : null,
            isset($row['cantidad_items']) ? (int)$row['cantidad_items'] : 1,
            isset($row['comision_aplicada']) ? (double)$row['comision_aplicada'] : 0.00,
            isset($row['premio_aplicado']) ? (double)$row['premio_aplicado'] : 0.00,
            isset($row['precio_aplicado']) ? (double)$row['precio_aplicado'] : 0.00,
            isset($row['vendedor_id']) ? (int)$row['vendedor_id'] : null
        );
    }

    public function snoozeAlarm($identifier, $minutes) {
        try {
            $identifier_escaped = mysqli_real_escape_string($this->db, $identifier);
            $minutes_escaped = (int)$minutes;
            $sql = "UPDATE `biartet_clientes` SET `posponer_hasta` = DATE_ADD(NOW(), INTERVAL $minutes_escaped MINUTE), `fecha_actualizacion` = NOW() WHERE `identificador_unico` = '$identifier_escaped'";
            return (bool)mysqli_query($this->db, $sql);
        } catch (Exception $e) {
            return false;
        }
    }

    public function clearAlarm($identifier) {
        try {
            $identifier_escaped = mysqli_real_escape_string($this->db, $identifier);
            $sql = "UPDATE `biartet_clientes` SET `lapso_tiempo` = '', `posponer_hasta` = NULL, `fecha_actualizacion` = NOW() WHERE `identificador_unico` = '$identifier_escaped'";
            return (bool)mysqli_query($this->db, $sql);
        } catch (Exception $e) {
            return false;
        }
    }
}

<?php
/**
 * Run schema migrations for Ventas y Campañas
 */
require_once dirname(__FILE__) . '/../src/Infrastructure/Database/DatabaseConnection.php';

try {
    $db = DatabaseConnection::getInstance();
    echo "Conexión a la base de datos establecida con éxito.\n";

    // 1. Create biartet_campanas table
    $sql1 = "CREATE TABLE IF NOT EXISTS `biartet_campanas` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `nombre` VARCHAR(255) NOT NULL,
        `charla_saludo` VARCHAR(2000) NOT NULL,
        `charla_desarrollo` TEXT NOT NULL,
        `charla_cierre` TEXT NOT NULL,
        `estado` TINYINT DEFAULT 1,
        `fecha_creacion` DATETIME NOT NULL,
        `fecha_actualizacion` DATETIME NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
    
    if (mysqli_query($db, $sql1)) {
        echo "Tabla 'biartet_campanas' creada/verificada con éxito.\n";
    } else {
        throw new Exception(mysqli_error($db));
    }

    // 2. Create biartet_campana_items table
    $sql2 = "CREATE TABLE IF NOT EXISTS `biartet_campana_items` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `campana_id` INT NOT NULL,
        `nombre_producto` VARCHAR(255) NOT NULL,
        `precio` DECIMAL(10,2) NOT NULL,
        `comision_venta` DECIMAL(10,2) NOT NULL,
        `premio_extra` DECIMAL(10,2) NOT NULL,
        FOREIGN KEY (`campana_id`) REFERENCES `biartet_campanas`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";

    if (mysqli_query($db, $sql2)) {
        echo "Tabla 'biartet_campana_items' creada/verificada con éxito.\n";
    } else {
        throw new Exception(mysqli_error($db));
    }

    // 3. Add columns to biartet_clientes
    $columns = array(
        'campana_id' => "INT NULL AFTER `posponer_hasta`",
        'campana_item_id' => "INT NULL AFTER `campana_id`",
        'cantidad_items' => "INT DEFAULT 1 AFTER `campana_item_id`",
        'comision_aplicada' => "DECIMAL(10,2) DEFAULT 0.00 AFTER `cantidad_items`",
        'premio_aplicado' => "DECIMAL(10,2) DEFAULT 0.00 AFTER `comision_aplicada`",
        'precio_aplicado' => "DECIMAL(10,2) DEFAULT 0.00 AFTER `premio_aplicado`"
    );

    // Check existing columns to avoid duplicate errors
    $result = mysqli_query($db, "SHOW COLUMNS FROM `biartet_clientes`");
    $existing_cols = array();
    if ($result) {
        while ($row = mysqli_fetch_assoc($result)) {
            $existing_cols[] = $row['Field'];
        }
    }

    foreach ($columns as $col => $definition) {
        if (!in_array($col, $existing_cols)) {
            $alter_sql = "ALTER TABLE `biartet_clientes` ADD COLUMN `$col` $definition;";
            if (mysqli_query($db, $alter_sql)) {
                echo "Columna '$col' agregada a 'biartet_clientes'.\n";
            } else {
                throw new Exception(mysqli_error($db));
            }
        } else {
            echo "Columna '$col' ya existe en 'biartet_clientes'.\n";
        }
    }

    echo "Migración completada con éxito.\n";
} catch (Exception $e) {
    echo "ERROR en la migración: " . $e->getMessage() . "\n";
    exit(1);
}

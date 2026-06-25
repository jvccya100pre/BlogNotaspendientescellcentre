<?php
/**
 * Database Migration Script
 * Creates tables and populates Venezuelan states, municipalities, and cities.
 */
require_once dirname(__FILE__) . '/DatabaseConnection.php';

try {
    $db = DatabaseConnection::getInstance();
    echo "Conexión a la base de datos establecida correctamente.\n";

    // 1. Create table biartet_venezuela_estados
    mysqli_query($db, "CREATE TABLE IF NOT EXISTS `biartet_venezuela_estados` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `nombre` VARCHAR(255) NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "Tabla `biartet_venezuela_estados` creada o ya existe.\n";

    // 2. Create table biartet_venezuela_municipios
    mysqli_query($db, "CREATE TABLE IF NOT EXISTS `biartet_venezuela_municipios` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `estado_id` INT NOT NULL,
        `nombre` VARCHAR(255) NOT NULL,
        FOREIGN KEY (`estado_id`) REFERENCES `biartet_venezuela_estados`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "Tabla `biartet_venezuela_municipios` creada o ya existe.\n";

    // 3. Create table biartet_venezuela_ciudades
    mysqli_query($db, "CREATE TABLE IF NOT EXISTS `biartet_venezuela_ciudades` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `municipio_id` INT NOT NULL,
        `nombre` VARCHAR(255) NOT NULL,
        FOREIGN KEY (`municipio_id`) REFERENCES `biartet_venezuela_municipios`(`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "Tabla `biartet_venezuela_ciudades` creada o ya existe.\n";

    // 4. Create table biartet_pedido
    mysqli_query($db, "CREATE TABLE IF NOT EXISTS `biartet_pedido` (
        `id` INT AUTO_INCREMENT PRIMARY KEY,
        `cliente` VARCHAR(255) NOT NULL,
        `telefono` VARCHAR(50) NOT NULL,
        `producto` VARCHAR(255) NOT NULL,
        `precio` VARCHAR(100) NOT NULL,
        `direccion` TEXT NOT NULL,
        `pago` VARCHAR(255) NOT NULL,
        `fecha` DATETIME NOT NULL,
        `nota` TEXT NOT NULL,
        `fecha_creacion` DATETIME NOT NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    echo "Tabla `biartet_pedido` creada o ya existe.\n";

    // 5. Add columns to biartet_clientes if they don't exist
    $columnsToAdd = array(
        'estado_id' => 'INT NULL AFTER `direccion`',
        'municipio_id' => 'INT NULL AFTER `estado_id`',
        'ciudad_id' => 'INT NULL AFTER `municipio_id`',
        'archivo_adjunto' => 'VARCHAR(255) NULL AFTER `ciudad_id`'
    );

    foreach ($columnsToAdd as $colName => $colDef) {
        $res = mysqli_query($db, "SHOW COLUMNS FROM `biartet_clientes` LIKE '$colName'");
        if ($res && mysqli_num_rows($res) === 0) {
            mysqli_query($db, "ALTER TABLE `biartet_clientes` ADD `$colName` $colDef");
            echo "Columna `$colName` añadida a `biartet_clientes`.\n";
        } else {
            echo "Columna `$colName` ya existe en `biartet_clientes`.\n";
        }
    }

    // 6. Ensure default users exist
    // jvczxc2021@gmail.com / Losteques.2026
    mysqli_query($db, "INSERT INTO `biartet_users` (`username`, `password`, `fecha_creacion`, `fecha_actualizacion`)
        SELECT 'jvczxc2021@gmail.com', '031b80c23a8c4d2a095f84acf64824535a12eb48', NOW(), NOW()
        FROM dual
        WHERE NOT EXISTS (SELECT 1 FROM `biartet_users` WHERE `username` = 'jvczxc2021@gmail.com')");
        
    // frank@gmail.com / 584126317284 (sha1: 2fb9023c72ccb72a6b2512f458139535eb089d8f)
    mysqli_query($db, "INSERT INTO `biartet_users` (`username`, `password`, `fecha_creacion`, `fecha_actualizacion`)
        SELECT 'frank@gmail.com', '2fb9023c72ccb72a6b2512f458139535eb089d8f', NOW(), NOW()
        FROM dual
        WHERE NOT EXISTS (SELECT 1 FROM `biartet_users` WHERE `username` = 'frank@gmail.com')");
    echo "Usuarios por defecto asegurados.\n";

    // 7. Check if states table is empty. If so, fetch and populate geography data
    $res = mysqli_query($db, "SELECT COUNT(*) FROM `biartet_venezuela_estados`");
    $row = mysqli_fetch_row($res);
    $count = $row ? $row[0] : 0;

    $inTransaction = false;
    if ($count == 0) {
        echo "Poblando datos geográficos de Venezuela...\n";
        $arrContextOptions = array(
            "ssl" => array(
                "verify_peer" => false,
                "verify_peer_name" => false,
            ),
        );
        $url = 'https://raw.githubusercontent.com/zokeber/venezuela-json/master/venezuela.json';
        $json = file_get_contents($url, false, stream_context_create($arrContextOptions));
        
        if ($json === false) {
            throw new Exception("No se pudo descargar el JSON de Venezuela desde $url");
        }
        
        $data = json_decode($json, true);
        if ($data === null) {
            throw new Exception("Error al decodificar el archivo JSON.");
        }

        mysqli_autocommit($db, FALSE);
        $inTransaction = true;

        $stmtState = mysqli_prepare($db, "INSERT INTO `biartet_venezuela_estados` (`id`, `nombre`) VALUES (?, ?)");
        $stmtMuni = mysqli_prepare($db, "INSERT INTO `biartet_venezuela_municipios` (`estado_id`, `nombre`) VALUES (?, ?)");
        $stmtCity = mysqli_prepare($db, "INSERT INTO `biartet_venezuela_ciudades` (`municipio_id`, `nombre`) VALUES (?, ?)");

        if (!$stmtState || !$stmtMuni || !$stmtCity) {
            throw new Exception("Error al preparar sentencias de MySQLi: " . mysqli_error($db));
        }

        $stateIdVal = 0;
        $stateNameVal = '';
        mysqli_stmt_bind_param($stmtState, 'is', $stateIdVal, $stateNameVal);

        $muniEstadoIdVal = 0;
        $muniNameVal = '';
        mysqli_stmt_bind_param($stmtMuni, 'is', $muniEstadoIdVal, $muniNameVal);

        $cityMuniIdVal = 0;
        $cityNameVal = '';
        mysqli_stmt_bind_param($stmtCity, 'is', $cityMuniIdVal, $cityNameVal);

        foreach ($data as $stateData) {
            $stateIdVal = (int)$stateData['id_estado'];
            $stateNameVal = $stateData['estado'];
            mysqli_stmt_execute($stmtState);

            foreach ($stateData['municipios'] as $muniData) {
                $muniEstadoIdVal = $stateIdVal;
                $muniNameVal = $muniData['municipio'];
                mysqli_stmt_execute($stmtMuni);
                $muniId = mysqli_insert_id($db);

                // Add the capital as a city first if not empty
                $capital = isset($muniData['capital']) ? trim($muniData['capital']) : '';
                if ($capital !== '') {
                    $cityMuniIdVal = $muniId;
                    $cityNameVal = $capital;
                    mysqli_stmt_execute($stmtCity);
                }

                // Add the parishes (parroquias) as cities/sectors of this municipality
                if (isset($muniData['parroquias']) && is_array($muniData['parroquias'])) {
                    foreach ($muniData['parroquias'] as $parroquia) {
                        $parroquia = trim($parroquia);
                        if ($parroquia !== '' && $parroquia !== $capital) {
                            $cityMuniIdVal = $muniId;
                            $cityNameVal = $parroquia;
                            mysqli_stmt_execute($stmtCity);
                        }
                    }
                }

                // Also map state-level cities to this municipality if they match capital or name
                if (isset($stateData['ciudades']) && is_array($stateData['ciudades'])) {
                    foreach ($stateData['ciudades'] as $city) {
                        $city = trim($city);
                        if (strcasecmp($city, $muniName) === 0 || strcasecmp($city, $capital) === 0) {
                            // Check if already added
                            // We can just add it to ensure it's listed under this municipality
                            $muniId_escaped = (int)$muniId;
                            $city_escaped = mysqli_real_escape_string($db, $city);
                            $check_sql = "SELECT COUNT(*) FROM `biartet_venezuela_ciudades` WHERE `municipio_id` = $muniId_escaped AND `nombre` = '$city_escaped'";
                            $check_res = mysqli_query($db, $check_sql);
                            $check_row = mysqli_fetch_row($check_res);
                            if ($check_row && $check_row[0] == 0) {
                                $cityMuniIdVal = $muniId;
                                $cityNameVal = $city;
                                mysqli_stmt_execute($stmtCity);
                            }
                        }
                    }
                }
            }
        }

        mysqli_commit($db);
        mysqli_autocommit($db, TRUE);
        $inTransaction = false;

        mysqli_stmt_close($stmtState);
        mysqli_stmt_close($stmtMuni);
        mysqli_stmt_close($stmtCity);

        echo "Datos geográficos de Venezuela importados con éxito.\n";
    } else {
        echo "La tabla `biartet_venezuela_estados` ya contiene datos. Omitiendo importación.\n";
    }

    echo "Migración completada con éxito.\n";
} catch (Exception $e) {
    if (isset($db) && $inTransaction) {
        mysqli_rollback($db);
        mysqli_autocommit($db, TRUE);
    }
    echo "ERROR en la migración: " . $e->getMessage() . "\n";
    exit(1);
}

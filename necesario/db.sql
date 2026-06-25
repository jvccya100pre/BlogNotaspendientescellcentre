-- SQL Setup for Call Center Application
-- Database name: createso_datosVPS
-- Table Prefix: biartet_

CREATE TABLE IF NOT EXISTS `biartet_users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `username` VARCHAR(100) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `fecha_creacion` DATETIME NOT NULL,
  `fecha_actualizacion` DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `biartet_clientes` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `identificador_unico` VARCHAR(50) NOT NULL UNIQUE,
  `telefono` VARCHAR(50) NOT NULL,
  `nombre` VARCHAR(255) NOT NULL,
  `direccion` TEXT NOT NULL,
  `estado_llamada` VARCHAR(100) NOT NULL,
  `observacion` TEXT,
  `lapso_tiempo` VARCHAR(50) DEFAULT NULL,
  `lapso_dias` VARCHAR(50) DEFAULT NULL,
  `estado` TINYINT(1) NOT NULL DEFAULT 1, -- 1 for active, 0 for inactive
  `fecha_creacion` DATETIME NOT NULL,
  `fecha_actualizacion` DATETIME NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default user if it does not exist (username: jvczxc2021@gmail.com, password: Losteques.2026 (sha1: 031b80c23a8c4d2a095f84acf64824535a12eb48))
INSERT INTO `biartet_users` (`username`, `password`, `fecha_creacion`, `fecha_actualizacion`)
SELECT 'jvczxc2021@gmail.com', '031b80c23a8c4d2a095f84acf64824535a12eb48', NOW(), NOW()
FROM dual
WHERE NOT EXISTS (
  SELECT 1 FROM `biartet_users` WHERE `username` = 'jvczxc2021@gmail.com'
);

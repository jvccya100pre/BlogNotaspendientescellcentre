-- SQL Script of Missing Tables and Schema Updates
-- Database name: createso_datosVPS
-- Table Prefix: biartet_

-- 1. Create table biartet_venezuela_estados
CREATE TABLE IF NOT EXISTS `biartet_venezuela_estados` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Create table biartet_venezuela_municipios
CREATE TABLE IF NOT EXISTS `biartet_venezuela_municipios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `estado_id` INT NOT NULL,
    `nombre` VARCHAR(255) NOT NULL,
    FOREIGN KEY (`estado_id`) REFERENCES `biartet_venezuela_estados`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Create table biartet_venezuela_ciudades
CREATE TABLE IF NOT EXISTS `biartet_venezuela_ciudades` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `municipio_id` INT NOT NULL,
    `nombre` VARCHAR(255) NOT NULL,
    FOREIGN KEY (`municipio_id`) REFERENCES `biartet_venezuela_municipios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Create table biartet_pedido
CREATE TABLE IF NOT EXISTS `biartet_pedido` (
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Add columns to biartet_clientes if they don't exist
ALTER TABLE `biartet_clientes` ADD COLUMN `estado_id` INT NULL AFTER `direccion`;
ALTER TABLE `biartet_clientes` ADD COLUMN `municipio_id` INT NULL AFTER `estado_id`;
ALTER TABLE `biartet_clientes` ADD COLUMN `ciudad_id` INT NULL AFTER `municipio_id`;
ALTER TABLE `biartet_clientes` ADD COLUMN `archivo_adjunto` VARCHAR(255) NULL AFTER `ciudad_id`;
ALTER TABLE `biartet_clientes` ADD COLUMN `posponer_hasta` DATETIME NULL AFTER `fecha_actualizacion`;

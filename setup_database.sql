-- Script para crear la base de datos y usuario de LanzaTaxi
CREATE DATABASE IF NOT EXISTS lanzataxi CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'lanza'@'localhost' IDENTIFIED BY 'lanza123';
GRANT ALL PRIVILEGES ON lanzataxi.* TO 'lanza'@'localhost';
FLUSH PRIVILEGES;
SELECT 'Base de datos y usuario creados exitosamente' AS status;

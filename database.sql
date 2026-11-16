-- Base de datos: agenda_personal
CREATE DATABASE IF NOT EXISTS agenda_personal
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;
USE agenda_personal;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    correo VARCHAR(150) NOT NULL,
    password VARCHAR(255) NOT NULL,
    fecha_registro TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uq_usuarios_correo (correo)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS contactos (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_usuario INT UNSIGNED NOT NULL,
    nombre VARCHAR(100) NOT NULL,
    telefono VARCHAR(30) NOT NULL,
    correo VARCHAR(150) NULL,
    ciudad VARCHAR(100) NULL,
    CONSTRAINT fk_contactos_usuario
      FOREIGN KEY (id_usuario) REFERENCES usuarios(id)
      ON DELETE CASCADE ON UPDATE CASCADE,
    INDEX idx_contactos_usuario (id_usuario)
) ENGINE=InnoDB;

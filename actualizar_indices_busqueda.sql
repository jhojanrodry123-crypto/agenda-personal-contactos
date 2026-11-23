-- Ejecutar una sola vez sobre la base de datos agenda_personal existente.
-- Estos índices no cambian ni eliminan datos de usuarios o contactos.
USE agenda_personal;

ALTER TABLE contactos
    ADD INDEX idx_contactos_usuario_nombre (id_usuario, nombre),
    ADD INDEX idx_contactos_usuario_correo (id_usuario, correo),
    ADD INDEX idx_contactos_usuario_ciudad (id_usuario, ciudad);

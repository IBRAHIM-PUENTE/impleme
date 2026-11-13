-- Ejecutar una sola vez en DBeaver sobre la base gymfit_db.
-- Añade estado de cuenta sin cambiar credenciales ni contraseñas existentes.
ALTER TABLE usuarios
    ADD COLUMN IF NOT EXISTS activo TINYINT(1) NOT NULL DEFAULT 1 AFTER rol;
UPDATE usuarios SET activo = 1 WHERE activo IS NULL;

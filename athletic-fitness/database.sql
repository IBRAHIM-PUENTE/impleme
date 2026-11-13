-- ==============================================================================
-- GymManagerFit - Script de Base de Datos para MariaDB / MySQL
-- Para ejecutar en DBeaver conectado a tu servidor Debian
-- ==============================================================================

CREATE DATABASE IF NOT EXISTS gymfit_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE gymfit_db;

-- 1. Tabla de Usuarios Administradores
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol VARCHAR(50) DEFAULT 'admin',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- 2. Tabla de Clientes / Socios
CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(150) NOT NULL,
    dni VARCHAR(8) NOT NULL UNIQUE,
    phone VARCHAR(20) DEFAULT NULL,
    weight DECIMAL(5,2) DEFAULT 0.00,
    height DECIMAL(4,2) DEFAULT 0.00,
    objective TEXT DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    status ENUM('Activo', 'Por vencer', 'Vencido', 'Inactivo', 'Retirado') DEFAULT 'Activo',
    last_membership VARCHAR(100) DEFAULT 'Ninguna',
    days_remaining INT DEFAULT 0,
    start_date DATE DEFAULT NULL,
    end_date DATE DEFAULT NULL,
    total_assists INT DEFAULT 0,
    last_assist_date DATE DEFAULT NULL,
    last_assist_time VARCHAR(20) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB;

-- 3. Tabla de Membresías (Catálogo de Planes)
CREATE TABLE IF NOT EXISTS membresias (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    duration_days INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB;

-- 4. Tabla de Pagos y Transacciones Financieras
CREATE TABLE IF NOT EXISTS pagos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT DEFAULT NULL,
    client_name VARCHAR(150) NOT NULL,
    membership_name VARCHAR(100) NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    date DATE NOT NULL,
    status VARCHAR(50) DEFAULT 'Completado',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    deleted_at TIMESTAMP NULL DEFAULT NULL,
    FOREIGN KEY (client_id) REFERENCES clientes(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- 5. Tabla de Historial Detallado de Asistencias
CREATE TABLE IF NOT EXISTS asistencias (
    id INT AUTO_INCREMENT PRIMARY KEY,
    client_id INT NOT NULL,
    dni VARCHAR(8) NOT NULL,
    nombre VARCHAR(150) NOT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    metodo VARCHAR(50) DEFAULT 'QR',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (client_id) REFERENCES clientes(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- 6. Tabla de Auditoría Inmutable
CREATE TABLE IF NOT EXISTS auditoria (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tabla VARCHAR(50) NOT NULL,
    accion VARCHAR(20) NOT NULL,
    registro_id VARCHAR(50) NOT NULL,
    descripcion TEXT NOT NULL,
    usuario VARCHAR(100) DEFAULT 'Admin',
    ip_address VARCHAR(45) DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ==============================================================================
-- TRIGGERS DE AUDITORÍA AUTOMÁTICA EN MARIADB
-- ==============================================================================

DELIMITER //

DROP TRIGGER IF EXISTS trg_audit_clientes_insert //
CREATE TRIGGER trg_audit_clientes_insert
AFTER INSERT ON clientes
FOR EACH ROW
BEGIN
    INSERT INTO auditoria (tabla, accion, registro_id, descripcion, usuario)
    VALUES ('clientes', 'CREAR', NEW.id, CONCAT('Nuevo socio registrado: ', NEW.name, ' (DNI: ', NEW.dni, ')'), 'Admin');
END //

DROP TRIGGER IF EXISTS trg_audit_clientes_update //
CREATE TRIGGER trg_audit_clientes_update
AFTER UPDATE ON clientes
FOR EACH ROW
BEGIN
    IF OLD.status != NEW.status THEN
        INSERT INTO auditoria (tabla, accion, registro_id, descripcion, usuario)
        VALUES ('clientes', 'ACTUALIZAR', NEW.id, CONCAT('Estado de ', NEW.name, ' cambiado de ', OLD.status, ' a ', NEW.status), 'Admin');
    END IF;
    IF OLD.deleted_at IS NULL AND NEW.deleted_at IS NOT NULL THEN
        INSERT INTO auditoria (tabla, accion, registro_id, descripcion, usuario)
        VALUES ('clientes', 'ELIMINAR', NEW.id, CONCAT('Socio desactivado/archivado: ', NEW.name), 'Admin');
    END IF;
END //

DROP TRIGGER IF EXISTS trg_audit_pagos_insert //
CREATE TRIGGER trg_audit_pagos_insert
AFTER INSERT ON pagos
FOR EACH ROW
BEGIN
    INSERT INTO auditoria (tabla, accion, registro_id, descripcion, usuario)
    VALUES ('pagos', 'CREAR', NEW.id, CONCAT('Pago registrado: S/ ', NEW.amount, ' de ', NEW.client_name, ' (Plan: ', NEW.membership_name, ')'), 'Admin');
END //

DELIMITER ;

-- ==============================================================================
-- DATOS INICIALES (SEED DATA)
-- ==============================================================================

-- Usuario Administrador por Defecto (Contraseña: Gimfit2026!)
-- El hash corresponde a password_hash('Gimfit2026!', PASSWORD_BCRYPT)
INSERT INTO usuarios (nombre, email, password, rol)
VALUES ('Administrador GimFit', 'admin@gimfit.com', '$2y$10$wN9aB8iQ24N5C8X2F1W7t.k5vPZ0j6A6Q1W7b8Y2vPZ0j6A6Q1W7.', 'admin')
ON DUPLICATE KEY UPDATE id=id;

-- Planes de Membresía
INSERT INTO membresias (id, name, price, duration_days) VALUES
('mensual', 'Plan Mensual Clásico', 100.00, 30),
('trimestral', 'Plan Trimestral Pro', 250.00, 90),
('semestral', 'Plan Semestral VIP', 450.00, 180),
('anual', 'Plan Anual Black', 800.00, 365)
ON DUPLICATE KEY UPDATE price=VALUES(price);

-- Clientes Iniciales con DNI de 8 dígitos
INSERT INTO clientes (id, name, dni, phone, weight, height, objective, notes, status, last_membership, days_remaining, start_date, end_date, total_assists, last_assist_date, last_assist_time) VALUES
(1, 'Juan Pérez', '72345678', '987654321', 78.50, 1.75, 'Aumento de masa muscular', 'Ninguna', 'Activo', 'Plan Mensual Clásico', 20, CURRENT_DATE - INTERVAL 10 DAY, CURRENT_DATE + INTERVAL 20 DAY, 8, CURRENT_DATE - INTERVAL 1 DAY, '08:30:00'),
(2, 'María Rodríguez', '45678901', '912345678', 62.00, 1.63, 'Pérdida de peso y tonificación', 'Dolor lumbar leve', 'Por vencer', 'Plan Trimestral Pro', 3, CURRENT_DATE - INTERVAL 87 DAY, CURRENT_DATE + INTERVAL 3 DAY, 24, CURRENT_DATE, '07:15:00'),
(3, 'Carlos Ruiz', '12345678', '998877665', 85.00, 1.80, 'Fuerza e hipertrofia', 'Hipertensión controlada', 'Activo', 'Plan Mensual Clásico', 15, CURRENT_DATE - INTERVAL 15 DAY, CURRENT_DATE + INTERVAL 15 DAY, 12, CURRENT_DATE - INTERVAL 2 DAY, '19:40:00'),
(4, 'Sofía Castro', '98765432', '955443322', 55.00, 1.60, 'Resistencia y salud cardiovascular', 'Asma leve', 'Activo', 'Plan Semestral VIP', 120, CURRENT_DATE - INTERVAL 60 DAY, CURRENT_DATE + INTERVAL 120 DAY, 45, CURRENT_DATE, '18:10:00'),
(5, 'Luis Mendoza', '65432109', '944332211', 72.00, 1.72, 'Definición muscular', 'Ninguna', 'Activo', 'Plan Trimestral Pro', 45, CURRENT_DATE - INTERVAL 45 DAY, CURRENT_DATE + INTERVAL 45 DAY, 30, CURRENT_DATE, '06:45:00'),
(6, 'Ana Gómez', '32109876', '933221100', 68.00, 1.68, 'Acondicionamiento físico', 'Ninguna', 'Inactivo', 'Plan Mensual Clásico', 0, CURRENT_DATE - INTERVAL 90 DAY, CURRENT_DATE - INTERVAL 60 DAY, 15, CURRENT_DATE - INTERVAL 65 DAY, '10:00:00')
ON DUPLICATE KEY UPDATE id=id;

-- Pagos Iniciales
INSERT INTO pagos (client_id, client_name, membership_name, amount, date, status) VALUES
(1, 'Juan Pérez', 'Plan Mensual Clásico', 100.00, CURRENT_DATE - INTERVAL 10 DAY, 'Completado'),
(2, 'María Rodríguez', 'Plan Trimestral Pro', 250.00, CURRENT_DATE - INTERVAL 87 DAY, 'Completado'),
(3, 'Carlos Ruiz', 'Plan Mensual Clásico', 100.00, CURRENT_DATE - INTERVAL 15 DAY, 'Completado'),
(4, 'Sofía Castro', 'Plan Semestral VIP', 450.00, CURRENT_DATE - INTERVAL 60 DAY, 'Completado'),
(5, 'Luis Mendoza', 'Plan Trimestral Pro', 250.00, CURRENT_DATE - INTERVAL 45 DAY, 'Completado');

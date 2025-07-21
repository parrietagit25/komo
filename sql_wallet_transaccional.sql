-- Script para actualizar la estructura de la base de datos para el sistema transaccional

-- 1. Modificar la tabla cash_wallet existente
ALTER TABLE `cash_wallet` 
ADD COLUMN `tipo` enum('carga','compra','venta','transferencia') NOT NULL DEFAULT 'carga' AFTER `monto`,
ADD COLUMN `id_orden` int(11) NULL AFTER `tipo`,
ADD COLUMN `id_stand` int(11) NULL AFTER `id_orden`,
ADD COLUMN `descripcion` varchar(255) NULL AFTER `id_stand`;

-- 2. Crear la tabla saldo_usuarios
CREATE TABLE `saldo_usuarios` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `id_user` int(11) NOT NULL,
  `saldo_actual` decimal(11,2) NOT NULL DEFAULT 0.00,
  `fecha_actualizacion` datetime NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_user` (`id_user`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Migrar datos existentes (opcional)
-- Si ya tienes datos en cash_wallet, puedes migrarlos:
-- INSERT INTO saldo_usuarios (id_user, saldo_actual)
-- SELECT id_user, SUM(monto) as saldo_actual 
-- FROM cash_wallet 
-- GROUP BY id_user;

-- 4. Actualizar el campo monto para usar decimal(11,2)
ALTER TABLE `cash_wallet` MODIFY COLUMN `monto` decimal(11,2) NOT NULL;

-- 5. Agregar índices para mejor rendimiento
ALTER TABLE `cash_wallet` ADD INDEX `idx_user_tipo` (`id_user`, `tipo`);
ALTER TABLE `cash_wallet` ADD INDEX `idx_fecha` (`fecha_log`);
ALTER TABLE `saldo_usuarios` ADD INDEX `idx_user` (`id_user`); 
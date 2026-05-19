-- SQL para arreglar tabla inventory_movements
-- Ejecutar en MySQL directamente

-- Hacer columnas nullable
ALTER TABLE `inventory_movements` MODIFY `supply_id` BIGINT UNSIGNED NULL;
ALTER TABLE `inventory_movements` MODIFY `warehouse_id` BIGINT UNSIGNED NULL;

-- Agregar columnas faltantes
ALTER TABLE `inventory_movements` ADD `product_variant_id` BIGINT UNSIGNED NULL AFTER `warehouse_id`;
ALTER TABLE `inventory_movements` ADD `description` VARCHAR(255) NULL AFTER `notes`;
ALTER TABLE `inventory_movements` ADD `user_id` BIGINT UNSIGNED NULL AFTER `description`;

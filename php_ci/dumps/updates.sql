-- 2020-04-15
ALTER TABLE `facturas`
	ADD COLUMN `descuento` DECIMAL(18,6) NOT NULL DEFAULT '0.000000' AFTER `subtotal`;

ALTER TABLE `facturas`
	ADD COLUMN `creado` DATE NULL AFTER `sello_sat`;

CREATE TABLE `clientes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `rfc` VARCHAR(15) NOT NULL,
  `nombre` VARCHAR(250) NOT NULL,
  `comercial` VARCHAR(250) NULL,
  `calle` VARCHAR(250) NULL,
  `numero` VARCHAR(10) NULL,
  `colonia` VARCHAR(250) NULL,
  `cp` VARCHAR(10) NULL,
  `telefono` VARCHAR(15) NULL,
  `celular` VARCHAR(15) NULL,
  `email` VARCHAR(200) NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `rfc_UNIQUE` (`rfc` ASC)
) ENGINE = InnoDB;


CREATE TABLE `facturas` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `tipo` ENUM('E','R') NOT NULL DEFAULT 'R' COMMENT 'Indica si es una factura Expedida o Recibida',
  `cliente_id` INT NOT NULL,
  `rfc` VARCHAR(15) NOT NULL,
  `fecha` DATETIME NOT NULL,
  `subtotal` DECIMAL(18,6) NOT NULL DEFAULT 0,
  `iva` DECIMAL(18,6) NOT NULL DEFAULT 0,
  `total` DECIMAL(18,6) NOT NULL DEFAULT 0,
  `activo` TINYINT NOT NULL DEFAULT 1 COMMENT 'Indica si la factura esta activa o cancelada (1 = Activa, 0 = Cancelada)',
  PRIMARY KEY (`id`),
  INDEX `ak_factura_cliente` (`cliente_id` ASC),
  INDEX `ak_factura_fecha` (`fecha` ASC),
  CONSTRAINT `fk_factura_cliente` FOREIGN KEY (`cliente_id`) REFERENCES `clientes` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE = InnoDB;

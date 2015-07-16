CREATE TABLE `db_facturas`.`clientes` (
  `id` INT NOT NULL AUTO_INCREMENT,
  `rfc` VARCHAR(15) NOT NULL,
  `nombre` VARCHAR(250) NOT NULL,
  `comercial` VARCHAR(250) NULL,
  `calle` VARCHAR(250) NULL,
  `numero` VARCHAR(10) NULL,
  `colonia` VARCHAR(250) NULL,
  `cp` VARCHAR(10) NULL,
  PRIMARY KEY (`id`),
  UNIQUE INDEX `rfc_UNIQUE` (`rfc` ASC)
) ENGINE = InnoDB;

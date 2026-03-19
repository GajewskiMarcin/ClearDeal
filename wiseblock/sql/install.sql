-- Install tables for WiseBlock
CREATE TABLE IF NOT EXISTS `{prefix}wiseblock_block` (
  `id_block` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `active` TINYINT(1) NOT NULL DEFAULT 1,
  `position` INT UNSIGNED NOT NULL DEFAULT 0,
  `logic_mode` ENUM('OR','AND') NOT NULL DEFAULT 'OR',
  `publish_from` DATETIME NULL,
  `publish_to` DATETIME NULL,
  `time_from` VARCHAR(5) NULL COMMENT 'Time restriction from (HH:MM)',
  `time_to` VARCHAR(5) NULL COMMENT 'Time restriction to (HH:MM)',
  `date_add` DATETIME NOT NULL,
  `date_upd` DATETIME NOT NULL,
  PRIMARY KEY (`id_block`),
  KEY `position` (`position`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `{prefix}wiseblock_block_hook` (
  `id_block` INT UNSIGNED NOT NULL,
  `hook_name` VARCHAR(128) NOT NULL,
  PRIMARY KEY (`id_block`, `hook_name`),
  KEY `hook_name` (`hook_name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `{prefix}wiseblock_block_lang` (
  `id_block` INT UNSIGNED NOT NULL,
  `id_lang` INT NOT NULL,
  `id_shop` INT NOT NULL,
  `title` VARCHAR(255) NULL,
  `content` LONGTEXT NOT NULL,
  PRIMARY KEY (`id_block`,`id_lang`,`id_shop`),
  KEY `id_lang` (`id_lang`),
  KEY `id_shop` (`id_shop`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `{prefix}wiseblock_rule` (
  `id_rule` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `id_block` INT UNSIGNED NOT NULL,
  `type` VARCHAR(32) NOT NULL COMMENT 'Rule type: category, tag, customer_group, country, cart_value, manufacturer, supplier, feature, cart_product, currency, utm',
  `id_object` VARCHAR(512) NOT NULL COMMENT 'Object ID or JSON data for complex rules',
  `value_max` DECIMAL(20,6) NULL COMMENT 'Max value for cart_value rules (NULL = no limit)',
  `include` TINYINT(1) NOT NULL DEFAULT 1,
  `with_children` TINYINT(1) NOT NULL DEFAULT 0,
  `id_lang` INT NULL,
  PRIMARY KEY (`id_rule`),
  KEY `id_block` (`id_block`),
  KEY `type` (`type`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `{prefix}wiseblock_hook` (
  `id_wiseblock_hook` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `hook_name` VARCHAR(128) NOT NULL UNIQUE,
  `enabled` TINYINT(1) NOT NULL DEFAULT 1,
  `description` VARCHAR(255) NULL,
  `date_add` DATETIME NOT NULL,
  `date_upd` DATETIME NOT NULL,
  PRIMARY KEY (`id_wiseblock_hook`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

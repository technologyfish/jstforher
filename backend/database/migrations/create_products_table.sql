-- 产品表
CREATE TABLE IF NOT EXISTS `products` (
  `id` INT(11) NOT NULL AUTO_INCREMENT,
  `name` VARCHAR(255) NOT NULL COMMENT '产品名称',
  `description` TEXT COMMENT '产品描述',
  `cover_image` VARCHAR(500) COMMENT '封面图URL',
  `sort_order` INT(11) DEFAULT 0 COMMENT '排序',
  `is_active` TINYINT(1) DEFAULT 1 COMMENT '是否启用：0-禁用，1-启用',
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `sort_order` (`sort_order`),
  KEY `is_active` (`is_active`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='产品表';


-- 一级栏目表
CREATE TABLE IF NOT EXISTS `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '栏目名称',
  `slug` varchar(100) NOT NULL COMMENT '栏目别名',
  `description` text COMMENT '栏目描述',
  `sort_order` int(11) DEFAULT 0 COMMENT '排序',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '是否启用',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `sort_order` (`sort_order`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='一级栏目表';



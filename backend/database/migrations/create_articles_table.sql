-- 文章表
CREATE TABLE IF NOT EXISTS `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) DEFAULT NULL COMMENT '所属一级栏目ID',
  `sub_category_id` int(11) DEFAULT NULL COMMENT '所属二级栏目ID',
  `title` varchar(200) NOT NULL COMMENT '文章标题',
  `slug` varchar(200) DEFAULT NULL COMMENT '文章别名',
  `description` text COMMENT '文章描述',
  `content` longtext COMMENT '文章内容',
  `images` text COMMENT '文章图片（JSON数组）',
  `cover_image` varchar(255) DEFAULT NULL COMMENT '封面图片',
  `sort_order` int(11) DEFAULT 0 COMMENT '排序',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '是否启用',
  `view_count` int(11) DEFAULT 0 COMMENT '浏览次数',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `category_id` (`category_id`),
  KEY `sub_category_id` (`sub_category_id`),
  KEY `sort_order` (`sort_order`),
  CONSTRAINT `articles_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
  CONSTRAINT `articles_sub_category_id_foreign` FOREIGN KEY (`sub_category_id`) REFERENCES `sub_categories` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='文章表';



-- JstForHer 数据库安装脚本
-- 执行此文件以创建所有必要的表

-- 管理员表
CREATE TABLE IF NOT EXISTS `admins` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(50) NOT NULL COMMENT '用户名',
  `email` varchar(100) NOT NULL COMMENT '邮箱',
  `password` varchar(255) NOT NULL COMMENT '密码',
  `name` varchar(100) DEFAULT NULL COMMENT '姓名',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '是否启用',
  `last_login_at` timestamp NULL DEFAULT NULL COMMENT '最后登录时间',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='管理员表';

-- 插入默认管理员账号 (密码: admin123)
-- 注意：此哈希是 admin123 的 bcrypt 哈希值
INSERT INTO `admins` (`username`, `email`, `password`, `name`) 
VALUES ('admin', 'admin@jstforher.com', '$2y$10$cVbNOj.byj5kzRGpGOC2G.w1wMT1hNcortIcCUcd1C2Y/PG1JdiV2', 'Administrator')
ON DUPLICATE KEY UPDATE `username` = `username`;

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

-- 二级栏目表
CREATE TABLE IF NOT EXISTS `sub_categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL COMMENT '所属一级栏目ID',
  `name` varchar(100) NOT NULL COMMENT '二级栏目名称',
  `slug` varchar(100) NOT NULL COMMENT '栏目别名',
  `description` text COMMENT '栏目描述',
  `cover_image` varchar(255) DEFAULT NULL COMMENT '封面图片',
  `sort_order` int(11) DEFAULT 0 COMMENT '排序',
  `is_active` tinyint(1) DEFAULT 1 COMMENT '是否启用',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  KEY `category_id` (`category_id`),
  KEY `sort_order` (`sort_order`),
  CONSTRAINT `sub_categories_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='二级栏目表';

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

-- 表单留资表
CREATE TABLE IF NOT EXISTS `contact_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '姓名',
  `email` varchar(100) NOT NULL COMMENT '邮箱',
  `phone` varchar(50) DEFAULT NULL COMMENT '电话',
  `subject` varchar(200) DEFAULT NULL COMMENT '主题',
  `message` text NOT NULL COMMENT '留言内容',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP地址',
  `user_agent` varchar(255) DEFAULT NULL COMMENT '用户代理',
  `is_read` tinyint(1) DEFAULT 0 COMMENT '是否已读',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `email` (`email`),
  KEY `is_read` (`is_read`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='表单留资表';


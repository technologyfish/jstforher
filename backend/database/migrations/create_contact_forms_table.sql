-- 表单留资表
CREATE TABLE IF NOT EXISTS `contact_forms` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL COMMENT '姓名',
  `email` varchar(100) NOT NULL COMMENT '邮箱',
  `phone` varchar(50) DEFAULT NULL COMMENT '电话',
  `subject` varchar(200) DEFAULT NULL COMMENT '主题',
  `message` text COMMENT '留言内容',
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


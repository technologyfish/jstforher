-- 订阅列表表
CREATE TABLE IF NOT EXISTS `newsletter_subscriptions` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `email` varchar(255) NOT NULL COMMENT '订阅邮箱',
  `status` tinyint(1) NOT NULL DEFAULT '0' COMMENT '处理状态: 0-未处理, 1-已处理',
  `admin_note` text COMMENT '管理员备注',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP地址',
  `user_agent` varchar(255) DEFAULT NULL COMMENT '用户代理',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '提交时间',
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更新时间',
  `processed_at` timestamp NULL DEFAULT NULL COMMENT '处理时间',
  `processed_by` int(11) DEFAULT NULL COMMENT '处理人ID',
  PRIMARY KEY (`id`),
  KEY `idx_email` (`email`),
  KEY `idx_status` (`status`),
  KEY `idx_created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT='邮件订阅表';


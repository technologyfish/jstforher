-- 修改contact_forms表的message字段，允许为NULL
ALTER TABLE `contact_forms` 
MODIFY COLUMN `message` text COMMENT '留言内容';


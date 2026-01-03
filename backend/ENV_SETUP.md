# 环境配置说明

## 重要提示
⚠️ Lumen 框架没有 `artisan` 命令！请按以下步骤手动配置。

## 步骤 1: 创建 .env 文件

在 `backend` 目录下创建 `.env` 文件，内容如下：

```env
APP_NAME=JstForHer
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost:8000
APP_TIMEZONE=Asia/Shanghai

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=jstforher
DB_USERNAME=root
DB_PASSWORD=

CACHE_DRIVER=file
QUEUE_CONNECTION=sync

# 重要！请使用下面生成的 JWT_SECRET
JWT_SECRET=LT7qZURyuZZBC+l8ININi00lGikLyCyEw5bv+cfCOog=
JWT_TTL=60
JWT_REFRESH_TTL=20160

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS=noreply@jstforher.com
MAIL_FROM_NAME="${APP_NAME}"
```

## 步骤 2: 修改数据库配置

根据你的实际情况修改以下配置：
- `DB_HOST`: 数据库主机地址
- `DB_PORT`: 数据库端口（通常是 3306）
- `DB_DATABASE`: 数据库名称（建议使用 `jstforher`）
- `DB_USERNAME`: 数据库用户名
- `DB_PASSWORD`: 数据库密码

## 步骤 3: 创建数据库

在 MySQL 中执行：

```sql
CREATE DATABASE jstforher CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## 步骤 4: 导入数据库结构

在 `backend` 目录下执行：

```bash
mysql -u root -p jstforher < database/migrations/install.sql
```

或者手动导入 `database/migrations/install.sql` 文件。

## 步骤 5: 设置文件权限

确保上传目录存在并有写入权限：

```bash
mkdir -p public/uploads/images
chmod -R 755 public/uploads
```

## 步骤 6: 安装依赖

```bash
composer install
```

## 步骤 7: 启动服务

```bash
php -S localhost:8000 -t public
```

## 验证安装

访问 http://localhost:8000 应该看到：
```json
{
  "success": true,
  "message": "JstForHer API",
  "version": "Lumen (8.x.x)"
}
```

## 常见问题

### Q: 为什么没有 artisan 命令？
A: 这个项目使用的是 Lumen，不是 Laravel。Lumen 是轻量级框架，没有 artisan 命令行工具。

### Q: 如何生成新的 JWT Secret？
A: 使用以下命令：
```bash
php -r "echo base64_encode(random_bytes(32));"
```
然后将生成的字符串复制到 `.env` 文件的 `JWT_SECRET` 中。

### Q: 数据库连接失败怎么办？
A: 
1. 确认 MySQL 服务正在运行
2. 检查 `.env` 中的数据库配置是否正确
3. 确认数据库用户有足够的权限
4. 尝试使用命令行连接：`mysql -u root -p`

### Q: 500 错误怎么办？
A: 
1. 检查 `.env` 文件是否存在
2. 确认 `JWT_SECRET` 已设置
3. 查看错误日志：`storage/logs/lumen-YYYY-MM-DD.log`
4. 确保 `storage` 目录有写入权限

## 下一步

配置完成后，可以：
1. 访问管理后台：http://localhost:3000/admin/login
2. 默认账号：admin / admin123
3. 开始添加栏目和文章内容



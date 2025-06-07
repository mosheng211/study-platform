<?php
/**
 * 数据导出工具 - 用于生产环境部署
 * 将开发环境的数据导出为SQL文件，便于生产环境导入
 */

// 设置PHP错误显示
ini_set('display_errors', 1);
error_reporting(E_ALL);

echo "=== 学习平台数据导出工具 ===\n";
echo "正在导出开发环境数据...\n\n";

try {
    // 数据库连接配置
    $config = [
        'host' => 'localhost',
        'database' => 'study_platform',
        'username' => 'root',
        'password' => '61263269'
    ];
    
    // 创建PDO连接
    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['database']};charset=utf8mb4",
        $config['username'],
        $config['password']
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ 数据库连接成功\n";
    
    // 获取当前时间戳
    $timestamp = date('Y-m-d_H-i-s');
    $exportDir = __DIR__ . '/export';
    
    // 创建导出目录
    if (!is_dir($exportDir)) {
        mkdir($exportDir, 0755, true);
    }
    
    // 导出文件路径
    $backupFile = $exportDir . "/study_platform_backup_{$timestamp}.sql";
    $structureFile = $exportDir . "/study_platform_structure_{$timestamp}.sql";
    $dataFile = $exportDir . "/study_platform_data_{$timestamp}.sql";
    
    echo "📁 创建导出目录: {$exportDir}\n";
    
    // 1. 导出完整备份（包含结构和数据）
    echo "\n🔄 导出完整数据库备份...\n";
    $cmd = "mysqldump -h {$config['host']} -u {$config['username']} -p{$config['password']} " .
           "--routines --triggers --single-transaction --lock-tables=false " .
           "{$config['database']} > \"{$backupFile}\"";
    
    exec($cmd, $output, $returnCode);
    
    if ($returnCode === 0 && file_exists($backupFile)) {
        $size = formatBytes(filesize($backupFile));
        echo "✅ 完整备份导出成功: {$backupFile} ({$size})\n";
    } else {
        echo "❌ 完整备份导出失败\n";
    }
    
    // 2. 导出仅结构
    echo "\n🔄 导出数据库结构...\n";
    $cmd = "mysqldump -h {$config['host']} -u {$config['username']} -p{$config['password']} " .
           "--no-data --routines --triggers " .
           "{$config['database']} > \"{$structureFile}\"";
    
    exec($cmd, $output, $returnCode);
    
    if ($returnCode === 0 && file_exists($structureFile)) {
        $size = formatBytes(filesize($structureFile));
        echo "✅ 结构导出成功: {$structureFile} ({$size})\n";
    } else {
        echo "❌ 结构导出失败\n";
    }
    
    // 3. 导出仅数据
    echo "\n🔄 导出数据内容...\n";
    $cmd = "mysqldump -h {$config['host']} -u {$config['username']} -p{$config['password']} " .
           "--no-create-info --single-transaction --lock-tables=false " .
           "{$config['database']} > \"{$dataFile}\"";
    
    exec($cmd, $output, $returnCode);
    
    if ($returnCode === 0 && file_exists($dataFile)) {
        $size = formatBytes(filesize($dataFile));
        echo "✅ 数据导出成功: {$dataFile} ({$size})\n";
    } else {
        echo "❌ 数据导出失败\n";
    }
    
    // 4. 生成统计信息
    echo "\n📊 数据库统计信息:\n";
    
    // 获取表统计
    $stmt = $pdo->query("
        SELECT 
            table_name as '表名',
            table_rows as '记录数',
            ROUND((data_length + index_length) / 1024 / 1024, 2) as '大小(MB)'
        FROM information_schema.tables 
        WHERE table_schema = '{$config['database']}'
        ORDER BY table_rows DESC
    ");
    
    $totalRecords = 0;
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf("  %-20s: %8s 条记录, %8s MB\n", 
            $row['表名'], 
            number_format($row['记录数']), 
            $row['大小(MB)']
        );
        $totalRecords += $row['记录数'];
    }
    
    echo "\n📈 总记录数: " . number_format($totalRecords) . " 条\n";
    
    // 5. 生成重要表的详细信息
    echo "\n🔍 重要表详细信息:\n";
    
    $importantTables = ['users', 'categories', 'resources', 'user_progress', 'study_plans'];
    
    foreach ($importantTables as $table) {
        try {
            $stmt = $pdo->query("SELECT COUNT(*) as count FROM {$table}");
            $count = $stmt->fetchColumn();
            echo "  - {$table}: {$count} 条记录\n";
            
            // 显示最新几条记录的时间
            if (in_array($table, ['users', 'resources', 'categories'])) {
                $stmt = $pdo->query("SELECT created_at FROM {$table} ORDER BY created_at DESC LIMIT 1");
                $latestRecord = $stmt->fetchColumn();
                if ($latestRecord) {
                    echo "    最新记录时间: {$latestRecord}\n";
                }
            }
        } catch (Exception $e) {
            echo "  - {$table}: 表不存在或查询失败\n";
        }
    }
    
    // 6. 创建生产环境导入脚本
    $importScript = $exportDir . "/import_to_production.sh";
    $importScriptContent = <<<SCRIPT
#!/bin/bash

# 生产环境数据导入脚本
# 使用方法: ./import_to_production.sh

echo "=== 学习平台生产环境数据导入 ==="
echo "请确认以下信息:"
echo "1. 生产环境数据库已创建"
echo "2. 数据库用户权限正确"
echo "3. 已备份现有数据 (如有)"
echo ""

read -p "确认继续? (y/N): " confirm
if [[ \$confirm != [yY] ]]; then
    echo "取消导入"
    exit 1
fi

# 生产环境数据库配置
read -p "数据库主机 [localhost]: " DB_HOST
DB_HOST=\${DB_HOST:-localhost}

read -p "数据库名 [study_platform]: " DB_NAME
DB_NAME=\${DB_NAME:-study_platform}

read -p "数据库用户名: " DB_USER
read -s -p "数据库密码: " DB_PASS
echo ""

# 检查备份文件
BACKUP_FILE="study_platform_backup_{$timestamp}.sql"
if [[ ! -f "\$BACKUP_FILE" ]]; then
    echo "❌ 备份文件不存在: \$BACKUP_FILE"
    exit 1
fi

echo ""
echo "🔄 开始导入数据..."

# 导入数据
mysql -h "\$DB_HOST" -u "\$DB_USER" -p"\$DB_PASS" "\$DB_NAME" < "\$BACKUP_FILE"

if [[ \$? -eq 0 ]]; then
    echo "✅ 数据导入成功!"
    echo ""
    echo "🎯 接下来的步骤:"
    echo "1. 配置 .env 文件中的数据库连接"
    echo "2. 运行 php artisan migrate --force"
    echo "3. 运行 php artisan config:cache"
    echo "4. 验证网站功能"
else
    echo "❌ 数据导入失败!"
    exit 1
fi
SCRIPT;
    
    file_put_contents($importScript, $importScriptContent);
    chmod($importScript, 0755);
    echo "\n📝 生成导入脚本: {$importScript}\n";
    
    // 7. 创建生产环境配置模板
    $configTemplate = $exportDir . "/production.env.template";
    $configContent = <<<CONFIG
# 生产环境配置模板
# 复制到 .env 并修改相应配置

APP_NAME="学习平台"
APP_ENV=production
APP_KEY=base64:your-generated-key-here
APP_DEBUG=false
APP_URL=https://yourdomain.com

LOG_CHANNEL=stack
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=debug

# 数据库配置 - 请修改为生产环境配置
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=study_platform
DB_USERNAME=your_production_user
DB_PASSWORD=your_production_password

# 缓存配置
BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=sync
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Redis配置 (可选)
# REDIS_HOST=127.0.0.1
# REDIS_PASSWORD=null
# REDIS_PORT=6379

# 邮件配置 (如需要)
MAIL_MAILER=smtp
MAIL_HOST=your-smtp-host
MAIL_PORT=587
MAIL_USERNAME=your-email
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="\${APP_NAME}"

# 其他配置
AWS_ACCESS_KEY_ID=
AWS_SECRET_ACCESS_KEY=
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=
AWS_USE_PATH_STYLE_ENDPOINT=false

PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
PUSHER_HOST=
PUSHER_PORT=443
PUSHER_SCHEME=https
PUSHER_APP_CLUSTER=mt1

VITE_PUSHER_APP_KEY="\${PUSHER_APP_KEY}"
VITE_PUSHER_HOST="\${PUSHER_HOST}"
VITE_PUSHER_PORT="\${PUSHER_PORT}"
VITE_PUSHER_SCHEME="\${PUSHER_SCHEME}"
VITE_PUSHER_APP_CLUSTER="\${PUSHER_APP_CLUSTER}"
CONFIG;
    
    file_put_contents($configTemplate, $configContent);
    echo "⚙️ 生成配置模板: {$configTemplate}\n";
    
    // 8. 创建部署检查清单
    $checklistFile = $exportDir . "/deployment_checklist.md";
    $checklistContent = <<<CHECKLIST
# 🚀 生产环境部署检查清单

## 📋 部署前准备

- [ ] 服务器环境准备完成 (PHP 8.1+, MySQL 5.7+, Nginx)
- [ ] 域名解析配置完成
- [ ] SSL证书准备 (推荐Let's Encrypt)
- [ ] 数据库创建完成
- [ ] 数据库用户和权限配置完成

## 📁 文件部署

- [ ] 项目代码上传完成
- [ ] Composer依赖安装: `composer install --optimize-autoloader --no-dev`
- [ ] 前端资源构建: `npm run build`
- [ ] 文件权限设置正确
- [ ] 配置文件 .env 配置完成
- [ ] 应用密钥生成: `php artisan key:generate`

## 🗄️ 数据库部署

- [ ] 数据库结构导入: `mysql -u user -p database < structure.sql`
- [ ] 数据库数据导入: `mysql -u user -p database < data.sql`
- [ ] 数据库迁移执行: `php artisan migrate --force`
- [ ] 数据库连接测试: `php artisan tinker`

## ⚙️ 服务器配置

- [ ] Nginx虚拟主机配置
- [ ] PHP-FPM配置优化
- [ ] SSL证书安装和配置
- [ ] 防火墙配置 (开放80, 443端口)
- [ ] 服务自启动配置

## 🚀 应用优化

- [ ] Laravel缓存优化:
  - [ ] `php artisan config:cache`
  - [ ] `php artisan route:cache`
  - [ ] `php artisan view:cache`
- [ ] 自动加载器优化: `composer dump-autoload --optimize`
- [ ] 文件权限最终检查

## 🔍 功能验证

- [ ] 网站首页正常访问
- [ ] API接口正常响应: `/api/resources`
- [ ] 管理后台正常访问: `/admin`
- [ ] 数据显示正确
- [ ] 用户注册/登录功能
- [ ] 学习资源浏览功能
- [ ] 移动端访问测试

## 🔒 安全检查

- [ ] .env 文件权限安全 (600)
- [ ] 敏感目录访问限制
- [ ] SQL注入防护
- [ ] XSS防护
- [ ] CSRF防护
- [ ] 定期备份策略

## 📈 监控配置

- [ ] 错误日志监控
- [ ] 性能监控
- [ ] 可用性监控
- [ ] 备份任务配置
- [ ] 异常告警配置

---

**✅ 完成所有检查项后，您的学习平台就可以正式上线了！**

导出时间: {$timestamp}
数据记录: {$totalRecords} 条
CHECKLIST;
    
    file_put_contents($checklistFile, $checklistContent);
    echo "📋 生成部署清单: {$checklistFile}\n";
    
    // 9. 生成总结报告
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "🎉 数据导出完成!\n";
    echo str_repeat("=", 60) . "\n";
    echo "📂 导出目录: {$exportDir}\n";
    echo "📁 导出文件:\n";
    echo "  - 完整备份: " . basename($backupFile) . "\n";
    echo "  - 仅结构: " . basename($structureFile) . "\n";
    echo "  - 仅数据: " . basename($dataFile) . "\n";
    echo "  - 导入脚本: " . basename($importScript) . "\n";
    echo "  - 配置模板: " . basename($configTemplate) . "\n";
    echo "  - 部署清单: " . basename($checklistFile) . "\n";
    echo "\n🚀 生产环境部署步骤:\n";
    echo "1. 将 export 目录上传到生产服务器\n";
    echo "2. 执行导入脚本: ./import_to_production.sh\n";
    echo "3. 配置 .env 文件 (参考 production.env.template)\n";
    echo "4. 执行 Laravel 部署命令\n";
    echo "5. 按照 deployment_checklist.md 进行验证\n";
    echo "\n📞 如有问题，请检查相关日志文件。\n";
    
} catch (Exception $e) {
    echo "❌ 导出失败: " . $e->getMessage() . "\n";
    echo "请检查数据库连接和权限配置。\n";
    exit(1);
}

/**
 * 格式化文件大小
 */
function formatBytes($size, $precision = 2) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');
    
    for ($i = 0; $size > 1024 && $i < count($units) - 1; $i++) {
        $size /= 1024;
    }
    
    return round($size, $precision) . ' ' . $units[$i];
}

?> 
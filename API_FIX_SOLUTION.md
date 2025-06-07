# API 错误修复解决方案

## 问题描述
API端点 `http://localhost:8000/api/resources/categories` 报错：
```
Call to undefined method App\Models\Resource::active()
```

## 已完成的修复

### ✅ 1. 添加了 scopeActive 方法
在 `app/Models/Resource.php` 中添加了缺失的 `scopeActive` 方法：

```php
/**
 * Scope a query to only include active resources.
 */
public function scopeActive($query)
{
    // 由于数据库中没有is_active字段，我们默认返回所有资源
    return $query;
}
```

### ✅ 2. 创建了测试脚本
创建了 `test_api.php` 来验证修复是否有效。

## 验证步骤

### 方法1：使用测试脚本（推荐）
1. 打开**命令提示符**（避免PowerShell问题）
2. 进入项目目录：`cd T:\HTML\StudyPlan\study-platform`
3. 运行测试脚本：`php test_api.php`

### 方法2：直接测试API
1. 确保Laravel服务器正在运行：`php artisan serve`
2. 在浏览器或Postman中访问：`http://localhost:8000/api/resources/categories`

### 方法3：运行种子数据（如果没有数据）
如果测试显示0条记录，需要添加种子数据：

**使用命令提示符**：
```cmd
cd T:\HTML\StudyPlan\study-platform
mysql -u root -p -e "USE study_platform; INSERT INTO migrations (migration, batch) VALUES ('2024_01_20_000003_create_resources_table', 2) ON DUPLICATE KEY UPDATE batch=2;"
php artisan db:seed --class=ResourceSeeder
```

**或者使用批处理脚本**：
双击运行 `fix_resources_migration.bat`

## 预期结果

### 成功的API响应应该是：
```json
{
    "success": true,
    "data": {
        "categories": [
            "前端开发",
            "后端开发",
            "数据库",
            "移动开发",
            // ... 更多分类
        ]
    }
}
```

### 成功的测试脚本输出应该是：
```
=== Resource Model 测试 ===
✓ Resource 模型已加载
✓ scopeActive 方法可用
✓ 数据库连接正常，resources 表中有 35 条记录
✓ categories 查询成功，找到 17 个分类
分类列表: 前端开发, 后端开发, 数据库, 移动开发, ...

=== 测试完成，API 应该可以正常工作 ===
```

## 其他相关API端点

修复后，这些端点也应该正常工作：
- `GET /api/resources` - 获取资源列表
- `GET /api/resources/{id}` - 获取单个资源
- `GET /api/resources/featured` - 获取推荐资源

## 如果仍有问题

1. **检查数据库连接**：确保MySQL服务正在运行
2. **检查环境配置**：确保 `.env` 文件中的数据库配置正确
3. **检查迁移状态**：运行 `php artisan migrate:status`
4. **查看Laravel日志**：检查 `storage/logs/laravel.log`

## 注意事项

- 我们添加的 `scopeActive()` 方法目前返回所有资源，因为数据库表中没有 `is_active` 字段
- 如果将来需要真正的激活/停用功能，需要添加数据库迁移来增加 `is_active` 字段
- 所有相关的Controller方法都会从这个修复中受益 
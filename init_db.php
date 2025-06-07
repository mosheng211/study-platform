<?php

echo "正在初始化数据库...\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '61263269');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ MySQL连接成功\n";
    
    // 创建数据库
    $pdo->exec("CREATE DATABASE IF NOT EXISTS study_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ 数据库创建成功\n";
    
    // 使用数据库
    $pdo->exec("USE study_platform");
    
    // 创建分类表
    $sql = "CREATE TABLE IF NOT EXISTS `categories` (
        `id` bigint unsigned NOT NULL AUTO_INCREMENT,
        `name` varchar(255) NOT NULL,
        `slug` varchar(255) NOT NULL,
        `description` text,
        `color` varchar(7) NOT NULL DEFAULT '#409eff',
        `icon` varchar(50) DEFAULT NULL,
        `sort_order` int NOT NULL DEFAULT '0',
        `is_active` tinyint(1) NOT NULL DEFAULT '1',
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `categories_slug_unique` (`slug`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    echo "✓ 分类表创建成功\n";
    
    // 检查是否有分类数据
    $count = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    if ($count == 0) {
        // 插入分类数据
        $categories = [
            ['HTML', 'html', 'HTML5语义化标签、网页结构基础', '#e34c26', 'Document'],
            ['CSS', 'css', 'CSS3样式、布局、响应式设计', '#1572b6', 'Brush'],
            ['JavaScript', 'javascript', 'JavaScript基础语法、DOM操作、异步编程', '#f7df1e', 'Lightning'],
            ['C#', 'csharp', 'C#语法基础、面向对象编程、.NET框架', '#512bd4', 'Code'],
            ['Vue.js', 'vuejs', 'Vue3组合式API、组件化开发、路由状态管理', '#4fc08d', 'Component'],
            ['CEF3', 'cef3', 'CEF3框架、浏览器自动化、CefSharp集成', '#2196f3', 'Monitor'],
            ['Selenium', 'selenium', 'Selenium WebDriver、浏览器自动化测试', '#43b02a', 'Robot'],
            ['算法与数据结构', 'algorithm', '基础算法、数据结构、编程思维训练', '#ff6b6b', 'MagicStick'],
            ['数据库', 'database', 'MySQL、SQL语句、数据库设计', '#336791', 'Coin'],
            ['项目实战', 'project', '综合项目、实战案例、完整开发流程', '#ffc107', 'Trophy']
        ];

        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, color, icon, sort_order, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, 1, NOW(), NOW())");
        
        foreach ($categories as $index => $category) {
            $stmt->execute([
                $category[0], // name
                $category[1], // slug
                $category[2], // description
                $category[3], // color
                $category[4], // icon
                $index + 1    // sort_order
            ]);
        }
        echo "✓ 分类数据插入成功 (10个分类)\n";
    } else {
        echo "✓ 分类数据已存在 ($count 个分类)\n";
    }
    
    echo "\n数据库初始化完成！\n";
    
} catch (Exception $e) {
    echo "❌ 错误: " . $e->getMessage() . "\n";
} 
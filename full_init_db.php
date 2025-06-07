<?php

echo "=== 完整数据库初始化 ===\n";

try {
    $pdo = new PDO('mysql:host=127.0.0.1;port=3306', 'root', '61263269');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "✓ MySQL连接成功\n";
    
    // 创建数据库
    $pdo->exec("CREATE DATABASE IF NOT EXISTS study_platform CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ 数据库创建成功\n";
    
    // 使用数据库
    $pdo->exec("USE study_platform");
    
    // 1. 创建用户表
    $userTable = "CREATE TABLE IF NOT EXISTS `users` (
        `id` bigint unsigned NOT NULL AUTO_INCREMENT,
        `username` varchar(255) NOT NULL,
        `real_name` varchar(255) NOT NULL,
        `email` varchar(255) NOT NULL,
        `email_verified_at` timestamp NULL DEFAULT NULL,
        `password` varchar(255) NOT NULL,
        `role` enum('student','teacher','admin') NOT NULL DEFAULT 'student',
        `avatar` varchar(255) DEFAULT NULL,
        `bio` text,
        `enrollment_date` date DEFAULT NULL,
        `is_active` tinyint(1) NOT NULL DEFAULT '1',
        `remember_token` varchar(100) DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `users_username_unique` (`username`),
        UNIQUE KEY `users_email_unique` (`email`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($userTable);
    echo "✓ 用户表创建成功\n";
    
    // 2. 创建分类表
    $categoryTable = "CREATE TABLE IF NOT EXISTS `categories` (
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
    
    $pdo->exec($categoryTable);
    echo "✓ 分类表创建成功\n";
    
    // 3. 创建资源表
    $resourceTable = "CREATE TABLE IF NOT EXISTS `resources` (
        `id` bigint unsigned NOT NULL AUTO_INCREMENT,
        `category_id` bigint unsigned DEFAULT NULL,
        `title` varchar(255) NOT NULL,
        `description` text,
        `type` enum('video','document','link','book','tool') NOT NULL DEFAULT 'link',
        `url` varchar(500) DEFAULT NULL,
        `thumbnail` varchar(255) DEFAULT NULL,
        `difficulty` enum('beginner','intermediate','advanced') NOT NULL DEFAULT 'beginner',
        `estimated_time` int DEFAULT NULL COMMENT '预估学习时间（分钟）',
        `tags` json DEFAULT NULL,
        `content` longtext,
        `is_active` tinyint(1) NOT NULL DEFAULT '1',
        `view_count` int NOT NULL DEFAULT '0',
        `like_count` int NOT NULL DEFAULT '0',
        `created_by` bigint unsigned DEFAULT NULL,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `resources_category_id_foreign` (`category_id`),
        KEY `resources_created_by_foreign` (`created_by`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($resourceTable);
    echo "✓ 资源表创建成功\n";
    
    // 4. 创建打卡表
    $checkinTable = "CREATE TABLE IF NOT EXISTS `checkins` (
        `id` bigint unsigned NOT NULL AUTO_INCREMENT,
        `user_id` bigint unsigned NOT NULL,
        `checkin_date` date NOT NULL,
        `study_minutes` int NOT NULL DEFAULT '0',
        `study_content` text,
        `mood` enum('excellent','good','normal','tired','difficult') NOT NULL DEFAULT 'normal',
        `completed_tasks` json DEFAULT NULL,
        `notes` text,
        `created_at` timestamp NULL DEFAULT NULL,
        `updated_at` timestamp NULL DEFAULT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `checkins_user_id_checkin_date_unique` (`user_id`,`checkin_date`),
        KEY `checkins_user_id_foreign` (`user_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($checkinTable);
    echo "✓ 打卡表创建成功\n";
    
    // 插入分类数据
    $categoryCount = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    if ($categoryCount == 0) {
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
                $category[0], $category[1], $category[2], $category[3], $category[4], $index + 1
            ]);
        }
        echo "✓ 分类数据插入成功 (10个分类)\n";
    } else {
        echo "✓ 分类数据已存在 ($categoryCount 个分类)\n";
    }
    
    // 插入测试用户
    $userCount = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($userCount == 0) {
        $testUsers = [
            ['admin', '管理员', 'admin@study.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin'],
            ['zhangsan', '张三', 'zhangsan@study.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'],
            ['lisi', '李四', 'lisi@study.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student'],
            ['wangwu', '王五', 'wangwu@study.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher']
        ];

        $stmt = $pdo->prepare("INSERT INTO users (username, real_name, email, password, role, is_active, enrollment_date, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 1, CURDATE(), NOW(), NOW())");
        
        foreach ($testUsers as $user) {
            $stmt->execute($user);
        }
        echo "✓ 测试用户创建成功 (4个用户)\n";
    } else {
        echo "✓ 用户数据已存在 ($userCount 个用户)\n";
    }
    
    // 插入测试资源
    $resourceCount = $pdo->query("SELECT COUNT(*) FROM resources")->fetchColumn();
    if ($resourceCount == 0) {
        $testResources = [
            [1, 'HTML5基础教程', 'HTML5语义化标签详解', 'video', 'https://example.com/html-basics', 'beginner', 120],
            [2, 'CSS3布局指南', 'Flexbox和Grid布局实战', 'document', 'https://example.com/css-layout', 'intermediate', 180],
            [3, 'JavaScript ES6+特性', '现代JavaScript语法详解', 'video', 'https://example.com/js-es6', 'intermediate', 240],
            [4, 'C#面向对象编程', 'C#类与对象基础教程', 'book', 'https://example.com/csharp-oop', 'beginner', 300],
            [5, 'Vue3组合式API', 'Vue3新特性详解', 'video', 'https://example.com/vue3-api', 'advanced', 200]
        ];

        $stmt = $pdo->prepare("INSERT INTO resources (category_id, title, description, type, url, difficulty, estimated_time, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())");
        
        foreach ($testResources as $resource) {
            $stmt->execute($resource);
        }
        echo "✓ 测试资源创建成功 (5个资源)\n";
    } else {
        echo "✓ 资源数据已存在 ($resourceCount 个资源)\n";
    }
    
    echo "\n=== 数据库初始化完成 ===\n";
    echo "用户数量: " . $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn() . "\n";
    echo "分类数量: " . $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn() . "\n";
    echo "资源数量: " . $pdo->query("SELECT COUNT(*) FROM resources")->fetchColumn() . "\n";
    echo "打卡记录: " . $pdo->query("SELECT COUNT(*) FROM checkins")->fetchColumn() . "\n";
    
} catch (Exception $e) {
    echo "❌ 错误: " . $e->getMessage() . "\n";
} 
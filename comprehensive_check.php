<?php

/**
 * 全面检查和修复脚本
 * 检查数据库连接、表结构、以及各项功能
 */

// 数据库配置
$dbConfig = [
    'host' => '127.0.0.1',
    'port' => '3306',
    'database' => 'study_platform',
    'username' => 'root',
    'password' => '61263269',
    'charset' => 'utf8mb4'
];

echo "=== 54天编程学习平台 - 全面功能检查和修复 ===\n\n";

try {
    // 连接数据库
    $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};charset={$dbConfig['charset']}";
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password']);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    echo "✓ 数据库连接成功\n";

    // 创建数据库（如果不存在）
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbConfig['database']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    echo "✓ 数据库创建/检查完成\n";

    // 使用数据库
    $pdo->exec("USE `{$dbConfig['database']}`");

    // 1. 检查并创建用户表
    echo "\n=== 1. 检查用户表 ===\n";
    $createUsersTable = "
    CREATE TABLE IF NOT EXISTS `users` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $pdo->exec($createUsersTable);
    echo "✓ 用户表检查/创建完成\n";

    // 2. 检查并创建分类表
    echo "\n=== 2. 检查分类表 ===\n";
    $createCategoriesTable = "
    CREATE TABLE IF NOT EXISTS `categories` (
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
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $pdo->exec($createCategoriesTable);
    echo "✓ 分类表检查/创建完成\n";

    // 3. 检查并创建资源表
    echo "\n=== 3. 检查资源表 ===\n";
    $createResourcesTable = "
    CREATE TABLE IF NOT EXISTS `resources` (
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
        KEY `resources_created_by_foreign` (`created_by`),
        CONSTRAINT `resources_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE SET NULL,
        CONSTRAINT `resources_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $pdo->exec($createResourcesTable);
    echo "✓ 资源表检查/创建完成\n";

    // 4. 检查并创建打卡表
    echo "\n=== 4. 检查打卡表 ===\n";
    $createCheckinsTable = "
    CREATE TABLE IF NOT EXISTS `checkins` (
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
        KEY `checkins_user_id_foreign` (`user_id`),
        CONSTRAINT `checkins_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ";
    $pdo->exec($createCheckinsTable);
    echo "✓ 打卡表检查/创建完成\n";

    // 5. 初始化分类数据
    echo "\n=== 5. 初始化分类数据 ===\n";
    $checkCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    if ($checkCategories == 0) {
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
        echo "✓ 分类数据初始化完成 (10个分类)\n";
    } else {
        echo "✓ 分类数据已存在 ($checkCategories 个分类)\n";
    }

    // 6. 创建测试用户
    echo "\n=== 6. 检查测试用户 ===\n";
    $checkUsers = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    if ($checkUsers == 0) {
        $testUsers = [
            ['admin', '管理员', 'admin@study.com', password_hash('123456', PASSWORD_DEFAULT), 'admin'],
            ['zhangsan', '张三', 'zhangsan@study.com', password_hash('123456', PASSWORD_DEFAULT), 'student'],
            ['lisi', '李四', 'lisi@study.com', password_hash('123456', PASSWORD_DEFAULT), 'student'],
            ['wangwu', '王五', 'wangwu@study.com', password_hash('123456', PASSWORD_DEFAULT), 'teacher']
        ];

        $stmt = $pdo->prepare("INSERT INTO users (username, real_name, email, password, role, is_active, enrollment_date, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 1, CURDATE(), NOW(), NOW())");
        
        foreach ($testUsers as $user) {
            $stmt->execute($user);
        }
        echo "✓ 测试用户创建完成 (4个用户)\n";
    } else {
        echo "✓ 用户数据已存在 ($checkUsers 个用户)\n";
    }

    // 7. 创建测试资源
    echo "\n=== 7. 检查测试资源 ===\n";
    $checkResources = $pdo->query("SELECT COUNT(*) FROM resources")->fetchColumn();
    if ($checkResources == 0) {
        // 获取分类ID
        $categoryMap = [];
        $categories = $pdo->query("SELECT id, slug FROM categories")->fetchAll(PDO::FETCH_ASSOC);
        foreach ($categories as $category) {
            $categoryMap[$category['slug']] = $category['id'];
        }

        $testResources = [
            [$categoryMap['html'] ?? 1, 'HTML5基础教程', 'HTML5语义化标签详解', 'video', 'https://example.com/html-basics', 'beginner', 120],
            [$categoryMap['css'] ?? 2, 'CSS3布局指南', 'Flexbox和Grid布局实战', 'document', 'https://example.com/css-layout', 'intermediate', 180],
            [$categoryMap['javascript'] ?? 3, 'JavaScript ES6+特性', '现代JavaScript语法详解', 'video', 'https://example.com/js-es6', 'intermediate', 240],
            [$categoryMap['csharp'] ?? 4, 'C#面向对象编程', 'C#类与对象基础教程', 'book', 'https://example.com/csharp-oop', 'beginner', 300],
            [$categoryMap['vuejs'] ?? 5, 'Vue3组合式API', 'Vue3新特性详解', 'video', 'https://example.com/vue3-api', 'advanced', 200]
        ];

        $stmt = $pdo->prepare("INSERT INTO resources (category_id, title, description, type, url, difficulty, estimated_time, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())");
        
        foreach ($testResources as $resource) {
            $stmt->execute($resource);
        }
        echo "✓ 测试资源创建完成 (5个资源)\n";
    } else {
        echo "✓ 资源数据已存在 ($checkResources 个资源)\n";
    }

    // 8. 创建测试打卡数据
    echo "\n=== 8. 检查测试打卡数据 ===\n";
    $checkCheckins = $pdo->query("SELECT COUNT(*) FROM checkins")->fetchColumn();
    if ($checkCheckins == 0) {
        // 获取用户ID（除了admin）
        $users = $pdo->query("SELECT id FROM users WHERE role != 'admin' LIMIT 3")->fetchAll(PDO::FETCH_COLUMN);
        
        if (!empty($users)) {
            $stmt = $pdo->prepare("INSERT INTO checkins (user_id, checkin_date, study_minutes, study_content, mood, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");
            
            $moods = ['excellent', 'good', 'normal', 'tired'];
            $contents = ['学习HTML基础', '练习CSS布局', '学习JavaScript语法', '复习算法题', '项目实战'];
            
            // 为每个用户创建最近7天的打卡记录
            foreach ($users as $userId) {
                for ($i = 0; $i < 7; $i++) {
                    $date = date('Y-m-d', strtotime("-$i days"));
                    $minutes = rand(30, 180);
                    $content = $contents[array_rand($contents)];
                    $mood = $moods[array_rand($moods)];
                    
                    $stmt->execute([$userId, $date, $minutes, $content, $mood]);
                }
            }
            echo "✓ 测试打卡数据创建完成\n";
        }
    } else {
        echo "✓ 打卡数据已存在 ($checkCheckins 条打卡记录)\n";
    }

    // 9. 验证数据完整性
    echo "\n=== 9. 数据完整性验证 ===\n";
    $stats = [];
    $stats['users'] = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $stats['categories'] = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
    $stats['resources'] = $pdo->query("SELECT COUNT(*) FROM resources")->fetchColumn();
    $stats['checkins'] = $pdo->query("SELECT COUNT(*) FROM checkins")->fetchColumn();
    
    echo "✓ 用户数量: {$stats['users']}\n";
    echo "✓ 分类数量: {$stats['categories']}\n";
    echo "✓ 资源数量: {$stats['resources']}\n";
    echo "✓ 打卡记录: {$stats['checkins']}\n";

    echo "\n=== 数据库检查和修复完成 ===\n";
    echo "所有表和数据已准备就绪，可以开始测试功能！\n";

} catch (PDOException $e) {
    echo "❌ 数据库错误: " . $e->getMessage() . "\n";
    exit(1);
} catch (Exception $e) {
    echo "❌ 系统错误: " . $e->getMessage() . "\n";
    exit(1);
} 
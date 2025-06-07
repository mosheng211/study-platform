-- 创建数据库
CREATE DATABASE IF NOT EXISTS `study_platform` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `study_platform`;

-- 用户表
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

-- 分类表
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

-- 资源表
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

-- 打卡表
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

-- 插入分类数据
INSERT INTO categories (name, slug, description, color, icon, sort_order, is_active, created_at, updated_at) VALUES
('HTML', 'html', 'HTML5语义化标签、网页结构基础', '#e34c26', 'Document', 1, 1, NOW(), NOW()),
('CSS', 'css', 'CSS3样式、布局、响应式设计', '#1572b6', 'Brush', 2, 1, NOW(), NOW()),
('JavaScript', 'javascript', 'JavaScript基础语法、DOM操作、异步编程', '#f7df1e', 'Lightning', 3, 1, NOW(), NOW()),
('C#', 'csharp', 'C#语法基础、面向对象编程、.NET框架', '#512bd4', 'Code', 4, 1, NOW(), NOW()),
('Vue.js', 'vuejs', 'Vue3组合式API、组件化开发、路由状态管理', '#4fc08d', 'Component', 5, 1, NOW(), NOW()),
('CEF3', 'cef3', 'CEF3框架、浏览器自动化、CefSharp集成', '#2196f3', 'Monitor', 6, 1, NOW(), NOW()),
('Selenium', 'selenium', 'Selenium WebDriver、浏览器自动化测试', '#43b02a', 'Robot', 7, 1, NOW(), NOW()),
('算法与数据结构', 'algorithm', '基础算法、数据结构、编程思维训练', '#ff6b6b', 'MagicStick', 8, 1, NOW(), NOW()),
('数据库', 'database', 'MySQL、SQL语句、数据库设计', '#336791', 'Coin', 9, 1, NOW(), NOW()),
('项目实战', 'project', '综合项目、实战案例、完整开发流程', '#ffc107', 'Trophy', 10, 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE slug=slug;

-- 插入测试用户
INSERT INTO users (username, real_name, email, password, role, is_active, enrollment_date, created_at, updated_at) VALUES
('admin', '管理员', 'admin@study.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', 1, CURDATE(), NOW(), NOW()),
('zhangsan', '张三', 'zhangsan@study.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 1, CURDATE(), NOW(), NOW()),
('lisi', '李四', 'lisi@study.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'student', 1, CURDATE(), NOW(), NOW()),
('wangwu', '王五', 'wangwu@study.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'teacher', 1, CURDATE(), NOW(), NOW())
ON DUPLICATE KEY UPDATE username=username;

-- 插入测试资源
INSERT INTO resources (category_id, title, description, type, url, difficulty, estimated_time, is_active, created_at, updated_at) VALUES
(1, 'HTML5基础教程', 'HTML5语义化标签详解', 'video', 'https://example.com/html-basics', 'beginner', 120, 1, NOW(), NOW()),
(2, 'CSS3布局指南', 'Flexbox和Grid布局实战', 'document', 'https://example.com/css-layout', 'intermediate', 180, 1, NOW(), NOW()),
(3, 'JavaScript ES6+特性', '现代JavaScript语法详解', 'video', 'https://example.com/js-es6', 'intermediate', 240, 1, NOW(), NOW()),
(4, 'C#面向对象编程', 'C#类与对象基础教程', 'book', 'https://example.com/csharp-oop', 'beginner', 300, 1, NOW(), NOW()),
(5, 'Vue3组合式API', 'Vue3新特性详解', 'video', 'https://example.com/vue3-api', 'advanced', 200, 1, NOW(), NOW());

-- 插入测试打卡数据（为每个学生用户创建最近7天的打卡）
INSERT INTO checkins (user_id, checkin_date, study_minutes, study_content, mood, created_at, updated_at) 
SELECT 
    u.id,
    DATE_SUB(CURDATE(), INTERVAL n.num DAY) as checkin_date,
    FLOOR(30 + RAND() * 150) as study_minutes,
    CASE 
        WHEN FLOOR(RAND() * 5) = 0 THEN '学习HTML基础'
        WHEN FLOOR(RAND() * 5) = 1 THEN '练习CSS布局'
        WHEN FLOOR(RAND() * 5) = 2 THEN '学习JavaScript语法'
        WHEN FLOOR(RAND() * 5) = 3 THEN '复习算法题'
        ELSE '项目实战'
    END as study_content,
    CASE 
        WHEN FLOOR(RAND() * 4) = 0 THEN 'excellent'
        WHEN FLOOR(RAND() * 4) = 1 THEN 'good'
        WHEN FLOOR(RAND() * 4) = 2 THEN 'normal'
        ELSE 'tired'
    END as mood,
    NOW(),
    NOW()
FROM 
    (SELECT id FROM users WHERE role = 'student' LIMIT 3) u
CROSS JOIN 
    (SELECT 0 as num UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6) n
ON DUPLICATE KEY UPDATE user_id=user_id; 
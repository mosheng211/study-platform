-- 设置字符集
SET NAMES utf8mb4;
SET CHARACTER SET utf8mb4;

USE study_platform;

-- 删除现有的categories表（如果存在）
DROP TABLE IF EXISTS categories;

-- 创建categories表
CREATE TABLE categories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL,
    description TEXT NULL,
    color VARCHAR(7) NOT NULL DEFAULT '#1890ff',
    icon VARCHAR(255) NULL,
    sort_order INT NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY categories_slug_unique (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 插入测试数据（不包含中文描述，避免编码问题）
INSERT INTO categories (name, slug, description, color, icon, sort_order, is_active, created_at, updated_at) VALUES
('HTML', 'html', 'HTML basics and tags', '#e34c26', 'mdi-language-html5', 1, 1, NOW(), NOW()),
('CSS', 'css', 'CSS styling and design', '#1572b6', 'mdi-language-css3', 2, 1, NOW(), NOW()),
('JavaScript', 'javascript', 'JavaScript programming', '#f7df1e', 'mdi-language-javascript', 3, 1, NOW(), NOW()),
('C#', 'csharp', 'C# programming language', '#239120', 'mdi-language-csharp', 4, 1, NOW(), NOW()),
('Vue.js', 'vuejs', 'Vue.js frontend framework', '#4fc08d', 'mdi-vuejs', 5, 1, NOW(), NOW()),
('CEF3', 'cef3', 'CEF3 desktop development', '#ff6b6b', 'mdi-desktop-classic', 6, 1, NOW(), NOW()),
('Selenium', 'selenium', 'Selenium automation testing', '#43b02a', 'mdi-robot', 7, 1, NOW(), NOW()),
('Algorithm', 'algorithms', 'Data structures and algorithms', '#6f42c1', 'mdi-brain', 8, 1, NOW(), NOW()),
('Database', 'database', 'Database design and management', '#fd7e14', 'mdi-database', 9, 1, NOW(), NOW()),
('Projects', 'projects', 'Practical project development', '#dc3545', 'mdi-folder-multiple', 10, 1, NOW(), NOW());

-- 检查插入结果
SELECT COUNT(*) as total_categories FROM categories;
SELECT id, name, slug, color FROM categories ORDER BY sort_order; 
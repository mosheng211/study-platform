USE study_platform;

-- 删除现有的resources表（如果存在）
DROP TABLE IF EXISTS resources;

-- 创建完整的resources表
CREATE TABLE resources (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    category_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    type VARCHAR(50) NOT NULL DEFAULT 'article',
    category VARCHAR(255) NULL,
    difficulty ENUM('beginner', 'intermediate', 'advanced') NOT NULL DEFAULT 'beginner',
    url VARCHAR(255) NULL,
    file_path VARCHAR(255) NULL,
    duration INT NULL,
    thumbnail VARCHAR(255) NULL,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    view_count INT NOT NULL DEFAULT 0,
    download_count INT NOT NULL DEFAULT 0,
    rating DECIMAL(3,1) NULL DEFAULT NULL,
    creator_id BIGINT UNSIGNED NULL,
    tags JSON NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_category_id (category_id),
    KEY idx_type (type),
    KEY idx_difficulty (difficulty),
    KEY idx_creator_id (creator_id),
    KEY idx_is_active (is_active),
    KEY idx_is_featured (is_featured),
    CONSTRAINT fk_resources_category 
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    CONSTRAINT fk_resources_creator 
        FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 插入一些测试数据
INSERT INTO resources (category_id, title, description, type, category, difficulty, is_featured, is_active, view_count, download_count, creator_id, created_at, updated_at) VALUES
(1, 'HTML Basic Tutorial', 'Learn HTML basics and tags', 'article', 'HTML', 'beginner', 0, 1, 156, 23, 1, NOW(), NOW()),
(1, 'HTML Forms Guide', 'Complete HTML forms tutorial', 'article', 'HTML', 'intermediate', 1, 1, 89, 12, 1, NOW(), NOW()),
(2, 'CSS Selectors Guide', 'Master CSS selectors', 'article', 'CSS', 'beginner', 0, 1, 234, 45, 1, NOW(), NOW()),
(2, 'CSS Flexbox Layout', 'Complete Flexbox guide', 'video', 'CSS', 'intermediate', 1, 1, 178, 34, 1, NOW(), NOW()),
(3, 'JavaScript Basics', 'JS fundamentals for beginners', 'article', 'JavaScript', 'beginner', 0, 1, 312, 67, 1, NOW(), NOW()),
(3, 'ES6 Features', 'Modern JavaScript features', 'video', 'JavaScript', 'advanced', 1, 1, 156, 28, 1, NOW(), NOW()),
(4, 'C# Introduction', 'C# programming basics', 'article', 'C#', 'beginner', 0, 1, 98, 15, 1, NOW(), NOW()),
(5, 'Vue.js Tutorial', 'Vue.js framework guide', 'video', 'Vue.js', 'intermediate', 1, 1, 145, 31, 1, NOW(), NOW());

-- 验证数据
SELECT COUNT(*) as total_resources FROM resources;
SELECT c.name, COUNT(r.id) as resource_count 
FROM categories c 
LEFT JOIN resources r ON c.id = r.category_id 
GROUP BY c.id, c.name 
ORDER BY c.sort_order; 
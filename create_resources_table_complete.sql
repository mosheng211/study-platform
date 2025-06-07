USE study_platform;

-- 删除现有的resources表（如果存在）
DROP TABLE IF EXISTS resources;

-- 创建resources表（包含category_id字段）
CREATE TABLE resources (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    category_id BIGINT UNSIGNED NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT NULL,
    content LONGTEXT NULL,
    type ENUM('video', 'article', 'exercise', 'quiz') NOT NULL DEFAULT 'article',
    url VARCHAR(255) NULL,
    file_path VARCHAR(255) NULL,
    duration INT NULL COMMENT '持续时间（秒）',
    difficulty ENUM('beginner', 'intermediate', 'advanced') NOT NULL DEFAULT 'beginner',
    tags JSON NULL,
    sort_order INT NOT NULL DEFAULT 0,
    view_count INT NOT NULL DEFAULT 0,
    like_count INT NOT NULL DEFAULT 0,
    is_featured TINYINT(1) NOT NULL DEFAULT 0,
    is_published TINYINT(1) NOT NULL DEFAULT 1,
    published_at TIMESTAMP NULL,
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    KEY idx_category_id (category_id),
    KEY idx_type (type),
    KEY idx_difficulty (difficulty),
    KEY idx_published (is_published),
    KEY idx_featured (is_featured),
    KEY idx_sort_order (sort_order),
    CONSTRAINT fk_resources_category 
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 插入测试数据
INSERT INTO resources (category_id, title, description, content, type, difficulty, sort_order, is_published, published_at, created_at, updated_at) VALUES
(1, 'HTML基础教程', 'HTML基础语法和标签使用', '这是HTML基础教程的内容...', 'article', 'beginner', 1, 1, NOW(), NOW(), NOW()),
(1, 'HTML表单元素', 'HTML表单的创建和使用', '这是HTML表单教程的内容...', 'article', 'intermediate', 2, 1, NOW(), NOW(), NOW()),
(2, 'CSS选择器详解', 'CSS各种选择器的使用方法', '这是CSS选择器教程的内容...', 'article', 'beginner', 1, 1, NOW(), NOW(), NOW()),
(2, 'CSS Flexbox布局', 'Flexbox弹性盒布局详解', '这是Flexbox布局教程的内容...', 'article', 'intermediate', 2, 1, NOW(), NOW(), NOW()),
(3, 'JavaScript变量和数据类型', 'JS基础语法学习', '这是JavaScript基础教程的内容...', 'article', 'beginner', 1, 1, NOW(), NOW(), NOW());

-- 验证数据
SELECT COUNT(*) as total_resources FROM resources;
SELECT r.id, r.title, c.name as category_name FROM resources r 
LEFT JOIN categories c ON r.category_id = c.id 
ORDER BY r.category_id, r.sort_order 
LIMIT 10; 
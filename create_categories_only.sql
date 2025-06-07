USE study_platform;

-- 删除现有的categories表（如果存在）
DROP TABLE IF EXISTS categories;

-- 创建categories表
CREATE TABLE categories (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL COMMENT '分类名称',
    slug VARCHAR(255) NOT NULL COMMENT '分类标识符',
    description TEXT NULL COMMENT '分类描述',
    color VARCHAR(7) NOT NULL DEFAULT '#1890ff' COMMENT '分类颜色',
    icon VARCHAR(255) NULL COMMENT '分类图标',
    sort_order INT NOT NULL DEFAULT 0 COMMENT '排序顺序',
    is_active TINYINT(1) NOT NULL DEFAULT 1 COMMENT '是否激活',
    created_at TIMESTAMP NULL DEFAULT NULL,
    updated_at TIMESTAMP NULL DEFAULT NULL,
    PRIMARY KEY (id),
    UNIQUE KEY categories_slug_unique (slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 插入测试数据
INSERT INTO categories (name, slug, description, color, icon, sort_order, is_active, created_at, updated_at) VALUES
('HTML', 'html', 'HTML基础语法与标签', '#e34c26', 'mdi-language-html5', 1, 1, NOW(), NOW()),
('CSS', 'css', 'CSS样式设计', '#1572b6', 'mdi-language-css3', 2, 1, NOW(), NOW()),
('JavaScript', 'javascript', 'JavaScript编程基础', '#f7df1e', 'mdi-language-javascript', 3, 1, NOW(), NOW()),
('C#', 'csharp', 'C#编程语言', '#239120', 'mdi-language-csharp', 4, 1, NOW(), NOW()),
('Vue.js', 'vuejs', 'Vue.js前端框架', '#4fc08d', 'mdi-vuejs', 5, 1, NOW(), NOW()),
('CEF3', 'cef3', 'CEF3桌面应用开发', '#ff6b6b', 'mdi-desktop-classic', 6, 1, NOW(), NOW()),
('Selenium', 'selenium', 'Selenium自动化测试', '#43b02a', 'mdi-robot', 7, 1, NOW(), NOW()),
('算法', 'algorithms', '数据结构与算法', '#6f42c1', 'mdi-brain', 8, 1, NOW(), NOW()),
('数据库', 'database', '数据库设计与管理', '#fd7e14', 'mdi-database', 9, 1, NOW(), NOW()),
('项目实战', 'projects', '实际项目开发', '#dc3545', 'mdi-folder-multiple', 10, 1, NOW(), NOW());

-- 检查插入结果
SELECT COUNT(*) as total_categories FROM categories;
SELECT name, slug, color FROM categories ORDER BY sort_order LIMIT 5; 
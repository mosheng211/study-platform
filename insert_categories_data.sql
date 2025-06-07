-- 插入初始分类数据

-- 确保categories表存在
CREATE TABLE IF NOT EXISTS categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) NOT NULL UNIQUE,
    description TEXT NULL,
    color VARCHAR(7) DEFAULT '#666666',
    icon VARCHAR(255) NULL,
    sort_order INT DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    resources_count INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- 插入编程学习分类
INSERT IGNORE INTO categories (id, name, slug, description, color, icon, sort_order, is_active) VALUES
(1, 'HTML', 'html', 'HTML基础语法和标签学习', '#e34c26', 'mdi-language-html5', 1, TRUE),
(2, 'CSS', 'css', 'CSS样式设计和布局技术', '#1572b6', 'mdi-language-css3', 2, TRUE),
(3, 'JavaScript', 'javascript', 'JavaScript编程语言基础', '#f7df1e', 'mdi-language-javascript', 3, TRUE),
(4, 'C#编程', 'csharp', 'C#编程语言学习', '#239120', 'mdi-language-csharp', 4, TRUE),
(5, 'Vue.js', 'vuejs', 'Vue.js前端框架开发', '#4fc08d', 'mdi-vuejs', 5, TRUE),
(6, 'CEF3', 'cef3', 'CEF3桌面应用开发', '#ff6b6b', 'mdi-desktop-classic', 6, TRUE),
(7, 'Selenium', 'selenium', 'Selenium自动化测试', '#43b02a', 'mdi-robot', 7, TRUE),
(8, 'Python', 'python', 'Python编程语言学习', '#3776ab', 'mdi-language-python', 8, TRUE),
(9, 'Java', 'java', 'Java编程语言基础', '#ed8b00', 'mdi-language-java', 9, TRUE),
(10, 'Node.js', 'nodejs', 'Node.js后端开发', '#339933', 'mdi-nodejs', 10, TRUE),
(11, '数据库', 'database', 'SQL和数据库设计', '#336791', 'mdi-database', 11, TRUE),
(12, '算法数据结构', 'algorithm', '算法和数据结构学习', '#ff4081', 'mdi-chart-tree', 12, TRUE),
(13, '开发工具', 'tools', '开发工具和IDE使用', '#607d8b', 'mdi-tools', 13, TRUE),
(14, '设计工具', 'design', 'UI/UX设计工具', '#9c27b0', 'mdi-palette', 14, TRUE),
(15, '构建工具', 'build', '构建工具和自动化', '#795548', 'mdi-cogs', 15, TRUE);

-- 更新资源的category_id（如果resources表存在并且有数据）
UPDATE resources SET category_id = 1 WHERE category_id IS NULL AND (category = 'HTML' OR category LIKE '%HTML%');
UPDATE resources SET category_id = 2 WHERE category_id IS NULL AND (category = 'CSS' OR category LIKE '%CSS%');
UPDATE resources SET category_id = 3 WHERE category_id IS NULL AND (category = 'JavaScript' OR category LIKE '%JavaScript%' OR category LIKE '%JS%');
UPDATE resources SET category_id = 4 WHERE category_id IS NULL AND (category = 'C#' OR category LIKE '%C#%' OR category LIKE '%csharp%');
UPDATE resources SET category_id = 5 WHERE category_id IS NULL AND (category = 'Vue' OR category LIKE '%Vue%');
UPDATE resources SET category_id = 6 WHERE category_id IS NULL AND (category = 'CEF' OR category LIKE '%CEF%');
UPDATE resources SET category_id = 7 WHERE category_id IS NULL AND (category = 'Selenium' OR category LIKE '%Selenium%');
UPDATE resources SET category_id = 8 WHERE category_id IS NULL AND (category = 'Python' OR category LIKE '%Python%');
UPDATE resources SET category_id = 9 WHERE category_id IS NULL AND (category = 'Java' OR category LIKE '%Java%');
UPDATE resources SET category_id = 10 WHERE category_id IS NULL AND (category = 'Node' OR category LIKE '%Node%');
UPDATE resources SET category_id = 11 WHERE category_id IS NULL AND (category LIKE '%数据库%' OR category LIKE '%SQL%' OR category LIKE '%database%');
UPDATE resources SET category_id = 12 WHERE category_id IS NULL AND (category LIKE '%算法%' OR category LIKE '%数据结构%');
UPDATE resources SET category_id = 13 WHERE category_id IS NULL AND (category LIKE '%工具%' OR category LIKE '%tool%');
UPDATE resources SET category_id = 14 WHERE category_id IS NULL AND (category LIKE '%设计%' OR category LIKE '%design%');
UPDATE resources SET category_id = 15 WHERE category_id IS NULL AND (category LIKE '%构建%' OR category LIKE '%build%');

-- 设置默认分类（如果还有未分类的资源）
UPDATE resources SET category_id = 13 WHERE category_id IS NULL;

-- 更新分类的资源计数
UPDATE categories SET resources_count = (
    SELECT COUNT(*) FROM resources WHERE category_id = categories.id
) WHERE id IN (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15);

-- 显示结果
SELECT '插入完成，当前分类列表:' as message;
SELECT id, name, slug, resources_count, is_active FROM categories ORDER BY sort_order;

SELECT '资源分类分布:' as message;
SELECT c.name as category_name, COUNT(r.id) as resource_count
FROM categories c
LEFT JOIN resources r ON c.id = r.category_id
GROUP BY c.id, c.name
ORDER BY c.sort_order; 
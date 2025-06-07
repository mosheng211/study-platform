-- 添加resources表缺失的字段

-- 检查当前表结构
SELECT 'Current table structure:' as message;
DESCRIBE resources;

-- 添加creator_id字段（如果不存在）
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'resources' 
     AND COLUMN_NAME = 'creator_id' 
     AND TABLE_SCHEMA = 'study_platform') > 0,
    'SELECT "creator_id field already exists"',
    'ALTER TABLE resources ADD COLUMN creator_id BIGINT UNSIGNED NULL AFTER category_id'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 添加content字段（如果不存在）
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'resources' 
     AND COLUMN_NAME = 'content' 
     AND TABLE_SCHEMA = 'study_platform') > 0,
    'SELECT "content field already exists"',
    'ALTER TABLE resources ADD COLUMN content LONGTEXT NULL AFTER description'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 添加difficulty字段（如果不存在）
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'resources' 
     AND COLUMN_NAME = 'difficulty' 
     AND TABLE_SCHEMA = 'study_platform') > 0,
    'SELECT "difficulty field already exists"',
    'ALTER TABLE resources ADD COLUMN difficulty VARCHAR(50) DEFAULT "beginner" AFTER category_id'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 添加url字段（如果不存在）
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'resources' 
     AND COLUMN_NAME = 'url' 
     AND TABLE_SCHEMA = 'study_platform') > 0,
    'SELECT "url field already exists"',
    'ALTER TABLE resources ADD COLUMN url TEXT NULL AFTER content'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 添加file_path字段（如果不存在）
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'resources' 
     AND COLUMN_NAME = 'file_path' 
     AND TABLE_SCHEMA = 'study_platform') > 0,
    'SELECT "file_path field already exists"',
    'ALTER TABLE resources ADD COLUMN file_path TEXT NULL AFTER url'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 添加duration字段（如果不存在）
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'resources' 
     AND COLUMN_NAME = 'duration' 
     AND TABLE_SCHEMA = 'study_platform') > 0,
    'SELECT "duration field already exists"',
    'ALTER TABLE resources ADD COLUMN duration VARCHAR(50) NULL AFTER file_path'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 添加tags字段（如果不存在）
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'resources' 
     AND COLUMN_NAME = 'tags' 
     AND TABLE_SCHEMA = 'study_platform') > 0,
    'SELECT "tags field already exists"',
    'ALTER TABLE resources ADD COLUMN tags TEXT NULL AFTER duration'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 添加sort_order字段（如果不存在）
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'resources' 
     AND COLUMN_NAME = 'sort_order' 
     AND TABLE_SCHEMA = 'study_platform') > 0,
    'SELECT "sort_order field already exists"',
    'ALTER TABLE resources ADD COLUMN sort_order INT DEFAULT 0 AFTER tags'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 添加is_featured字段（如果不存在）
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'resources' 
     AND COLUMN_NAME = 'is_featured' 
     AND TABLE_SCHEMA = 'study_platform') > 0,
    'SELECT "is_featured field already exists"',
    'ALTER TABLE resources ADD COLUMN is_featured BOOLEAN DEFAULT FALSE AFTER sort_order'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 添加is_published字段（如果不存在）
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'resources' 
     AND COLUMN_NAME = 'is_published' 
     AND TABLE_SCHEMA = 'study_platform') > 0,
    'SELECT "is_published field already exists"',
    'ALTER TABLE resources ADD COLUMN is_published BOOLEAN DEFAULT TRUE AFTER is_featured'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 添加is_active字段（如果不存在）
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'resources' 
     AND COLUMN_NAME = 'is_active' 
     AND TABLE_SCHEMA = 'study_platform') > 0,
    'SELECT "is_active field already exists"',
    'ALTER TABLE resources ADD COLUMN is_active BOOLEAN DEFAULT TRUE AFTER is_published'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 创建默认管理员用户（如果不存在）
INSERT IGNORE INTO users (id, username, email, real_name, password, role, created_at, updated_at) 
VALUES (1, 'admin', 'admin@example.com', '系统管理员', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin', NOW(), NOW());

-- 添加外键约束（如果不存在）
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE 
     WHERE TABLE_NAME = 'resources' 
     AND COLUMN_NAME = 'creator_id' 
     AND CONSTRAINT_NAME LIKE 'resources_creator_id_foreign'
     AND TABLE_SCHEMA = 'study_platform') > 0,
    'SELECT "Foreign key already exists"',
    'ALTER TABLE resources ADD CONSTRAINT resources_creator_id_foreign FOREIGN KEY (creator_id) REFERENCES users(id) ON DELETE SET NULL'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 添加view_count字段（如果不存在）
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'resources' 
     AND COLUMN_NAME = 'view_count' 
     AND TABLE_SCHEMA = 'study_platform') > 0,
    'SELECT "view_count field already exists"',
    'ALTER TABLE resources ADD COLUMN view_count INT DEFAULT 0 AFTER is_active'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 添加download_count字段（如果不存在）
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'resources' 
     AND COLUMN_NAME = 'download_count' 
     AND TABLE_SCHEMA = 'study_platform') > 0,
    'SELECT "download_count field already exists"',
    'ALTER TABLE resources ADD COLUMN download_count INT DEFAULT 0 AFTER view_count'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 添加rating字段（如果不存在）
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'resources' 
     AND COLUMN_NAME = 'rating' 
     AND TABLE_SCHEMA = 'study_platform') > 0,
    'SELECT "rating field already exists"',
    'ALTER TABLE resources ADD COLUMN rating DECIMAL(3,1) DEFAULT 0.0 AFTER download_count'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 添加thumbnail字段（如果不存在）
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'resources' 
     AND COLUMN_NAME = 'thumbnail' 
     AND TABLE_SCHEMA = 'study_platform') > 0,
    'SELECT "thumbnail field already exists"',
    'ALTER TABLE resources ADD COLUMN thumbnail TEXT NULL AFTER file_path'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 添加category字段（如果不存在，为了向后兼容）
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_NAME = 'resources' 
     AND COLUMN_NAME = 'category' 
     AND TABLE_SCHEMA = 'study_platform') > 0,
    'SELECT "category field already exists"',
    'ALTER TABLE resources ADD COLUMN category VARCHAR(255) NULL AFTER type'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- 更新现有记录的creator_id为管理员ID（如果为空）
UPDATE resources SET creator_id = 1 WHERE creator_id IS NULL;

-- 设置默认值
UPDATE resources SET view_count = 0 WHERE view_count IS NULL;
UPDATE resources SET download_count = 0 WHERE download_count IS NULL;
UPDATE resources SET rating = 0.0 WHERE rating IS NULL;
UPDATE resources SET is_active = TRUE WHERE is_active IS NULL;
UPDATE resources SET is_published = TRUE WHERE is_published IS NULL;
UPDATE resources SET sort_order = 0 WHERE sort_order IS NULL;

-- 显示更新后的表结构
SELECT 'Updated table structure:' as message;
DESCRIBE resources;

-- 显示统计信息
SELECT 'Resource count:' as message, COUNT(*) as count FROM resources;
SELECT 'Resources with creator:' as message, COUNT(*) as count FROM resources WHERE creator_id IS NOT NULL;
SELECT 'Active resources:' as message, COUNT(*) as count FROM resources WHERE is_active = TRUE; 
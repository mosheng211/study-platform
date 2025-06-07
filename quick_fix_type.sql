-- 快速修复 resources 表的 type 字段问题
-- 将type字段改为VARCHAR类型以支持所有资源类型

-- 修改type字段为VARCHAR类型
ALTER TABLE resources MODIFY COLUMN type VARCHAR(50) NOT NULL DEFAULT 'article';

-- 验证修复结果
SELECT 'Type字段修复完成' as message;
SHOW COLUMNS FROM resources LIKE 'type'; 
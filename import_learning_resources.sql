-- 批量导入学习资源 - 根据54天编程学习计划
-- 执行前请确保categories表已有相应分类

-- 清理可能的重复数据（可选）
-- DELETE FROM resources WHERE title LIKE '%学习计划%';

-- ============================================
-- 第一阶段：Web技术基础（HTML/CSS/JavaScript）
-- ============================================

-- HTML基础资源
INSERT INTO resources (title, description, type, category_id, difficulty, url, content, tags, duration, is_featured, is_published, is_active, creator_id, created_at, updated_at) VALUES
('菜鸟教程 - HTML完整教程', 'HTML从入门到精通，包含所有标签详解、实例演示，适合零基础学习者系统学习HTML结构与语义', 'document', 1, 'beginner', 'https://www.runoob.com/html/html-tutorial.html', '涵盖HTML基础语法、常用标签、表单元素、语义化标签等核心知识点，提供在线编辑器实时练习', '["HTML", "前端基础", "菜鸟教程", "零基础"]', 480, 1, 1, 1, 1, NOW(), NOW()),

('菜鸟教程 - CSS完整教程', '系统学习CSS样式设计，掌握选择器、盒模型、布局技术，从基础样式到响应式设计', 'document', 2, 'beginner', 'https://www.runoob.com/css/css-tutorial.html', '详细讲解CSS语法、选择器优先级、盒模型、Flexbox、Grid布局等现代CSS技术', '["CSS", "样式设计", "布局", "响应式"]', 600, 1, 1, 1, 1, NOW(), NOW()),

('廖雪峰官网 - JavaScript教程', '由资深程序员廖雪峰编写的JavaScript权威教程，深入浅出讲解JS核心概念', 'document', 3, 'beginner', 'https://www.liaoxuefeng.com/wiki/1022910821149312', '涵盖JS基础语法、函数、对象、DOM操作、异步编程、ES6新特性等完整知识体系', '["JavaScript", "前端编程", "廖雪峰", "权威教程"]', 720, 1, 1, 1, 1, NOW(), NOW()),

('尚硅谷前端HTML+CSS+JS基础教程', 'B站知名培训机构尚硅谷出品的前端基础视频教程，适合系统学习', 'video', 1, 'beginner', 'https://www.bilibili.com/video/BV1Kg411T7t9', '200+集完整视频教程，包含大量实战项目，从零基础到能够独立开发网页', '["尚硅谷", "视频教程", "前端基础", "项目实战"]', 1200, 1, 1, 1, 1, NOW(), NOW()),

('黑马程序员前端基础教程', 'B站黑马程序员前端开发基础课程，包含HTML5+CSS3+JavaScript核心技术', 'video', 1, 'beginner', 'https://www.bilibili.com/video/BV14J4114768', '系统讲解现代前端开发技术，注重实战应用和企业级开发规范', '["黑马程序员", "HTML5", "CSS3", "企业级开发"]', 960, 1, 1, 1, 1, NOW(), NOW()),

-- CSS进阶和布局
('CSS世界 - 张鑫旭博客', '国内CSS专家张鑫旭的技术博客，深度解析CSS原理和高级技巧', 'document', 2, 'intermediate', 'https://www.zhangxinxu.com/', '包含大量CSS实战案例、浏览器兼容性解决方案、CSS新特性应用等高质量文章', '["张鑫旭", "CSS进阶", "浏览器兼容", "实战案例"]', 0, 1, 1, 1, 1, NOW(), NOW()),

-- JavaScript进阶
('ES6入门教程 - 阮一峰', '前端大神阮一峰编写的ES6权威入门教程，全面讲解现代JavaScript特性', 'document', 3, 'intermediate', 'https://es6.ruanyifeng.com/', '详细介绍ES6/ES2015+新语法、模块化、Promise、async/await等现代JS开发必备知识', '["阮一峰", "ES6", "现代JavaScript", "异步编程"]', 480, 1, 1, 1, 1, NOW(), NOW()),

('JavaScript高级程序设计讲解', '基于经典红宝书的JavaScript深度讲解视频，适合有基础的开发者提升', 'video', 3, 'advanced', 'https://www.bilibili.com/video/BV1YW411T7GX', '深入JavaScript核心机制，包括作用域、闭包、原型链、异步编程等高级概念', '["JavaScript进阶", "红宝书", "核心机制", "高级概念"]', 800, 1, 1, 1, 1, NOW(), NOW()),

-- ============================================
-- 第二阶段：C#编程基础
-- ============================================

-- C#基础教程
('微软.NET官方中文文档', '微软官方提供的.NET和C#学习文档，权威且全面的学习资源', 'document', 4, 'beginner', 'https://docs.microsoft.com/zh-cn/dotnet/', '包含C#语法指南、.NET框架介绍、最佳实践、API参考等官方权威内容', '["微软官方", ".NET", "C#基础", "权威文档"]', 0, 1, 1, 1, 1, NOW(), NOW()),

('菜鸟教程 - C#教程', '系统的C#编程语言教程，从基础语法到高级特性的完整学习路径', 'document', 4, 'beginner', 'https://www.runoob.com/csharp/csharp-tutorial.html', '涵盖C#数据类型、控制结构、面向对象、泛型、LINQ等核心概念，提供大量示例', '["C#基础", "面向对象", "泛型", "LINQ"]', 480, 1, 1, 1, 1, NOW(), NOW()),

('刘铁猛C#语言入门详解', 'B站知名C#讲师刘铁猛的语言入门课程，讲解透彻易懂', 'video', 4, 'beginner', 'https://www.bilibili.com/video/BV13b411b7Ht', '深入浅出讲解C#语言特性，注重编程思维培养和最佳实践', '["刘铁猛", "C#入门", "编程思维", "最佳实践"]', 720, 1, 1, 1, 1, NOW(), NOW()),

('黑马程序员C#全套教程', '黑马程序员出品的C#从入门到精通完整视频教程', 'video', 4, 'beginner', 'https://www.bilibili.com/video/BV1FJ411W7e5', '包含C#基础语法、面向对象、WinForms、数据库操作等企业开发技能', '["黑马程序员", "C#全套", "企业开发", "项目实战"]', 960, 1, 1, 1, 1, NOW(), NOW()),

-- C#进阶资源
('博客园 - .NET技术社区', '国内最大的.NET技术社区，汇集大量C#开发经验和最佳实践', 'document', 4, 'intermediate', 'https://www.cnblogs.com/cate/dotnet/', '包含C#进阶技术文章、框架使用经验、性能优化、设计模式等深度内容', '["博客园", ".NET社区", "进阶技术", "最佳实践"]', 0, 1, 1, 1, 1, NOW(), NOW()),

('C#异步编程深入理解', '深度讲解C#异步编程模型，包含async/await、Task、并发等核心概念', 'video', 4, 'advanced', 'https://www.bilibili.com/video/BV1Jt411w7rS', '系统学习.NET异步编程，掌握现代C#应用开发的关键技术', '["异步编程", "async/await", "Task", "并发编程"]', 480, 1, 1, 1, 1, NOW(), NOW()),

-- ============================================
-- 第三阶段：Vue.js框架
-- ============================================

-- Vue基础教程
('Vue.js官方中文文档', 'Vue.js框架的官方中文学习指南，最权威的Vue学习资源', 'document', 5, 'beginner', 'https://cn.vuejs.org/guide/', '完整的Vue3学习指南，包含基础概念、组件系统、状态管理、路由等核心功能', '["Vue.js官方", "Vue3", "组件系统", "状态管理"]', 0, 1, 1, 1, 1, NOW(), NOW()),

('技术胖Vue3实战教程', 'B站知名前端讲师技术胖的Vue3完整实战课程', 'video', 5, 'beginner', 'https://www.bilibili.com/video/BV1dS4y1K7sH', '从Vue3基础到项目实战，包含Composition API、Pinia、Vue Router等现代开发技术', '["技术胖", "Vue3实战", "Composition API", "项目开发"]', 720, 1, 1, 1, 1, NOW(), NOW()),

('尚硅谷Vue3全家桶教程', '尚硅谷出品的Vue3生态系统完整教程，包含企业级开发实战', 'video', 5, 'intermediate', 'https://www.bilibili.com/video/BV1Za4y1r7KE', '深入学习Vue3+TypeScript+Vite+Pinia全技术栈，适合企业级项目开发', '["尚硅谷", "Vue3全家桶", "TypeScript", "企业级开发"]', 960, 1, 1, 1, 1, NOW(), NOW()),

-- Vue进阶资源
('掘金Vue专栏文章集', '掘金社区Vue技术专栏，汇集大量实战经验和最佳实践', 'document', 5, 'intermediate', 'https://juejin.cn/tag/Vue.js', '包含Vue组件设计、性能优化、架构设计、生态工具等进阶内容', '["掘金", "Vue专栏", "组件设计", "性能优化"]', 0, 1, 1, 1, 1, NOW(), NOW()),

('慕课网Vue实战课程', '慕课网提供的Vue项目实战课程，注重企业级应用开发', 'video', 5, 'intermediate', 'https://www.imooc.com/learn/1091', '通过实际项目学习Vue开发流程，掌握组件化开发和工程化实践', '["慕课网", "Vue实战", "企业应用", "工程化开发"]', 600, 1, 1, 1, 1, NOW(), NOW()),

-- ============================================
-- 第四阶段：浏览器自动化&CEF3
-- ============================================

-- Selenium自动化基础
('Selenium自动化测试教程', '全面的Selenium WebDriver中文教程，适合自动化测试入门', 'document', 6, 'beginner', 'https://selenium-python-zh.readthedocs.io/en/latest/', '详细介绍Selenium的安装配置、元素定位、页面操作、等待机制等核心功能', '["Selenium", "自动化测试", "WebDriver", "元素定位"]', 360, 1, 1, 1, 1, NOW(), NOW()),

('虫师Selenium自动化实战', 'B站知名自动化测试专家虫师的Selenium实战课程', 'video', 6, 'beginner', 'https://www.bilibili.com/video/BV1dt411k7s6', '从基础操作到复杂场景处理，全面掌握Web自动化测试技术', '["虫师", "自动化实战", "测试框架", "实际项目"]', 720, 1, 1, 1, 1, NOW(), NOW()),

-- CEF3和CefSharp资源
('CefSharp官方文档中文指南', 'CefSharp框架的使用指南和API文档，C#开发者必备', 'document', 7, 'intermediate', 'https://github.com/cefsharp/CefSharp/wiki', 'CefSharp集成指南、事件处理、JavaScript交互、性能优化等关键技术', '["CefSharp", "CEF3", "JavaScript交互", "C#集成"]', 0, 1, 1, 1, 1, NOW(), NOW()),

('C#浏览器自动化开发实战', 'B站专门讲解C#+CEF3进行浏览器自动化开发的实战教程', 'video', 7, 'advanced', 'https://www.bilibili.com/video/BV1zJ4m1jEeY', '使用C#和CEF3构建浏览器自动化应用，包含登录、数据采集、界面操作等实战案例', '["C#自动化", "CEF3实战", "浏览器控制", "数据采集"]', 480, 1, 1, 1, 1, NOW(), NOW()),

-- 开源项目和案例
('开源中国 - CEF相关项目', '开源中国平台上的CEF相关开源项目和技术文章集合', 'document', 7, 'intermediate', 'https://www.oschina.net/search?scope=project&q=CEF', '实际的CEF应用项目源码、技术分享、解决方案等实用资源', '["开源中国", "CEF项目", "开源代码", "技术分享"]', 0, 1, 1, 1, 1, NOW(), NOW()),

('博客园自动化测试专栏', '博客园自动化测试技术专栏，包含大量实战经验和案例分析', 'document', 6, 'intermediate', 'https://www.cnblogs.com/tag/自动化测试/', '涵盖Web自动化、移动端自动化、接口自动化等全方位测试技术', '["博客园", "自动化测试", "实战经验", "案例分析"]', 0, 1, 1, 1, 1, NOW(), NOW()),

-- ============================================
-- 综合实战和工具资源
-- ============================================

-- 在线工具和平台
('CodePen在线编程', '在线前端代码编辑器，支持HTML/CSS/JavaScript实时预览和分享', 'tool', 1, 'beginner', 'https://codepen.io/', '提供丰富的前端代码示例和模板，支持实时编辑和效果预览，是学习前端的优秀平台', '["在线编程", "代码分享", "前端工具", "实时预览"]', 0, 1, 1, 1, 1, NOW(), NOW()),

('菜鸟教程在线编辑器', '菜鸟教程提供的多语言在线编程环境，支持即时运行和调试', 'tool', 8, 'beginner', 'https://c.runoob.com/', '支持C#、JavaScript、Python等多种编程语言的在线执行环境', '["菜鸟教程", "在线编辑器", "多语言支持", "即时运行"]', 0, 1, 1, 1, 1, NOW(), NOW()),

-- 学习社区和论坛
('CSDN技术社区', '国内最大的程序员技术社区，包含各种编程语言和技术的学习资源', 'document', 8, 'beginner', 'https://www.csdn.net/', '提供技术博客、问答社区、在线课程等丰富的学习资源和交流平台', '["CSDN", "技术社区", "程序员", "学习交流"]', 0, 1, 1, 1, 1, NOW(), NOW()),

('Stack Overflow中文版', 'SegmentFault思否，中国版的程序员问答社区', 'document', 8, 'beginner', 'https://segmentfault.com/', '程序员技术问答、技术分享、求职招聘的综合性技术社区平台', '["SegmentFault", "技术问答", "程序员社区", "技术分享"]', 0, 1, 1, 1, 1, NOW(), NOW()),

-- 项目实战资源
('GitHub中文项目精选', 'GitHub上优秀的中文开源项目集合，包含各种技术栈的实战项目', 'document', 8, 'intermediate', 'https://github.com/topics/chinese', '精选的中文开源项目，适合学习实际项目的代码结构和开发规范', '["GitHub", "开源项目", "中文项目", "代码学习"]', 0, 1, 1, 1, 1, NOW(), NOW()),

('Gitee码云平台', '国内最大的代码托管平台，汇集大量优秀的开源项目和学习资源', 'document', 8, 'beginner', 'https://gitee.com/', '提供Git代码托管、项目管理、协作开发等服务，是学习开源项目的重要平台', '["Gitee", "代码托管", "开源项目", "协作开发"]', 0, 1, 1, 1, 1, NOW(), NOW());

-- 更新现有资源的创建时间和更新时间为当前时间戳
UPDATE resources SET 
    created_at = NOW(), 
    updated_at = NOW(),
    sort_order = id * 10,
    view_count = 0,
    download_count = 0,
    rating = 4.5
WHERE creator_id = 1;

-- 显示导入结果
SELECT 
    c.name as category_name,
    COUNT(r.id) as resource_count
FROM categories c
LEFT JOIN resources r ON c.id = r.category_id
GROUP BY c.id, c.name
ORDER BY c.id; 
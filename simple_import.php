<?php

echo "开始批量导入学习资源...\n";

try {
    $pdo = new PDO('mysql:host=localhost;dbname=study_platform;charset=utf8mb4', 'root', '61263269');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    echo "✅ 数据库连接成功\n";
    
    // 检查categories表
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM categories");
    $categoryCount = $stmt->fetch()['count'];
    echo "📂 当前分类数量: {$categoryCount}\n";
    
    if ($categoryCount == 0) {
        echo "⚠️ 分类数据为空，先创建基础分类...\n";
        
        $categories = [
            "INSERT INTO categories (name, description, created_at, updated_at) VALUES ('HTML/CSS', 'HTML和CSS基础', NOW(), NOW())",
            "INSERT INTO categories (name, description, created_at, updated_at) VALUES ('JavaScript', 'JavaScript编程', NOW(), NOW())",
            "INSERT INTO categories (name, description, created_at, updated_at) VALUES ('C#', 'C#编程语言', NOW(), NOW())",
            "INSERT INTO categories (name, description, created_at, updated_at) VALUES ('Vue.js', 'Vue框架', NOW(), NOW())",
            "INSERT INTO categories (name, description, created_at, updated_at) VALUES ('自动化测试', '测试自动化', NOW(), NOW())",
            "INSERT INTO categories (name, description, created_at, updated_at) VALUES ('CEF3', '浏览器自动化', NOW(), NOW())",
            "INSERT INTO categories (name, description, created_at, updated_at) VALUES ('在线工具', '开发工具', NOW(), NOW())",
        ];
        
        foreach ($categories as $sql) {
            $pdo->exec($sql);
        }
        echo "✅ 基础分类创建完成\n";
    }
    
    // 检查是否已有学习资源
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM resources WHERE title LIKE '%菜鸟教程%'");
    $existingCount = $stmt->fetch()['count'];
    
    if ($existingCount > 0) {
        echo "⚠️ 检测到已有导入的资源，先清理重复数据...\n";
        $pdo->exec("DELETE FROM resources WHERE title LIKE '%菜鸟教程%' OR title LIKE '%廖雪峰%' OR title LIKE '%尚硅谷%'");
    }
    
    echo "🚀 开始导入学习资源...\n";
    
    $resources = [
        // Web基础资源
        [
            'title' => '菜鸟教程 - HTML完整教程',
            'description' => 'HTML从入门到精通，包含所有标签详解、实例演示，适合零基础学习者系统学习HTML结构与语义',
            'type' => 'document',
            'category_id' => 1,
            'difficulty' => 'beginner',
            'url' => 'https://www.runoob.com/html/html-tutorial.html',
            'content' => '涵盖HTML基础语法、常用标签、表单元素、语义化标签等核心知识点，提供在线编辑器实时练习',
            'tags' => '["HTML", "前端基础", "菜鸟教程", "零基础"]',
            'duration' => 480,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        [
            'title' => '菜鸟教程 - CSS完整教程',
            'description' => '系统学习CSS样式设计，掌握选择器、盒模型、布局技术，从基础样式到响应式设计',
            'type' => 'document',
            'category_id' => 1,
            'difficulty' => 'beginner',
            'url' => 'https://www.runoob.com/css/css-tutorial.html',
            'content' => '详细讲解CSS语法、选择器优先级、盒模型、Flexbox、Grid布局等现代CSS技术',
            'tags' => '["CSS", "样式设计", "布局", "响应式"]',
            'duration' => 600,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        [
            'title' => '廖雪峰官网 - JavaScript教程',
            'description' => '由资深程序员廖雪峰编写的JavaScript权威教程，深入浅出讲解JS核心概念',
            'type' => 'document',
            'category_id' => 2,
            'difficulty' => 'beginner',
            'url' => 'https://www.liaoxuefeng.com/wiki/1022910821149312',
            'content' => '涵盖JS基础语法、函数、对象、DOM操作、异步编程、ES6新特性等完整知识体系',
            'tags' => '["JavaScript", "前端编程", "廖雪峰", "权威教程"]',
            'duration' => 720,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        [
            'title' => '尚硅谷前端HTML+CSS+JS基础教程',
            'description' => 'B站知名培训机构尚硅谷出品的前端基础视频教程，适合系统学习',
            'type' => 'video',
            'category_id' => 1,
            'difficulty' => 'beginner',
            'url' => 'https://www.bilibili.com/video/BV1Kg411T7t9',
            'content' => '200+集完整视频教程，包含大量实战项目，从零基础到能够独立开发网页',
            'tags' => '["尚硅谷", "视频教程", "前端基础", "项目实战"]',
            'duration' => 1200,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        [
            'title' => '黑马程序员前端基础教程',
            'description' => 'B站黑马程序员前端开发基础课程，包含HTML5+CSS3+JavaScript核心技术',
            'type' => 'video',
            'category_id' => 1,
            'difficulty' => 'beginner',
            'url' => 'https://www.bilibili.com/video/BV14J4114768',
            'content' => '系统讲解现代前端开发技术，注重实战应用和企业级开发规范',
            'tags' => '["黑马程序员", "HTML5", "CSS3", "企业级开发"]',
            'duration' => 960,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        // C#资源
        [
            'title' => '微软.NET官方中文文档',
            'description' => '微软官方提供的.NET和C#学习文档，权威且全面的学习资源',
            'type' => 'document',
            'category_id' => 3,
            'difficulty' => 'beginner',
            'url' => 'https://docs.microsoft.com/zh-cn/dotnet/',
            'content' => '包含C#语法指南、.NET框架介绍、最佳实践、API参考等官方权威内容',
            'tags' => '["微软官方", ".NET", "C#基础", "权威文档"]',
            'duration' => 0,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        [
            'title' => '菜鸟教程 - C#教程',
            'description' => '系统的C#编程语言教程，从基础语法到高级特性的完整学习路径',
            'type' => 'document',
            'category_id' => 3,
            'difficulty' => 'beginner',
            'url' => 'https://www.runoob.com/csharp/csharp-tutorial.html',
            'content' => '涵盖C#数据类型、控制结构、面向对象、泛型、LINQ等核心概念，提供大量示例',
            'tags' => '["C#基础", "面向对象", "泛型", "LINQ"]',
            'duration' => 480,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        // Vue.js资源
        [
            'title' => 'Vue.js官方中文文档',
            'description' => 'Vue.js框架的官方中文学习指南，最权威的Vue学习资源',
            'type' => 'document',
            'category_id' => 4,
            'difficulty' => 'beginner',
            'url' => 'https://cn.vuejs.org/guide/',
            'content' => '完整的Vue3学习指南，包含基础概念、组件系统、状态管理、路由等核心功能',
            'tags' => '["Vue.js官方", "Vue3", "组件系统", "状态管理"]',
            'duration' => 0,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        [
            'title' => '技术胖Vue3实战教程',
            'description' => 'B站知名前端讲师技术胖的Vue3完整实战课程',
            'type' => 'video',
            'category_id' => 4,
            'difficulty' => 'beginner',
            'url' => 'https://www.bilibili.com/video/BV1dS4y1K7sH',
            'content' => '从Vue3基础到项目实战，包含Composition API、Pinia、Vue Router等现代开发技术',
            'tags' => '["技术胖", "Vue3实战", "Composition API", "项目开发"]',
            'duration' => 720,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        // 自动化测试资源
        [
            'title' => 'Selenium自动化测试教程',
            'description' => '全面的Selenium WebDriver中文教程，适合自动化测试入门',
            'type' => 'document',
            'category_id' => 5,
            'difficulty' => 'beginner',
            'url' => 'https://selenium-python-zh.readthedocs.io/en/latest/',
            'content' => '详细介绍Selenium的安装配置、元素定位、页面操作、等待机制等核心功能',
            'tags' => '["Selenium", "自动化测试", "WebDriver", "元素定位"]',
            'duration' => 360,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        [
            'title' => 'CefSharp官方文档中文指南',
            'description' => 'CefSharp框架的使用指南和API文档，C#开发者必备',
            'type' => 'document',
            'category_id' => 6,
            'difficulty' => 'intermediate',
            'url' => 'https://github.com/cefsharp/CefSharp/wiki',
            'content' => 'CefSharp集成指南、事件处理、JavaScript交互、性能优化等关键技术',
            'tags' => '["CefSharp", "CEF3", "JavaScript交互", "C#集成"]',
            'duration' => 0,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        // 在线工具
        [
            'title' => 'CodePen在线编程',
            'description' => '在线前端代码编辑器，支持HTML/CSS/JavaScript实时预览和分享',
            'type' => 'tool',
            'category_id' => 7,
            'difficulty' => 'beginner',
            'url' => 'https://codepen.io/',
            'content' => '提供丰富的前端代码示例和模板，支持实时编辑和效果预览，是学习前端的优秀平台',
            'tags' => '["在线编程", "代码分享", "前端工具", "实时预览"]',
            'duration' => 0,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        [
            'title' => 'CSDN技术社区',
            'description' => '国内最大的程序员技术社区，包含各种编程语言和技术的学习资源',
            'type' => 'document',
            'category_id' => 7,
            'difficulty' => 'beginner',
            'url' => 'https://www.csdn.net/',
            'content' => '提供技术博客、问答社区、在线课程等丰富的学习资源和交流平台',
            'tags' => '["CSDN", "技术社区", "程序员", "学习交流"]',
            'duration' => 0,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        [
            'title' => 'GitHub中文项目精选',
            'description' => 'GitHub上优秀的中文开源项目集合，包含各种技术栈的实战项目',
            'type' => 'document',
            'category_id' => 7,
            'difficulty' => 'intermediate',
            'url' => 'https://github.com/topics/chinese',
            'content' => '精选的中文开源项目，适合学习实际项目的代码结构和开发规范',
            'tags' => '["GitHub", "开源项目", "中文项目", "代码学习"]',
            'duration' => 0,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
    ];
    
    $sql = "INSERT INTO resources (title, description, type, category_id, difficulty, url, content, tags, duration, is_featured, is_published, is_active, creator_id, created_at, updated_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())";
    
    $stmt = $pdo->prepare($sql);
    
    $success_count = 0;
    $error_count = 0;
    
    foreach ($resources as $resource) {
        try {
            $stmt->execute([
                $resource['title'],
                $resource['description'],
                $resource['type'],
                $resource['category_id'],
                $resource['difficulty'],
                $resource['url'],
                $resource['content'],
                $resource['tags'],
                $resource['duration'],
                $resource['is_featured'],
                $resource['is_published'],
                $resource['is_active'],
                $resource['creator_id']
            ]);
            echo "✅ 成功导入: {$resource['title']}\n";
            $success_count++;
        } catch (Exception $e) {
            echo "❌ 导入失败: {$resource['title']} - {$e->getMessage()}\n";
            $error_count++;
        }
    }
    
    echo "\n================================================\n";
    echo "📊 导入统计结果\n";
    echo "================================================\n";
    echo "✅ 成功导入: {$success_count} 个资源\n";
    echo "❌ 导入失败: {$error_count} 个资源\n";
    echo "📚 总计资源: " . ($success_count + $error_count) . " 个\n\n";
    
    // 按分类统计
    $stmt = $pdo->query("
        SELECT c.name, COUNT(r.id) as count 
        FROM categories c 
        LEFT JOIN resources r ON c.id = r.category_id 
        GROUP BY c.id, c.name 
        ORDER BY c.id
    ");
    
    echo "📂 分类统计:\n";
    while ($row = $stmt->fetch()) {
        echo "  - {$row['name']}: {$row['count']} 个资源\n";
    }
    
    echo "\n🎯 现在可以访问:\n";
    echo "- 前台学习资源页面: http://localhost:5173/resources\n";
    echo "- 后台资源管理页面: http://localhost:5173/admin\n\n";
    
    echo "🎉 学习资源导入完成！\n";
    
} catch (Exception $e) {
    echo "❌ 导入过程出现错误: " . $e->getMessage() . "\n";
    echo "请检查数据库连接和表结构是否正确。\n";
} 
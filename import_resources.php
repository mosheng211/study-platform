<?php

require_once __DIR__ . '/vendor/autoload.php';

use App\Models\Resource;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

// 启动Laravel应用
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->bootstrap();

echo "================================================\n";
echo "📚 批量导入学习资源 - 54天编程学习计划\n";
echo "================================================\n\n";

try {
    // 检查数据库连接
    DB::connection()->getPdo();
    echo "✅ 数据库连接成功\n\n";
    
    // 检查分类数据
    $categories = Category::all();
    echo "📂 当前分类数量: " . $categories->count() . "\n";
    foreach ($categories as $category) {
        echo "  - {$category->name} (ID: {$category->id})\n";
    }
    echo "\n";
    
    // 开始导入资源
    echo "🚀 开始导入学习资源...\n\n";
    
    $resources = [
        // 第一阶段：Web技术基础
        [
            'title' => '菜鸟教程 - HTML完整教程',
            'description' => 'HTML从入门到精通，包含所有标签详解、实例演示，适合零基础学习者系统学习HTML结构与语义',
            'type' => 'document',
            'category_id' => 1,
            'difficulty' => 'beginner',
            'url' => 'https://www.runoob.com/html/html-tutorial.html',
            'content' => '涵盖HTML基础语法、常用标签、表单元素、语义化标签等核心知识点，提供在线编辑器实时练习',
            'tags' => json_encode(['HTML', '前端基础', '菜鸟教程', '零基础']),
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
            'tags' => json_encode(['CSS', '样式设计', '布局', '响应式']),
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
            'category_id' => 1,
            'difficulty' => 'beginner',
            'url' => 'https://www.liaoxuefeng.com/wiki/1022910821149312',
            'content' => '涵盖JS基础语法、函数、对象、DOM操作、异步编程、ES6新特性等完整知识体系',
            'tags' => json_encode(['JavaScript', '前端编程', '廖雪峰', '权威教程']),
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
            'tags' => json_encode(['尚硅谷', '视频教程', '前端基础', '项目实战']),
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
            'tags' => json_encode(['黑马程序员', 'HTML5', 'CSS3', '企业级开发']),
            'duration' => 960,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        [
            'title' => 'CSS世界 - 张鑫旭博客',
            'description' => '国内CSS专家张鑫旭的技术博客，深度解析CSS原理和高级技巧',
            'type' => 'document',
            'category_id' => 1,
            'difficulty' => 'intermediate',
            'url' => 'https://www.zhangxinxu.com/',
            'content' => '包含大量CSS实战案例、浏览器兼容性解决方案、CSS新特性应用等高质量文章',
            'tags' => json_encode(['张鑫旭', 'CSS进阶', '浏览器兼容', '实战案例']),
            'duration' => 0,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        [
            'title' => 'ES6入门教程 - 阮一峰',
            'description' => '前端大神阮一峰编写的ES6权威入门教程，全面讲解现代JavaScript特性',
            'type' => 'document',
            'category_id' => 1,
            'difficulty' => 'intermediate',
            'url' => 'https://es6.ruanyifeng.com/',
            'content' => '详细介绍ES6/ES2015+新语法、模块化、Promise、async/await等现代JS开发必备知识',
            'tags' => json_encode(['阮一峰', 'ES6', '现代JavaScript', '异步编程']),
            'duration' => 480,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        [
            'title' => 'JavaScript高级程序设计讲解',
            'description' => '基于经典红宝书的JavaScript深度讲解视频，适合有基础的开发者提升',
            'type' => 'video',
            'category_id' => 1,
            'difficulty' => 'advanced',
            'url' => 'https://www.bilibili.com/video/BV1YW411T7GX',
            'content' => '深入JavaScript核心机制，包括作用域、闭包、原型链、异步编程等高级概念',
            'tags' => json_encode(['JavaScript进阶', '红宝书', '核心机制', '高级概念']),
            'duration' => 800,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        
        // 第二阶段：C#编程基础
        [
            'title' => '微软.NET官方中文文档',
            'description' => '微软官方提供的.NET和C#学习文档，权威且全面的学习资源',
            'type' => 'document',
            'category_id' => 4,
            'difficulty' => 'beginner',
            'url' => 'https://docs.microsoft.com/zh-cn/dotnet/',
            'content' => '包含C#语法指南、.NET框架介绍、最佳实践、API参考等官方权威内容',
            'tags' => json_encode(['微软官方', '.NET', 'C#基础', '权威文档']),
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
            'category_id' => 4,
            'difficulty' => 'beginner',
            'url' => 'https://www.runoob.com/csharp/csharp-tutorial.html',
            'content' => '涵盖C#数据类型、控制结构、面向对象、泛型、LINQ等核心概念，提供大量示例',
            'tags' => json_encode(['C#基础', '面向对象', '泛型', 'LINQ']),
            'duration' => 480,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        [
            'title' => '刘铁猛C#语言入门详解',
            'description' => 'B站知名C#讲师刘铁猛的语言入门课程，讲解透彻易懂',
            'type' => 'video',
            'category_id' => 4,
            'difficulty' => 'beginner',
            'url' => 'https://www.bilibili.com/video/BV13b411b7Ht',
            'content' => '深入浅出讲解C#语言特性，注重编程思维培养和最佳实践',
            'tags' => json_encode(['刘铁猛', 'C#入门', '编程思维', '最佳实践']),
            'duration' => 720,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        [
            'title' => '黑马程序员C#全套教程',
            'description' => '黑马程序员出品的C#从入门到精通完整视频教程',
            'type' => 'video',
            'category_id' => 4,
            'difficulty' => 'beginner',
            'url' => 'https://www.bilibili.com/video/BV1FJ411W7e5',
            'content' => '包含C#基础语法、面向对象、WinForms、数据库操作等企业开发技能',
            'tags' => json_encode(['黑马程序员', 'C#全套', '企业开发', '项目实战']),
            'duration' => 960,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        [
            'title' => '博客园 - .NET技术社区',
            'description' => '国内最大的.NET技术社区，汇集大量C#开发经验和最佳实践',
            'type' => 'document',
            'category_id' => 4,
            'difficulty' => 'intermediate',
            'url' => 'https://www.cnblogs.com/cate/dotnet/',
            'content' => '包含C#进阶技术文章、框架使用经验、性能优化、设计模式等深度内容',
            'tags' => json_encode(['博客园', '.NET社区', '进阶技术', '最佳实践']),
            'duration' => 0,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        [
            'title' => 'C#异步编程深入理解',
            'description' => '深度讲解C#异步编程模型，包含async/await、Task、并发等核心概念',
            'type' => 'video',
            'category_id' => 4,
            'difficulty' => 'advanced',
            'url' => 'https://www.bilibili.com/video/BV1Jt411w7rS',
            'content' => '系统学习.NET异步编程，掌握现代C#应用开发的关键技术',
            'tags' => json_encode(['异步编程', 'async/await', 'Task', '并发编程']),
            'duration' => 480,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        
        // 第三阶段：Vue.js框架
        [
            'title' => 'Vue.js官方中文文档',
            'description' => 'Vue.js框架的官方中文学习指南，最权威的Vue学习资源',
            'type' => 'document',
            'category_id' => 5,
            'difficulty' => 'beginner',
            'url' => 'https://cn.vuejs.org/guide/',
            'content' => '完整的Vue3学习指南，包含基础概念、组件系统、状态管理、路由等核心功能',
            'tags' => json_encode(['Vue.js官方', 'Vue3', '组件系统', '状态管理']),
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
            'category_id' => 5,
            'difficulty' => 'beginner',
            'url' => 'https://www.bilibili.com/video/BV1dS4y1K7sH',
            'content' => '从Vue3基础到项目实战，包含Composition API、Pinia、Vue Router等现代开发技术',
            'tags' => json_encode(['技术胖', 'Vue3实战', 'Composition API', '项目开发']),
            'duration' => 720,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        [
            'title' => '尚硅谷Vue3全家桶教程',
            'description' => '尚硅谷出品的Vue3生态系统完整教程，包含企业级开发实战',
            'type' => 'video',
            'category_id' => 5,
            'difficulty' => 'intermediate',
            'url' => 'https://www.bilibili.com/video/BV1Za4y1r7KE',
            'content' => '深入学习Vue3+TypeScript+Vite+Pinia全技术栈，适合企业级项目开发',
            'tags' => json_encode(['尚硅谷', 'Vue3全家桶', 'TypeScript', '企业级开发']),
            'duration' => 960,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        [
            'title' => '掘金Vue专栏文章集',
            'description' => '掘金社区Vue技术专栏，汇集大量实战经验和最佳实践',
            'type' => 'document',
            'category_id' => 5,
            'difficulty' => 'intermediate',
            'url' => 'https://juejin.cn/tag/Vue.js',
            'content' => '包含Vue组件设计、性能优化、架构设计、生态工具等进阶内容',
            'tags' => json_encode(['掘金', 'Vue专栏', '组件设计', '性能优化']),
            'duration' => 0,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        [
            'title' => '慕课网Vue实战课程',
            'description' => '慕课网提供的Vue项目实战课程，注重企业级应用开发',
            'type' => 'video',
            'category_id' => 5,
            'difficulty' => 'intermediate',
            'url' => 'https://www.imooc.com/learn/1091',
            'content' => '通过实际项目学习Vue开发流程，掌握组件化开发和工程化实践',
            'tags' => json_encode(['慕课网', 'Vue实战', '企业应用', '工程化开发']),
            'duration' => 600,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        
        // 第四阶段：浏览器自动化&CEF3
        [
            'title' => 'Selenium自动化测试教程',
            'description' => '全面的Selenium WebDriver中文教程，适合自动化测试入门',
            'type' => 'document',
            'category_id' => 6,
            'difficulty' => 'beginner',
            'url' => 'https://selenium-python-zh.readthedocs.io/en/latest/',
            'content' => '详细介绍Selenium的安装配置、元素定位、页面操作、等待机制等核心功能',
            'tags' => json_encode(['Selenium', '自动化测试', 'WebDriver', '元素定位']),
            'duration' => 360,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        [
            'title' => '虫师Selenium自动化实战',
            'description' => 'B站知名自动化测试专家虫师的Selenium实战课程',
            'type' => 'video',
            'category_id' => 6,
            'difficulty' => 'beginner',
            'url' => 'https://www.bilibili.com/video/BV1dt411k7s6',
            'content' => '从基础操作到复杂场景处理，全面掌握Web自动化测试技术',
            'tags' => json_encode(['虫师', '自动化实战', '测试框架', '实际项目']),
            'duration' => 720,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        [
            'title' => 'CefSharp官方文档中文指南',
            'description' => 'CefSharp框架的使用指南和API文档，C#开发者必备',
            'type' => 'document',
            'category_id' => 7,
            'difficulty' => 'intermediate',
            'url' => 'https://github.com/cefsharp/CefSharp/wiki',
            'content' => 'CefSharp集成指南、事件处理、JavaScript交互、性能优化等关键技术',
            'tags' => json_encode(['CefSharp', 'CEF3', 'JavaScript交互', 'C#集成']),
            'duration' => 0,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        [
            'title' => 'C#浏览器自动化开发实战',
            'description' => 'B站专门讲解C#+CEF3进行浏览器自动化开发的实战教程',
            'type' => 'video',
            'category_id' => 7,
            'difficulty' => 'advanced',
            'url' => 'https://www.bilibili.com/video/BV1zJ4m1jEeY',
            'content' => '使用C#和CEF3构建浏览器自动化应用，包含登录、数据采集、界面操作等实战案例',
            'tags' => json_encode(['C#自动化', 'CEF3实战', '浏览器控制', '数据采集']),
            'duration' => 480,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        [
            'title' => '开源中国 - CEF相关项目',
            'description' => '开源中国平台上的CEF相关开源项目和技术文章集合',
            'type' => 'document',
            'category_id' => 7,
            'difficulty' => 'intermediate',
            'url' => 'https://www.oschina.net/search?scope=project&q=CEF',
            'content' => '实际的CEF应用项目源码、技术分享、解决方案等实用资源',
            'tags' => json_encode(['开源中国', 'CEF项目', '开源代码', '技术分享']),
            'duration' => 0,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        [
            'title' => '博客园自动化测试专栏',
            'description' => '博客园自动化测试技术专栏，包含大量实战经验和案例分析',
            'type' => 'document',
            'category_id' => 6,
            'difficulty' => 'intermediate',
            'url' => 'https://www.cnblogs.com/tag/自动化测试/',
            'content' => '涵盖Web自动化、移动端自动化、接口自动化等全方位测试技术',
            'tags' => json_encode(['博客园', '自动化测试', '实战经验', '案例分析']),
            'duration' => 0,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        
        // 综合实战和工具资源
        [
            'title' => 'CodePen在线编程',
            'description' => '在线前端代码编辑器，支持HTML/CSS/JavaScript实时预览和分享',
            'type' => 'tool',
            'category_id' => 8,
            'difficulty' => 'beginner',
            'url' => 'https://codepen.io/',
            'content' => '提供丰富的前端代码示例和模板，支持实时编辑和效果预览，是学习前端的优秀平台',
            'tags' => json_encode(['在线编程', '代码分享', '前端工具', '实时预览']),
            'duration' => 0,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        [
            'title' => '菜鸟教程在线编辑器',
            'description' => '菜鸟教程提供的多语言在线编程环境，支持即时运行和调试',
            'type' => 'tool',
            'category_id' => 8,
            'difficulty' => 'beginner',
            'url' => 'https://c.runoob.com/',
            'content' => '支持C#、JavaScript、Python等多种编程语言的在线执行环境',
            'tags' => json_encode(['菜鸟教程', '在线编辑器', '多语言支持', '即时运行']),
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
            'category_id' => 8,
            'difficulty' => 'beginner',
            'url' => 'https://www.csdn.net/',
            'content' => '提供技术博客、问答社区、在线课程等丰富的学习资源和交流平台',
            'tags' => json_encode(['CSDN', '技术社区', '程序员', '学习交流']),
            'duration' => 0,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        [
            'title' => 'Stack Overflow中文版',
            'description' => 'SegmentFault思否，中国版的程序员问答社区',
            'type' => 'document',
            'category_id' => 8,
            'difficulty' => 'beginner',
            'url' => 'https://segmentfault.com/',
            'content' => '程序员技术问答、技术分享、求职招聘的综合性技术社区平台',
            'tags' => json_encode(['SegmentFault', '技术问答', '程序员社区', '技术分享']),
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
            'category_id' => 8,
            'difficulty' => 'intermediate',
            'url' => 'https://github.com/topics/chinese',
            'content' => '精选的中文开源项目，适合学习实际项目的代码结构和开发规范',
            'tags' => json_encode(['GitHub', '开源项目', '中文项目', '代码学习']),
            'duration' => 0,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
        [
            'title' => 'Gitee码云平台',
            'description' => '国内最大的代码托管平台，汇集大量优秀的开源项目和学习资源',
            'type' => 'document',
            'category_id' => 8,
            'difficulty' => 'beginner',
            'url' => 'https://gitee.com/',
            'content' => '提供Git代码托管、项目管理、协作开发等服务，是学习开源项目的重要平台',
            'tags' => json_encode(['Gitee', '代码托管', '开源项目', '协作开发']),
            'duration' => 0,
            'is_featured' => 1,
            'is_published' => 1,
            'is_active' => 1,
            'creator_id' => 1,
        ],
    ];

    $success_count = 0;
    $error_count = 0;
    
    foreach ($resources as $resourceData) {
        try {
            $resource = Resource::create($resourceData);
            echo "✅ 成功导入: {$resource->title}\n";
            $success_count++;
        } catch (Exception $e) {
            echo "❌ 导入失败: {$resourceData['title']} - {$e->getMessage()}\n";
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
    echo "📂 分类统计:\n";
    $categoryStats = DB::table('resources')
        ->join('categories', 'resources.category_id', '=', 'categories.id')
        ->select('categories.name', DB::raw('count(*) as count'))
        ->groupBy('categories.id', 'categories.name')
        ->get();
        
    foreach ($categoryStats as $stat) {
        echo "  - {$stat->name}: {$stat->count} 个资源\n";
    }
    
    echo "\n🎯 现在可以访问:\n";
    echo "- 前台学习资源页面: http://localhost:5173/resources\n";
    echo "- 后台资源管理页面: http://localhost:5173/admin\n";
    echo "- 资源导入验证页面: " . __DIR__ . "/test_resources_import.html\n\n";
    
    echo "🎉 学习资源导入完成！\n";
    
} catch (Exception $e) {
    echo "❌ 导入过程出现错误: " . $e->getMessage() . "\n";
    echo "请检查数据库连接和表结构是否正确。\n";
} 
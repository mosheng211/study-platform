<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Resource;
use App\Models\Category;
use Illuminate\Support\Facades\DB;

class ImportResources extends Command
{
    protected $signature = 'import:resources';
    protected $description = '导入学习资源 - 54天编程学习计划';

    public function handle()
    {
        $this->info('=== 开始导入学习资源 ===');
        
        try {
            // 检查并创建基础分类
            $categoryCount = Category::count();
            $this->info("当前分类数量: {$categoryCount}");
            
            if ($categoryCount < 7) {
                $this->info('创建基础分类...');
                Category::truncate();
                
                $categories = [
                    ['id' => 1, 'name' => 'HTML/CSS', 'description' => 'HTML和CSS基础'],
                    ['id' => 2, 'name' => 'JavaScript', 'description' => 'JavaScript编程'],
                    ['id' => 3, 'name' => 'C#', 'description' => 'C#编程语言'],
                    ['id' => 4, 'name' => 'Vue.js', 'description' => 'Vue框架'],
                    ['id' => 5, 'name' => '自动化测试', 'description' => '测试自动化'],
                    ['id' => 6, 'name' => 'CEF3', 'description' => '浏览器自动化'],
                    ['id' => 7, 'name' => '在线工具', 'description' => '开发工具'],
                ];
                
                foreach ($categories as $category) {
                    Category::create($category);
                }
                $this->info('✅ 7个基础分类创建完成');
            }
            
            // 清理现有的导入资源
            Resource::where('title', 'like', '%菜鸟教程%')
                ->orWhere('title', 'like', '%廖雪峰%')
                ->orWhere('title', 'like', '%尚硅谷%')
                ->orWhere('title', 'like', '%黑马程序员%')
                ->delete();
            
            $this->info('开始导入学习资源...');
            
            // 资源数据
            $resources = [
                // Web基础 - HTML/CSS
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
                    'is_featured' => true,
                    'is_published' => true,
                    'is_active' => true,
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
                    'is_featured' => true,
                    'is_published' => true,
                    'is_active' => true,
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
                    'is_featured' => true,
                    'is_published' => true,
                    'is_active' => true,
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
                    'is_featured' => true,
                    'is_published' => true,
                    'is_active' => true,
                    'creator_id' => 1,
                ],
                // JavaScript
                [
                    'title' => '廖雪峰官网 - JavaScript教程',
                    'description' => '由资深程序员廖雪峰编写的JavaScript权威教程，深入浅出讲解JS核心概念',
                    'type' => 'document',
                    'category_id' => 2,
                    'difficulty' => 'beginner',
                    'url' => 'https://www.liaoxuefeng.com/wiki/1022910821149312',
                    'content' => '涵盖JS基础语法、函数、对象、DOM操作、异步编程、ES6新特性等完整知识体系',
                    'tags' => json_encode(['JavaScript', '前端编程', '廖雪峰', '权威教程']),
                    'duration' => 720,
                    'is_featured' => true,
                    'is_published' => true,
                    'is_active' => true,
                    'creator_id' => 1,
                ],
                [
                    'title' => 'ES6入门教程 - 阮一峰',
                    'description' => '前端大神阮一峰编写的ES6权威入门教程，全面讲解现代JavaScript特性',
                    'type' => 'document',
                    'category_id' => 2,
                    'difficulty' => 'intermediate',
                    'url' => 'https://es6.ruanyifeng.com/',
                    'content' => '详细介绍ES6/ES2015+新语法、模块化、Promise、async/await等现代JS开发必备知识',
                    'tags' => json_encode(['阮一峰', 'ES6', '现代JavaScript', '异步编程']),
                    'duration' => 480,
                    'is_featured' => true,
                    'is_published' => true,
                    'is_active' => true,
                    'creator_id' => 1,
                ],
                [
                    'title' => 'JavaScript高级程序设计讲解',
                    'description' => '基于经典红宝书的JavaScript深度讲解视频，适合有基础的开发者提升',
                    'type' => 'video',
                    'category_id' => 2,
                    'difficulty' => 'advanced',
                    'url' => 'https://www.bilibili.com/video/BV1YW411T7GX',
                    'content' => '深入JavaScript核心机制，包括作用域、闭包、原型链、异步编程等高级概念',
                    'tags' => json_encode(['JavaScript进阶', '红宝书', '核心机制', '高级概念']),
                    'duration' => 800,
                    'is_featured' => true,
                    'is_published' => true,
                    'is_active' => true,
                    'creator_id' => 1,
                ],
                // C#编程
                [
                    'title' => '微软.NET官方中文文档',
                    'description' => '微软官方提供的.NET和C#学习文档，权威且全面的学习资源',
                    'type' => 'document',
                    'category_id' => 3,
                    'difficulty' => 'beginner',
                    'url' => 'https://docs.microsoft.com/zh-cn/dotnet/',
                    'content' => '包含C#语法指南、.NET框架介绍、最佳实践、API参考等官方权威内容',
                    'tags' => json_encode(['微软官方', '.NET', 'C#基础', '权威文档']),
                    'duration' => 0,
                    'is_featured' => true,
                    'is_published' => true,
                    'is_active' => true,
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
                    'tags' => json_encode(['C#基础', '面向对象', '泛型', 'LINQ']),
                    'duration' => 480,
                    'is_featured' => true,
                    'is_published' => true,
                    'is_active' => true,
                    'creator_id' => 1,
                ],
                [
                    'title' => '刘铁猛C#语言入门详解',
                    'description' => 'B站知名C#讲师刘铁猛的语言入门课程，讲解透彻易懂',
                    'type' => 'video',
                    'category_id' => 3,
                    'difficulty' => 'beginner',
                    'url' => 'https://www.bilibili.com/video/BV13b411b7Ht',
                    'content' => '深入浅出讲解C#语言特性，注重编程思维培养和最佳实践',
                    'tags' => json_encode(['刘铁猛', 'C#入门', '编程思维', '最佳实践']),
                    'duration' => 720,
                    'is_featured' => true,
                    'is_published' => true,
                    'is_active' => true,
                    'creator_id' => 1,
                ],
                [
                    'title' => '黑马程序员C#全套教程',
                    'description' => '黑马程序员出品的C#从入门到精通完整视频教程',
                    'type' => 'video',
                    'category_id' => 3,
                    'difficulty' => 'beginner',
                    'url' => 'https://www.bilibili.com/video/BV1FJ411W7e5',
                    'content' => '包含C#基础语法、面向对象、WinForms、数据库操作等企业开发技能',
                    'tags' => json_encode(['黑马程序员', 'C#全套', '企业开发', '项目实战']),
                    'duration' => 960,
                    'is_featured' => true,
                    'is_published' => true,
                    'is_active' => true,
                    'creator_id' => 1,
                ],
                // Vue.js
                [
                    'title' => 'Vue.js官方中文文档',
                    'description' => 'Vue.js框架的官方中文学习指南，最权威的Vue学习资源',
                    'type' => 'document',
                    'category_id' => 4,
                    'difficulty' => 'beginner',
                    'url' => 'https://cn.vuejs.org/guide/',
                    'content' => '完整的Vue3学习指南，包含基础概念、组件系统、状态管理、路由等核心功能',
                    'tags' => json_encode(['Vue.js官方', 'Vue3', '组件系统', '状态管理']),
                    'duration' => 0,
                    'is_featured' => true,
                    'is_published' => true,
                    'is_active' => true,
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
                    'tags' => json_encode(['技术胖', 'Vue3实战', 'Composition API', '项目开发']),
                    'duration' => 720,
                    'is_featured' => true,
                    'is_published' => true,
                    'is_active' => true,
                    'creator_id' => 1,
                ],
                [
                    'title' => '尚硅谷Vue3全家桶教程',
                    'description' => '尚硅谷出品的Vue3生态系统完整教程，包含企业级开发实战',
                    'type' => 'video',
                    'category_id' => 4,
                    'difficulty' => 'intermediate',
                    'url' => 'https://www.bilibili.com/video/BV1Za4y1r7KE',
                    'content' => '深入学习Vue3+TypeScript+Vite+Pinia全技术栈，适合企业级项目开发',
                    'tags' => json_encode(['尚硅谷', 'Vue3全家桶', 'TypeScript', '企业级开发']),
                    'duration' => 960,
                    'is_featured' => true,
                    'is_published' => true,
                    'is_active' => true,
                    'creator_id' => 1,
                ],
                // 自动化测试
                [
                    'title' => 'Selenium自动化测试教程',
                    'description' => '全面的Selenium WebDriver中文教程，适合自动化测试入门',
                    'type' => 'document',
                    'category_id' => 5,
                    'difficulty' => 'beginner',
                    'url' => 'https://selenium-python-zh.readthedocs.io/en/latest/',
                    'content' => '详细介绍Selenium的安装配置、元素定位、页面操作、等待机制等核心功能',
                    'tags' => json_encode(['Selenium', '自动化测试', 'WebDriver', '元素定位']),
                    'duration' => 360,
                    'is_featured' => true,
                    'is_published' => true,
                    'is_active' => true,
                    'creator_id' => 1,
                ],
                [
                    'title' => '虫师Selenium自动化实战',
                    'description' => 'B站知名自动化测试专家虫师的Selenium实战课程',
                    'type' => 'video',
                    'category_id' => 5,
                    'difficulty' => 'beginner',
                    'url' => 'https://www.bilibili.com/video/BV1dt411k7s6',
                    'content' => '从基础操作到复杂场景处理，全面掌握Web自动化测试技术',
                    'tags' => json_encode(['虫师', '自动化实战', '测试框架', '实际项目']),
                    'duration' => 720,
                    'is_featured' => true,
                    'is_published' => true,
                    'is_active' => true,
                    'creator_id' => 1,
                ],
                // CEF3
                [
                    'title' => 'CefSharp官方文档中文指南',
                    'description' => 'CefSharp框架的使用指南和API文档，C#开发者必备',
                    'type' => 'document',
                    'category_id' => 6,
                    'difficulty' => 'intermediate',
                    'url' => 'https://github.com/cefsharp/CefSharp/wiki',
                    'content' => 'CefSharp集成指南、事件处理、JavaScript交互、性能优化等关键技术',
                    'tags' => json_encode(['CefSharp', 'CEF3', 'JavaScript交互', 'C#集成']),
                    'duration' => 0,
                    'is_featured' => true,
                    'is_published' => true,
                    'is_active' => true,
                    'creator_id' => 1,
                ],
                [
                    'title' => 'C#浏览器自动化开发实战',
                    'description' => 'B站专门讲解C#+CEF3进行浏览器自动化开发的实战教程',
                    'type' => 'video',
                    'category_id' => 6,
                    'difficulty' => 'advanced',
                    'url' => 'https://www.bilibili.com/video/BV1zJ4m1jEeY',
                    'content' => '使用C#和CEF3构建浏览器自动化应用，包含登录、数据采集、界面操作等实战案例',
                    'tags' => json_encode(['C#自动化', 'CEF3实战', '浏览器控制', '数据采集']),
                    'duration' => 480,
                    'is_featured' => true,
                    'is_published' => true,
                    'is_active' => true,
                    'creator_id' => 1,
                ],
                // 在线工具和社区
                [
                    'title' => 'CodePen在线编程',
                    'description' => '在线前端代码编辑器，支持HTML/CSS/JavaScript实时预览和分享',
                    'type' => 'tool',
                    'category_id' => 7,
                    'difficulty' => 'beginner',
                    'url' => 'https://codepen.io/',
                    'content' => '提供丰富的前端代码示例和模板，支持实时编辑和效果预览，是学习前端的优秀平台',
                    'tags' => json_encode(['在线编程', '代码分享', '前端工具', '实时预览']),
                    'duration' => 0,
                    'is_featured' => true,
                    'is_published' => true,
                    'is_active' => true,
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
                    'tags' => json_encode(['CSDN', '技术社区', '程序员', '学习交流']),
                    'duration' => 0,
                    'is_featured' => true,
                    'is_published' => true,
                    'is_active' => true,
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
                    'tags' => json_encode(['GitHub', '开源项目', '中文项目', '代码学习']),
                    'duration' => 0,
                    'is_featured' => true,
                    'is_published' => true,
                    'is_active' => true,
                    'creator_id' => 1,
                ]
            ];
            
            $success_count = 0;
            $error_count = 0;
            
            foreach ($resources as $resourceData) {
                try {
                    Resource::create($resourceData);
                    $this->info("✅ 导入成功: {$resourceData['title']}");
                    $success_count++;
                } catch (\Exception $e) {
                    $this->error("❌ 导入失败: {$resourceData['title']} - {$e->getMessage()}");
                    $error_count++;
                }
            }
            
            $this->info("\n=== 导入完成 ===");
            $this->info("✅ 成功导入: {$success_count} 个资源");
            $this->error("❌ 导入失败: {$error_count} 个资源");
            $this->info("📚 总计: " . ($success_count + $error_count) . " 个资源");
            
            // 统计结果
            $categoryStats = DB::table('resources')
                ->join('categories', 'resources.category_id', '=', 'categories.id')
                ->select('categories.name', DB::raw('count(*) as count'))
                ->groupBy('categories.id', 'categories.name')
                ->get();
            
            $this->info("\n📊 分类统计:");
            foreach ($categoryStats as $stat) {
                $this->info("  - {$stat->name}: {$stat->count} 个资源");
            }
            
            $this->info("\n🎯 可以访问:");
            $this->info("- 前台学习资源: http://localhost:5173/resources");
            $this->info("- 后台资源管理: http://localhost:5173/admin");
            $this->info("- 资源API: http://localhost:8000/api/resources");
            
            $this->info("\n🎉 学习资源导入完成！");
            
        } catch (\Exception $e) {
            $this->error("❌ 错误: " . $e->getMessage());
            $this->error("请检查数据库连接和表结构。");
            return 1;
        }
        
        return 0;
    }
} 
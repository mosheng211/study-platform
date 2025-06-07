<?php
require_once 'vendor/autoload.php';

// 加载 Laravel 环境
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "开始检查并修复categories表...\n";

try {
    // 检查categories表是否存在
    if (!Schema::hasTable('categories')) {
        echo "categories表不存在，正在创建...\n";
        
        // 创建categories表
        Schema::create('categories', function ($table) {
            $table->id();
            $table->string('name')->comment('分类名称');
            $table->string('slug')->unique()->comment('分类别名');
            $table->text('description')->nullable()->comment('分类描述');
            $table->string('color', 7)->default('#007bff')->comment('分类颜色');
            $table->string('icon')->nullable()->comment('分类图标');
            $table->integer('sort_order')->default(0)->comment('排序');
            $table->boolean('is_active')->default(true)->comment('是否激活');
            $table->timestamps();
        });
        
        echo "categories表创建成功！\n";
        
        // 插入测试数据
        echo "正在插入测试数据...\n";
        
        $categories = [
            ['name' => 'HTML', 'slug' => 'html', 'description' => 'HTML基础语法与标签', 'color' => '#e34c26', 'icon' => 'mdi-language-html5', 'sort_order' => 1],
            ['name' => 'CSS', 'slug' => 'css', 'description' => 'CSS样式设计', 'color' => '#1572b6', 'icon' => 'mdi-language-css3', 'sort_order' => 2],
            ['name' => 'JavaScript', 'slug' => 'javascript', 'description' => 'JavaScript编程基础', 'color' => '#f7df1e', 'icon' => 'mdi-language-javascript', 'sort_order' => 3],
            ['name' => 'C#', 'slug' => 'csharp', 'description' => 'C#编程语言', 'color' => '#239120', 'icon' => 'mdi-language-csharp', 'sort_order' => 4],
            ['name' => 'Vue.js', 'slug' => 'vuejs', 'description' => 'Vue.js前端框架', 'color' => '#4fc08d', 'icon' => 'mdi-vuejs', 'sort_order' => 5],
            ['name' => 'CEF3', 'slug' => 'cef3', 'description' => 'CEF3桌面应用开发', 'color' => '#ff6b6b', 'icon' => 'mdi-desktop-classic', 'sort_order' => 6],
            ['name' => 'Selenium', 'slug' => 'selenium', 'description' => 'Selenium自动化测试', 'color' => '#43b02a', 'icon' => 'mdi-robot', 'sort_order' => 7],
            ['name' => '算法', 'slug' => 'algorithms', 'description' => '数据结构与算法', 'color' => '#6f42c1', 'icon' => 'mdi-brain', 'sort_order' => 8],
            ['name' => '数据库', 'slug' => 'database', 'description' => '数据库设计与管理', 'color' => '#fd7e14', 'icon' => 'mdi-database', 'sort_order' => 9],
            ['name' => '项目实战', 'slug' => 'projects', 'description' => '实际项目开发', 'color' => '#dc3545', 'icon' => 'mdi-folder-multiple', 'sort_order' => 10]
        ];
        
        foreach ($categories as $category) {
            DB::table('categories')->insert(array_merge($category, [
                'created_at' => now(),
                'updated_at' => now()
            ]));
        }
        
        echo "测试数据插入成功！\n";
        
    } else {
        echo "categories表已存在\n";
    }
    
    // 检查表结构
    $columns = Schema::getColumnListing('categories');
    echo "categories表字段: " . implode(', ', $columns) . "\n";
    
    // 检查数据
    $count = DB::table('categories')->count();
    echo "categories表记录数: {$count}\n";
    
    if ($count > 0) {
        echo "前5条记录:\n";
        $records = DB::table('categories')->limit(5)->get();
        foreach ($records as $record) {
            echo "- {$record->name} ({$record->slug})\n";
        }
    }
    
    echo "\n检查完成！categories表修复成功！\n";
    
} catch (Exception $e) {
    echo "错误: " . $e->getMessage() . "\n";
    echo "详细信息: " . $e->getTraceAsString() . "\n";
} 
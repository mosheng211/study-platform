<?php

require_once 'vendor/autoload.php';

use Illuminate\Database\Capsule\Manager as Capsule;

// 数据库配置
$capsule = new Capsule;
$capsule->addConnection([
    'driver' => 'mysql',
    'host' => 'localhost',
    'database' => 'study_platform',
    'username' => 'root',
    'password' => '61263269',
    'charset' => 'utf8mb4',
    'collation' => 'utf8mb4_unicode_ci',
    'prefix' => '',
]);

$capsule->setAsGlobal();
$capsule->bootEloquent();

// 创建categories表
try {
    Capsule::schema()->create('categories', function ($table) {
        $table->id();
        $table->string('name')->comment('分类名称');
        $table->string('slug')->unique()->comment('分类标识符');
        $table->text('description')->nullable()->comment('分类描述');
        $table->string('color', 7)->default('#1890ff')->comment('分类颜色');
        $table->string('icon')->nullable()->comment('分类图标');
        $table->integer('sort_order')->default(0)->comment('排序顺序');
        $table->boolean('is_active')->default(true)->comment('是否激活');
        $table->timestamps();
    });
    echo "Categories table created successfully.\n";
} catch (Exception $e) {
    echo "Categories table might already exist: " . $e->getMessage() . "\n";
}

// 添加category_id字段到resources表
try {
    Capsule::schema()->table('resources', function ($table) {
        $table->unsignedBigInteger('category_id')->nullable()->after('id')->comment('分类ID');
        $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
    });
    echo "Category_id field added to resources table.\n";
} catch (Exception $e) {
    echo "Category_id field might already exist: " . $e->getMessage() . "\n";
}

// 插入初始分类数据
$categories = [
    [
        'name' => 'HTML',
        'slug' => 'html',
        'description' => 'HTML5语义化标签、网页结构基础',
        'color' => '#e34c26',
        'icon' => 'html5',
        'sort_order' => 1,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'CSS',
        'slug' => 'css',
        'description' => 'CSS3样式、布局、响应式设计',
        'color' => '#1572b6',
        'icon' => 'css3',
        'sort_order' => 2,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'JavaScript',
        'slug' => 'javascript',
        'description' => 'JavaScript基础语法、DOM操作、异步编程',
        'color' => '#f7df1e',
        'icon' => 'javascript',
        'sort_order' => 3,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'C#',
        'slug' => 'csharp',
        'description' => 'C#语法基础、面向对象编程、.NET框架',
        'color' => '#512bd4',
        'icon' => 'csharp',
        'sort_order' => 4,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'Vue.js',
        'slug' => 'vuejs',
        'description' => 'Vue3组合式API、组件化开发、路由状态管理',
        'color' => '#4fc08d',
        'icon' => 'vuejs',
        'sort_order' => 5,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'CEF3',
        'slug' => 'cef3',
        'description' => 'CEF3框架、浏览器自动化、CefSharp集成',
        'color' => '#2196f3',
        'icon' => 'browser',
        'sort_order' => 6,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => 'Selenium',
        'slug' => 'selenium',
        'description' => 'Selenium WebDriver、浏览器自动化测试',
        'color' => '#43b02a',
        'icon' => 'selenium',
        'sort_order' => 7,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => '算法与数据结构',
        'slug' => 'algorithm',
        'description' => '基础算法、数据结构、编程思维训练',
        'color' => '#ff6b6b',
        'icon' => 'algorithm',
        'sort_order' => 8,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => '数据库',
        'slug' => 'database',
        'description' => 'MySQL、SQL语句、数据库设计',
        'color' => '#336791',
        'icon' => 'database',
        'sort_order' => 9,
        'created_at' => now(),
        'updated_at' => now(),
    ],
    [
        'name' => '项目实战',
        'slug' => 'project',
        'description' => '综合项目、实战案例、完整开发流程',
        'color' => '#ffc107',
        'icon' => 'project',
        'sort_order' => 10,
        'created_at' => now(),
        'updated_at' => now(),
    ],
];

try {
    Capsule::table('categories')->insert($categories);
    echo "Categories data inserted successfully.\n";
} catch (Exception $e) {
    echo "Error inserting categories: " . $e->getMessage() . "\n";
}

function now() {
    return date('Y-m-d H:i:s');
}

echo "Category initialization completed!\n"; 
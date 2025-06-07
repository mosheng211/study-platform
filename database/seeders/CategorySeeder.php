<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'HTML',
                'slug' => 'html',
                'description' => 'HTML5语义化标签、网页结构基础',
                'color' => '#e34c26',
                'icon' => 'html5',
                'sort_order' => 1,
            ],
            [
                'name' => 'CSS',
                'slug' => 'css',
                'description' => 'CSS3样式、布局、响应式设计',
                'color' => '#1572b6',
                'icon' => 'css3',
                'sort_order' => 2,
            ],
            [
                'name' => 'JavaScript',
                'slug' => 'javascript',
                'description' => 'JavaScript基础语法、DOM操作、异步编程',
                'color' => '#f7df1e',
                'icon' => 'javascript',
                'sort_order' => 3,
            ],
            [
                'name' => 'C#',
                'slug' => 'csharp',
                'description' => 'C#语法基础、面向对象编程、.NET框架',
                'color' => '#512bd4',
                'icon' => 'csharp',
                'sort_order' => 4,
            ],
            [
                'name' => 'Vue.js',
                'slug' => 'vuejs',
                'description' => 'Vue3组合式API、组件化开发、路由状态管理',
                'color' => '#4fc08d',
                'icon' => 'vuejs',
                'sort_order' => 5,
            ],
            [
                'name' => 'CEF3',
                'slug' => 'cef3',
                'description' => 'CEF3框架、浏览器自动化、CefSharp集成',
                'color' => '#2196f3',
                'icon' => 'browser',
                'sort_order' => 6,
            ],
            [
                'name' => 'Selenium',
                'slug' => 'selenium',
                'description' => 'Selenium WebDriver、浏览器自动化测试',
                'color' => '#43b02a',
                'icon' => 'selenium',
                'sort_order' => 7,
            ],
            [
                'name' => '算法与数据结构',
                'slug' => 'algorithm',
                'description' => '基础算法、数据结构、编程思维训练',
                'color' => '#ff6b6b',
                'icon' => 'algorithm',
                'sort_order' => 8,
            ],
            [
                'name' => '数据库',
                'slug' => 'database',
                'description' => 'MySQL、SQL语句、数据库设计',
                'color' => '#336791',
                'icon' => 'database',
                'sort_order' => 9,
            ],
            [
                'name' => '项目实战',
                'slug' => 'project',
                'description' => '综合项目、实战案例、完整开发流程',
                'color' => '#ffc107',
                'icon' => 'project',
                'sort_order' => 10,
            ],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
} 
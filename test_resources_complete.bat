@echo off
chcp 65001 > nul
echo ==========================================
echo 资源管理功能完整测试
echo ==========================================
echo.

echo [1] 测试资源API响应...
php -r "
$url = 'http://localhost:8000/api/admin/resources?page=1&pageSize=20&search=&type=';
$response = file_get_contents($url);
$data = json_decode($response, true);

echo '原始API响应:' . PHP_EOL;
echo json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . PHP_EOL;

echo PHP_EOL . '=== 数据结构分析 ===' . PHP_EOL;
echo '数据路径: response.data.data.data' . PHP_EOL;
echo '资源数量: ' . count($data['data']['data']) . PHP_EOL;
echo '总记录数: ' . $data['data']['total'] . PHP_EOL;
echo '当前页: ' . $data['data']['current_page'] . PHP_EOL;
echo '每页数量: ' . $data['data']['per_page'] . PHP_EOL;

echo PHP_EOL . '=== 资源列表 ===' . PHP_EOL;
foreach ($data['data']['data'] as $resource) {
    echo sprintf('ID: %d | 标题: %s | 分类: %d | 类型: %s | 难度: %s', 
        $resource['id'], 
        $resource['title'], 
        $resource['category_id'],
        $resource['type'],
        $resource['difficulty']
    ) . PHP_EOL;
}
"

echo.
echo [2] 检查数据库资源表结构...
php -r "
try {
    \$pdo = new PDO('mysql:host=localhost;dbname=study_platform;charset=utf8mb4', 'root', '61263269');
    \$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // 检查resources表结构
    \$stmt = \$pdo->query('DESCRIBE resources');
    echo '资源表字段:' . PHP_EOL;
    while (\$row = \$stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf('  %s: %s %s', \$row['Field'], \$row['Type'], \$row['Null'] === 'NO' ? '(NOT NULL)' : '') . PHP_EOL;
    }
    
    // 统计资源数量
    \$stmt = \$pdo->query('SELECT COUNT(*) as count FROM resources');
    \$count = \$stmt->fetch(PDO::FETCH_ASSOC)['count'];
    echo PHP_EOL . '资源总数: ' . \$count . PHP_EOL;
    
    // 按分类统计
    \$stmt = \$pdo->query('SELECT category_id, COUNT(*) as count FROM resources GROUP BY category_id');
    echo PHP_EOL . '分类分布:' . PHP_EOL;
    while (\$row = \$stmt->fetch(PDO::FETCH_ASSOC)) {
        echo sprintf('  分类 %d: %d 个资源', \$row['category_id'], \$row['count']) . PHP_EOL;
    }
    
} catch (Exception \$e) {
    echo 'Error: ' . \$e->getMessage() . PHP_EOL;
}
"

echo.
echo [3] 测试不同筛选条件...

echo 测试搜索功能...
php -r "
\$url = 'http://localhost:8000/api/admin/resources?page=1&pageSize=20&search=HTML&type=';
\$response = file_get_contents(\$url);
\$data = json_decode(\$response, true);
echo '搜索 \"HTML\" 结果数量: ' . count(\$data['data']['data']) . PHP_EOL;
"

echo 测试类型筛选...
php -r "
\$url = 'http://localhost:8000/api/admin/resources?page=1&pageSize=20&search=&type=article';
\$response = file_get_contents(\$url);
\$data = json_decode(\$response, true);
echo '筛选 \"article\" 结果数量: ' . count(\$data['data']['data']) . PHP_EOL;
"

echo.
echo [4] 验证前端数据访问路径...
echo 正确的数据访问方式：
echo   resources.value = response.data.data.data
echo   total = response.data.data.total
echo   currentPage = response.data.data.current_page

echo.
echo ==========================================
echo 资源管理测试完成！
echo ==========================================
echo.
echo 现在请：
echo 1. 刷新管理后台页面 (http://localhost:5173)
echo 2. 点击"资源管理"标签
echo 3. 查看是否显示 5 个学习资源
echo 4. 测试搜索和筛选功能
echo.
pause 
@echo off
chcp 65001 >nul
echo ================================================
echo 📚 批量导入学习资源 - 54天编程学习计划
echo ================================================
echo.

echo 正在连接数据库并导入资源...
echo.

cd /d "%~dp0"

:: 导入学习资源
mysql -h localhost -u root -p61263269 study_platform < import_learning_resources.sql

if %errorlevel% equ 0 (
    echo ✅ 学习资源导入成功！
    echo.
    echo 📊 已导入的资源统计：
    echo - Web基础阶段：8个资源 ^(HTML/CSS/JavaScript^)
    echo - C#编程阶段：6个资源 ^(.NET/C#基础和进阶^)
    echo - Vue.js阶段：5个资源 ^(Vue3全家桶^)
    echo - 自动化阶段：6个资源 ^(Selenium/CEF3^)
    echo - 综合工具：5个资源 ^(在线工具/社区平台^)
    echo.
    echo 总计：30个精选中文学习资源
    echo.
    echo 🎯 现在可以访问：
    echo - 前台学习资源页面：http://localhost:5174/resources
    echo - 后台资源管理页面：http://localhost:5174/admin
    echo.
) else (
    echo ❌ 导入失败，请检查：
    echo 1. MySQL服务是否启动
    echo 2. 数据库连接信息是否正确
    echo 3. 数据库study_platform是否存在
    echo 4. categories表是否已有分类数据
    echo.
)

echo 按任意键退出...
pause >nul 
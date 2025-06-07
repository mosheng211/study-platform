@echo off
echo ===== 54天编程学习平台 - 启动脚本 =====
echo.

echo 1. 设置环境配置...
call setup_env.bat

echo.
echo 2. 创建数据库和表...
mysql -u root -p61263269 < create_tables.sql
if %errorlevel% equ 0 (
    echo ✓ 数据库创建成功
) else (
    echo ❌ 数据库创建失败，请检查MySQL连接
    pause
    exit /b 1
)

echo.
echo 3. 启动Laravel后端服务...
start "Laravel Backend" cmd /k "php artisan serve --host=0.0.0.0 --port=8000"

echo.
echo 4. 等待后端启动...
timeout /t 3 /nobreak > nul

echo.
echo 5. 启动前端开发服务...
cd frontend
start "Vue Frontend" cmd /k "npm run dev"
cd ..

echo.
echo 6. 等待前端启动...
timeout /t 5 /nobreak > nul

echo.
echo 7. 测试API功能...
php test_api.php

echo.
echo ===== 启动完成 =====
echo 前端地址: http://localhost:5173
echo 后端地址: http://localhost:8000
echo 管理后台: http://localhost:5173/admin
echo.
echo 测试账号:
echo 管理员: admin / 123456
echo 学生: zhangsan / 123456
echo 学生: lisi / 123456
echo 教师: wangwu / 123456
echo.
pause 
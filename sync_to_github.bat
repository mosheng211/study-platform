@echo off
chcp 65001 >nul
echo ===========================
echo  同步到GitHub仓库
echo ===========================
echo.

echo 🎯 目标仓库: https://github.com/mosheng211/study-platform.git
echo.

echo 🔍 检查Git安装状态...
git --version >nul 2>&1
if errorlevel 1 (
    echo ❌ Git 未安装或未添加到环境变量
    echo.
    echo 📥 请先安装Git:
    echo    下载地址: https://git-scm.com/download/win
    echo    安装时请勾选 "Add Git to PATH"
    echo.
    echo 💡 安装完成后重新运行此脚本
    pause
    exit /b 1
)

echo ✅ Git 已安装
git --version
echo.

echo 📂 切换到项目目录...
cd /d "%~dp0"

echo.
echo 🔧 配置Git用户信息...
echo 请输入您的GitHub用户信息:
set /p "username=GitHub用户名: "
set /p "email=GitHub邮箱: "

git config --global user.name "%username%"
git config --global user.email "%email%"
echo ✅ 用户信息配置完成
echo.

echo 🔍 检查是否已为Git仓库...
if not exist ".git" (
    echo 📦 初始化Git仓库...
    git init
    if errorlevel 1 (
        echo ❌ Git仓库初始化失败
        pause
        exit /b 1
    )
    echo ✅ Git仓库初始化完成
) else (
    echo ✅ 已是Git仓库
)

echo.
echo 📋 添加文件到暂存区...
git add .
if errorlevel 1 (
    echo ❌ 添加文件失败
    pause
    exit /b 1
)
echo ✅ 文件添加完成

echo.
echo 📝 提交更改...
git commit -m "feat: 初始化学习平台项目

🎯 项目特性:
- Laravel 9+ 后端框架
- Vue.js 3 + Vite 前端应用
- 21个精选中文学习资源
- 完整的54天学习路径
- 支持资源管理、进度跟踪、签到系统
- 包含完整部署文档和工具

📚 学习资源涵盖:
- Web基础: HTML/CSS/JavaScript
- C#编程: 微软官方文档、社区教程
- Vue.js框架: 官方文档、实战项目
- 自动化测试: Selenium、CEF3
- 开发工具: CodePen、GitHub、技术社区

🚀 部署支持:
- 传统Linux服务器部署
- 宝塔面板一键部署
- Docker容器化部署
- 数据导入导出工具"

if errorlevel 1 (
    echo ❌ 提交失败
    pause
    exit /b 1
)
echo ✅ 提交完成

echo.
echo 🔗 添加远程仓库...
git remote remove origin >nul 2>&1
git remote add origin https://github.com/mosheng211/study-platform.git
if errorlevel 1 (
    echo ❌ 添加远程仓库失败
    pause
    exit /b 1
)
echo ✅ 远程仓库添加完成

echo.
echo 📤 推送到GitHub...
echo 正在推送到: https://github.com/mosheng211/study-platform.git
echo.

git branch -M main
git push -u origin main

if errorlevel 1 (
    echo.
    echo ❌ 推送失败，可能的原因:
    echo 1. 网络连接问题
    echo 2. GitHub认证失败
    echo 3. 仓库不存在或无权限
    echo.
    echo 💡 解决方案:
    echo 1. 确保GitHub仓库已创建: https://github.com/mosheng211/study-platform
    echo 2. 配置GitHub认证:
    echo    - 使用个人访问令牌 (推荐)
    echo    - 或配置SSH密钥
    echo.
    echo 🔑 GitHub个人访问令牌设置:
    echo    1. GitHub → Settings → Developer settings → Personal access tokens
    echo    2. Generate new token (classic)
    echo    3. 勾选 'repo' 权限
    echo    4. 推送时使用令牌作为密码
    echo.
    echo 📖 详细说明请查看: Git同步指南.md
    pause
    exit /b 1
) else (
    echo.
    echo 🎉 同步成功！
    echo.
    echo ✅ 您的学习平台项目已成功同步到GitHub!
    echo.
    echo 📊 项目统计:
    echo - 🏗️ Laravel + Vue.js 技术栈
    echo - 📚 21个精选中文学习资源  
    echo - 📋 54天完整学习路径
    echo - 🚀 3种部署方案
    echo - 📖 完整项目文档
    echo.
    echo 🔗 仓库地址: https://github.com/mosheng211/study-platform
    echo 🌐 在线访问: https://github.com/mosheng211/study-platform
    echo.
    echo 🌟 别忘了给项目加个Star哦！
)

echo.
echo 按任意键退出...
pause >nul 
@echo off
chcp 65001 >nul
cls
echo ╔══════════════════════════════════════════════════════════════════════════════╗
echo ║                        🚀 立即同步到GitHub仓库                               ║
echo ║                   https://github.com/mosheng211/study-platform              ║
echo ╚══════════════════════════════════════════════════════════════════════════════╝
echo.

:: 设置仓库URL
set "REPO_URL=https://github.com/mosheng211/study-platform.git"
set "USERNAME=mosheng211"

echo 📂 当前目录: %CD%
echo 🎯 目标仓库: %REPO_URL%
echo 👤 用户名: %USERNAME%
echo.

:: 检查Git是否安装
echo 🔍 检查Git安装状态...
git --version >nul 2>&1
if errorlevel 1 (
    echo ❌ 错误: Git未安装或未添加到环境变量
    echo.
    echo 📥 请按以下步骤安装Git:
    echo    1. 访问: https://git-scm.com/download/win
    echo    2. 下载Git for Windows
    echo    3. 安装时确保勾选 "Add Git to PATH"
    echo    4. 重启命令提示符后再运行此脚本
    echo.
    goto :error
)

git --version
echo ✅ Git已安装
echo.

:: 配置Git用户信息
echo 🔧 配置Git用户信息...
echo 请输入您的GitHub信息 (用于提交记录):
set /p "USER_NAME=GitHub用户名 [%USERNAME%]: "
if "%USER_NAME%"=="" set "USER_NAME=%USERNAME%"

set /p "USER_EMAIL=GitHub邮箱: "
if "%USER_EMAIL%"=="" (
    echo ❌ 错误: 邮箱不能为空
    goto :error
)

git config --global user.name "%USER_NAME%"
git config --global user.email "%USER_EMAIL%"
echo ✅ 用户信息配置完成
echo.

:: 初始化Git仓库
echo 📦 初始化Git仓库...
if exist ".git" (
    echo ✅ 已是Git仓库
) else (
    git init
    if errorlevel 1 (
        echo ❌ 错误: Git仓库初始化失败
        goto :error
    )
    echo ✅ Git仓库初始化完成
)
echo.

:: 添加文件到暂存区
echo 📋 添加文件到暂存区...
git add .
if errorlevel 1 (
    echo ❌ 错误: 添加文件失败
    goto :error
)
echo ✅ 所有文件已添加到暂存区
echo.

:: 检查是否有文件需要提交
git diff --cached --quiet
if not errorlevel 1 (
    echo ⚠️  警告: 没有检测到需要提交的更改
    echo.
)

:: 提交更改
echo 📝 提交更改到本地仓库...
git commit -m "feat: 🎉 初始化学习平台项目

🎯 项目特性:
✅ Laravel 9+ 后端框架 (PHP 8.1+)
✅ Vue.js 3 + Vite 前端应用
✅ MySQL 8.0 数据库
✅ Element Plus UI组件库
✅ 响应式设计支持

📚 学习资源 (21个精选):
✅ Web基础: HTML/CSS/JavaScript (菜鸟教程、MDN、廖雪峰)
✅ C#编程: 微软官方文档、社区教程
✅ Vue.js框架: 官方文档、实战项目
✅ 自动化测试: Selenium、CEF3
✅ 开发工具: CodePen、GitHub、技术社区

🚀 核心功能:
✅ 资源管理系统 (增删改查)
✅ 分类管理 (7大技术分类)
✅ 进度跟踪系统
✅ 每日签到功能
✅ 数据统计分析
✅ 管理员后台

📋 54天学习路径:
📅 第1-18天: Web基础技术
📅 第19-30天: C#编程进阶
📅 第31-42天: Vue.js框架
📅 第43-54天: 浏览器自动化

🛠️ 部署支持:
✅ 传统Linux服务器
✅ 宝塔面板一键部署
✅ Docker容器化
✅ 数据导入导出工具
✅ 完整部署文档

💡 技术亮点:
✅ RESTful API设计
✅ JWT认证机制
✅ 数据库迁移系统
✅ 前后端分离架构
✅ 模块化组件设计"

if errorlevel 1 (
    echo ❌ 错误: 提交失败
    goto :error
)
echo ✅ 提交完成
echo.

:: 添加远程仓库
echo 🔗 配置远程仓库...
git remote remove origin >nul 2>&1
git remote add origin "%REPO_URL%"
if errorlevel 1 (
    echo ❌ 错误: 添加远程仓库失败
    goto :error
)
echo ✅ 远程仓库配置完成
echo.

:: 设置主分支
echo 🌿 设置主分支为main...
git branch -M main
echo ✅ 主分支设置完成
echo.

:: 推送到GitHub
echo 📤 推送到GitHub...
echo ⚠️  注意: 推送时需要GitHub认证
echo 💡 建议使用个人访问令牌 (Personal Access Token)
echo.
echo 🔑 如果没有令牌，请访问: https://github.com/settings/tokens
echo    1. 点击 "Generate new token (classic)"
echo    2. 勾选 "repo" 权限
echo    3. 复制生成的令牌
echo    4. 推送时用令牌替代密码
echo.

echo 正在推送到: %REPO_URL%
git push -u origin main

if errorlevel 1 (
    echo.
    echo ❌ 推送失败，可能的原因:
    echo 1. 🔐 认证失败 - 需要GitHub个人访问令牌
    echo 2. 🌐 网络连接问题
    echo 3. 📁 仓库权限不足
    echo.
    echo 💡 解决方案:
    echo 1. 获取个人访问令牌: https://github.com/settings/tokens
    echo 2. 确保网络连接正常
    echo 3. 确认对仓库有写入权限
    echo.
    goto :error
) else (
    echo.
    echo 🎉 同步成功！
    echo.
    echo ╔══════════════════════════════════════════════════════════════════════════════╗
    echo ║                          ✅ 同步完成统计                                      ║
    echo ╠══════════════════════════════════════════════════════════════════════════════╣
    echo ║ 🏗️  技术栈: Laravel + Vue.js + MySQL                                        ║
    echo ║ 📚 学习资源: 21个精选中文资源                                                  ║
    echo ║ 📋 学习路径: 54天完整计划                                                      ║
    echo ║ 🚀 部署方案: 3种部署方式                                                      ║
    echo ║ 📖 项目文档: 完整的使用和部署指南                                               ║
    echo ║ 🛠️  开发工具: 数据导入导出、自动化脚本                                         ║
    echo ╚══════════════════════════════════════════════════════════════════════════════╝
    echo.
    echo 🔗 仓库地址: https://github.com/mosheng211/study-platform
    echo 🌐 在线查看: https://github.com/mosheng211/study-platform
    echo.
    echo 🌟 别忘了给项目加个Star支持一下！
    echo 📢 欢迎提交Issue和Pull Request
    echo.
)

goto :end

:error
echo.
echo ❌ 操作失败！请检查上述错误信息并重试。
echo 📖 如需帮助，请查看 "GitHub同步操作指南.md"
echo.
pause
exit /b 1

:end
echo 按任意键退出...
pause >nul 
@echo off
chcp 65001 >nul
echo ===========================
echo  学习平台 Git 同步工具
echo ===========================
echo.

echo 检查Git安装状态...
git --version >nul 2>&1
if errorlevel 1 (
    echo ❌ Git 未安装或未添加到环境变量
    echo.
    echo 📥 请先安装Git:
    echo    下载地址: https://git-scm.com/download/win
    echo    安装时请勾选 "Add Git to PATH"
    echo.
    pause
    exit /b 1
)

echo ✅ Git 已安装
echo.

echo 🔧 配置Git用户信息 (如果尚未配置)
set /p "username=请输入您的Git用户名: "
set /p "email=请输入您的Git邮箱: "

git config --global user.name "%username%"
git config --global user.email "%email%"

echo.
echo 📂 切换到项目目录...
cd /d "%~dp0"

echo.
echo 🔍 检查是否已为Git仓库...
if not exist ".git" (
    echo 📦 初始化Git仓库...
    git init
    echo ✅ Git仓库初始化完成
) else (
    echo ✅ 已是Git仓库
)

echo.
echo 📋 添加文件到暂存区...
git add .

echo.
echo 📝 提交更改...
set "commit_msg=feat: 初始化学习平台项目 - 包含21个精选中文学习资源"
git commit -m "%commit_msg%"

echo.
echo 🔗 添加远程仓库...
echo.
echo 请选择Git平台:
echo 1. GitHub
echo 2. Gitee (码云)  
echo 3. GitLab
echo 4. 其他
echo.
set /p "platform=请输入选项 (1-4): "

if "%platform%"=="1" (
    echo.
    echo 📋 GitHub 设置指南:
    echo 1. 在GitHub上创建新仓库 (建议名称: study-platform)
    echo 2. 不要初始化README、.gitignore或LICENSE
    echo 3. 复制仓库URL
    echo.
    set /p "repo_url=请输入GitHub仓库URL: "
) else if "%platform%"=="2" (
    echo.
    echo 📋 Gitee 设置指南:
    echo 1. 在Gitee上创建新仓库 (建议名称: study-platform)
    echo 2. 不要初始化README、.gitignore或LICENSE
    echo 3. 复制仓库URL
    echo.
    set /p "repo_url=请输入Gitee仓库URL: "
) else if "%platform%"=="3" (
    echo.
    echo 📋 GitLab 设置指南:
    echo 1. 在GitLab上创建新仓库 (建议名称: study-platform)
    echo 2. 不要初始化README、.gitignore或LICENSE
    echo 3. 复制仓库URL
    echo.
    set /p "repo_url=请输入GitLab仓库URL: "
) else (
    echo.
    set /p "repo_url=请输入您的Git仓库URL: "
)

echo.
echo 🔗 添加远程仓库...
git remote add origin "%repo_url%"

echo.
echo 📤 推送到远程仓库...
git branch -M main
git push -u origin main

if errorlevel 1 (
    echo.
    echo ❌ 推送失败，可能的原因:
    echo 1. 网络连接问题
    echo 2. 远程仓库URL不正确
    echo 3. 认证失败 (需要配置SSH密钥或个人访问令牌)
    echo.
    echo 💡 解决方案:
    echo - 检查网络连接
    echo - 确认仓库URL正确
    echo - 配置SSH密钥或使用HTTPS认证
    echo.
    echo 📖 详细说明请查看: Git同步指南.md
) else (
    echo.
    echo ✅ 同步成功!
    echo.
    echo 🎉 您的学习平台项目已成功同步到Git仓库!
    echo.
    echo 📊 项目包含:
    echo - Laravel后端框架
    echo - Vue.js前端应用  
    echo - 21个精选中文学习资源
    echo - 完整的部署文档
    echo - 数据导入/导出工具
    echo.
    echo 🔗 仓库地址: %repo_url%
)

echo.
echo 按任意键退出...
pause >nul 
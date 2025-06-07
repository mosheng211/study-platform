# 📚 学习平台 - Git同步指南

## 🎯 概述

本指南将帮助您将学习平台项目同步到Git仓库，便于版本控制、备份和协作开发。

## 🔧 前提条件

### 1. 安装Git
- **下载地址**: https://git-scm.com/download/win
- **安装要点**: 
  - ✅ 勾选 "Add Git to PATH"
  - ✅ 选择 "Use Git from the command line and also from 3rd-party software"
  - ✅ 推荐使用默认设置

### 2. 选择Git托管平台
| 平台 | 优势 | 适用场景 |
|------|------|----------|
| 🐙 **GitHub** | 全球最大，生态丰富 | 开源项目、国际合作 |
| 🎋 **Gitee (码云)** | 国内访问快，中文界面 | 国内项目、企业使用 |
| 🦊 **GitLab** | 功能完整，可私有部署 | 企业级项目 |

## 🚀 快速同步 (推荐)

### 方法一: 使用自动化脚本
```bash
# 在study-platform目录下运行
git_sync.bat
```

脚本将自动完成：
- ✅ 检查Git安装状态
- ✅ 配置用户信息
- ✅ 初始化Git仓库
- ✅ 添加和提交文件
- ✅ 连接远程仓库
- ✅ 推送代码

## 📖 手动同步步骤

### 1. 创建远程仓库

#### GitHub操作步骤
1. 登录 https://github.com
2. 点击右上角 "+" → "New repository"
3. 仓库名称: `study-platform`
4. 描述: `基于Laravel+Vue.js的学习资源管理平台`
5. 设置为 **Public** 或 **Private**
6. **❌ 不要** 勾选 "Initialize with README"
7. 点击 "Create repository"

#### Gitee操作步骤
1. 登录 https://gitee.com
2. 点击右上角 "+" → "新建仓库"
3. 仓库名称: `study-platform`
4. 仓库介绍: `基于Laravel+Vue.js的学习资源管理平台`
5. 设置为 **开源** 或 **私有**
6. **❌ 不要** 勾选 "使用Readme文件初始化这个仓库"
7. 点击 "创建"

### 2. 本地Git操作

#### 2.1 打开命令行
```bash
# 进入项目目录
cd T:\HTML\StudyPlan\study-platform
```

#### 2.2 配置Git用户信息
```bash
# 设置用户名和邮箱 (全局配置)
git config --global user.name "您的用户名"
git config --global user.email "您的邮箱@example.com"
```

#### 2.3 初始化仓库
```bash
# 初始化Git仓库
git init

# 查看状态
git status
```

#### 2.4 添加文件
```bash
# 添加所有文件到暂存区
git add .

# 查看暂存区状态
git status
```

#### 2.5 提交更改
```bash
# 提交到本地仓库
git commit -m "feat: 初始化学习平台项目

- 添加Laravel后端框架
- 添加Vue.js前端应用
- 包含21个精选中文学习资源
- 添加完整部署文档
- 添加数据导入导出工具"
```

#### 2.6 连接远程仓库
```bash
# 添加远程仓库 (替换为您的仓库URL)
# GitHub示例:
git remote add origin https://github.com/yourusername/study-platform.git

# Gitee示例:
git remote add origin https://gitee.com/yourusername/study-platform.git

# 验证远程仓库
git remote -v
```

#### 2.7 推送到远程仓库
```bash
# 设置主分支并推送
git branch -M main
git push -u origin main
```

## 🔐 认证配置

### 方法一: HTTPS认证 (简单)
对于HTTPS URL，Git会提示输入用户名和密码/令牌。

#### GitHub个人访问令牌
1. GitHub → Settings → Developer settings → Personal access tokens
2. Generate new token (classic)
3. 勾选 `repo` 权限
4. 生成并保存令牌
5. 推送时使用令牌作为密码

#### Gitee私人令牌
1. Gitee → 设置 → 私人令牌
2. 生成新令牌
3. 勾选 `projects` 权限
4. 生成并保存令牌

### 方法二: SSH密钥 (推荐)

#### 生成SSH密钥
```bash
# 生成SSH密钥
ssh-keygen -t ed25519 -C "your_email@example.com"

# 启动ssh-agent
eval "$(ssh-agent -s)"

# 添加私钥
ssh-add ~/.ssh/id_ed25519
```

#### 添加公钥到平台
```bash
# 复制公钥内容
cat ~/.ssh/id_ed25519.pub
```

1. **GitHub**: Settings → SSH and GPG keys → New SSH key
2. **Gitee**: 设置 → SSH公钥 → 添加公钥

#### 使用SSH URL
```bash
# GitHub SSH URL
git remote set-url origin git@github.com:yourusername/study-platform.git

# Gitee SSH URL  
git remote set-url origin git@gitee.com:yourusername/study-platform.git
```

## 📁 仓库结构

同步后的仓库将包含：

```
study-platform/
├── 📁 app/                    # Laravel应用代码
│   ├── Http/Controllers/      # 控制器
│   ├── Models/               # 数据模型
│   └── Console/Commands/     # Artisan命令
├── 📁 frontend/              # Vue.js前端代码
│   ├── src/                  # 源代码
│   ├── public/               # 静态资源
│   └── package.json          # 依赖配置
├── 📁 database/              # 数据库
│   ├── migrations/           # 数据库迁移
│   └── seeders/             # 数据填充
├── 📁 public/                # Web根目录
├── 📁 resources/             # 资源文件
├── 📁 routes/                # 路由定义
├── 📁 storage/               # 存储目录
├── 📄 .gitignore             # Git忽略规则
├── 📄 composer.json          # PHP依赖
├── 📄 部署文档-生产环境.md    # 部署文档
├── 📄 部署文档-宝塔面板.md    # 宝塔部署
├── 📄 快速部署指南.md        # 快速指南
├── 📄 export_data.php        # 数据导出
├── 📄 import.php             # Web导入
└── 📄 README.md              # 项目说明
```

## 🔄 日常Git操作

### 查看状态
```bash
# 查看工作区状态
git status

# 查看提交历史
git log --oneline

# 查看远程仓库
git remote -v
```

### 更新和推送
```bash
# 添加更改
git add .

# 提交更改
git commit -m "描述您的更改"

# 推送到远程仓库
git push

# 拉取远程更改
git pull
```

### 分支管理
```bash
# 创建新分支
git checkout -b feature/new-feature

# 切换分支
git checkout main

# 合并分支
git merge feature/new-feature

# 删除分支
git branch -d feature/new-feature
```

## 🔍 项目亮点

### 📚 学习资源系统
- **21个精选中文资源**: 涵盖Web技术栈完整学习路径
- **7大技术分类**: HTML/CSS、JavaScript、C#、Vue.js、自动化测试、CEF3、在线工具
- **多种资源类型**: 文档教程、视频课程、在线工具、实战项目

### 🏗️ 技术架构
- **后端**: Laravel 9+ (PHP 8.1+)
- **前端**: Vue.js 3 + Vite
- **数据库**: MySQL 8.0
- **部署**: 支持传统Linux和宝塔面板

### 🚀 部署方案
- **完整部署文档**: 支持生产环境和宝塔面板
- **自动化工具**: 数据导入导出、一键部署脚本
- **配置模板**: 生产环境配置模板和检查清单

## ❗ 常见问题

### 1. 推送失败: 认证问题
```bash
# 解决方案1: 重新配置远程URL
git remote set-url origin https://username:token@github.com/username/repo.git

# 解决方案2: 使用SSH
git remote set-url origin git@github.com:username/repo.git
```

### 2. 文件过大
```bash
# 检查大文件
git ls-files | xargs ls -la | sort -k5 -nr | head -10

# 移除大文件
git rm --cached large-file.zip
git commit -m "移除大文件"
```

### 3. 中文乱码
```bash
# 配置Git支持中文
git config --global core.quotepath false
git config --global gui.encoding utf-8
git config --global i18n.commit.encoding utf-8
```

### 4. 网络问题
```bash
# 配置代理 (如果需要)
git config --global http.proxy http://proxy.company.com:8080
git config --global https.proxy https://proxy.company.com:8080

# 取消代理
git config --global --unset http.proxy
git config --global --unset https.proxy
```

## 📞 获取帮助

- **Git官方文档**: https://git-scm.com/doc
- **GitHub帮助**: https://docs.github.com
- **Gitee帮助**: https://help.gitee.com
- **Git中文教程**: https://www.liaoxuefeng.com/wiki/896043488029600

---

**🎉 完成Git同步后，您的学习平台项目将获得：**
- ✅ 版本控制和历史记录
- ✅ 代码备份和恢复
- ✅ 团队协作能力
- ✅ 持续集成/部署基础
- ✅ 开源社区展示平台

**🌟 祝您的开源项目获得更多关注和贡献！** 
# 🚀 GitHub同步操作指南

## 📋 准备工作

### 1. 确保GitHub仓库已创建
- 仓库地址: https://github.com/mosheng211/study-platform
- 如果仓库不存在，请先在GitHub上创建

### 2. 安装Git (如果尚未安装)
- 下载地址: https://git-scm.com/download/win
- 安装时勾选 "Add Git to PATH"

## 🎯 快速同步 (推荐)

### 方法一: 使用自动化脚本
```bash
# 在study-platform目录下双击运行
sync_to_github.bat
```

### 方法二: 手动命令行操作

#### 1. 打开命令提示符
```bash
# 进入项目目录
cd T:\HTML\StudyPlan\study-platform
```

#### 2. 配置Git用户信息
```bash
git config --global user.name "mosheng211"
git config --global user.email "your-email@example.com"
```

#### 3. 初始化并提交
```bash
# 初始化Git仓库
git init

# 添加所有文件
git add .

# 提交更改
git commit -m "feat: 初始化学习平台项目 - 包含21个精选中文学习资源"
```

#### 4. 连接远程仓库并推送
```bash
# 添加远程仓库
git remote add origin https://github.com/mosheng211/study-platform.git

# 推送到GitHub
git branch -M main
git push -u origin main
```

## 🔐 认证配置

### GitHub个人访问令牌 (推荐)

1. **生成令牌**:
   - 访问: https://github.com/settings/tokens
   - 点击 "Generate new token (classic)"
   - 勾选 `repo` 权限
   - 生成并复制令牌

2. **使用令牌**:
   - 推送时输入用户名: `mosheng211`
   - 输入密码时使用令牌 (不是GitHub密码)

### SSH密钥配置 (可选)

```bash
# 生成SSH密钥
ssh-keygen -t ed25519 -C "your-email@example.com"

# 复制公钥
cat ~/.ssh/id_ed25519.pub

# 在GitHub添加SSH密钥: Settings → SSH and GPG keys
```

## 📊 项目同步内容

同步到GitHub的内容包括:

### 🏗️ 核心代码
- **Laravel后端**: 完整的API和管理系统
- **Vue.js前端**: 响应式用户界面
- **数据库结构**: 迁移文件和模型

### 📚 学习资源
- **21个精选资源**: 涵盖完整技术栈
- **分类管理**: 7大技术分类
- **导入工具**: 一键导入脚本

### 📖 文档系统
- **部署文档**: 生产环境和宝塔面板
- **快速指南**: 安装和使用说明
- **API文档**: 接口说明和示例

### 🛠️ 工具脚本
- **数据导入**: 多种导入方式
- **数据导出**: 备份和迁移工具
- **部署脚本**: 自动化部署

## ✅ 验证同步结果

同步完成后，访问以下地址验证:

1. **GitHub仓库**: https://github.com/mosheng211/study-platform
2. **项目README**: 查看项目介绍和使用说明
3. **代码结构**: 确认所有文件已正确上传

## 🔄 后续更新

### 日常更新流程
```bash
# 添加更改
git add .

# 提交更改
git commit -m "描述您的更改"

# 推送到GitHub
git push
```

### 拉取远程更改
```bash
git pull origin main
```

## ❗ 常见问题

### 1. 推送失败: 认证错误
**解决方案**: 使用个人访问令牌替代密码

### 2. 推送失败: 仓库不存在
**解决方案**: 确认GitHub仓库已创建且URL正确

### 3. 文件过大警告
**解决方案**: 检查.gitignore文件，排除不必要的大文件

### 4. 中文乱码
**解决方案**: 
```bash
git config --global core.quotepath false
git config --global gui.encoding utf-8
```

## 🎉 同步完成后的收益

✅ **版本控制**: 完整的代码历史记录  
✅ **备份安全**: 云端代码备份  
✅ **团队协作**: 支持多人协作开发  
✅ **开源展示**: 项目公开展示平台  
✅ **持续集成**: 支持CI/CD流程  

---

**🌟 项目特色**:
- 📚 21个精选中文学习资源
- 🎯 54天完整学习路径  
- 🏗️ Laravel + Vue.js 现代技术栈
- 🚀 多种部署方案支持
- 📖 完整的项目文档

**🔗 仓库地址**: https://github.com/mosheng211/study-platform

**💡 如有问题，请参考 `Git同步指南.md` 获取详细帮助！** 
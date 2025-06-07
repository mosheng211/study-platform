# 📚 学习平台 - Study Platform

基于Laravel + Vue.js的学习资源管理平台，包含21个精选中文学习资源，涵盖完整的Web技术栈学习路径。

## 🎯 项目概述

这是一个为期54天的编程学习平台，旨在帮助开发者系统性地学习Web开发技术栈。平台提供了完整的学习资源管理系统，包括资源分类、进度跟踪、签到系统等功能。

## ✨ 主要特性

### 📖 学习资源系统
- **21个精选中文资源**: 来自菜鸟教程、廖雪峰、尚硅谷、黑马程序员等知名平台
- **7大技术分类**: HTML/CSS、JavaScript、C#、Vue.js、自动化测试、CEF3、在线工具
- **多种资源类型**: 文档教程、视频课程、在线工具、实战项目
- **难度分级**: 初级、中级、高级，适合不同水平的学习者

### 🏗️ 技术架构
- **后端框架**: Laravel 9+ (PHP 8.1+)
- **前端框架**: Vue.js 3 + Vite
- **数据库**: MySQL 8.0
- **UI组件**: Element Plus
- **开发工具**: Composer, NPM/Yarn

### 🚀 核心功能
- **资源管理**: 增删改查学习资源
- **分类系统**: 按技术栈分类管理
- **进度跟踪**: 记录学习进度和状态
- **签到系统**: 每日学习签到功能
- **统计分析**: 学习数据统计和可视化
- **响应式设计**: 支持PC和移动端访问

## 📋 学习路径

### 第一阶段: Web基础 (1-18天)
- **HTML/CSS基础**: 菜鸟教程、MDN文档
- **JavaScript核心**: 廖雪峰JavaScript教程、现代JavaScript教程
- **前端工具**: CodePen在线编辑器

### 第二阶段: C#编程 (19-30天)
- **C#基础**: 微软官方文档、刘铁猛C#教程
- **面向对象**: 博客园C#进阶教程
- **.NET生态**: 官方学习资源

### 第三阶段: Vue.js框架 (31-42天)
- **Vue.js基础**: 官方文档、技术胖视频教程
- **组件开发**: 掘金Vue.js实战
- **项目实战**: 慕课网Vue项目

### 第四阶段: 浏览器自动化 (43-54天)
- **Selenium**: Web自动化测试
- **CEF3**: 浏览器嵌入框架
- **CefSharp**: .NET浏览器控件

## 🛠️ 安装部署

### 环境要求
- PHP >= 8.1
- MySQL >= 8.0
- Node.js >= 16
- Composer
- Git

### 快速开始

#### 1. 克隆项目
```bash
git clone https://github.com/mosheng211/study-platform.git
cd study-platform
```

#### 2. 后端安装
```bash
# 安装PHP依赖
composer install

# 复制环境配置
cp .env.example .env

# 生成应用密钥
php artisan key:generate

# 配置数据库连接 (编辑.env文件)
DB_DATABASE=study_platform
DB_USERNAME=root
DB_PASSWORD=your_password

# 运行数据库迁移
php artisan migrate

# 启动后端服务
php artisan serve
```

#### 3. 前端安装
```bash
cd frontend

# 安装依赖
npm install

# 启动开发服务器
npm run dev
```

#### 4. 导入学习资源
访问 `http://localhost:8000/import.php` 一键导入21个精选学习资源。

### 访问地址
- **后端API**: http://localhost:8000
- **前端应用**: http://localhost:5173
- **管理后台**: http://localhost:5173/admin

## 📚 学习资源列表

### Web基础技术
1. **菜鸟教程 - HTML教程**: HTML基础入门
2. **菜鸟教程 - CSS教程**: CSS样式设计
3. **MDN Web文档**: 权威Web技术文档
4. **廖雪峰JavaScript教程**: JavaScript核心概念

### JavaScript进阶
5. **现代JavaScript教程**: ES6+新特性
6. **JavaScript高级程序设计**: 深入理解JS
7. **Vue.js官方文档**: Vue框架学习

### C#编程
8. **微软C#官方文档**: 官方权威教程
9. **刘铁猛C#教程**: 系统性C#学习
10. **博客园C#专区**: 社区优质文章

### 框架与工具
11. **Vue.js生态系统**: 完整技术栈
12. **Element Plus**: Vue3 UI组件库
13. **Vite构建工具**: 现代前端构建

### 自动化测试
14. **Selenium WebDriver**: Web自动化
15. **测试框架对比**: 选择合适工具
16. **CEF3文档**: 浏览器嵌入

### 开发工具
17. **CodePen**: 在线代码编辑
18. **GitHub**: 代码托管平台
19. **CSDN技术社区**: 中文技术文章
20. **掘金开发者社区**: 前端技术分享
21. **慕课网**: 在线编程教育

## 🚀 部署指南

项目支持多种部署方式：

### 传统Linux部署
详见 [部署文档-生产环境.md](部署文档-生产环境.md)

### 宝塔面板部署
详见 [部署文档-宝塔面板.md](部署文档-宝塔面板.md)

### 快速部署
详见 [快速部署指南.md](快速部署指南.md)

## 📊 项目统计

- **代码行数**: 10,000+ 行
- **学习资源**: 21个精选资源
- **技术分类**: 7大类别
- **支持平台**: Windows, Linux, macOS
- **部署方式**: 3种部署方案

## 🤝 贡献指南

欢迎提交Issue和Pull Request！

### 贡献方式
1. Fork本仓库
2. 创建特性分支 (`git checkout -b feature/AmazingFeature`)
3. 提交更改 (`git commit -m 'Add some AmazingFeature'`)
4. 推送到分支 (`git push origin feature/AmazingFeature`)
5. 开启Pull Request

### 开发规范
- 遵循PSR-12 PHP编码标准
- 使用Vue.js 3 Composition API
- 编写单元测试
- 更新相关文档

## 📄 许可证

本项目采用 [MIT License](LICENSE) 开源协议。

## 📞 联系方式

- **项目地址**: https://github.com/mosheng211/study-platform
- **问题反馈**: [Issues](https://github.com/mosheng211/study-platform/issues)
- **功能建议**: [Discussions](https://github.com/mosheng211/study-platform/discussions)

## 🙏 致谢

感谢以下优秀的开源项目和学习平台：

- [Laravel](https://laravel.com/) - 优雅的PHP框架
- [Vue.js](https://vuejs.org/) - 渐进式JavaScript框架
- [Element Plus](https://element-plus.org/) - Vue3组件库
- [菜鸟教程](https://www.runoob.com/) - 编程入门教程
- [廖雪峰官方网站](https://www.liaoxuefeng.com/) - 实用编程教程

---

**🌟 如果这个项目对您有帮助，请给个Star支持一下！**

**�� 开始您的54天编程学习之旅吧！**

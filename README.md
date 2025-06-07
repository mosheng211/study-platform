# 📚 编程学习计划管理平台

> 一个基于 Laravel + Vue3 的现代化学习管理系统，专为54天编程学习计划设计

## 🚀 技术栈

### 后端 (Laravel)
- **Laravel 10.x** - PHP Web框架
- **MySQL** - 数据库
- **Laravel Sanctum** - API认证
- **Eloquent ORM** - 数据库操作

### 前端 (Vue3)
- **Vue 3** - 渐进式JavaScript框架
- **Vue Router 4** - 路由管理
- **Pinia** - 状态管理
- **Element Plus** - UI组件库
- **Axios** - HTTP客户端
- **Vite** - 构建工具

## 📋 功能特色

- 🎯 **54天学习计划**：完整的编程学习路径规划
- 📊 **学习进度跟踪**：可视化进度管理和统计分析
- ✅ **每日打卡系统**：连续打卡激励和班级排行榜
- 📚 **资源管理中心**：分类整理学习资源，支持搜索筛选
- 👥 **用户权限管理**：学生/教师/管理员多角色支持
- 📱 **移动端适配**：响应式设计，完美支持各种设备
- 🎨 **现代化UI**：基于Element Plus的美观界面设计

## 🏗️ 项目结构

```
study-platform/
├── app/                    # Laravel应用核心
│   ├── Http/
│   │   └── Controllers/    # API控制器
│   │   └── Models/         # 数据模型
│   └── Models/            # 数据模型
├── database/
│   └── migrations/        # 数据库迁移文件
├── routes/
│   └── api.php           # API路由定义
├── frontend/             # Vue3前端项目
│   ├── src/
│   │   ├── components/   # Vue组件
│   │   ├── views/        # 页面视图
│   │   ├── router/       # 路由配置
│   │   └── stores/       # Pinia状态管理
│   └── public/           # 静态资源
└── storage/              # 文件存储
```

## 🛠️ 安装和部署

### 环境要求
- PHP >= 8.1
- MySQL >= 5.7
- Node.js >= 16
- Composer

### 安装步骤

1. **克隆项目**
```bash
git clone <repository-url>
cd StudyPlan
```

2. **安装Laravel后端**
```bash
cd study-platform
composer install
cp .env.example .env
php artisan key:generate
```

3. **配置数据库**
```bash
# 编辑 .env 文件，设置数据库连接信息
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=study_platform
DB_USERNAME=root
DB_PASSWORD=your_password

# 运行数据库迁移
php artisan migrate
```

4. **安装Vue3前端**
```bash
cd frontend
npm install
```

### 启动开发服务器

**终端1 - 启动Laravel后端**
```bash
cd study-platform
php artisan serve
# 后端运行在: http://localhost:8000
```

**终端2 - 启动Vue3前端**
```bash
cd study-platform/frontend
npm run dev
# 前端运行在: http://localhost:5173
```

## 🔑 测试账号

系统已预置以下测试账号，可直接使用：

### 管理员账号
- **邮箱**：`admin@study.com`
- **密码**：`admin123456`
- **权限**：系统管理员，拥有所有权限

### 教师账号
- **邮箱**：`teacher@study.com`
- **密码**：`teacher123`
- **权限**：教师权限，可管理学习资源和查看学生进度

### 学生账号
- **邮箱**：`student@study.com`
- **密码**：`student123`
- **权限**：学生权限，可进行学习和打卡

> **注意**：以上为测试账号，生产环境请及时修改密码或创建新的管理员账号

## 📖 学习路径

### 第一阶段：Web技术基础 (14天)
- HTML5语义化标签
- CSS3样式和布局
- JavaScript基础语法
- DOM操作和事件处理

### 第二阶段：C#编程基础 (14天)
- C#语法基础
- 面向对象编程
- 数据结构和算法
- 异常处理机制

### 第三阶段：Vue.js框架 (10天)
- Vue3组合式API
- 组件化开发
- 路由和状态管理
- 项目实战

### 第四阶段：浏览器自动化 (16天)
- CEF3框架入门
- 浏览器控制和操作
- 数据采集和处理
- 完整项目开发

## 🔧 API文档

### 认证相关
- `POST /api/auth/register` - 用户注册
- `POST /api/auth/login` - 用户登录
- `GET /api/auth/profile` - 获取用户信息
- `POST /api/auth/logout` - 用户登出

### 学习进度
- `GET /api/study-progress` - 获取学习进度
- `PUT /api/study-progress/{id}` - 更新学习进度

### 资源管理
- `GET /api/resources` - 获取学习资源列表
- `POST /api/resources` - 创建学习资源

### 打卡系统
- `POST /api/checkins` - 每日打卡
- `GET /api/checkins/leaderboard` - 获取排行榜

## 🤝 贡献指南

1. Fork 本仓库
2. 创建新的功能分支 (`git checkout -b feature/新功能`)
3. 提交更改 (`git commit -am '添加新功能'`)
4. 推送到分支 (`git push origin feature/新功能`)
5. 创建Pull Request

## 📝 版本历史

- **v2.0.0** (2025-01-XX) - Laravel + Vue3 重构版本
  - 采用现代化技术栈
  - 重新设计数据库架构
  - 响应式UI设计
  - 完善的API文档

- **v1.0.0** (2024-XX-XX) - Node.js 初始版本

## 📄 许可证

本项目采用 MIT 许可证 - 查看 [LICENSE](LICENSE) 文件了解详情

## 💬 支持与反馈

如有问题或建议，请通过以下方式联系：
- 提交 [Issue](../../issues)
- 发送邮件到: [your-email@domain.com]

---

**让学习更有趣，让进步更可见！** 🚀 
<template>
  <div id="app">
    <!-- 导航栏 -->
    <header class="app-header">
      <nav class="navbar">
        <div class="navbar-brand">
          <router-link to="/" class="brand-link">
            <el-icon class="brand-icon"><School /></el-icon>
            <span class="brand-text">编程学习平台</span>
          </router-link>
        </div>
        
        <div class="navbar-menu">
          <div class="navbar-nav">
            <!-- 公共导航 -->
            <router-link to="/" class="nav-link">首页</router-link>
            <router-link to="/resources" class="nav-link">学习资源</router-link>
            
            <!-- 已登录用户导航 -->
            <template v-if="authStore.isAuthenticated">
              <router-link to="/dashboard" class="nav-link">学习看板</router-link>
              <router-link to="/progress" class="nav-link">学习进度</router-link>
              <router-link to="/checkin" class="nav-link">每日打卡</router-link>
              <router-link to="/leaderboard" class="nav-link">排行榜</router-link>
              
              <!-- 管理员导航 -->
              <router-link v-if="authStore.isAdmin" to="/admin" class="nav-link">管理后台</router-link>
            </template>
          </div>
          
          <div class="navbar-actions">
            <!-- 未登录状态 -->
            <template v-if="!authStore.isAuthenticated">
              <el-button type="primary" plain @click="$router.push('/login')">登录</el-button>
              <el-button type="primary" @click="$router.push('/register')">注册</el-button>
            </template>
            
            <!-- 已登录状态 -->
            <template v-else>
              <el-dropdown @command="handleUserCommand">
                <span class="user-dropdown">
                  <el-avatar :size="32" :src="authStore.user?.avatar">
                    {{ authStore.userName?.charAt(0) }}
                  </el-avatar>
                  <span class="user-name">{{ authStore.userName }}</span>
                  <el-icon><ArrowDown /></el-icon>
                </span>
                <template #dropdown>
                  <el-dropdown-menu>
                    <el-dropdown-item command="profile">
                      <el-icon><User /></el-icon>个人信息
                    </el-dropdown-item>
                    <el-dropdown-item command="settings">
                      <el-icon><Setting /></el-icon>设置
                    </el-dropdown-item>
                    <el-dropdown-item divided command="logout">
                      <el-icon><SwitchButton /></el-icon>退出登录
                    </el-dropdown-item>
                  </el-dropdown-menu>
                </template>
              </el-dropdown>
            </template>
          </div>
        </div>
      </nav>
    </header>
    
    <!-- 主要内容区域 -->
    <main class="app-main">
      <router-view />
    </main>
    
    <!-- 页脚 -->
    <footer class="app-footer">
      <div class="footer-content">
        <p>&copy; 2025 编程学习平台. 专注于Web开发、C#编程和浏览器自动化学习</p>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { ElMessage } from 'element-plus'

const router = useRouter()
const authStore = useAuthStore()

// 处理用户下拉菜单命令
const handleUserCommand = (command) => {
  switch (command) {
    case 'profile':
      router.push('/profile')
      break
    case 'settings':
      // TODO: 实现设置页面
      ElMessage.info('设置功能即将上线')
      break
    case 'logout':
      handleLogout()
      break
  }
}

// 处理登出
const handleLogout = async () => {
  try {
    await authStore.logout()
    ElMessage.success('已成功退出登录')
    router.push('/')
  } catch (error) {
    ElMessage.error('退出登录失败')
  }
}

// 初始化认证状态
onMounted(async () => {
  await authStore.initAuth()
})
</script>

<style>
#app {
  height: 100vh;
  display: flex;
  flex-direction: column;
}
</style>

<style scoped>
.app-header {
  flex-shrink: 0;
  height: 60px;
  background: #fff;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  z-index: 1000;
}

.navbar {
  display: flex;
  justify-content: space-between;
  align-items: center;
  height: 100%;
  padding: 0 20px;
  max-width: 100%;
}

.navbar-brand .brand-link {
  display: flex;
  align-items: center;
  text-decoration: none;
  color: #409eff;
  font-size: 20px;
  font-weight: bold;
}

.brand-icon {
  margin-right: 8px;
  font-size: 24px;
}

.brand-text {
  margin-left: 8px;
}

.navbar-menu {
  display: flex;
  align-items: center;
  gap: 20px;
}

.navbar-nav {
  display: flex;
  gap: 20px;
}

.nav-link {
  text-decoration: none;
  color: #606266;
  padding: 8px 12px;
  border-radius: 4px;
  transition: all 0.3s;
}

.nav-link:hover,
.nav-link.router-link-active {
  color: #409eff;
  background-color: #ecf5ff;
}

.navbar-actions {
  display: flex;
  align-items: center;
  gap: 12px;
}

.user-dropdown {
  display: flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
  padding: 8px;
  border-radius: 4px;
  transition: background-color 0.3s;
}

.user-dropdown:hover {
  background-color: #f5f7fa;
}

.user-name {
  color: #606266;
  font-size: 14px;
}

.app-main {
  flex: 1;
  background-color: #f5f7fa;
  overflow-y: auto;
  min-height: 0;
}

.app-footer {
  flex-shrink: 0;
  height: 60px;
  background-color: #fff;
  border-top: 1px solid #e4e7ed;
  display: flex;
  align-items: center;
  justify-content: center;
}

.footer-content {
  text-align: center;
  color: #909399;
  font-size: 14px;
}

/* 移动端适配 */
@media (max-width: 768px) {
  .navbar {
    padding: 0 12px;
  }
  
  .navbar-nav {
    display: none;
  }
  
  .brand-text {
    display: none;
  }
  
  .user-name {
    display: none;
  }
}
</style>

<template>
  <div class="home-page">
    <!-- 头部横幅 -->
    <section class="hero-section">
      <div class="container">
        <h1 class="hero-title">📚 编程学习计划管理平台</h1>
        <p class="hero-subtitle">
          一个完整的编程学习平台，帮助学员从零基础掌握 Web 技术、C# 编程，最终实现浏览器自动化开发
        </p>
        <div class="hero-actions">
          <el-button v-if="!authStore.isAuthenticated" type="primary" size="large" @click="$router.push('/register')">
            立即开始学习
          </el-button>
          <el-button v-else type="primary" size="large" @click="$router.push('/dashboard')">
            进入学习看板
          </el-button>
          <el-button size="large" plain @click="$router.push('/resources')">
            浏览学习资源
          </el-button>
        </div>
      </div>
    </section>

    <!-- 功能特色 -->
    <section class="features-section">
      <div class="container">
        <h2 class="section-title">🌟 功能特色</h2>
        <div class="features-grid">
          <div class="feature-card" v-for="feature in features" :key="feature.id">
            <div class="feature-icon">{{ feature.icon }}</div>
            <h3 class="feature-title">{{ feature.title }}</h3>
            <p class="feature-description">{{ feature.description }}</p>
          </div>
        </div>
      </div>
    </section>

    <!-- 学习路径 -->
    <section class="learning-path-section">
      <div class="container">
        <h2 class="section-title">🎯 学习路径</h2>
        <div class="learning-phases">
          <div class="phase-item" v-for="(phase, index) in learningPhases" :key="phase.id">
            <div class="phase-number">{{ index + 1 }}</div>
            <div class="phase-content">
              <h3 class="phase-title">{{ phase.title }}</h3>
              <p class="phase-description">{{ phase.description }}</p>
              <div class="phase-duration">{{ phase.duration }}</div>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- 统计数据 -->
    <section class="stats-section">
      <div class="container">
        <div class="stats-grid">
          <div class="stat-item" v-for="stat in stats" :key="stat.label">
            <div class="stat-number">{{ stat.value }}</div>
            <div class="stat-label">{{ stat.label }}</div>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA区域 -->
    <section class="cta-section">
      <div class="container">
        <h2 class="cta-title">准备开始你的编程学习之旅吗？</h2>
        <p class="cta-description">
          加入我们的学习平台，跟随专业的54天学习计划，从零基础到掌握浏览器自动化开发
        </p>
        <el-button v-if="!authStore.isAuthenticated" type="primary" size="large" @click="$router.push('/register')">
          免费注册开始学习
        </el-button>
        <el-button v-else type="primary" size="large" @click="$router.push('/progress')">
          查看我的学习进度
        </el-button>
      </div>
    </section>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

// 功能特色数据
const features = ref([
  {
    id: 1,
    icon: '📊',
    title: '学习进度跟踪',
    description: '54天完整学习计划，实时进度可视化'
  },
  {
    id: 2,
    icon: '📅',
    title: '每日打卡系统',
    description: '连续打卡统计，班级排行榜'
  },
  {
    id: 3,
    icon: '📚',
    title: '资源管理中心',
    description: '分类整理学习资源，支持搜索筛选'
  },
  {
    id: 4,
    icon: '📱',
    title: '移动端适配',
    description: '响应式设计，手机平板完美支持'
  },
  {
    id: 5,
    icon: '👥',
    title: '用户权限管理',
    description: '学生/教师/管理员角色权限'
  },
  {
    id: 6,
    icon: '📈',
    title: '数据统计分析',
    description: '学习时长统计，完成率分析'
  }
])

// 学习阶段数据
const learningPhases = ref([
  {
    id: 1,
    title: 'Web技术基础',
    description: 'HTML/CSS/JavaScript',
    duration: '14天'
  },
  {
    id: 2,
    title: 'C#编程基础',
    description: '面向对象编程、语法基础',
    duration: '14天'
  },
  {
    id: 3,
    title: 'Vue.js框架',
    description: '前端框架学习',
    duration: '10天'
  },
  {
    id: 4,
    title: '浏览器自动化',
    description: 'CEF3框架应用',
    duration: '16天'
  }
])

// 统计数据
const stats = ref([
  { value: '54', label: '学习天数' },
  { value: '100+', label: '学习资源' },
  { value: '4', label: '学习阶段' },
  { value: '24/7', label: '在线支持' }
])
</script>

<style scoped>
.home-page {
  width: 100%;
}

/* 容器样式 */
.container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 0 20px;
}

/* Hero 区域 */
.hero-section {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  color: white;
  padding: 80px 0;
  text-align: center;
}

.hero-title {
  font-size: 3rem;
  font-weight: bold;
  margin-bottom: 20px;
  text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
}

.hero-subtitle {
  font-size: 1.2rem;
  line-height: 1.6;
  margin-bottom: 40px;
  opacity: 0.9;
  max-width: 800px;
  margin-left: auto;
  margin-right: auto;
}

.hero-actions {
  display: flex;
  gap: 16px;
  justify-content: center;
  flex-wrap: wrap;
}

/* 功能特色区域 */
.features-section {
  padding: 80px 0;
  background: #fff;
}

.section-title {
  text-align: center;
  font-size: 2.5rem;
  margin-bottom: 60px;
  color: #2c3e50;
}

.features-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
  gap: 30px;
  margin-top: 40px;
}

.feature-card {
  text-align: center;
  padding: 40px 20px;
  border-radius: 12px;
  background: #f8f9fa;
  transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.feature-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
}

.feature-icon {
  font-size: 3rem;
  margin-bottom: 20px;
}

.feature-title {
  font-size: 1.2rem;
  font-weight: bold;
  margin-bottom: 12px;
  color: #2c3e50;
}

.feature-description {
  color: #606266;
  line-height: 1.6;
}

/* 学习路径区域 */
.learning-path-section {
  padding: 80px 0;
  background: #f8f9fa;
}

.learning-phases {
  display: flex;
  justify-content: center;
  align-items: stretch;
  flex-wrap: wrap;
  gap: 20px;
}

.phase-item {
  display: flex;
  align-items: center;
  background: white;
  padding: 30px;
  border-radius: 12px;
  box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
  flex: 1;
  min-width: 250px;
  max-width: 280px;
}

.phase-number {
  width: 50px;
  height: 50px;
  background: #409eff;
  color: white;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.5rem;
  font-weight: bold;
  margin-right: 20px;
  flex-shrink: 0;
}

.phase-content {
  flex: 1;
}

.phase-title {
  font-size: 1.1rem;
  font-weight: bold;
  margin-bottom: 8px;
  color: #2c3e50;
}

.phase-description {
  color: #606266;
  margin-bottom: 8px;
}

.phase-duration {
  color: #409eff;
  font-weight: bold;
  font-size: 0.9rem;
}

/* 统计数据区域 */
.stats-section {
  padding: 60px 0;
  background: #fff;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 30px;
  text-align: center;
}

.stat-item {
  padding: 20px;
}

.stat-number {
  font-size: 3rem;
  font-weight: bold;
  color: #409eff;
  margin-bottom: 8px;
}

.stat-label {
  color: #606266;
  font-size: 1rem;
}

/* CTA区域 */
.cta-section {
  padding: 80px 0;
  background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
  color: white;
  text-align: center;
}

.cta-title {
  font-size: 2.2rem;
  font-weight: bold;
  margin-bottom: 20px;
}

.cta-description {
  font-size: 1.1rem;
  line-height: 1.6;
  margin-bottom: 40px;
  opacity: 0.9;
  max-width: 600px;
  margin-left: auto;
  margin-right: auto;
}

/* 移动端适配 */
@media (max-width: 768px) {
  .container {
    padding: 0 15px;
  }

  .hero-section,
  .features-section,
  .learning-path-section,
  .stats-section,
  .cta-section {
    padding: 40px 0;
  }

  .hero-title {
    font-size: 2rem;
  }
  
  .hero-subtitle {
    font-size: 1rem;
  }
  
  .hero-actions {
    flex-direction: column;
    align-items: center;
    gap: 12px;
  }
  
  .section-title {
    font-size: 2rem;
    margin-bottom: 40px;
  }
  
  .features-grid {
    grid-template-columns: 1fr;
    gap: 20px;
  }
  
  .learning-phases {
    flex-direction: column;
    gap: 16px;
  }
  
  .phase-item {
    max-width: 100%;
    flex-direction: column;
    text-align: center;
    padding: 20px;
  }

  .phase-number {
    margin-right: 0;
    margin-bottom: 16px;
  }
  
  .stats-grid {
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
  }
  
  .cta-title {
    font-size: 1.8rem;
  }
}
</style> 
<template>
  <div class="dashboard">
    <!-- 欢迎横幅 -->
    <div class="welcome-banner">
      <div class="welcome-content">
        <div class="welcome-text">
          <h1>欢迎回来，{{ authStore.userName }}！</h1>
          <p>继续你的编程学习之旅，距离目标更近一步</p>
        </div>
        <div class="welcome-actions">
          <el-button type="primary" @click="$router.push('/checkin')">
            今日打卡
          </el-button>
          <el-button @click="$router.push('/progress')">
            查看进度
          </el-button>
        </div>
      </div>
    </div>

    <!-- 统计卡片 -->
    <el-row :gutter="24" class="stats-cards">
      <el-col :xs="12" :sm="6">
        <el-card class="stat-card">
          <div class="stat-content">
            <div class="stat-icon progress-icon">
              📊
            </div>
            <div class="stat-info">
              <div class="stat-number">25/54</div>
              <div class="stat-label">学习进度</div>
            </div>
          </div>
        </el-card>
      </el-col>
      
      <el-col :xs="12" :sm="6">
        <el-card class="stat-card">
          <div class="stat-content">
            <div class="stat-icon checkin-icon">
              📅
            </div>
            <div class="stat-info">
              <div class="stat-number">15</div>
              <div class="stat-label">连续打卡</div>
            </div>
          </div>
        </el-card>
      </el-col>
      
      <el-col :xs="12" :sm="6">
        <el-card class="stat-card">
          <div class="stat-content">
            <div class="stat-icon time-icon">
              ⏰
            </div>
            <div class="stat-info">
              <div class="stat-number">128h</div>
              <div class="stat-label">学习时长</div>
            </div>
          </div>
        </el-card>
      </el-col>
      
      <el-col :xs="12" :sm="6">
        <el-card class="stat-card">
          <div class="stat-content">
            <div class="stat-icon rank-icon">
              🏆
            </div>
            <div class="stat-info">
              <div class="stat-number">#3</div>
              <div class="stat-label">班级排名</div>
            </div>
          </div>
        </el-card>
      </el-col>
    </el-row>

    <!-- 主要内容区域 -->
    <el-row :gutter="24" class="main-content">
      <!-- 学习进度 -->
      <el-col :xs="24" :lg="16">
        <el-card class="progress-card">
          <template #header>
            <div class="card-header">
              <span>📊 学习进度概览</span>
              <el-button type="text" @click="$router.push('/progress')">查看详情</el-button>
            </div>
          </template>
          
          <div class="progress-overview">
            <div class="phase-progress" v-for="phase in learningPhases" :key="phase.id">
              <div class="phase-info">
                <span class="phase-name">{{ phase.name }}</span>
                <span class="phase-status">{{ phase.completed }}/{{ phase.total }}</span>
              </div>
              <el-progress 
                :percentage="Math.round((phase.completed / phase.total) * 100)"
                :status="phase.completed === phase.total ? 'success' : ''"
                :stroke-width="8"
              />
            </div>
          </div>
        </el-card>
      </el-col>

      <!-- 最新动态 -->
      <el-col :xs="24" :lg="8">
        <el-card class="activity-card">
          <template #header>
            <div class="card-header">
              <span>📢 最新动态</span>
              <el-button type="text">查看更多</el-button>
            </div>
          </template>
          
          <div class="activity-list">
            <div class="activity-item" v-for="activity in recentActivities" :key="activity.id">
              <div class="activity-icon">
                {{ getActivityIcon(activity.icon) }}
              </div>
              <div class="activity-content">
                <div class="activity-text">{{ activity.text }}</div>
                <div class="activity-time">{{ activity.time }}</div>
              </div>
            </div>
          </div>
        </el-card>
      </el-col>
    </el-row>

    <!-- 快速操作 -->
    <el-card class="quick-actions-card">
      <template #header>
        <span>🚀 快速操作</span>
      </template>
      
      <div class="quick-actions">
        <div class="action-item" @click="$router.push('/resources')">
          <div class="action-icon">📚</div>
          <span class="action-text">学习资源</span>
        </div>
        
        <div class="action-item" @click="$router.push('/checkin')">
          <div class="action-icon">📅</div>
          <span class="action-text">每日打卡</span>
        </div>
        
        <div class="action-item" @click="$router.push('/leaderboard')">
          <div class="action-icon">🏆</div>
          <span class="action-text">排行榜</span>
        </div>
        
        <div class="action-item" @click="$router.push('/profile')">
          <div class="action-icon">👤</div>
          <span class="action-text">个人中心</span>
        </div>
      </div>
    </el-card>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

// 获取活动图标
const getActivityIcon = (iconName) => {
  const iconMap = {
    'Trophy': '🏆',
    'Calendar': '📅',
    'Star': '⭐',
    'Collection': '📚'
  }
  return iconMap[iconName] || '📌'
}

// 学习阶段进度数据
const learningPhases = ref([
  { id: 1, name: 'Web技术基础', completed: 14, total: 14 },
  { id: 2, name: 'C#编程基础', completed: 8, total: 14 },
  { id: 3, name: 'Vue.js框架', completed: 3, total: 10 },
  { id: 4, name: '浏览器自动化', completed: 0, total: 16 }
])

// 最新动态数据
const recentActivities = ref([
  {
    id: 1,
    icon: 'Trophy',
    text: '恭喜你完成了第14天的学习任务！',
    time: '2小时前'
  },
  {
    id: 2,
    icon: 'Calendar',
    text: '连续打卡已达15天，继续保持！',
    time: '1天前'
  },
  {
    id: 3,
    icon: 'Star',
    text: '你的排名上升到班级第3名',
    time: '2天前'
  },
  {
    id: 4,
    icon: 'Collection',
    text: '新增了Vue.js进阶学习资源',
    time: '3天前'
  }
])
</script>

<style scoped>
.dashboard {
  max-width: 1200px;
  margin: 0 auto;
}

.welcome-banner {
  background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
  border-radius: 12px;
  padding: 40px;
  margin-bottom: 24px;
  color: white;
}

.welcome-content {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 20px;
}

.welcome-text h1 {
  font-size: 2rem;
  margin: 0 0 8px 0;
}

.welcome-text p {
  font-size: 1.1rem;
  margin: 0;
  opacity: 0.9;
}

.welcome-actions {
  display: flex;
  gap: 12px;
}

.stats-cards {
  margin-bottom: 24px;
}

.stat-card {
  height: 100%;
}

.stat-content {
  display: flex;
  align-items: center;
  gap: 16px;
}

.stat-icon {
  width: 50px;
  height: 50px;
  border-radius: 12px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 24px;
  color: white;
}

.progress-icon { background: #409eff; }
.checkin-icon { background: #67c23a; }
.time-icon { background: #e6a23c; }
.rank-icon { background: #f56c6c; }

.stat-number {
  font-size: 1.8rem;
  font-weight: bold;
  color: #2c3e50;
}

.stat-label {
  color: #606266;
  font-size: 0.9rem;
}

.main-content {
  margin-bottom: 24px;
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.progress-overview {
  display: flex;
  flex-direction: column;
  gap: 20px;
}

.phase-progress {
  display: flex;
  flex-direction: column;
  gap: 8px;
}

.phase-info {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.phase-name {
  font-weight: 500;
  color: #2c3e50;
}

.phase-status {
  color: #606266;
  font-size: 0.9rem;
}

.activity-list {
  display: flex;
  flex-direction: column;
  gap: 16px;
}

.activity-item {
  display: flex;
  align-items: flex-start;
  gap: 12px;
}

.activity-icon {
  width: 32px;
  height: 32px;
  background: #f0f2f5;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #409eff;
  flex-shrink: 0;
}

.activity-content {
  flex: 1;
}

.activity-text {
  color: #2c3e50;
  font-size: 0.9rem;
  line-height: 1.4;
}

.activity-time {
  color: #909399;
  font-size: 0.8rem;
  margin-top: 4px;
}

.quick-actions {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
  gap: 16px;
}

.action-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  padding: 20px;
  border-radius: 8px;
  background: #f8f9fa;
  cursor: pointer;
  transition: all 0.3s;
}

.action-item:hover {
  background: #e3f2fd;
  transform: translateY(-2px);
}

.action-icon {
  font-size: 32px;
  color: #409eff;
}

.action-text {
  color: #2c3e50;
  font-weight: 500;
}

/* 移动端适配 */
@media (max-width: 768px) {
  .welcome-banner {
    padding: 24px;
  }
  
  .welcome-content {
    flex-direction: column;
    text-align: center;
  }
  
  .welcome-text h1 {
    font-size: 1.5rem;
  }
  
  .quick-actions {
    grid-template-columns: repeat(2, 1fr);
  }
}
</style> 
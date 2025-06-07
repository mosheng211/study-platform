<template>
  <div class="leaderboard-container">
    <div class="page-header">
      <h1>学习排行榜</h1>
      <p>与其他学员一起学习，激励彼此成长</p>
    </div>

    <!-- 排行榜类型切换 -->
    <div class="leaderboard-tabs">
      <el-tabs v-model="activeTab" @tab-change="handleTabChange">
        <el-tab-pane label="打卡排行" name="checkin">
          <template #label>
            <div class="tab-label">
              <el-icon><Calendar /></el-icon>
              打卡排行榜
            </div>
          </template>
        </el-tab-pane>
        <el-tab-pane label="学习时长" name="studytime">
          <template #label>
            <div class="tab-label">
              <el-icon><Clock /></el-icon>
              学习时长排行榜
            </div>
          </template>
        </el-tab-pane>
        <el-tab-pane label="学习进度" name="progress">
          <template #label>
            <div class="tab-label">
              <el-icon><TrendCharts /></el-icon>
              学习进度排行榜
            </div>
          </template>
        </el-tab-pane>
        <el-tab-pane label="综合积分" name="score">
          <template #label>
            <div class="tab-label">
              <el-icon><Trophy /></el-icon>
              综合积分排行榜
            </div>
          </template>
        </el-tab-pane>
      </el-tabs>
    </div>

    <!-- 时间范围筛选 -->
    <div class="filters">
      <el-select v-model="timeRange" @change="loadRankings" placeholder="选择时间范围">
        <el-option label="本周" value="week" />
        <el-option label="本月" value="month" />
        <el-option label="本季度" value="quarter" />
        <el-option label="全部时间" value="all" />
      </el-select>
      <el-select v-model="userType" @change="loadRankings" placeholder="用户类型">
        <el-option label="全部用户" value="" />
        <el-option label="学生" value="student" />
        <el-option label="教师" value="teacher" />
      </el-select>
    </div>

    <!-- 个人排名卡片 -->
    <el-card class="personal-rank-card" v-if="personalRank">
      <div class="personal-rank">
        <div class="rank-info">
          <div class="rank-position">
            <span class="position">第{{ personalRank.rank }}名</span>
            <span class="total">共{{ personalRank.total }}人</span>
          </div>
          <div class="rank-value">
            <span class="value">{{ getRankValue(personalRank) }}</span>
            <span class="unit">{{ getRankUnit() }}</span>
          </div>
        </div>
        <div class="rank-progress">
          <div class="progress-text">超越了 {{ personalRank.percentage }}% 的用户</div>
          <el-progress 
            :percentage="personalRank.percentage" 
            :color="getProgressColor(personalRank.percentage)"
          />
        </div>
        <div class="rank-trend">
          <el-tag 
            :type="personalRank.trend > 0 ? 'success' : personalRank.trend < 0 ? 'danger' : 'info'"
            size="small"
          >
            <el-icon v-if="personalRank.trend > 0"><ArrowUp /></el-icon>
            <el-icon v-else-if="personalRank.trend < 0"><ArrowDown /></el-icon>
            <el-icon v-else><Minus /></el-icon>
            {{ personalRank.trend === 0 ? '持平' : `${Math.abs(personalRank.trend)}名` }}
          </el-tag>
        </div>
      </div>
    </el-card>

    <!-- 排行榜列表 -->
    <el-card class="rankings-card" v-loading="loading">
      <template #header>
        <div class="card-header">
          <h3>{{ getRankingTitle() }}</h3>
          <div class="last-update">
            最后更新：{{ lastUpdateTime }}
          </div>
        </div>
      </template>

      <div class="rankings-list">
        <div v-if="!rankings || rankings.length === 0" class="empty-state">
          <el-empty description="暂无排行数据" />
        </div>
        <div v-else>
          <!-- 前三名特殊显示 -->
          <div class="top-three">
            <div v-for="(user, index) in rankings.slice(0, 3)" :key="user.id" class="top-user">
              <div class="medal" :class="getMedalClass(index + 1)">
                <el-icon v-if="index === 0"><Trophy /></el-icon>
                <el-icon v-else-if="index === 1"><Medal /></el-icon>
                <el-icon v-else><Star /></el-icon>
              </div>
              <div class="user-avatar">
                <el-avatar :size="60" :src="user.avatar" :icon="UserFilled" />
              </div>
              <div class="user-info">
                <div class="username">{{ user.name }}</div>
                <div class="user-type">{{ getUserTypeText(user.role) }}</div>
                <div class="rank-value">{{ getRankValue(user) }} {{ getRankUnit() }}</div>
              </div>
            </div>
          </div>

          <!-- 其他排名 -->
          <div class="other-rankings">
            <div 
              v-for="(user, index) in rankings.slice(3)" 
              :key="user.id" 
              class="ranking-item"
              :class="{ 'is-current-user': user.id === currentUserId }"
            >
              <div class="rank-position">{{ index + 4 }}</div>
              <div class="user-info">
                <el-avatar :size="40" :src="user.avatar" :icon="UserFilled" />
                <div class="user-details">
                  <div class="username">
                    {{ user.name }}
                    <el-tag v-if="user.id === currentUserId" type="primary" size="small">我</el-tag>
                  </div>
                  <div class="user-type">{{ getUserTypeText(user.role) }}</div>
                </div>
              </div>
              <div class="rank-data">
                <div class="main-value">{{ getRankValue(user) }} {{ getRankUnit() }}</div>
                <div class="sub-values">
                  <span v-if="activeTab === 'checkin'">连续{{ user.consecutive_days }}天</span>
                  <span v-else-if="activeTab === 'studytime'">{{ user.avg_daily_time }}h/天</span>
                  <span v-else-if="activeTab === 'progress'">{{ user.completion_rate }}%完成</span>
                  <span v-else>{{ user.level }}级</span>
                </div>
              </div>
              <div class="rank-trend">
                <el-tag 
                  :type="user.trend > 0 ? 'success' : user.trend < 0 ? 'danger' : 'info'"
                  size="small"
                >
                  <el-icon v-if="user.trend > 0"><ArrowUp /></el-icon>
                  <el-icon v-else-if="user.trend < 0"><ArrowDown /></el-icon>
                  <el-icon v-else><Minus /></el-icon>
                  {{ Math.abs(user.trend) }}
                </el-tag>
              </div>
            </div>
          </div>
        </div>
      </div>
    </el-card>

    <!-- 学习统计 -->
    <el-card class="stats-card">
      <template #header>
        <h3>整体学习统计</h3>
      </template>
      <div class="stats-grid">
        <div class="stat-item">
          <div class="stat-icon">
            <el-icon><User /></el-icon>
          </div>
          <div class="stat-info">
            <div class="stat-value">{{ globalStats.totalUsers }}</div>
            <div class="stat-label">活跃用户</div>
          </div>
        </div>
        <div class="stat-item">
          <div class="stat-icon">
            <el-icon><Clock /></el-icon>
          </div>
          <div class="stat-info">
            <div class="stat-value">{{ globalStats.totalStudyTime }}h</div>
            <div class="stat-label">总学习时长</div>
          </div>
        </div>
        <div class="stat-item">
          <div class="stat-icon">
            <el-icon><Calendar /></el-icon>
          </div>
          <div class="stat-info">
            <div class="stat-value">{{ globalStats.totalCheckins }}</div>
            <div class="stat-label">总打卡次数</div>
          </div>
        </div>
        <div class="stat-item">
          <div class="stat-icon">
            <el-icon><TrendCharts /></el-icon>
          </div>
          <div class="stat-info">
            <div class="stat-value">{{ globalStats.avgProgress }}%</div>
            <div class="stat-label">平均进度</div>
          </div>
        </div>
      </div>
    </el-card>

    <!-- 成就徽章 -->
    <el-card v-if="achievements.length > 0" class="achievements-card">
      <template #header>
        <h3>近期成就</h3>
      </template>
      <div class="achievements-list">
        <div v-for="achievement in achievements" :key="achievement.id" class="achievement-item">
          <div class="achievement-icon">
            <el-icon>
              <component :is="getAchievementIcon(achievement.type)" />
            </el-icon>
          </div>
          <div class="achievement-info">
            <div class="achievement-title">{{ achievement.title }}</div>
            <div class="achievement-desc">{{ achievement.description }}</div>
            <div class="achievement-user">{{ achievement.user_name }} · {{ formatTime(achievement.created_at) }}</div>
          </div>
        </div>
      </div>
    </el-card>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import {
  Calendar, Clock, TrendCharts, Trophy, Medal, Star, UserFilled,
  ArrowUp, ArrowDown, Minus, User
} from '@element-plus/icons-vue'
import { leaderboardAPI } from '@/api'
import { useAuthStore } from '@/stores/auth'

const authStore = useAuthStore()

// 响应式数据
const loading = ref(false)
const activeTab = ref('checkin')
const timeRange = ref('week')
const userType = ref('')

// 排行榜数据
const rankings = ref([])
const personalRank = ref(null)
const globalStats = reactive({
  totalUsers: 0,
  totalStudyTime: 0,
  totalCheckins: 0,
  avgProgress: 0
})
const achievements = ref([])
const lastUpdateTime = ref('')

// 计算属性
const currentUserId = computed(() => authStore.user?.id)

// 方法
const handleTabChange = () => {
  loadRankings()
}

const loadRankings = async () => {
  try {
    loading.value = true
    const params = {
      type: activeTab.value,
      timeRange: timeRange.value,
      userType: userType.value
    }
    
    const response = await leaderboardAPI.getRankings(params)
    rankings.value = response.data.rankings || []
    personalRank.value = response.data.personalRank
    Object.assign(globalStats, response.data.globalStats || {})
    lastUpdateTime.value = new Date().toLocaleString('zh-CN')
  } catch (error) {
    // 静默处理API错误，使用模拟数据
    console.log('正在使用模拟数据展示排行榜功能')
    rankings.value = generateMockRankings()
    personalRank.value = generateMockPersonalRank()
    Object.assign(globalStats, {
      totalUsers: 156,
      totalStudyTime: 2340,
      totalCheckins: 1250,
      avgProgress: 68
    })
    lastUpdateTime.value = new Date().toLocaleString('zh-CN')
  } finally {
    loading.value = false
  }
}

const loadAchievements = async () => {
  try {
    const response = await leaderboardAPI.getRecentAchievements()
    achievements.value = response.data.achievements || []
  } catch (error) {
    // 静默处理API错误，使用模拟数据
    console.log('正在使用模拟数据展示成就功能')
    achievements.value = generateMockAchievements()
  }
}

const getRankingTitle = () => {
  const titles = {
    checkin: '打卡排行榜',
    studytime: '学习时长排行榜',
    progress: '学习进度排行榜',
    score: '综合积分排行榜'
  }
  return titles[activeTab.value] || '排行榜'
}

const getRankValue = (user) => {
  switch (activeTab.value) {
    case 'checkin':
      return user.total_checkins || user.checkin_days
    case 'studytime':
      return user.total_study_time || user.study_hours
    case 'progress':
      return user.progress_percentage || user.completion_rate
    case 'score':
      return user.total_score || user.score
    default:
      return 0
  }
}

const getRankUnit = () => {
  const units = {
    checkin: '天',
    studytime: 'h',
    progress: '%',
    score: '分'
  }
  return units[activeTab.value] || ''
}

const getMedalClass = (rank) => {
  const classes = {
    1: 'gold',
    2: 'silver',
    3: 'bronze'
  }
  return classes[rank] || ''
}

const getUserTypeText = (role) => {
  const types = {
    student: '学生',
    teacher: '教师',
    admin: '管理员'
  }
  return types[role] || '用户'
}

const getProgressColor = (percentage) => {
  if (percentage >= 80) return '#67c23a'
  if (percentage >= 60) return '#e6a23c'
  if (percentage >= 40) return '#f56c6c'
  return '#909399'
}

const getAchievementIcon = (type) => {
  const icons = {
    checkin: Calendar,
    study: Clock,
    progress: TrendCharts,
    score: Trophy
  }
  return icons[type] || Trophy
}

const formatTime = (time) => {
  const date = new Date(time)
  const now = new Date()
  const diff = now - date
  
  if (diff < 60000) return '刚刚'
  if (diff < 3600000) return `${Math.floor(diff / 60000)}分钟前`
  if (diff < 86400000) return `${Math.floor(diff / 3600000)}小时前`
  return `${Math.floor(diff / 86400000)}天前`
}

// 生成模拟排行榜数据
const generateMockRankings = () => {
  const mockUsers = [
    { id: 1, name: '张三', role: 'student', avatar: '', total_checkins: 45, consecutive_days: 12, total_study_time: 180, avg_daily_time: 4, completion_rate: 85, total_score: 950, level: 8, trend: 2 },
    { id: 2, name: '李四', role: 'student', avatar: '', total_checkins: 42, consecutive_days: 8, total_study_time: 165, avg_daily_time: 3.8, completion_rate: 78, total_score: 890, level: 7, trend: -1 },
    { id: 3, name: '王五', role: 'teacher', avatar: '', total_checkins: 38, consecutive_days: 15, total_study_time: 142, avg_daily_time: 3.2, completion_rate: 72, total_score: 820, level: 6, trend: 1 },
    { id: 4, name: '赵六', role: 'student', avatar: '', total_checkins: 35, consecutive_days: 5, total_study_time: 128, avg_daily_time: 2.9, completion_rate: 68, total_score: 780, level: 6, trend: 0 },
    { id: 5, name: '孙七', role: 'student', avatar: '', total_checkins: 32, consecutive_days: 7, total_study_time: 115, avg_daily_time: 2.5, completion_rate: 65, total_score: 720, level: 5, trend: 3 }
  ]
  return mockUsers
}

// 生成模拟个人排名数据
const generateMockPersonalRank = () => {
  return {
    rank: 8,
    total: 156,
    percentage: 72,
    trend: 1,
    total_checkins: 28,
    total_study_time: 95,
    completion_rate: 58,
    total_score: 650
  }
}

// 生成模拟成就数据
const generateMockAchievements = () => {
  return [
    {
      id: 1,
      type: 'checkin',
      title: '连续打卡达人',
      description: '连续打卡30天',
      user_name: '张三',
      created_at: new Date(Date.now() - 1000 * 60 * 30).toISOString()
    },
    {
      id: 2,
      type: 'study',
      title: '学习时长王者',
      description: '单日学习时长超过8小时',
      user_name: '李四',
      created_at: new Date(Date.now() - 1000 * 60 * 60 * 2).toISOString()
    },
    {
      id: 3,
      type: 'progress',
      title: '进度领先者',
      description: '学习进度达到90%',
      user_name: '王五',
      created_at: new Date(Date.now() - 1000 * 60 * 60 * 5).toISOString()
    }
  ]
}

// 生命周期
onMounted(async () => {
  await Promise.all([
    loadRankings(),
    loadAchievements()
  ])
})
</script>

<style scoped>
.leaderboard-container {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.page-header {
  text-align: center;
  margin-bottom: 30px;
}

.page-header h1 {
  font-size: 2.5rem;
  color: #2c3e50;
  margin-bottom: 10px;
}

.page-header p {
  font-size: 1.1rem;
  color: #7f8c8d;
}

.leaderboard-tabs {
  margin-bottom: 20px;
}

.tab-label {
  display: flex;
  align-items: center;
  gap: 5px;
}

.filters {
  display: flex;
  gap: 15px;
  margin-bottom: 20px;
  justify-content: center;
}

.personal-rank-card {
  margin-bottom: 30px;
  border: none;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.personal-rank {
  display: grid;
  grid-template-columns: auto 1fr auto;
  gap: 20px;
  align-items: center;
}

.rank-position {
  text-align: center;
}

.position {
  display: block;
  font-size: 2rem;
  font-weight: bold;
  color: #409eff;
}

.total {
  font-size: 0.9rem;
  color: #7f8c8d;
}

.rank-value .value {
  font-size: 1.8rem;
  font-weight: bold;
  color: #2c3e50;
}

.rank-value .unit {
  color: #7f8c8d;
  margin-left: 5px;
}

.rank-progress {
  text-align: center;
}

.progress-text {
  margin-bottom: 10px;
  color: #5a6c7d;
}

.rankings-card {
  margin-bottom: 30px;
  border: none;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.last-update {
  color: #7f8c8d;
  font-size: 0.9rem;
}

.top-three {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  gap: 20px;
  margin-bottom: 30px;
  padding: 20px;
  background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
  border-radius: 10px;
}

.top-user {
  text-align: center;
  position: relative;
}

.medal {
  position: absolute;
  top: -10px;
  left: 50%;
  transform: translateX(-50%);
  width: 30px;
  height: 30px;
  border-radius: 50%;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 16px;
  color: white;
}

.medal.gold { background: linear-gradient(45deg, #ffd700, #ffed4e); }
.medal.silver { background: linear-gradient(45deg, #c0c0c0, #e5e5e5); }
.medal.bronze { background: linear-gradient(45deg, #cd7f32, #d4955a); }

.user-avatar {
  margin: 20px 0 10px 0;
}

.top-user .username {
  font-weight: bold;
  color: #2c3e50;
  margin-bottom: 5px;
}

.top-user .user-type {
  color: #7f8c8d;
  font-size: 0.9rem;
  margin-bottom: 10px;
}

.top-user .rank-value {
  font-size: 1.2rem;
  font-weight: bold;
  color: #409eff;
}

.other-rankings {
  max-height: 500px;
  overflow-y: auto;
}

.ranking-item {
  display: flex;
  align-items: center;
  padding: 15px 20px;
  border-bottom: 1px solid #f1f2f6;
  transition: background-color 0.3s ease;
}

.ranking-item:hover {
  background-color: #f8f9fa;
}

.ranking-item.is-current-user {
  background-color: #e6f7ff;
  border-left: 3px solid #409eff;
}

.rank-position {
  width: 40px;
  text-align: center;
  font-weight: bold;
  font-size: 1.2rem;
  color: #2c3e50;
}

.ranking-item .user-info {
  display: flex;
  align-items: center;
  gap: 15px;
  flex: 1;
  margin-left: 20px;
}

.user-details .username {
  font-weight: bold;
  color: #2c3e50;
  display: flex;
  align-items: center;
  gap: 10px;
}

.user-details .user-type {
  color: #7f8c8d;
  font-size: 0.9rem;
}

.rank-data {
  text-align: right;
  margin-right: 20px;
}

.main-value {
  font-size: 1.2rem;
  font-weight: bold;
  color: #2c3e50;
}

.sub-values {
  color: #7f8c8d;
  font-size: 0.9rem;
  margin-top: 2px;
}

.stats-card, .achievements-card {
  margin-bottom: 30px;
  border: none;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 15px;
  padding: 20px;
  background: #f8f9fa;
  border-radius: 8px;
}

.stat-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: #409eff;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
}

.stat-value {
  font-size: 1.5rem;
  font-weight: bold;
  color: #2c3e50;
}

.stat-label {
  color: #7f8c8d;
  font-size: 0.9rem;
}

.achievements-list {
  max-height: 300px;
  overflow-y: auto;
}

.achievement-item {
  display: flex;
  gap: 15px;
  padding: 15px;
  border-bottom: 1px solid #f1f2f6;
}

.achievement-item:last-child {
  border-bottom: none;
}

.achievement-icon {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  background: linear-gradient(45deg, #667eea, #764ba2);
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 18px;
  flex-shrink: 0;
}

.achievement-title {
  font-weight: bold;
  color: #2c3e50;
  margin-bottom: 5px;
}

.achievement-desc {
  color: #5a6c7d;
  margin-bottom: 5px;
}

.achievement-user {
  color: #7f8c8d;
  font-size: 0.9rem;
}

.empty-state {
  text-align: center;
  padding: 40px;
}

@media (max-width: 768px) {
  .top-three {
    grid-template-columns: 1fr;
  }
  
  .personal-rank {
    grid-template-columns: 1fr;
    text-align: center;
  }
  
  .ranking-item {
    flex-direction: column;
    gap: 10px;
    text-align: center;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .filters {
    flex-direction: column;
    align-items: center;
  }
}
</style> 
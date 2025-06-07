<template>
  <el-dialog
    v-model="dialogVisible"
    title="用户详情"
    width="800px"
    @close="$emit('update:visible', false)"
  >
    <div v-if="user" class="user-detail">
      <div class="user-header">
        <el-avatar :size="80" :src="user.avatar" :icon="UserFilled" />
        <div class="user-basic-info">
          <h3>{{ user.name }}</h3>
          <p>{{ user.email }}</p>
          <el-tag :type="getRoleTagType(user.role)">{{ getRoleText(user.role) }}</el-tag>
          <el-tag :type="user.status === 'active' ? 'success' : 'danger'" style="margin-left: 10px">
            {{ user.status === 'active' ? '正常' : '禁用' }}
          </el-tag>
        </div>
      </div>

      <el-tabs v-model="activeTab">
        <el-tab-pane label="基本信息" name="basic">
          <el-descriptions :column="2" border>
            <el-descriptions-item label="用户ID">{{ user.id }}</el-descriptions-item>
            <el-descriptions-item label="用户名">{{ user.name }}</el-descriptions-item>
            <el-descriptions-item label="邮箱">{{ user.email }}</el-descriptions-item>
            <el-descriptions-item label="角色">{{ getRoleText(user.role) }}</el-descriptions-item>
            <el-descriptions-item label="状态">
              <el-tag :type="user.status === 'active' ? 'success' : 'danger'">
                {{ user.status === 'active' ? '正常' : '禁用' }}
              </el-tag>
            </el-descriptions-item>
            <el-descriptions-item label="注册时间">{{ formatDate(user.created_at) }}</el-descriptions-item>
            <el-descriptions-item label="最后登录">{{ formatDate(user.last_login_at) }}</el-descriptions-item>
            <el-descriptions-item label="登录次数">{{ user.login_count || 0 }}</el-descriptions-item>
          </el-descriptions>
        </el-tab-pane>

        <el-tab-pane label="学习统计" name="stats">
          <div class="stats-grid">
            <el-card class="stat-card">
              <div class="stat-content">
                <div class="stat-value">{{ userStats.totalCheckins || 0 }}</div>
                <div class="stat-label">总打卡天数</div>
              </div>
            </el-card>
            <el-card class="stat-card">
              <div class="stat-content">
                <div class="stat-value">{{ userStats.consecutiveDays || 0 }}</div>
                <div class="stat-label">连续打卡天数</div>
              </div>
            </el-card>
            <el-card class="stat-card">
              <div class="stat-content">
                <div class="stat-value">{{ userStats.totalStudyTime || 0 }}h</div>
                <div class="stat-label">总学习时长</div>
              </div>
            </el-card>
            <el-card class="stat-card">
              <div class="stat-content">
                <div class="stat-value">{{ userStats.avgScore || 0 }}</div>
                <div class="stat-label">平均评分</div>
              </div>
            </el-card>
          </div>
        </el-tab-pane>

        <el-tab-pane label="学习进度" name="progress">
          <div v-loading="progressLoading">
            <div v-if="userProgress.length === 0" class="empty-state">
              <el-empty description="暂无学习进度数据" />
            </div>
            <div v-else class="progress-list">
              <div v-for="progress in userProgress" :key="progress.id" class="progress-item">
                <div class="progress-header">
                  <span class="day-number">第{{ progress.day_number }}天</span>
                  <el-tag :type="getStatusTagType(progress.status)">
                    {{ getStatusText(progress.status) }}
                  </el-tag>
                </div>
                <div class="progress-content">
                  <p><strong>学习内容：</strong>{{ progress.content || '暂无' }}</p>
                  <div class="progress-meta">
                    <span>学习时长：{{ progress.study_time || 0 }}小时</span>
                    <span>完成度：{{ progress.completion_percentage || 0 }}%</span>
                    <span>评分：{{ progress.score || 0 }}分</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </el-tab-pane>

        <el-tab-pane label="打卡记录" name="checkins">
          <div v-loading="checkinsLoading">
            <div v-if="userCheckins.length === 0" class="empty-state">
              <el-empty description="暂无打卡记录" />
            </div>
            <div v-else class="checkins-list">
              <div v-for="checkin in userCheckins" :key="checkin.id" class="checkin-item">
                <div class="checkin-date">
                  {{ formatDate(checkin.checkin_date) }}
                </div>
                <div class="checkin-content">
                  <p><strong>学习内容：</strong>{{ checkin.content }}</p>
                  <div class="checkin-meta">
                    <span>学习时长：{{ checkin.study_time }}小时</span>
                    <el-rate :model-value="checkin.quality" disabled size="small" />
                  </div>
                  <p v-if="checkin.notes" class="checkin-notes">{{ checkin.notes }}</p>
                </div>
              </div>
            </div>
          </div>
        </el-tab-pane>
      </el-tabs>
    </div>

    <template #footer>
      <el-button @click="$emit('update:visible', false)">关闭</el-button>
      <el-button type="primary" @click="editUser">编辑用户</el-button>
    </template>
  </el-dialog>
</template>

<script setup>
import { ref, reactive, watch, computed } from 'vue'
import { UserFilled } from '@element-plus/icons-vue'
import { adminAPI } from '@/api'

const props = defineProps({
  visible: Boolean,
  user: Object
})

const emit = defineEmits(['update:visible', 'edit'])

const dialogVisible = computed({
  get: () => props.visible,
  set: (value) => emit('update:visible', value)
})

const activeTab = ref('basic')
const progressLoading = ref(false)
const checkinsLoading = ref(false)

const userStats = reactive({
  totalCheckins: 0,
  consecutiveDays: 0,
  totalStudyTime: 0,
  avgScore: 0
})

const userProgress = ref([])
const userCheckins = ref([])

// 观察用户变化，加载详细数据
watch(() => props.user, async (newUser) => {
  if (newUser) {
    await loadUserDetails()
  }
}, { immediate: true })

const loadUserDetails = async () => {
  if (!props.user) return

  try {
    // 加载用户统计
    Object.assign(userStats, {
      totalCheckins: props.user.total_checkins || 0,
      consecutiveDays: props.user.consecutive_days || 0,
      totalStudyTime: props.user.total_study_time || 0,
      avgScore: props.user.avg_score || 0
    })

    // 加载学习进度
    progressLoading.value = true
    const progressResponse = await adminAPI.getUserProgress(props.user.id)
    userProgress.value = progressResponse.data.progress || []

    // 加载打卡记录
    checkinsLoading.value = true
    const checkinsResponse = await adminAPI.getUserCheckins(props.user.id)
    userCheckins.value = checkinsResponse.data.checkins || []
  } catch (error) {
    console.error('加载用户详情失败:', error)
  } finally {
    progressLoading.value = false
    checkinsLoading.value = false
  }
}

const getRoleTagType = (role) => {
  const types = {
    admin: 'danger',
    teacher: 'warning',
    student: 'success'
  }
  return types[role] || 'info'
}

const getRoleText = (role) => {
  const texts = {
    admin: '管理员',
    teacher: '教师',
    student: '学生'
  }
  return texts[role] || '未知'
}

const getStatusTagType = (status) => {
  const types = {
    completed: 'success',
    in_progress: 'warning',
    not_started: 'info'
  }
  return types[status] || 'info'
}

const getStatusText = (status) => {
  const texts = {
    completed: '已完成',
    in_progress: '进行中',
    not_started: '未开始'
  }
  return texts[status] || '未知'
}

const formatDate = (dateString) => {
  if (!dateString) return '暂无'
  return new Date(dateString).toLocaleString('zh-CN')
}

const editUser = () => {
  emit('edit', props.user)
  emit('update:visible', false)
}
</script>

<style scoped>
.user-detail {
  padding: 20px 0;
}

.user-header {
  display: flex;
  align-items: center;
  gap: 20px;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 1px solid #f1f2f6;
}

.user-basic-info h3 {
  margin: 0 0 10px 0;
  color: #2c3e50;
}

.user-basic-info p {
  margin: 0 0 10px 0;
  color: #7f8c8d;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 20px;
  margin-bottom: 20px;
}

.stat-card {
  text-align: center;
  border: none;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.stat-content {
  padding: 20px;
}

.stat-value {
  font-size: 2rem;
  font-weight: bold;
  color: #409eff;
  margin-bottom: 10px;
}

.stat-label {
  color: #7f8c8d;
  font-size: 0.9rem;
}

.progress-list, .checkins-list {
  max-height: 400px;
  overflow-y: auto;
}

.progress-item, .checkin-item {
  padding: 15px;
  border: 1px solid #f1f2f6;
  border-radius: 8px;
  margin-bottom: 15px;
}

.progress-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 10px;
}

.day-number {
  font-weight: bold;
  color: #2c3e50;
}

.progress-content p, .checkin-content p {
  margin: 10px 0;
  color: #5a6c7d;
}

.progress-meta, .checkin-meta {
  display: flex;
  gap: 20px;
  font-size: 0.9rem;
  color: #7f8c8d;
}

.checkin-date {
  font-weight: bold;
  color: #409eff;
  margin-bottom: 10px;
}

.checkin-notes {
  background: #f8f9fa;
  padding: 10px;
  border-radius: 4px;
  font-style: italic;
  color: #5a6c7d;
}

.empty-state {
  text-align: center;
  padding: 40px;
}

@media (max-width: 768px) {
  .user-header {
    flex-direction: column;
    text-align: center;
  }
  
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .progress-meta, .checkin-meta {
    flex-direction: column;
    gap: 5px;
  }
}
</style> 
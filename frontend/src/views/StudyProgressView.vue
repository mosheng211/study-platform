<template>
  <div class="study-progress">
    <!-- 页面头部 -->
    <div class="page-header">
      <h1>📈 我的学习进度</h1>
      <p>跟踪你的54天编程学习之旅</p>
    </div>

    <!-- 总体统计 -->
    <div class="stats-overview" v-if="stats">
      <el-row :gutter="20">
        <el-col :span="6">
          <div class="stat-card">
            <div class="stat-number">{{ stats.completed_days }}</div>
            <div class="stat-label">已完成天数</div>
            <div class="stat-total">/ {{ stats.total_days }}</div>
          </div>
        </el-col>
        <el-col :span="6">
          <div class="stat-card">
            <div class="stat-number">{{ Math.round((stats.completed_days / stats.total_days) * 100) }}%</div>
            <div class="stat-label">完成百分比</div>
          </div>
        </el-col>
        <el-col :span="6">
          <div class="stat-card">
            <div class="stat-number">{{ stats.total_study_hours || 0 }}</div>
            <div class="stat-label">总学习时长(小时)</div>
          </div>
        </el-col>
        <el-col :span="6">
          <div class="stat-card">
            <div class="stat-number">{{ stats.average_score ? stats.average_score.toFixed(1) : 0 }}</div>
            <div class="stat-label">平均分数</div>
          </div>
        </el-col>
      </el-row>
    </div>

    <!-- 阶段进度 -->
    <div class="phase-progress">
      <h2>📚 各阶段进度</h2>
      <el-row :gutter="20">
        <el-col :span="6" v-for="phase in phases" :key="phase.key">
          <div class="phase-card">
            <div class="phase-header">
              <h3>{{ phase.name }}</h3>
              <span class="phase-duration">{{ phase.duration }}</span>
            </div>
            <div class="phase-stats">
              <el-progress 
                :percentage="getPhaseProgress(phase.key)" 
                :status="getPhaseProgress(phase.key) === 100 ? 'success' : ''"
              />
              <div class="phase-details">
                <span>完成: {{ getPhaseCompleted(phase.key) }}/{{ getPhaseTotal(phase.key) }}</span>
                <span>进行中: {{ getPhaseInProgress(phase.key) }}</span>
              </div>
            </div>
          </div>
        </el-col>
      </el-row>
    </div>

    <!-- 筛选和搜索 -->
    <div class="filter-section">
      <el-row :gutter="20" align="middle">
        <el-col :span="8">
          <el-select v-model="filterPhase" placeholder="筛选阶段" @change="loadProgress" clearable>
            <el-option label="全部阶段" value="" />
            <el-option v-for="phase in phases" :key="phase.key" :label="phase.name" :value="phase.key" />
          </el-select>
        </el-col>
        <el-col :span="8">
          <el-select v-model="filterStatus" placeholder="筛选状态" @change="loadProgress" clearable>
            <el-option label="全部状态" value="" />
            <el-option label="未开始" value="not_started" />
            <el-option label="进行中" value="in_progress" />
            <el-option label="已完成" value="completed" />
          </el-select>
        </el-col>
        <el-col :span="8">
          <el-button @click="initializeProgress" type="primary" v-if="!hasProgress">
            初始化学习计划
          </el-button>
          <el-button @click="loadProgress" type="primary" v-else>
            刷新数据
          </el-button>
        </el-col>
      </el-row>
    </div>

    <!-- 学习进度列表 -->
    <div class="progress-list" v-loading="loading">
      <div v-if="progressList.length === 0 && !loading" class="empty-state">
        <el-icon size="80" color="#909399"><DocumentCopy /></el-icon>
        <p>暂无学习进度记录</p>
        <el-button @click="initializeProgress" type="primary">
          初始化学习计划
        </el-button>
      </div>

      <div v-else class="progress-grid">
        <div 
          v-for="progress in progressList" 
          :key="progress.id"
          class="progress-card"
          :class="getCardClass(progress.status)"
        >
          <div class="progress-header">
            <div class="day-info">
              <span class="day-number">第 {{ progress.day_number }} 天</span>
              <el-tag :type="getStatusColor(progress.status)" size="small">
                {{ getStatusText(progress.status) }}
              </el-tag>
            </div>
            <div class="phase-badge">
              {{ getPhaseText(progress.phase) }}
            </div>
          </div>

          <div class="progress-content">
            <h3 class="progress-title">{{ progress.title }}</h3>
            <p class="progress-description">{{ progress.content }}</p>
            
            <div class="progress-meta" v-if="progress.status !== 'not_started'">
              <div class="meta-item" v-if="progress.study_hours">
                <span class="label">学习时长:</span>
                <span class="value">{{ progress.study_hours }} 小时</span>
              </div>
              <div class="meta-item" v-if="progress.score">
                <span class="label">分数:</span>
                <span class="value">{{ progress.score }} 分</span>
              </div>
              <div class="meta-item" v-if="progress.started_at">
                <span class="label">开始时间:</span>
                <span class="value">{{ formatDate(progress.started_at) }}</span>
              </div>
              <div class="meta-item" v-if="progress.completed_at">
                <span class="label">完成时间:</span>
                <span class="value">{{ formatDate(progress.completed_at) }}</span>
              </div>
            </div>

            <div class="homework-section" v-if="progress.homework_description">
              <h4>📝 作业内容</h4>
              <p>{{ progress.homework_description }}</p>
              <div v-if="progress.homework_submission" class="homework-submission">
                <strong>已提交:</strong>
                <p>{{ progress.homework_submission }}</p>
              </div>
            </div>

            <div class="notes-section" v-if="progress.notes">
              <h4>📋 学习笔记</h4>
              <p>{{ progress.notes }}</p>
            </div>
          </div>

          <div class="progress-actions">
            <el-button @click="updateProgress(progress)" size="small">
              更新进度
            </el-button>
            <el-button @click="viewDetails(progress)" size="small" type="primary">
              查看详情
            </el-button>
          </div>
        </div>
      </div>
    </div>

    <!-- 更新进度对话框 -->
    <el-dialog
      v-model="showUpdateDialog"
      :title="`更新第${editingProgress?.day_number}天进度`"
      width="60%"
      destroy-on-close
    >
      <ProgressUpdateForm
        v-if="editingProgress"
        :progress="editingProgress"
        @success="handleProgressUpdated"
        @cancel="handleCancelUpdate"
      />
    </el-dialog>

    <!-- 详情对话框 -->
    <el-dialog
      v-model="showDetailDialog"
      :title="`第${selectedProgress?.day_number}天学习详情`"
      width="70%"
      destroy-on-close
    >
      <ProgressDetail
        v-if="selectedProgress"
        :progress="selectedProgress"
        @close="showDetailDialog = false"
      />
    </el-dialog>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { progressAPI } from '@/api/index'
import { ElMessage } from 'element-plus'
import ProgressUpdateForm from '@/components/ProgressUpdateForm.vue'
import ProgressDetail from '@/components/ProgressDetail.vue'

const authStore = useAuthStore()

// 响应式数据
const loading = ref(false)
const progressList = ref([])
const stats = ref(null)
const filterPhase = ref('')
const filterStatus = ref('')

// 对话框状态
const showUpdateDialog = ref(false)
const showDetailDialog = ref(false)
const editingProgress = ref(null)
const selectedProgress = ref(null)

// 阶段配置
const phases = [
  { key: 'web-basics', name: 'Web技术基础', duration: '14天' },
  { key: 'csharp-basics', name: 'C#编程基础', duration: '14天' },
  { key: 'vue-framework', name: 'Vue.js框架', duration: '10天' },
  { key: 'automation', name: '浏览器自动化', duration: '16天' }
]

// 计算属性
const hasProgress = computed(() => progressList.value.length > 0)

// 统计方法
const getPhaseProgress = (phaseKey) => {
  const phaseItems = progressList.value.filter(p => p.phase === phaseKey)
  if (phaseItems.length === 0) return 0
  const completed = phaseItems.filter(p => p.status === 'completed').length
  return Math.round((completed / phaseItems.length) * 100)
}

const getPhaseCompleted = (phaseKey) => {
  return progressList.value.filter(p => p.phase === phaseKey && p.status === 'completed').length
}

const getPhaseTotal = (phaseKey) => {
  return progressList.value.filter(p => p.phase === phaseKey).length
}

const getPhaseInProgress = (phaseKey) => {
  return progressList.value.filter(p => p.phase === phaseKey && p.status === 'in_progress').length
}

// 辅助方法
const getStatusColor = (status) => {
  const colors = {
    'not_started': 'info',
    'in_progress': 'warning',
    'completed': 'success'
  }
  return colors[status] || 'info'
}

const getStatusText = (status) => {
  const texts = {
    'not_started': '未开始',
    'in_progress': '进行中',
    'completed': '已完成'
  }
  return texts[status] || status
}

const getPhaseText = (phase) => {
  const phaseObj = phases.find(p => p.key === phase)
  return phaseObj ? phaseObj.name : phase
}

const getCardClass = (status) => {
  return `status-${status}`
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString('zh-CN')
}

// 数据加载方法
const loadProgress = async () => {
  loading.value = true
  try {
    const params = {
      phase: filterPhase.value,
      status: filterStatus.value
    }

    const response = await progressAPI.getList(params)
    if (response.success) {
      progressList.value = response.data.progress
      stats.value = response.data.stats
    }
  } catch (error) {
    console.error('加载学习进度失败:', error)
  } finally {
    loading.value = false
  }
}

const initializeProgress = async () => {
  try {
    await progressAPI.initialize()
    ElMessage.success('学习计划初始化成功')
    await loadProgress()
  } catch (error) {
    console.error('初始化学习计划失败:', error)
  }
}

// 操作方法
const updateProgress = (progress) => {
  editingProgress.value = progress
  showUpdateDialog.value = true
}

const viewDetails = (progress) => {
  selectedProgress.value = progress
  showDetailDialog.value = true
}

const handleProgressUpdated = () => {
  showUpdateDialog.value = false
  editingProgress.value = null
  loadProgress()
}

const handleCancelUpdate = () => {
  showUpdateDialog.value = false
  editingProgress.value = null
}

// 生命周期
onMounted(() => {
  loadProgress()
})
</script>

<style scoped>
.study-progress {
  max-width: 1200px;
  margin: 0 auto;
  padding: 20px;
}

.page-header {
  text-align: center;
  margin-bottom: 30px;
}

.page-header h1 {
  margin: 0 0 10px 0;
  color: #2c3e50;
  font-size: 2rem;
}

.page-header p {
  margin: 0;
  color: #606266;
  font-size: 1rem;
}

.stats-overview {
  margin-bottom: 30px;
}

.stat-card {
  background: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  text-align: center;
}

.stat-number {
  font-size: 2rem;
  font-weight: bold;
  color: #409eff;
  margin-bottom: 5px;
}

.stat-label {
  color: #606266;
  font-size: 0.9rem;
  margin-bottom: 5px;
}

.stat-total {
  color: #909399;
  font-size: 0.8rem;
}

.phase-progress {
  margin-bottom: 30px;
}

.phase-progress h2 {
  margin: 0 0 20px 0;
  color: #2c3e50;
}

.phase-card {
  background: #fff;
  padding: 20px;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.phase-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.phase-header h3 {
  margin: 0;
  font-size: 1rem;
  color: #2c3e50;
}

.phase-duration {
  color: #409eff;
  font-size: 0.8rem;
}

.phase-details {
  display: flex;
  justify-content: space-between;
  margin-top: 10px;
  font-size: 0.8rem;
  color: #606266;
}

.filter-section {
  margin-bottom: 20px;
  padding: 20px;
  background: #fff;
  border-radius: 8px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.progress-list {
  margin-bottom: 30px;
}

.empty-state {
  text-align: center;
  padding: 60px 20px;
  color: #909399;
}

.empty-state p {
  margin: 20px 0;
  font-size: 1.1rem;
}

.progress-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
  gap: 20px;
}

.progress-card {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  overflow: hidden;
  transition: transform 0.3s, box-shadow 0.3s;
  border-left: 4px solid #e4e7ed;
}

.progress-card:hover {
  transform: translateY(-2px);
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.15);
}

.progress-card.status-not_started {
  border-left-color: #909399;
}

.progress-card.status-in_progress {
  border-left-color: #e6a23c;
}

.progress-card.status-completed {
  border-left-color: #67c23a;
}

.progress-header {
  padding: 15px 20px;
  background: #f8f9fa;
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.day-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

.day-number {
  font-weight: 600;
  color: #2c3e50;
}

.phase-badge {
  background: #409eff;
  color: white;
  padding: 4px 8px;
  border-radius: 4px;
  font-size: 0.7rem;
}

.progress-content {
  padding: 20px;
}

.progress-title {
  margin: 0 0 10px 0;
  font-size: 1.1rem;
  font-weight: 600;
  color: #2c3e50;
}

.progress-description {
  margin: 0 0 15px 0;
  color: #606266;
  font-size: 0.9rem;
  line-height: 1.5;
}

.progress-meta {
  margin-bottom: 15px;
  padding: 10px;
  background: #f8f9fa;
  border-radius: 6px;
  font-size: 0.8rem;
}

.meta-item {
  display: flex;
  justify-content: space-between;
  margin-bottom: 5px;
}

.meta-item:last-child {
  margin-bottom: 0;
}

.meta-item .label {
  color: #909399;
  font-weight: 500;
}

.meta-item .value {
  color: #606266;
}

.homework-section,
.notes-section {
  margin-bottom: 15px;
  padding: 10px;
  background: #f0f9ff;
  border-radius: 6px;
}

.homework-section h4,
.notes-section h4 {
  margin: 0 0 8px 0;
  font-size: 0.9rem;
  color: #2c3e50;
}

.homework-section p,
.notes-section p {
  margin: 0;
  font-size: 0.8rem;
  color: #606266;
  line-height: 1.4;
}

.homework-submission {
  margin-top: 10px;
  padding: 8px;
  background: #e8f5e8;
  border-radius: 4px;
}

.homework-submission strong {
  color: #67c23a;
  font-size: 0.8rem;
}

.progress-actions {
  padding: 15px 20px;
  background: #f8f9fa;
  display: flex;
  gap: 10px;
  justify-content: flex-end;
}

/* 移动端适配 */
@media (max-width: 768px) {
  .study-progress {
    padding: 15px;
  }
  
  .page-header h1 {
    font-size: 1.5rem;
  }
  
  .stats-overview .el-col {
    margin-bottom: 15px;
  }
  
  .phase-progress .el-col {
    margin-bottom: 15px;
  }
  
  .progress-grid {
    grid-template-columns: 1fr;
  }
  
  .progress-header {
    flex-direction: column;
    gap: 10px;
    align-items: flex-start;
  }
  
  .progress-actions {
    flex-direction: column;
  }
}
</style> 
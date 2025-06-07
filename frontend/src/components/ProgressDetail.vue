<template>
  <div class="progress-detail">
    <div class="detail-header">
      <div class="progress-info">
        <h2>第{{ progress.day_number }}天：{{ progress.title }}</h2>
        <div class="progress-meta">
          <el-tag :type="getStatusColor(progress.status)" size="large">
            {{ getStatusText(progress.status) }}
          </el-tag>
          <span class="phase-info">{{ getPhaseText(progress.phase) }}</span>
        </div>
      </div>
    </div>

    <div class="detail-content">
      <el-row :gutter="20">
        <!-- 主要内容 -->
        <el-col :span="16">
          <div class="content-section">
            <h3>📖 学习内容</h3>
            <div class="learning-content">
              {{ progress.content }}
            </div>
          </div>

          <div class="content-section" v-if="progress.homework_description">
            <h3>📝 作业要求</h3>
            <div class="homework-description">
              {{ progress.homework_description }}
            </div>
            
            <div v-if="progress.homework_submission" class="homework-submission">
              <h4>已提交的作业</h4>
              <div class="submission-content">
                {{ progress.homework_submission }}
              </div>
              <div class="submission-status">
                <el-tag :type="getHomeworkStatusColor(progress.homework_status)" size="small">
                  {{ getHomeworkStatusText(progress.homework_status) }}
                </el-tag>
              </div>
            </div>
          </div>

          <div class="content-section" v-if="progress.notes">
            <h3>📋 学习笔记</h3>
            <div class="learning-notes">
              {{ progress.notes }}
            </div>
          </div>

          <!-- 学习统计 -->
          <div class="content-section" v-if="progress.status !== 'not_started'">
            <h3>📊 学习统计</h3>
            <div class="study-stats">
              <div class="stat-item" v-if="progress.study_hours">
                <span class="stat-label">学习时长:</span>
                <span class="stat-value">{{ progress.study_hours }} 小时</span>
              </div>
              <div class="stat-item" v-if="progress.score">
                <span class="stat-label">完成分数:</span>
                <span class="stat-value">{{ progress.score }} 分</span>
              </div>
              <div class="stat-item" v-if="progress.started_at">
                <span class="stat-label">开始时间:</span>
                <span class="stat-value">{{ formatDate(progress.started_at) }}</span>
              </div>
              <div class="stat-item" v-if="progress.completed_at">
                <span class="stat-label">完成时间:</span>
                <span class="stat-value">{{ formatDate(progress.completed_at) }}</span>
              </div>
            </div>
          </div>
        </el-col>

        <!-- 侧边信息 -->
        <el-col :span="8">
          <div class="sidebar">
            <!-- 基本信息 -->
            <div class="info-section">
              <h4>基本信息</h4>
              <div class="info-list">
                <div class="info-item">
                  <span class="label">学习阶段:</span>
                  <span class="value">{{ getPhaseText(progress.phase) }}</span>
                </div>
                <div class="info-item">
                  <span class="label">天数:</span>
                  <span class="value">第 {{ progress.day_number }} 天</span>
                </div>
                <div class="info-item">
                  <span class="label">状态:</span>
                  <span class="value">{{ getStatusText(progress.status) }}</span>
                </div>
                <div class="info-item">
                  <span class="label">创建时间:</span>
                  <span class="value">{{ formatDate(progress.created_at) }}</span>
                </div>
                <div class="info-item">
                  <span class="label">更新时间:</span>
                  <span class="value">{{ formatDate(progress.updated_at) }}</span>
                </div>
              </div>
            </div>

            <!-- 进度条 -->
            <div class="progress-section">
              <h4>完成进度</h4>
              <div class="progress-indicator">
                <el-progress 
                  :percentage="getProgressPercentage()" 
                  :status="progress.status === 'completed' ? 'success' : ''"
                  :stroke-width="12"
                />
                <div class="progress-text">
                  {{ getProgressText() }}
                </div>
              </div>
            </div>

            <!-- 学习建议 -->
            <div class="tips-section" v-if="progress.status !== 'completed'">
              <h4>学习建议</h4>
              <div class="tips-content">
                <ul>
                  <li v-if="progress.status === 'not_started'">
                    💡 建议先阅读学习内容，制定学习计划
                  </li>
                  <li v-if="progress.status === 'in_progress'">
                    ⏰ 专注学习，记录重点知识
                  </li>
                  <li>📝 完成作业能帮助你更好地理解知识点</li>
                  <li>🤔 遇到问题及时记录并寻求帮助</li>
                  <li>📖 学习笔记是复习的好帮手</li>
                </ul>
              </div>
            </div>
          </div>
        </el-col>
      </el-row>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  progress: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['close'])

// 阶段配置
const phases = {
  'web-basics': 'Web技术基础',
  'csharp-basics': 'C#编程基础',
  'vue-framework': 'Vue.js框架',
  'automation': '浏览器自动化'
}

// 计算属性
const getProgressPercentage = () => {
  switch (props.progress.status) {
    case 'not_started':
      return 0
    case 'in_progress':
      return 50
    case 'completed':
      return 100
    default:
      return 0
  }
}

const getProgressText = () => {
  switch (props.progress.status) {
    case 'not_started':
      return '还未开始学习'
    case 'in_progress':
      return '正在学习中...'
    case 'completed':
      return '已完成学习！'
    default:
      return '未知状态'
  }
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
  return phases[phase] || phase
}

const getHomeworkStatusColor = (status) => {
  const colors = {
    'submitted': 'success',
    'pending': 'warning',
    'reviewed': 'info'
  }
  return colors[status] || 'info'
}

const getHomeworkStatusText = (status) => {
  const texts = {
    'submitted': '已提交',
    'pending': '待审核',
    'reviewed': '已审核'
  }
  return texts[status] || '未提交'
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString('zh-CN')
}
</script>

<style scoped>
.progress-detail {
  padding: 20px 0;
}

.detail-header {
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 1px solid #e4e7ed;
}

.progress-info h2 {
  margin: 0 0 15px 0;
  font-size: 1.6rem;
  font-weight: 600;
  color: #2c3e50;
  line-height: 1.4;
}

.progress-meta {
  display: flex;
  gap: 15px;
  align-items: center;
  flex-wrap: wrap;
}

.phase-info {
  color: #606266;
  font-size: 0.9rem;
  background: #f0f2f5;
  padding: 4px 8px;
  border-radius: 4px;
}

.content-section {
  margin-bottom: 30px;
}

.content-section h3 {
  margin: 0 0 15px 0;
  font-size: 1.2rem;
  font-weight: 600;
  color: #2c3e50;
}

.learning-content,
.homework-description,
.learning-notes {
  line-height: 1.6;
  color: #606266;
  font-size: 1rem;
  white-space: pre-wrap;
  background: #f8f9fa;
  padding: 15px;
  border-radius: 8px;
}

.homework-submission {
  margin-top: 20px;
  padding: 15px;
  background: #e8f5e8;
  border-radius: 8px;
  border-left: 4px solid #67c23a;
}

.homework-submission h4 {
  margin: 0 0 10px 0;
  color: #67c23a;
  font-size: 1rem;
}

.submission-content {
  color: #606266;
  line-height: 1.5;
  margin-bottom: 10px;
  white-space: pre-wrap;
}

.submission-status {
  display: flex;
  justify-content: flex-end;
}

.study-stats {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
  gap: 15px;
}

.stat-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px;
  background: #f8f9fa;
  border-radius: 6px;
  font-size: 0.9rem;
}

.stat-label {
  color: #909399;
  font-weight: 500;
}

.stat-value {
  color: #606266;
  font-weight: 600;
}

.sidebar {
  background: #f8f9fa;
  border-radius: 8px;
  padding: 20px;
}

.sidebar h4 {
  margin: 0 0 15px 0;
  font-size: 1rem;
  font-weight: 600;
  color: #2c3e50;
}

.info-section,
.progress-section,
.tips-section {
  margin-bottom: 25px;
}

.info-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.info-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.9rem;
}

.info-item .label {
  color: #909399;
  font-weight: 500;
  min-width: 80px;
}

.info-item .value {
  color: #606266;
  text-align: right;
  flex: 1;
}

.progress-indicator {
  text-align: center;
}

.progress-text {
  margin-top: 10px;
  color: #606266;
  font-size: 0.9rem;
}

.tips-content ul {
  margin: 0;
  padding-left: 20px;
  color: #606266;
}

.tips-content li {
  margin-bottom: 8px;
  line-height: 1.4;
  font-size: 0.9rem;
}

/* 移动端适配 */
@media (max-width: 768px) {
  .progress-info h2 {
    font-size: 1.3rem;
  }
  
  .progress-meta {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }
  
  .detail-content .el-col {
    margin-bottom: 20px;
  }
  
  .study-stats {
    grid-template-columns: 1fr;
  }
}
</style> 
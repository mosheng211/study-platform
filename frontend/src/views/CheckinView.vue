<template>
  <div class="checkin-container">
    <div class="page-header">
      <h1>每日打卡</h1>
      <p>坚持每日学习，记录成长点滴</p>
    </div>

    <!-- 打卡统计概览 -->
    <div class="stats-grid">
      <el-card class="stat-card">
        <div class="stat-content">
          <div class="stat-icon primary">
            <el-icon><Calendar /></el-icon>
          </div>
          <div class="stat-info">
            <div class="stat-value">{{ stats.totalDays }}</div>
            <div class="stat-label">总打卡天数</div>
          </div>
        </div>
      </el-card>

      <el-card class="stat-card">
        <div class="stat-content">
          <div class="stat-icon success">
            <el-icon><Trophy /></el-icon>
          </div>
          <div class="stat-info">
            <div class="stat-value">{{ stats.consecutiveDays }}</div>
            <div class="stat-label">连续打卡天数</div>
          </div>
        </div>
      </el-card>

      <el-card class="stat-card">
        <div class="stat-content">
          <div class="stat-icon warning">
            <el-icon><Clock /></el-icon>
          </div>
          <div class="stat-info">
            <div class="stat-value">{{ stats.avgStudyTime }}h</div>
            <div class="stat-label">平均学习时长</div>
          </div>
        </div>
      </el-card>

      <el-card class="stat-card">
        <div class="stat-content">
          <div class="stat-icon info">
            <el-icon><Star /></el-icon>
          </div>
          <div class="stat-info">
            <div class="stat-value">{{ stats.weeklyRate }}%</div>
            <div class="stat-label">本周打卡率</div>
          </div>
        </div>
      </el-card>
    </div>

    <!-- 今日打卡 -->
    <el-card class="today-checkin-card" v-loading="loading">
      <template #header>
        <div class="card-header">
          <h3>今日打卡 - {{ currentDate }}</h3>
          <el-tag v-if="todayCheckin" type="success" size="large">
            <el-icon><Check /></el-icon>
            已打卡
          </el-tag>
          <el-tag v-else type="info" size="large">未打卡</el-tag>
        </div>
      </template>

      <div v-if="!todayCheckin" class="checkin-form">
        <el-form ref="checkinFormRef" :model="checkinForm" :rules="checkinRules" label-position="top">
          <div class="form-row">
            <div class="form-col">
              <el-form-item label="今日学习内容" prop="content">
                <el-input
                  v-model="checkinForm.content"
                  type="textarea"
                  :rows="4"
                  placeholder="简要描述今天学习的内容..."
                  maxlength="500"
                  show-word-limit
                />
              </el-form-item>
            </div>
            <div class="form-col">
              <el-form-item label="学习时长(小时)" prop="studyTime">
                <el-input-number
                  v-model="checkinForm.studyTime"
                  :min="0.5"
                  :max="24"
                  :step="0.5"
                  :precision="1"
                  style="width: 100%"
                />
              </el-form-item>
              <el-form-item label="学习质量评分" prop="quality">
                <el-rate
                  v-model="checkinForm.quality"
                  :max="5"
                  show-text
                  :texts="qualityTexts"
                />
              </el-form-item>
            </div>
          </div>
          
          <el-form-item label="学习心得" prop="notes">
            <el-input
              v-model="checkinForm.notes"
              type="textarea"
              :rows="3"
              placeholder="记录今天的学习感悟、遇到的问题或收获..."
              maxlength="1000"
              show-word-limit
            />
          </el-form-item>

          <el-form-item label="学习标签">
            <el-select
              v-model="checkinForm.tags"
              multiple
              filterable
              allow-create
              placeholder="选择或输入学习标签"
              style="width: 100%"
            >
              <el-option
                v-for="tag in predefinedTags"
                :key="tag"
                :label="tag"
                :value="tag"
              />
            </el-select>
          </el-form-item>

          <div class="form-actions">
            <el-button type="primary" size="large" @click="submitCheckin" :loading="submitting">
              <el-icon><Check /></el-icon>
              立即打卡
            </el-button>
          </div>
        </el-form>
      </div>

      <div v-else class="today-checkin-info">
        <div class="checkin-success">
          <el-icon class="success-icon"><CircleCheck /></el-icon>
          <h4>今日已完成打卡！</h4>
          <p>学习时长：{{ todayCheckin.study_time }}小时 | 质量评分：{{ todayCheckin.quality }}分</p>
        </div>
        <div class="checkin-details">
          <h5>学习内容：</h5>
          <p>{{ todayCheckin.content }}</p>
          <h5 v-if="todayCheckin.notes">学习心得：</h5>
          <p v-if="todayCheckin.notes">{{ todayCheckin.notes }}</p>
          <div v-if="todayCheckin.tags && todayCheckin.tags.length" class="tags">
            <el-tag v-for="tag in todayCheckin.tags" :key="tag" class="tag">{{ tag }}</el-tag>
          </div>
        </div>
      </div>
    </el-card>

    <!-- 打卡日历 -->
    <el-card class="calendar-card">
      <template #header>
        <h3>打卡日历</h3>
      </template>
      <el-calendar v-model="calendarValue">
        <template #date-cell="{ data }">
          <div class="calendar-day" :class="getCalendarDayClass(data.day)">
            <span class="day-number">{{ data.day.split('-').pop() }}</span>
            <div v-if="getCheckinForDate(data.day)" class="checkin-indicator">
              <el-icon class="checkin-icon"><Check /></el-icon>
            </div>
          </div>
        </template>
      </el-calendar>
    </el-card>

    <!-- 打卡历史 -->
    <el-card class="history-card">
      <template #header>
        <div class="card-header">
          <h3>打卡历史</h3>
          <div class="filters">
            <el-date-picker
              v-model="dateRange"
              type="daterange"
              range-separator="至"
              start-placeholder="开始日期"
              end-placeholder="结束日期"
              @change="loadCheckinHistory"
            />
            <el-select v-model="qualityFilter" placeholder="筛选质量" @change="loadCheckinHistory">
              <el-option label="全部" value="" />
              <el-option label="5分" :value="5" />
              <el-option label="4分" :value="4" />
              <el-option label="3分" :value="3" />
              <el-option label="2分" :value="2" />
              <el-option label="1分" :value="1" />
            </el-select>
          </div>
        </div>
      </template>

      <div class="history-list" v-loading="historyLoading">
        <div v-if="checkinHistory.length === 0" class="empty-state">
          <el-empty description="暂无打卡记录" />
        </div>
        <div v-else>
          <div v-for="checkin in checkinHistory" :key="checkin.id" class="history-item">
            <div class="history-date">
              <div class="date">{{ formatDate(checkin.checkin_date) }}</div>
              <div class="weekday">{{ getWeekday(checkin.checkin_date) }}</div>
            </div>
            <div class="history-content">
              <div class="history-header">
                <h4>{{ checkin.content }}</h4>
                <div class="history-meta">
                  <el-tag size="small">{{ checkin.study_time }}h</el-tag>
                  <el-rate
                    :model-value="checkin.quality"
                    disabled
                    size="small"
                  />
                </div>
              </div>
              <p v-if="checkin.notes" class="history-notes">{{ checkin.notes }}</p>
              <div v-if="checkin.tags && checkin.tags.length" class="history-tags">
                <el-tag v-for="tag in checkin.tags" :key="tag" size="small" class="tag">{{ tag }}</el-tag>
              </div>
            </div>
          </div>
        </div>
      </div>
    </el-card>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted, computed } from 'vue'
import { ElMessage, ElMessageBox } from 'element-plus'
import {
  Calendar, Trophy, Clock, Star, Check, CircleCheck
} from '@element-plus/icons-vue'
import { checkinAPI } from '@/api'

// 响应式数据
const loading = ref(false)
const submitting = ref(false)
const historyLoading = ref(false)

// 统计数据
const stats = reactive({
  totalDays: 0,
  consecutiveDays: 0,
  avgStudyTime: 0,
  weeklyRate: 0
})

// 今日打卡
const todayCheckin = ref(null)
const currentDate = ref(new Date().toLocaleDateString('zh-CN'))

// 打卡表单
const checkinFormRef = ref()
const checkinForm = reactive({
  content: '',
  studyTime: 2,
  quality: 4,
  notes: '',
  tags: []
})

const checkinRules = {
  content: [
    { required: true, message: '请输入学习内容', trigger: 'blur' },
    { min: 10, message: '学习内容至少10个字符', trigger: 'blur' }
  ],
  studyTime: [
    { required: true, message: '请输入学习时长', trigger: 'blur' }
  ],
  quality: [
    { required: true, message: '请评分学习质量', trigger: 'change' }
  ]
}

// 预定义标签
const predefinedTags = ref([
  'HTML/CSS', 'JavaScript', 'Vue.js', 'React', 'Node.js', 'Python',
  'Algorithm', 'Database', 'Git', 'Project', 'Reading', 'Practice'
])

const qualityTexts = ['很差', '一般', '良好', '很好', '优秀']

// 日历
const calendarValue = ref(new Date())
const checkinDates = ref([])

// 历史记录
const checkinHistory = ref([])
const dateRange = ref([])
const qualityFilter = ref('')

// 计算属性
const getCalendarDayClass = (date) => {
  const checkin = getCheckinForDate(date)
  if (checkin) {
    if (checkin.quality >= 4) return 'checkin-excellent'
    if (checkin.quality >= 3) return 'checkin-good'
    return 'checkin-normal'
  }
  return ''
}

const getCheckinForDate = (date) => {
  return checkinDates.value.find(c => c.checkin_date === date)
}

// 方法
const loadStats = async () => {
  try {
    const response = await checkinAPI.getStats()
    Object.assign(stats, response.data.stats)
  } catch (error) {
    console.error('加载统计数据失败:', error)
  }
}

const loadTodayCheckin = async () => {
  try {
    loading.value = true
    const response = await checkinAPI.getTodayCheckin()
    todayCheckin.value = response.data.checkin
  } catch (error) {
    console.error('加载今日打卡失败:', error)
  } finally {
    loading.value = false
  }
}

const loadCheckinDates = async () => {
  try {
    const response = await checkinAPI.getCheckinDates()
    checkinDates.value = response.data.dates
  } catch (error) {
    console.error('加载打卡日期失败:', error)
  }
}

const loadCheckinHistory = async () => {
  try {
    historyLoading.value = true
    const params = {}
    if (dateRange.value && dateRange.value.length === 2) {
      params.start_date = dateRange.value[0]
      params.end_date = dateRange.value[1]
    }
    if (qualityFilter.value) {
      params.quality = qualityFilter.value
    }
    
    const response = await checkinAPI.getCheckinHistory(params)
    checkinHistory.value = response.data.checkins
  } catch (error) {
    console.error('加载打卡历史失败:', error)
  } finally {
    historyLoading.value = false
  }
}

const submitCheckin = async () => {
  if (!checkinFormRef.value) return
  
  try {
    await checkinFormRef.value.validate()
    submitting.value = true
    
    await checkinAPI.createCheckin(checkinForm)
    
    ElMessage.success('打卡成功！')
    await loadTodayCheckin()
    await loadStats()
    await loadCheckinDates()
    await loadCheckinHistory()
    
    // 重置表单
    Object.assign(checkinForm, {
      content: '',
      studyTime: 2,
      quality: 4,
      notes: '',
      tags: []
    })
  } catch (error) {
    if (error.errors) {
      // 表单验证错误
      return
    }
    console.error('打卡失败:', error)
    ElMessage.error('打卡失败，请重试')
  } finally {
    submitting.value = false
  }
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleDateString('zh-CN')
}

const getWeekday = (dateString) => {
  const weekdays = ['周日', '周一', '周二', '周三', '周四', '周五', '周六']
  return weekdays[new Date(dateString).getDay()]
}

// 生命周期
onMounted(async () => {
  await Promise.all([
    loadStats(),
    loadTodayCheckin(),
    loadCheckinDates(),
    loadCheckinHistory()
  ])
})
</script>

<style scoped>
.checkin-container {
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

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
  gap: 20px;
  margin-bottom: 30px;
}

.stat-card {
  border: none;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  transition: transform 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-5px);
}

.stat-content {
  display: flex;
  align-items: center;
  gap: 15px;
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

.stat-icon.primary { background: linear-gradient(45deg, #667eea 0%, #764ba2 100%); }
.stat-icon.success { background: linear-gradient(45deg, #56ab2f 0%, #a8e6cf 100%); }
.stat-icon.warning { background: linear-gradient(45deg, #f093fb 0%, #f5576c 100%); }
.stat-icon.info { background: linear-gradient(45deg, #4facfe 0%, #00f2fe 100%); }

.stat-value {
  font-size: 2rem;
  font-weight: bold;
  color: #2c3e50;
}

.stat-label {
  color: #7f8c8d;
  font-size: 0.9rem;
}

.today-checkin-card {
  margin-bottom: 30px;
  border: none;
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
}

.checkin-form {
  padding: 20px 0;
}

.form-row {
  display: grid;
  grid-template-columns: 2fr 1fr;
  gap: 30px;
  margin-bottom: 20px;
}

.form-actions {
  text-align: center;
  margin-top: 30px;
}

.today-checkin-info {
  padding: 20px 0;
}

.checkin-success {
  text-align: center;
  margin-bottom: 20px;
}

.success-icon {
  font-size: 3rem;
  color: #67c23a;
  margin-bottom: 10px;
}

.checkin-details h5 {
  color: #2c3e50;
  margin: 15px 0 5px 0;
}

.tags {
  margin-top: 10px;
}

.tag {
  margin-right: 8px;
  margin-bottom: 5px;
}

.calendar-card, .history-card {
  margin-bottom: 30px;
  border: none;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.calendar-day {
  position: relative;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
}

.day-number {
  font-size: 14px;
}

.checkin-indicator {
  position: absolute;
  bottom: 2px;
  right: 2px;
}

.checkin-icon {
  font-size: 12px;
  color: #67c23a;
}

.calendar-day.checkin-excellent { background-color: #f0f9ff; border-left: 3px solid #67c23a; }
.calendar-day.checkin-good { background-color: #fefce8; border-left: 3px solid #f59e0b; }
.calendar-day.checkin-normal { background-color: #fef2f2; border-left: 3px solid #ef4444; }

.filters {
  display: flex;
  gap: 10px;
}

.history-list {
  max-height: 600px;
  overflow-y: auto;
}

.history-item {
  display: flex;
  gap: 20px;
  padding: 20px;
  border-bottom: 1px solid #f1f2f6;
}

.history-item:last-child {
  border-bottom: none;
}

.history-date {
  flex-shrink: 0;
  text-align: center;
  min-width: 80px;
}

.date {
  font-weight: bold;
  color: #2c3e50;
}

.weekday {
  font-size: 0.8rem;
  color: #7f8c8d;
}

.history-content {
  flex: 1;
}

.history-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 10px;
}

.history-header h4 {
  margin: 0;
  color: #2c3e50;
}

.history-meta {
  display: flex;
  align-items: center;
  gap: 10px;
}

.history-notes {
  color: #5a6c7d;
  margin: 10px 0;
  line-height: 1.5;
}

.history-tags {
  margin-top: 10px;
}

.empty-state {
  text-align: center;
  padding: 40px;
}

@media (max-width: 768px) {
  .stats-grid {
    grid-template-columns: 1fr;
  }
  
  .form-row {
    grid-template-columns: 1fr;
    gap: 20px;
  }
  
  .history-item {
    flex-direction: column;
    gap: 10px;
  }
  
  .history-header {
    flex-direction: column;
    gap: 10px;
  }
}
</style> 
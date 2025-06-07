<template>
  <div class="resource-detail">
    <div class="detail-header">
      <div class="resource-info">
        <h2 class="resource-title">{{ resource.title }}</h2>
        <div class="resource-meta">
          <el-tag :type="getDifficultyColor(resource.difficulty)" size="small">
            {{ getDifficultyText(resource.difficulty) }}
          </el-tag>
          <el-tag v-if="resource.is_featured" type="danger" size="small">推荐</el-tag>
          <span class="resource-type">{{ getTypeText(resource.type) }}</span>
          <span class="resource-category">{{ resource.category }}</span>
        </div>
      </div>
      <div class="resource-actions">
        <el-button v-if="resource.url" type="primary" @click="openResource">
          <el-icon><Link /></el-icon>
          访问资源
        </el-button>
      </div>
    </div>

    <div class="detail-content">
      <el-row :gutter="20">
        <!-- 主要内容 -->
        <el-col :span="16">
          <div class="content-section">
            <h3>资源描述</h3>
            <div class="resource-description">
              {{ resource.description }}
            </div>
          </div>

          <div class="content-section" v-if="resource.tags && resource.tags.length > 0">
            <h3>相关标签</h3>
            <div class="resource-tags">
              <el-tag 
                v-for="tag in resource.tags" 
                :key="tag"
                style="margin-right: 8px; margin-bottom: 8px;"
              >
                {{ tag }}
              </el-tag>
            </div>
          </div>

          <!-- 评价和反馈区域 -->
          <div class="content-section" v-if="authStore.isAuthenticated">
            <h3>评价反馈</h3>
            <div class="rating-section">
              <el-rate v-model="userRating" @change="handleRatingChange" show-text />
              <p class="rating-text">为这个资源评分</p>
            </div>
          </div>
        </el-col>

        <!-- 侧边信息 -->
        <el-col :span="8">
          <div class="sidebar">
            <!-- 资源预览 -->
            <div class="preview-section" v-if="resource.thumbnail">
              <h4>资源预览</h4>
              <img :src="resource.thumbnail" :alt="resource.title" class="preview-image" />
            </div>

            <!-- 基本信息 -->
            <div class="info-section">
              <h4>基本信息</h4>
              <div class="info-list">
                <div class="info-item">
                  <span class="label">资源类型：</span>
                  <span class="value">{{ getTypeText(resource.type) }}</span>
                </div>
                <div class="info-item">
                  <span class="label">分类：</span>
                  <span class="value">{{ resource.category }}</span>
                </div>
                <div class="info-item">
                  <span class="label">难度等级：</span>
                  <span class="value">{{ getDifficultyText(resource.difficulty) }}</span>
                </div>
                <div class="info-item" v-if="resource.duration">
                  <span class="label">时长：</span>
                  <span class="value">{{ formatDuration(resource.duration) }}</span>
                </div>
                <div class="info-item">
                  <span class="label">创建者：</span>
                  <span class="value">{{ resource.creator?.real_name || resource.creator?.username }}</span>
                </div>
                <div class="info-item">
                  <span class="label">创建时间：</span>
                  <span class="value">{{ formatDate(resource.created_at) }}</span>
                </div>
              </div>
            </div>

            <!-- 统计信息 -->
            <div class="stats-section">
              <h4>统计信息</h4>
              <div class="stats-list">
                <div class="stat-item">
                  <el-icon><View /></el-icon>
                  <span>浏览 {{ resource.view_count || 0 }} 次</span>
                </div>
                <div class="stat-item">
                  <el-icon><Download /></el-icon>
                  <span>下载 {{ resource.download_count || 0 }} 次</span>
                </div>
                <div class="stat-item" v-if="resource.rating && Number(resource.rating) > 0">
                  <el-icon><Star /></el-icon>
                  <span>评分 {{ Number(resource.rating).toFixed(1) }} 分</span>
                </div>
              </div>
            </div>

            <!-- 相关操作 -->
            <div class="actions-section" v-if="authStore.isAuthenticated">
              <h4>相关操作</h4>
              <div class="action-buttons">
                <el-button @click="addToFavorites" :disabled="isFavorited">
                  <el-icon><Star /></el-icon>
                  {{ isFavorited ? '已收藏' : '收藏' }}
                </el-button>
                <el-button @click="reportResource">
                  <el-icon><Warning /></el-icon>
                  举报
                </el-button>
              </div>
            </div>
          </div>
        </el-col>
      </el-row>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { ElMessage } from 'element-plus'

const props = defineProps({
  resource: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['close'])

const authStore = useAuthStore()
const userRating = ref(0)
const isFavorited = ref(false)

// 计算属性
const resourceUrl = computed(() => props.resource.url)

// 方法
const openResource = () => {
  if (resourceUrl.value) {
    window.open(resourceUrl.value, '_blank')
  }
}

const getDifficultyColor = (difficulty) => {
  const colors = {
    beginner: 'success',
    intermediate: 'warning',
    advanced: 'danger'
  }
  return colors[difficulty] || 'info'
}

const getDifficultyText = (difficulty) => {
  const texts = {
    beginner: '初级',
    intermediate: '中级',
    advanced: '高级'
  }
  return texts[difficulty] || difficulty
}

const getTypeText = (type) => {
  const texts = {
    video: '视频教程',
    document: '文档资料',
    link: '在线链接',
    book: '电子书籍',
    tool: '开发工具'
  }
  return texts[type] || type
}

const formatDuration = (minutes) => {
  if (minutes < 60) {
    return `${minutes}分钟`
  }
  const hours = Math.floor(minutes / 60)
  const mins = minutes % 60
  return `${hours}小时${mins > 0 ? mins + '分钟' : ''}`
}

const formatDate = (dateString) => {
  return new Date(dateString).toLocaleString('zh-CN')
}

const handleRatingChange = (value) => {
  // TODO: 实现评分功能
  ElMessage.success(`您给这个资源评了 ${value} 分`)
}

const addToFavorites = () => {
  // TODO: 实现收藏功能
  isFavorited.value = true
  ElMessage.success('已添加到收藏')
}

const reportResource = () => {
  // TODO: 实现举报功能
  ElMessage.info('举报功能开发中')
}
</script>

<style scoped>
.resource-detail {
  padding: 20px 0;
}

.detail-header {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 1px solid #e4e7ed;
}

.resource-title {
  margin: 0 0 15px 0;
  font-size: 1.8rem;
  font-weight: 600;
  color: #2c3e50;
  line-height: 1.4;
}

.resource-meta {
  display: flex;
  gap: 10px;
  align-items: center;
  flex-wrap: wrap;
}

.resource-type,
.resource-category {
  color: #606266;
  font-size: 0.9rem;
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

.resource-description {
  line-height: 1.6;
  color: #606266;
  font-size: 1rem;
  white-space: pre-wrap;
}

.resource-tags .el-tag {
  margin-right: 8px;
  margin-bottom: 8px;
}

.rating-section {
  display: flex;
  align-items: center;
  gap: 15px;
}

.rating-text {
  margin: 0;
  color: #909399;
  font-size: 0.9rem;
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

.preview-section {
  margin-bottom: 25px;
}

.preview-image {
  width: 100%;
  border-radius: 6px;
  border: 1px solid #e4e7ed;
}

.info-section,
.stats-section,
.actions-section {
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

.stats-list {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.stat-item {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.9rem;
  color: #606266;
}

.action-buttons {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.action-buttons .el-button {
  justify-content: flex-start;
}

/* 移动端适配 */
@media (max-width: 768px) {
  .detail-header {
    flex-direction: column;
    gap: 15px;
  }
  
  .resource-title {
    font-size: 1.4rem;
  }
  
  .detail-content .el-col {
    margin-bottom: 20px;
  }
  
  .rating-section {
    flex-direction: column;
    align-items: flex-start;
    gap: 10px;
  }
}
</style> 
<template>
  <el-dialog
    v-model="dialogVisible"
    title="资源详情"
    width="800px"
    @close="$emit('update:visible', false)"
  >
    <div v-if="resource" class="resource-detail">
      <div class="resource-header">
        <div class="resource-thumbnail">
          <img v-if="resource.thumbnail" :src="resource.thumbnail" :alt="resource.title" />
          <div v-else class="default-thumbnail">
            <el-icon size="60"><Document /></el-icon>
          </div>
        </div>
        <div class="resource-basic-info">
          <h3>{{ resource.title }}</h3>
          <p class="resource-description">{{ resource.description || '暂无描述' }}</p>
          <div class="resource-meta">
            <el-tag>{{ getResourceTypeText(resource.type) }}</el-tag>
            <el-tag type="warning" style="margin-left: 10px">
              难度：{{ resource.difficulty }}/5
            </el-tag>
            <el-tag type="info" style="margin-left: 10px">
              {{ getCategoryText(resource.category) }}
            </el-tag>
          </div>
        </div>
      </div>

      <el-tabs v-model="activeTab">
        <el-tab-pane label="基本信息" name="basic">
          <el-descriptions :column="2" border>
            <el-descriptions-item label="资源ID">{{ resource.id }}</el-descriptions-item>
            <el-descriptions-item label="标题">{{ resource.title }}</el-descriptions-item>
            <el-descriptions-item label="类型">{{ getResourceTypeText(resource.type) }}</el-descriptions-item>
            <el-descriptions-item label="分类">{{ getCategoryText(resource.category) }}</el-descriptions-item>
            <el-descriptions-item label="难度">
              <el-rate :model-value="resource.difficulty" disabled />
            </el-descriptions-item>
            <el-descriptions-item label="推荐度">
              <el-rate :model-value="resource.recommended" disabled />
            </el-descriptions-item>
            <el-descriptions-item label="创建者">{{ resource.created_by_name || '未知' }}</el-descriptions-item>
            <el-descriptions-item label="创建时间">{{ formatDate(resource.created_at) }}</el-descriptions-item>
            <el-descriptions-item label="更新时间">{{ formatDate(resource.updated_at) }}</el-descriptions-item>
            <el-descriptions-item label="浏览次数">{{ resource.views || 0 }}</el-descriptions-item>
          </el-descriptions>

          <div v-if="resource.tags && resource.tags.length" class="resource-tags">
            <h4>标签</h4>
            <el-tag v-for="tag in resource.tags" :key="tag" class="tag">{{ tag }}</el-tag>
          </div>

          <div v-if="resource.description" class="resource-description-full">
            <h4>详细描述</h4>
            <div class="description-content">{{ resource.description }}</div>
          </div>
        </el-tab-pane>

        <el-tab-pane label="内容链接" name="content">
          <div class="resource-content">
            <div v-if="resource.url" class="resource-url">
              <h4>资源链接</h4>
              <el-input
                :model-value="resource.url"
                readonly
                style="margin-bottom: 10px"
              >
                <template #append>
                  <el-button @click="openUrl(resource.url)">
                    <el-icon><Link /></el-icon>
                    打开
                  </el-button>
                </template>
              </el-input>
            </div>

            <div v-if="resource.download_url" class="resource-download">
              <h4>下载链接</h4>
              <el-button type="primary" @click="downloadResource">
                <el-icon><Download /></el-icon>
                下载资源
              </el-button>
            </div>

            <div v-if="resource.content" class="resource-text-content">
              <h4>文本内容</h4>
              <div class="content-text">{{ resource.content }}</div>
            </div>

            <div v-if="resource.metadata" class="resource-metadata">
              <h4>元数据</h4>
              <el-descriptions :column="1" border size="small">
                <el-descriptions-item 
                  v-for="(value, key) in resource.metadata" 
                  :key="key" 
                  :label="key"
                >
                  {{ value }}
                </el-descriptions-item>
              </el-descriptions>
            </div>
          </div>
        </el-tab-pane>

        <el-tab-pane label="学习记录" name="records">
          <div v-loading="recordsLoading">
            <div v-if="learningRecords.length === 0" class="empty-state">
              <el-empty description="暂无学习记录" />
            </div>
            <div v-else class="records-list">
              <div v-for="record in learningRecords" :key="record.id" class="record-item">
                <div class="record-header">
                  <div class="user-info">
                    <el-avatar :size="32" :src="record.user_avatar" :icon="UserFilled" />
                    <span class="username">{{ record.user_name }}</span>
                  </div>
                  <div class="record-time">{{ formatDate(record.created_at) }}</div>
                </div>
                <div class="record-content">
                  <div class="record-progress">
                    <el-progress 
                      :percentage="record.progress || 0" 
                      :color="getProgressColor(record.progress)"
                    />
                  </div>
                  <div v-if="record.notes" class="record-notes">
                    <strong>学习笔记：</strong>{{ record.notes }}
                  </div>
                  <div class="record-meta">
                    <span>学习时长：{{ record.study_time || 0 }}分钟</span>
                    <span>完成状态：{{ getStatusText(record.status) }}</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </el-tab-pane>

        <el-tab-pane label="评价反馈" name="feedback">
          <div v-loading="feedbackLoading">
            <div v-if="resourceFeedback.length === 0" class="empty-state">
              <el-empty description="暂无评价反馈" />
            </div>
            <div v-else class="feedback-list">
              <div v-for="feedback in resourceFeedback" :key="feedback.id" class="feedback-item">
                <div class="feedback-header">
                  <div class="user-info">
                    <el-avatar :size="32" :src="feedback.user_avatar" :icon="UserFilled" />
                    <span class="username">{{ feedback.user_name }}</span>
                  </div>
                  <div class="feedback-rating">
                    <el-rate :model-value="feedback.rating" disabled size="small" />
                  </div>
                  <div class="feedback-time">{{ formatDate(feedback.created_at) }}</div>
                </div>
                <div class="feedback-content">
                  <p>{{ feedback.comment }}</p>
                </div>
              </div>
            </div>
          </div>
        </el-tab-pane>
      </el-tabs>
    </div>

    <template #footer>
      <el-button @click="$emit('update:visible', false)">关闭</el-button>
      <el-button type="primary" @click="editResource">编辑资源</el-button>
    </template>
  </el-dialog>
</template>

<script setup>
import { ref, computed, watch } from 'vue'
import { Document, Link, Download, UserFilled } from '@element-plus/icons-vue'
import { ElMessage } from 'element-plus'

const props = defineProps({
  visible: Boolean,
  resource: Object
})

const emit = defineEmits(['update:visible', 'edit'])

const dialogVisible = computed({
  get: () => props.visible,
  set: (value) => emit('update:visible', value)
})

const activeTab = ref('basic')
const recordsLoading = ref(false)
const feedbackLoading = ref(false)

const learningRecords = ref([])
const resourceFeedback = ref([])

// 观察资源变化，加载相关数据
watch(() => props.resource, async (newResource) => {
  if (newResource) {
    await loadResourceData()
  }
}, { immediate: true })

const loadResourceData = async () => {
  // 这里可以加载学习记录和反馈数据
  // 暂时使用模拟数据
  learningRecords.value = []
  resourceFeedback.value = []
}

const getResourceTypeText = (type) => {
  const types = {
    video: '视频',
    document: '文档',
    link: '链接',
    book: '书籍',
    tool: '工具',
    course: '课程'
  }
  return types[type] || '未知'
}

const getCategoryText = (category) => {
  const categories = {
    frontend: '前端开发',
    backend: '后端开发',
    mobile: '移动开发',
    database: '数据库',
    algorithm: '算法',
    tools: '开发工具',
    other: '其他'
  }
  return categories[category] || '未分类'
}

const getStatusText = (status) => {
  const statuses = {
    not_started: '未开始',
    in_progress: '学习中',
    completed: '已完成',
    paused: '已暂停'
  }
  return statuses[status] || '未知'
}

const getProgressColor = (percentage) => {
  if (percentage >= 80) return '#67c23a'
  if (percentage >= 60) return '#e6a23c'
  if (percentage >= 40) return '#f56c6c'
  return '#909399'
}

const formatDate = (dateString) => {
  if (!dateString) return '暂无'
  return new Date(dateString).toLocaleString('zh-CN')
}

const openUrl = (url) => {
  window.open(url, '_blank')
}

const downloadResource = () => {
  if (props.resource?.download_url) {
    window.open(props.resource.download_url, '_blank')
  } else {
    ElMessage.warning('暂无下载链接')
  }
}

const editResource = () => {
  emit('edit', props.resource)
  emit('update:visible', false)
}
</script>

<style scoped>
.resource-detail {
  padding: 20px 0;
}

.resource-header {
  display: flex;
  gap: 20px;
  margin-bottom: 30px;
  padding-bottom: 20px;
  border-bottom: 1px solid #f1f2f6;
}

.resource-thumbnail {
  flex-shrink: 0;
}

.resource-thumbnail img {
  width: 120px;
  height: 120px;
  object-fit: cover;
  border-radius: 8px;
}

.default-thumbnail {
  width: 120px;
  height: 120px;
  background: #f5f7fa;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  color: #c0c4cc;
}

.resource-basic-info {
  flex: 1;
}

.resource-basic-info h3 {
  margin: 0 0 10px 0;
  color: #2c3e50;
  font-size: 1.5rem;
}

.resource-description {
  color: #7f8c8d;
  margin: 10px 0;
  line-height: 1.6;
}

.resource-meta {
  margin-top: 15px;
}

.resource-tags {
  margin: 20px 0;
}

.resource-tags h4 {
  margin-bottom: 10px;
  color: #2c3e50;
}

.tag {
  margin-right: 8px;
  margin-bottom: 5px;
}

.resource-description-full {
  margin: 20px 0;
}

.resource-description-full h4 {
  margin-bottom: 10px;
  color: #2c3e50;
}

.description-content {
  background: #f8f9fa;
  padding: 15px;
  border-radius: 4px;
  line-height: 1.6;
  color: #5a6c7d;
}

.resource-content {
  padding: 20px 0;
}

.resource-url, .resource-download, .resource-text-content, .resource-metadata {
  margin-bottom: 30px;
}

.resource-url h4, .resource-download h4, .resource-text-content h4, .resource-metadata h4 {
  margin-bottom: 15px;
  color: #2c3e50;
}

.content-text {
  background: #f8f9fa;
  padding: 15px;
  border-radius: 4px;
  white-space: pre-wrap;
  line-height: 1.6;
  color: #5a6c7d;
}

.records-list, .feedback-list {
  max-height: 400px;
  overflow-y: auto;
}

.record-item, .feedback-item {
  padding: 15px;
  border: 1px solid #f1f2f6;
  border-radius: 8px;
  margin-bottom: 15px;
}

.record-header, .feedback-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}

.user-info {
  display: flex;
  align-items: center;
  gap: 10px;
}

.username {
  font-weight: bold;
  color: #2c3e50;
}

.record-time, .feedback-time {
  color: #7f8c8d;
  font-size: 0.9rem;
}

.record-progress {
  margin-bottom: 10px;
}

.record-notes {
  margin: 10px 0;
  color: #5a6c7d;
}

.record-meta {
  display: flex;
  gap: 20px;
  font-size: 0.9rem;
  color: #7f8c8d;
}

.feedback-content p {
  margin: 0;
  line-height: 1.6;
  color: #5a6c7d;
}

.empty-state {
  text-align: center;
  padding: 40px;
}

@media (max-width: 768px) {
  .resource-header {
    flex-direction: column;
  }
  
  .resource-thumbnail {
    align-self: center;
  }
  
  .record-header, .feedback-header {
    flex-direction: column;
    gap: 10px;
    align-items: flex-start;
  }
  
  .record-meta {
    flex-direction: column;
    gap: 5px;
  }
}
</style> 
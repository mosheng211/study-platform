<template>
  <div class="progress-update-form">
    <el-form
      ref="formRef"
      :model="form"
      :rules="rules"
      label-width="120px"
      @submit.prevent="handleSubmit"
    >
      <el-row :gutter="20">
        <el-col :span="24">
          <div class="progress-info">
            <h3>第{{ progress.day_number }}天：{{ progress.title }}</h3>
            <p>{{ progress.content }}</p>
          </div>
        </el-col>
        
        <el-col :span="12">
          <el-form-item label="学习状态" prop="status">
            <el-select v-model="form.status" placeholder="选择学习状态" style="width: 100%">
              <el-option label="未开始" value="not_started" />
              <el-option label="进行中" value="in_progress" />
              <el-option label="已完成" value="completed" />
            </el-select>
          </el-form-item>
        </el-col>
        
        <el-col :span="12">
          <el-form-item label="学习时长(小时)" prop="study_hours">
            <el-input-number 
              v-model="form.study_hours" 
              :min="0" 
              :max="24"
              :step="0.5"
              placeholder="学习时长"
              style="width: 100%"
            />
          </el-form-item>
        </el-col>
        
        <el-col :span="12" v-if="form.status === 'completed'">
          <el-form-item label="完成分数" prop="score">
            <el-input-number 
              v-model="form.score" 
              :min="0" 
              :max="100"
              placeholder="0-100分"
              style="width: 100%"
            />
          </el-form-item>
        </el-col>
        
        <el-col :span="24" v-if="progress.homework_description">
          <el-form-item label="作业提交" prop="homework_submission">
            <el-input
              v-model="form.homework_submission"
              type="textarea"
              :rows="4"
              placeholder="请提交你的作业内容或作业链接"
            />
            <div class="homework-info">
              <strong>作业要求：</strong>
              <p>{{ progress.homework_description }}</p>
            </div>
          </el-form-item>
        </el-col>
        
        <el-col :span="24">
          <el-form-item label="学习笔记" prop="notes">
            <el-input
              v-model="form.notes"
              type="textarea"
              :rows="6"
              placeholder="记录你的学习心得、重点知识、遇到的问题等"
            />
          </el-form-item>
        </el-col>
      </el-row>
    </el-form>
    
    <div class="form-actions">
      <el-button @click="$emit('cancel')">取消</el-button>
      <el-button type="primary" @click="handleSubmit" :loading="loading">
        更新进度
      </el-button>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, watch } from 'vue'
import { progressAPI } from '@/api/index'
import { ElMessage } from 'element-plus'

const props = defineProps({
  progress: {
    type: Object,
    required: true
  }
})

const emit = defineEmits(['success', 'cancel'])

const formRef = ref()
const loading = ref(false)

// 表单数据
const form = reactive({
  status: '',
  study_hours: null,
  score: null,
  homework_submission: '',
  notes: ''
})

// 表单验证规则
const rules = {
  status: [
    { required: true, message: '请选择学习状态', trigger: 'change' }
  ],
  study_hours: [
    { type: 'number', min: 0, max: 24, message: '学习时长应在0-24小时之间', trigger: 'blur' }
  ],
  score: [
    { type: 'number', min: 0, max: 100, message: '分数应在0-100之间', trigger: 'blur' }
  ]
}

// 监听progress变化，初始化表单
watch(() => props.progress, (newProgress) => {
  if (newProgress) {
    Object.assign(form, {
      status: newProgress.status || 'not_started',
      study_hours: newProgress.study_hours || null,
      score: newProgress.score || null,
      homework_submission: newProgress.homework_submission || '',
      notes: newProgress.notes || ''
    })
  }
}, { immediate: true })

// 提交表单
const handleSubmit = async () => {
  try {
    await formRef.value.validate()
    
    loading.value = true
    
    const response = await progressAPI.update(props.progress.day_number, form)
    if (response.success) {
      ElMessage.success('学习进度更新成功')
      emit('success')
    }
  } catch (error) {
    console.error('更新进度失败:', error)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.progress-update-form {
  padding: 20px 0;
}

.progress-info {
  margin-bottom: 20px;
  padding: 15px;
  background: #f8f9fa;
  border-radius: 8px;
}

.progress-info h3 {
  margin: 0 0 10px 0;
  color: #2c3e50;
  font-size: 1.1rem;
}

.progress-info p {
  margin: 0;
  color: #606266;
  line-height: 1.5;
}

.homework-info {
  margin-top: 10px;
  padding: 10px;
  background: #e8f4fd;
  border-radius: 6px;
  border-left: 4px solid #409eff;
}

.homework-info strong {
  color: #409eff;
  font-size: 0.9rem;
}

.homework-info p {
  margin: 5px 0 0 0;
  color: #606266;
  font-size: 0.9rem;
  line-height: 1.4;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 30px;
  padding-top: 20px;
  border-top: 1px solid #e4e7ed;
}
</style> 
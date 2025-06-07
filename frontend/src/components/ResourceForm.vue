<template>
  <div class="resource-form">
    <el-form
      ref="formRef"
      :model="form"
      :rules="rules"
      label-width="100px"
      @submit.prevent="handleSubmit"
    >
      <el-row :gutter="20">
        <el-col :span="24">
          <el-form-item label="资源标题" prop="title">
            <el-input v-model="form.title" placeholder="请输入资源标题" />
          </el-form-item>
        </el-col>
        
        <el-col :span="12">
          <el-form-item label="资源类型" prop="type">
            <el-select v-model="form.type" placeholder="选择资源类型" style="width: 100%">
              <el-option label="视频教程" value="video" />
              <el-option label="文档资料" value="document" />
              <el-option label="在线链接" value="link" />
              <el-option label="电子书籍" value="book" />
              <el-option label="开发工具" value="tool" />
            </el-select>
          </el-form-item>
        </el-col>
        
        <el-col :span="12">
          <el-form-item label="分类" prop="category">
            <el-input v-model="form.category" placeholder="如：Web基础、C#编程等" />
          </el-form-item>
        </el-col>
        
        <el-col :span="12">
          <el-form-item label="难度等级" prop="difficulty">
            <el-select v-model="form.difficulty" placeholder="选择难度等级" style="width: 100%">
              <el-option label="初级" value="beginner" />
              <el-option label="中级" value="intermediate" />
              <el-option label="高级" value="advanced" />
            </el-select>
          </el-form-item>
        </el-col>
        
        <el-col :span="12">
          <el-form-item label="时长(分钟)" prop="duration">
            <el-input-number 
              v-model="form.duration" 
              :min="0" 
              placeholder="可选"
              style="width: 100%"
            />
          </el-form-item>
        </el-col>
        
        <el-col :span="24">
          <el-form-item label="资源描述" prop="description">
            <el-input
              v-model="form.description"
              type="textarea"
              :rows="4"
              placeholder="详细描述资源内容、适用对象等"
            />
          </el-form-item>
        </el-col>
        
        <el-col :span="24">
          <el-form-item label="访问链接" prop="url">
            <el-input v-model="form.url" placeholder="https://example.com" />
          </el-form-item>
        </el-col>
        
        <el-col :span="24">
          <el-form-item label="缩略图链接" prop="thumbnail">
            <el-input v-model="form.thumbnail" placeholder="可选，资源预览图URL" />
          </el-form-item>
        </el-col>
        
        <el-col :span="24">
          <el-form-item label="标签">
            <el-tag
              v-for="(tag, index) in form.tags"
              :key="index"
              closable
              @close="removeTag(index)"
              style="margin-right: 8px; margin-bottom: 8px;"
            >
              {{ tag }}
            </el-tag>
            <el-input
              v-if="showTagInput"
              ref="tagInput"
              v-model="newTag"
              size="small"
              style="width: 120px"
              @keyup.enter="addTag"
              @blur="addTag"
            />
            <el-button v-else size="small" @click="showNewTagInput">
              + 添加标签
            </el-button>
          </el-form-item>
        </el-col>
        
        <el-col :span="24" v-if="authStore.isAdmin">
          <el-form-item>
            <el-checkbox v-model="form.is_featured">推荐资源</el-checkbox>
          </el-form-item>
        </el-col>
      </el-row>
    </el-form>
    
    <div class="form-actions">
      <el-button @click="$emit('cancel')">取消</el-button>
      <el-button type="primary" @click="handleSubmit" :loading="loading">
        {{ isEdit ? '更新' : '创建' }}
      </el-button>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, nextTick, watch } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { resourceAPI } from '@/api/index'
import { ElMessage } from 'element-plus'

const props = defineProps({
  resource: {
    type: Object,
    default: null
  }
})

const emit = defineEmits(['success', 'cancel'])

const authStore = useAuthStore()
const formRef = ref()
const loading = ref(false)

// 标签相关
const showTagInput = ref(false)
const newTag = ref('')
const tagInput = ref()

// 表单数据
const form = reactive({
  title: '',
  description: '',
  type: '',
  category: '',
  url: '',
  thumbnail: '',
  duration: null,
  difficulty: '',
  tags: [],
  is_featured: false
})

// 计算属性
const isEdit = computed(() => !!props.resource)

// 表单验证规则
const rules = {
  title: [
    { required: true, message: '请输入资源标题', trigger: 'blur' },
    { min: 2, max: 255, message: '标题长度应在2-255个字符之间', trigger: 'blur' }
  ],
  description: [
    { required: true, message: '请输入资源描述', trigger: 'blur' },
    { min: 10, message: '描述至少10个字符', trigger: 'blur' }
  ],
  type: [
    { required: true, message: '请选择资源类型', trigger: 'change' }
  ],
  category: [
    { required: true, message: '请输入分类', trigger: 'blur' }
  ],
  difficulty: [
    { required: true, message: '请选择难度等级', trigger: 'change' }
  ],
  url: [
    { type: 'url', message: '请输入有效的URL', trigger: 'blur' }
  ],
  thumbnail: [
    { type: 'url', message: '请输入有效的图片URL', trigger: 'blur' }
  ]
}

// 监听resource变化，用于编辑模式
watch(() => props.resource, (newResource) => {
  if (newResource) {
    Object.assign(form, {
      title: newResource.title || '',
      description: newResource.description || '',
      type: newResource.type || '',
      category: newResource.category || '',
      url: newResource.url || '',
      thumbnail: newResource.thumbnail || '',
      duration: newResource.duration,
      difficulty: newResource.difficulty || '',
      tags: newResource.tags || [],
      is_featured: newResource.is_featured || false
    })
  }
}, { immediate: true })

// 方法
const handleSubmit = async () => {
  try {
    await formRef.value.validate()
    
    loading.value = true
    
    const submitData = {
      ...form,
      tags: form.tags.length > 0 ? form.tags : null
    }
    
    if (isEdit.value) {
      await resourceAPI.update(props.resource.id, submitData)
      ElMessage.success('资源更新成功')
    } else {
      await resourceAPI.create(submitData)
      ElMessage.success('资源创建成功')
    }
    
    emit('success')
  } catch (error) {
    console.error('提交失败:', error)
  } finally {
    loading.value = false
  }
}

const showNewTagInput = () => {
  showTagInput.value = true
  nextTick(() => {
    tagInput.value?.focus()
  })
}

const addTag = () => {
  const tag = newTag.value.trim()
  if (tag && !form.tags.includes(tag)) {
    form.tags.push(tag)
  }
  newTag.value = ''
  showTagInput.value = false
}

const removeTag = (index) => {
  form.tags.splice(index, 1)
}
</script>

<style scoped>
.resource-form {
  padding: 20px 0;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  margin-top: 30px;
  padding-top: 20px;
  border-top: 1px solid #e4e7ed;
}

.el-tag {
  margin-right: 8px;
  margin-bottom: 8px;
}
</style> 
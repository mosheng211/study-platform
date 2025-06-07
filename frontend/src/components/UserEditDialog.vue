<template>
  <el-dialog
    v-model="dialogVisible"
    title="编辑用户"
    width="600px"
    @close="$emit('update:visible', false)"
  >
    <el-form
      ref="formRef"
      :model="form"
      :rules="rules"
      label-width="100px"
    >
      <el-form-item label="用户名" prop="name">
        <el-input v-model="form.name" placeholder="请输入用户名" />
      </el-form-item>

      <el-form-item label="邮箱" prop="email">
        <el-input v-model="form.email" placeholder="请输入邮箱" />
      </el-form-item>

      <el-form-item label="角色" prop="role">
        <el-select v-model="form.role" placeholder="请选择角色" style="width: 100%">
          <el-option label="学生" value="student" />
          <el-option label="教师" value="teacher" />
          <el-option label="管理员" value="admin" />
        </el-select>
      </el-form-item>

      <el-form-item label="状态" prop="status">
        <el-radio-group v-model="form.status">
          <el-radio label="active">正常</el-radio>
          <el-radio label="disabled">禁用</el-radio>
        </el-radio-group>
      </el-form-item>

      <el-form-item label="重置密码">
        <el-switch
          v-model="resetPassword"
          active-text="是"
          inactive-text="否"
        />
      </el-form-item>

      <el-form-item v-if="resetPassword" label="新密码" prop="password">
        <el-input
          v-model="form.password"
          type="password"
          placeholder="请输入新密码"
          show-password
        />
      </el-form-item>

      <el-form-item v-if="resetPassword" label="确认密码" prop="password_confirmation">
        <el-input
          v-model="form.password_confirmation"
          type="password"
          placeholder="请确认密码"
          show-password
        />
      </el-form-item>
    </el-form>

    <template #footer>
      <el-button @click="$emit('update:visible', false)">取消</el-button>
      <el-button type="primary" @click="submitForm" :loading="loading">保存</el-button>
    </template>
  </el-dialog>
</template>

<script setup>
import { ref, reactive, watch, computed } from 'vue'
import { ElMessage } from 'element-plus'
import { adminAPI } from '@/api'

const props = defineProps({
  visible: Boolean,
  user: Object
})

const emit = defineEmits(['update:visible', 'updated'])

const dialogVisible = computed({
  get: () => props.visible,
  set: (value) => emit('update:visible', value)
})

const formRef = ref()
const loading = ref(false)
const resetPassword = ref(false)

const form = reactive({
  name: '',
  email: '',
  role: 'student',
  status: 'active',
  password: '',
  password_confirmation: ''
})

const rules = {
  name: [
    { required: true, message: '请输入用户名', trigger: 'blur' },
    { min: 2, max: 50, message: '用户名长度在 2 到 50 个字符', trigger: 'blur' }
  ],
  email: [
    { required: true, message: '请输入邮箱地址', trigger: 'blur' },
    { type: 'email', message: '请输入正确的邮箱格式', trigger: 'blur' }
  ],
  role: [
    { required: true, message: '请选择用户角色', trigger: 'change' }
  ],
  status: [
    { required: true, message: '请选择用户状态', trigger: 'change' }
  ],
  password: [
    { min: 6, message: '密码长度不能少于6个字符', trigger: 'blur' }
  ],
  password_confirmation: [
    {
      validator: (rule, value, callback) => {
        if (resetPassword.value) {
          if (value === '') {
            callback(new Error('请再次输入密码'))
          } else if (value !== form.password) {
            callback(new Error('两次输入密码不一致'))
          } else {
            callback()
          }
        } else {
          callback()
        }
      },
      trigger: 'blur'
    }
  ]
}

// 观察用户变化，初始化表单
watch(() => props.user, (newUser) => {
  if (newUser) {
    Object.assign(form, {
      name: newUser.name || '',
      email: newUser.email || '',
      role: newUser.role || 'student',
      status: newUser.status || 'active',
      password: '',
      password_confirmation: ''
    })
    resetPassword.value = false
  }
}, { immediate: true })

// 观察重置密码开关
watch(resetPassword, (newValue) => {
  if (!newValue) {
    form.password = ''
    form.password_confirmation = ''
  }
})

const submitForm = async () => {
  if (!formRef.value) return

  try {
    await formRef.value.validate()
    loading.value = true

    const updateData = {
      name: form.name,
      email: form.email,
      role: form.role,
      status: form.status
    }

    if (resetPassword.value) {
      updateData.password = form.password
      updateData.password_confirmation = form.password_confirmation
    }

    await adminAPI.updateUser(props.user.id, updateData)
    
    ElMessage.success('用户信息更新成功')
    emit('updated')
    emit('update:visible', false)
    
    // 重置表单
    resetPassword.value = false
    form.password = ''
    form.password_confirmation = ''
  } catch (error) {
    console.error('更新用户失败:', error)
    if (error.response?.data?.message) {
      ElMessage.error(error.response.data.message)
    } else {
      ElMessage.error('更新失败，请重试')
    }
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
.el-form {
  padding: 20px 0;
}
</style> 
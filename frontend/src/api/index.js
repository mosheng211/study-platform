import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
import { ElMessage } from 'element-plus'

// 创建axios实例
const api = axios.create({
  baseURL: 'http://localhost:8000/api',
  timeout: 10000,
  headers: {
    'Content-Type': 'application/json',
    'Accept': 'application/json'
  }
})

// 请求拦截器
api.interceptors.request.use(
  (config) => {
    const authStore = useAuthStore()
    if (authStore.token) {
      config.headers.Authorization = `Bearer ${authStore.token}`
    }
    return config
  },
  (error) => {
    return Promise.reject(error)
  }
)

// 响应拦截器
api.interceptors.response.use(
  (response) => {
    return response.data
  },
  (error) => {
    if (error.response) {
      const { status, data } = error.response
      
      switch (status) {
        case 401:
          const authStore = useAuthStore()
          authStore.logout()
          ElMessage.error('登录已过期，请重新登录')
          break
        case 403:
          ElMessage.error(data.message || '没有权限访问')
          break
        case 404:
          ElMessage.error(data.message || '资源不存在')
          break
        case 422:
          ElMessage.error(data.message || '数据验证失败')
          break
        case 500:
          ElMessage.error('服务器错误，请稍后重试')
          break
        default:
          ElMessage.error(data.message || '请求失败')
      }
    } else {
      ElMessage.error('网络错误，请检查网络连接')
    }
    
    return Promise.reject(error)
  }
)

// API服务
export const authAPI = {
  register: (data) => api.post('/auth/register', data),
  login: (data) => api.post('/auth/login', data),
  logout: () => api.post('/auth/logout'),
  profile: () => api.get('/auth/profile'),
  updateProfile: (data) => api.put('/auth/profile', data),
  changePassword: (data) => api.put('/auth/change-password', data)
}

export const resourceAPI = {
  getList: (params) => api.get('/resources', { params }),
  getById: (id) => api.get(`/resources/${id}`),
  create: (data) => api.post('/resources', data),
  update: (id, data) => api.put(`/resources/${id}`, data),
  delete: (id) => api.delete(`/resources/${id}`),
  getFeatured: () => api.get('/resources/featured'),
  getCategories: () => api.get('/resources/categories'),
  download: (id) => api.get(`/resources/${id}/download`)
}

export const progressAPI = {
  getList: (params) => api.get('/progress', { params }),
  getById: (dayNumber) => api.get(`/progress/${dayNumber}`),
  update: (dayNumber, data) => api.put(`/progress/${dayNumber}`, data),
  initialize: () => api.post('/progress/initialize'),
  getStats: () => api.get('/progress/stats')
}

export const checkinAPI = {
  getList: (params) => api.get('/checkin', { params }),
  create: (data) => api.post('/checkin', data),
  createCheckin: (data) => api.post('/checkin', data), // 添加别名方法
  getTodayCheckin: () => api.get('/checkin/today'),
  getTodayStatus: () => api.get('/checkin/today'),
  getCheckinHistory: (params) => api.get('/checkin/history', { params }),
  getHistory: (params) => api.get('/checkin/history', { params }),
  getCheckinDates: () => api.get('/checkin/dates'),
  getStats: () => api.get('/checkin/stats'),
  update: (id, data) => api.put(`/checkin/${id}`, data),
  delete: (id) => api.delete(`/checkin/${id}`),
  getLeaderboard: (params) => api.get('/leaderboard', { params })
}

// 排行榜 API
export const leaderboardAPI = {
  getRankings: (params) => api.get('/leaderboard', { params }),
  getPersonalRank: (params) => api.get('/leaderboard/personal', { params }),
  getRecentAchievements: () => api.get('/leaderboard/achievements')
}

export const adminAPI = {
  // 系统概览
  getSystemStats: () => api.get('/admin/stats'),
  
  // 用户管理
  getUsers: (params) => api.get('/admin/users', { params }),
  getUserDetail: (id) => api.get(`/admin/users/${id}`),
  updateUser: (id, data) => api.put(`/admin/users/${id}`, data),
  updateUserStatus: (id, data) => api.patch(`/admin/users/${id}/status`, data),
  deleteUser: (id) => api.delete(`/admin/users/${id}`),
  createUser: (data) => api.post('/admin/users', data),
  getUserProgress: (userId) => api.get(`/admin/users/${userId}/progress`),
  getUserCheckins: (userId) => api.get(`/admin/users/${userId}/checkins`),
  
  // 学习数据
  getStudyData: (params) => api.get('/admin/study-data', { params }),
  
  // 资源管理  
  getResources: (params) => api.get('/admin/resources', { params }),
  getResourceDetail: (id) => api.get(`/admin/resources/${id}`),
  createResource: (data) => api.post('/admin/resources', data),
  updateResource: (id, data) => api.put(`/admin/resources/${id}`, data),
  deleteResource: (id) => api.delete(`/admin/resources/${id}`),
  
  // 分类管理
  getCategories: (params) => api.get('/admin/categories', { params }),
  getActiveCategories: () => api.get('/admin/categories/active'),
  getCategoryDetail: (id) => api.get(`/admin/categories/${id}`),
  createCategory: (data) => api.post('/admin/categories', data),
  updateCategory: (id, data) => api.put(`/admin/categories/${id}`, data),
  deleteCategory: (id) => api.delete(`/admin/categories/${id}`),
  
  // 系统设置
  getSystemSettings: () => api.get('/admin/settings'),
  updateSystemSettings: (data) => api.put('/admin/settings', data),
  
  // 备份管理
  createBackup: () => api.post('/admin/backup'),
  getBackupHistory: () => api.get('/admin/backup'),
  deleteBackup: (id) => api.delete(`/admin/backup/${id}`),
  downloadBackup: (id) => api.get(`/admin/backup/${id}/download`, { responseType: 'blob' }),
  
  // 数据导出
  exportData: (params) => api.get('/admin/export', { params })
}

export default api 
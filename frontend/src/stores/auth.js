import { defineStore } from 'pinia'
import { authAPI } from '@/api/index'

export const useAuthStore = defineStore('auth', {
  state: () => ({
    user: null,
    token: localStorage.getItem('token') || null,
    loading: false,
    error: null
  }),

  getters: {
    isAuthenticated: (state) => !!state.token,
    isAdmin: (state) => state.user?.role === 'admin',
    isTeacher: (state) => ['teacher', 'admin'].includes(state.user?.role),
    userName: (state) => state.user?.real_name || state.user?.username
  },

  actions: {
    // 设置认证令牌
    setToken(token) {
      this.token = token
      localStorage.setItem('token', token)
    },

    // 清除认证信息
    clearAuth() {
      this.user = null
      this.token = null
      this.error = null
      localStorage.removeItem('token')
    },

    // 用户注册
    async register(userData) {
      this.loading = true
      this.error = null
      
      try {
        const response = await authAPI.register(userData)
        
        if (response.success) {
          this.user = response.data.user
          this.setToken(response.data.token)
          return { success: true }
        }
      } catch (error) {
        this.error = error.response?.data?.message || '注册失败'
        return { success: false, message: this.error }
      } finally {
        this.loading = false
      }
    },

    // 用户登录
    async login(credentials) {
      this.loading = true
      this.error = null
      
      try {
        const response = await authAPI.login(credentials)
        
        if (response.success) {
          this.user = response.data.user
          this.setToken(response.data.token)
          return { success: true }
        }
      } catch (error) {
        this.error = error.response?.data?.message || '登录失败'
        return { success: false, message: this.error }
      } finally {
        this.loading = false
      }
    },

    // 用户登出
    async logout() {
      try {
        if (this.token) {
          await authAPI.logout()
        }
      } catch (error) {
        console.error('Logout error:', error)
      } finally {
        this.clearAuth()
      }
    },

    // 获取用户信息
    async fetchProfile() {
      if (!this.token) return

      try {
        const response = await authAPI.profile()
        
        if (response.success) {
          this.user = response.data.user
        }
      } catch (error) {
        if (error.response?.status === 401) {
          this.clearAuth()
        }
      }
    },

    // 更新用户信息
    async updateProfile(userData) {
      this.loading = true
      this.error = null
      
      try {
        const response = await authAPI.updateProfile(userData)
        
        if (response.success) {
          this.user = response.data.user
          return { success: true }
        }
      } catch (error) {
        this.error = error.response?.data?.message || '更新失败'
        return { success: false, message: this.error }
      } finally {
        this.loading = false
      }
    },

    // 修改密码
    async changePassword(passwordData) {
      this.loading = true
      this.error = null
      
      try {
        const response = await authAPI.changePassword(passwordData)
        
        if (response.success) {
          return { success: true, message: response.message }
        }
      } catch (error) {
        this.error = error.response?.data?.message || '修改密码失败'
        return { success: false, message: this.error }
      } finally {
        this.loading = false
      }
    },

    // 初始化认证状态
    async initAuth() {
      if (this.token) {
        await this.fetchProfile()
      }
    }
  }
}) 
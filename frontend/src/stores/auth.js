import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/api/axios'

export const useAuthStore = defineStore('auth', () => {
  const user = ref(null)
  const token = ref(null)
  const isAuthenticated = ref(false)

  async function login(email, password) {
    try {
      const response = await api.post('/login', { email, password })
      
      if (response.data.success) {
        user.value = response.data.data.user
        token.value = response.data.data.token
        isAuthenticated.value = true
        return { success: true }
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Login failed'
      }
    }
  }

  async function logout() {
    try {
      await api.post('/logout')
    } catch (error) {
      console.error('Logout error:', error)
    } finally {
      user.value = null
      token.value = null
      isAuthenticated.value = false
    }
  }

  async function getUser() {
    try {
      const response = await api.get('/me')
      if (response.data.success) {
        user.value = response.data.data
      }
    } catch (error) {
      console.error('Get user error:', error)
    }
  }

  return {
    user,
    token,
    isAuthenticated,
    login,
    logout,
    getUser
  }
}, {
  persist: true
})

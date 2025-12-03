import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/api/axios'

export const useDashboardStore = defineStore('dashboard', () => {
  const dashboardData = ref(null)
  const loading = ref(false)

  async function fetchDashboard(params = {}) {
    loading.value = true
    try {
      const response = await api.get('/dashboard', { params })
      if (response.data.success) {
        dashboardData.value = response.data.data
      }
    } catch (error) {
      console.error('Fetch dashboard error:', error)
    } finally {
      loading.value = false
    }
  }

  async function fetchLowStockAlert() {
    try {
      const response = await api.get('/dashboard/low-stock-alert')
      if (response.data.success) {
        return { success: true, data: response.data.data }
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch low stock alert'
      }
    }
  }

  return {
    dashboardData,
    loading,
    fetchDashboard,
    fetchLowStockAlert
  }
})

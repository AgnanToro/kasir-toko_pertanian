import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/api/axios'

export const useCategoryStore = defineStore('category', () => {
  const categories = ref([])
  const loading = ref(false)

  async function fetchCategories(params = {}) {
    loading.value = true
    try {
      const response = await api.get('/categories', { params })
      if (response.data.success) {
        categories.value = response.data.data
      }
    } catch (error) {
      console.error('Fetch categories error:', error)
    } finally {
      loading.value = false
    }
  }

  async function createCategory(data) {
    try {
      const response = await api.post('/categories', data)
      if (response.data.success) {
        return { success: true, data: response.data.data }
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to create category'
      }
    }
  }

  async function updateCategory(id, data) {
    try {
      const response = await api.put(`/categories/${id}`, data)
      if (response.data.success) {
        return { success: true, data: response.data.data }
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to update category'
      }
    }
  }

  async function deleteCategory(id) {
    try {
      const response = await api.delete(`/categories/${id}`)
      if (response.data.success) {
        return { success: true }
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to delete category'
      }
    }
  }

  return {
    categories,
    loading,
    fetchCategories,
    createCategory,
    updateCategory,
    deleteCategory
  }
})

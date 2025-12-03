import { defineStore } from 'pinia'
import { ref } from 'vue'
import api from '@/api/axios'

export const useProductStore = defineStore('product', () => {
  const products = ref([])
  const product = ref(null)
  const loading = ref(false)
  const pagination = ref({})

  async function fetchProducts(params = {}) {
    loading.value = true
    try {
      const response = await api.get('/products', { params })
      if (response.data.success) {
        products.value = response.data.data.data
        pagination.value = {
          current_page: response.data.data.current_page,
          last_page: response.data.data.last_page,
          per_page: response.data.data.per_page,
          total: response.data.data.total
        }
      }
    } catch (error) {
      console.error('Fetch products error:', error)
    } finally {
      loading.value = false
    }
  }

  async function fetchProduct(id) {
    loading.value = true
    try {
      const response = await api.get(`/products/${id}`)
      if (response.data.success) {
        product.value = response.data.data
      }
    } catch (error) {
      console.error('Fetch product error:', error)
    } finally {
      loading.value = false
    }
  }

  async function searchByBarcode(barcode) {
    try {
      const response = await api.get(`/products/barcode/${barcode}`)
      if (response.data.success) {
        return { success: true, data: response.data.data }
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Product not found'
      }
    }
  }

  async function createProduct(data) {
    try {
      const formData = new FormData()
      for (const key in data) {
        if (data[key] !== null && data[key] !== undefined) {
          formData.append(key, data[key])
        }
      }

      const response = await api.post('/products', formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })

      if (response.data.success) {
        return { success: true, data: response.data.data }
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to create product'
      }
    }
  }

  async function updateProduct(id, data) {
    try {
      const formData = new FormData()
      for (const key in data) {
        if (data[key] !== null && data[key] !== undefined) {
          formData.append(key, data[key])
        }
      }
      formData.append('_method', 'PUT')

      const response = await api.post(`/products/${id}`, formData, {
        headers: { 'Content-Type': 'multipart/form-data' }
      })

      if (response.data.success) {
        return { success: true, data: response.data.data }
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to update product'
      }
    }
  }

  async function deleteProduct(id) {
    try {
      const response = await api.delete(`/products/${id}`)
      if (response.data.success) {
        return { success: true }
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to delete product'
      }
    }
  }

  async function fetchLowStock() {
    try {
      const response = await api.get('/products/low-stock')
      if (response.data.success) {
        return { success: true, data: response.data.data }
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to fetch low stock'
      }
    }
  }

  return {
    products,
    product,
    loading,
    pagination,
    fetchProducts,
    fetchProduct,
    searchByBarcode,
    createProduct,
    updateProduct,
    deleteProduct,
    fetchLowStock
  }
})

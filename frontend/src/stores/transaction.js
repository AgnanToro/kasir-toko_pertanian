import { defineStore } from 'pinia'
import { ref, computed } from 'vue'
import api from '@/api/axios'

export const useTransactionStore = defineStore('transaction', () => {
  const transactions = ref([])
  const transaction = ref(null)
  const loading = ref(false)
  const pagination = ref({})

  // Cart state
  const cartItems = ref([])

  const cartTotal = computed(() => {
    return cartItems.value.reduce((total, item) => {
      return total + (item.price * item.quantity - (item.discount || 0))
    }, 0)
  })

  const cartCount = computed(() => {
    return cartItems.value.reduce((total, item) => total + item.quantity, 0)
  })

  function addToCart(product) {
    const existingItem = cartItems.value.find(item => item.product_id === product.id)
    
    if (existingItem) {
      if (existingItem.quantity < product.stock) {
        existingItem.quantity++
      } else {
        return { success: false, message: 'Insufficient stock' }
      }
    } else {
      if (product.stock > 0) {
        cartItems.value.push({
          product_id: product.id,
          product_name: product.name,
          product_sku: product.sku,
          price: parseFloat(product.selling_price),
          quantity: 1,
          discount: 0,
          max_stock: product.stock
        })
      } else {
        return { success: false, message: 'Out of stock' }
      }
    }
    
    return { success: true }
  }

  function updateCartItem(productId, quantity) {
    const item = cartItems.value.find(i => i.product_id === productId)
    if (item) {
      if (quantity > 0 && quantity <= item.max_stock) {
        item.quantity = quantity
        return { success: true }
      } else if (quantity > item.max_stock) {
        return { success: false, message: 'Quantity exceeds stock' }
      }
    }
    return { success: false, message: 'Item not found' }
  }

  function updateCartItemDiscount(productId, discount) {
    const item = cartItems.value.find(i => i.product_id === productId)
    if (item) {
      item.discount = parseFloat(discount) || 0
    }
  }

  function removeFromCart(productId) {
    const index = cartItems.value.findIndex(i => i.product_id === productId)
    if (index !== -1) {
      cartItems.value.splice(index, 1)
    }
  }

  function clearCart() {
    cartItems.value = []
  }

  async function fetchTransactions(params = {}) {
    loading.value = true
    try {
      const response = await api.get('/transactions', { params })
      if (response.data.success) {
        transactions.value = response.data.data.data
        pagination.value = {
          current_page: response.data.data.current_page,
          last_page: response.data.data.last_page,
          per_page: response.data.data.per_page,
          total: response.data.data.total
        }
      }
    } catch (error) {
      console.error('Fetch transactions error:', error)
    } finally {
      loading.value = false
    }
  }

  async function fetchTransaction(id) {
    loading.value = true
    try {
      const response = await api.get(`/transactions/${id}`)
      if (response.data.success) {
        transaction.value = response.data.data
      }
    } catch (error) {
      console.error('Fetch transaction error:', error)
    } finally {
      loading.value = false
    }
  }

  async function createTransaction(data) {
    try {
      const response = await api.post('/transactions', data)
      if (response.data.success) {
        clearCart()
        return { success: true, data: response.data.data }
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to create transaction'
      }
    }
  }

  async function deleteTransaction(id) {
    try {
      const response = await api.delete(`/transactions/${id}`)
      if (response.data.success) {
        return { success: true }
      }
    } catch (error) {
      return {
        success: false,
        message: error.response?.data?.message || 'Failed to delete transaction'
      }
    }
  }

  return {
    transactions,
    transaction,
    loading,
    pagination,
    cartItems,
    cartTotal,
    cartCount,
    addToCart,
    updateCartItem,
    updateCartItemDiscount,
    removeFromCart,
    clearCart,
    fetchTransactions,
    fetchTransaction,
    createTransaction,
    deleteTransaction
  }
})

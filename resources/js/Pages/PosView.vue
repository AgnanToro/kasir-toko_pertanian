<template>
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    <!-- Product Selection -->
    <div class="lg:col-span-2">
      <div class="card">
        <!-- Search & Filter Bar -->
        <div class="mb-4">
          <div class="flex gap-2">
            <!-- Unified Search Input (Wider) -->
            <div class="relative flex-[2]">
              <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
              <input
                v-model="searchQuery"
                @input="filterProducts"
                @keyup.enter="quickAddBySearch"
                type="text"
                class="input pl-10 w-full"
                placeholder="Cari nama produk, SKU, atau scan barcode..."
              />
            </div>
            
            <!-- Category Filter (Narrower) -->
            <select v-model="selectedCategory" @change="filterProducts" class="input flex-1">
              <option value="">Semua Kategori</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">
                {{ category.name }}
              </option>
            </select>
          </div>
        </div>

        <!-- Product Grid -->
        <div class="grid grid-cols-3 gap-4 max-h-[600px] overflow-y-auto">
          <!-- Loading State -->
          <div v-if="loading" class="col-span-full text-center py-12">
            <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-3"></i>
            <p class="text-gray-500">Memuat produk...</p>
          </div>

          <!-- Empty State -->
          <div v-else-if="paginatedProducts.length === 0" class="col-span-full text-center py-12">
            <i class="fas fa-inbox text-5xl text-gray-300 mb-3"></i>
            <p class="text-gray-500">Produk tidak ditemukan</p>
            <p class="text-sm text-gray-400">Coba kata kunci lain atau pilih kategori berbeda</p>
          </div>

          <!-- Product Cards -->
          <div
            v-for="product in paginatedProducts"
            :key="product.id"
            @click="addProductToCart(product)"
            class="border rounded-lg overflow-hidden hover:shadow-lg cursor-pointer transition-all transform hover:scale-105"
            :class="product.stock <= 0 ? 'opacity-50 cursor-not-allowed' : ''"
          >
            <!-- Product Image -->
            <div class="aspect-square bg-gray-100 flex items-center justify-center overflow-hidden">
              <img
                v-if="product.image"
                :src="getImageUrl(product.image)"
                :alt="product.name"
                class="w-full h-full object-cover"
                @error="handleImageError"
              />
              <div v-else class="text-5xl text-gray-300">
                <i class="fas fa-box-open"></i>
              </div>
            </div>

            <!-- Product Info -->
            <div class="p-3">
              <h4 class="font-semibold text-sm mb-1 truncate" :title="product.name">
                {{ product.name }}
              </h4>
              <p class="text-xs text-gray-500 mb-2 truncate">
                <i class="fas fa-tag mr-1"></i>{{ product.category?.name || 'Tanpa Kategori' }}
              </p>
              <div class="flex items-center justify-between">
                <p class="text-emerald-600 font-bold text-sm">
                  Rp {{ formatNumber(product.selling_price) }}
                </p>
                <div class="relative">
                  <p 
                    class="text-xs px-2 py-1 rounded-full"
                    :class="product.stock <= product.minimum_stock ? 'bg-red-100 text-red-600 animate-pulse font-bold' : 'bg-gray-100 text-gray-600'"
                  >
                    <i :class="product.stock <= product.minimum_stock ? 'fas fa-exclamation-triangle' : 'fas fa-boxes'" class="mr-1"></i>
                    {{ product.stock }}
                  </p>
                  <!-- Low Stock Ping Animation -->
                  <div v-if="product.stock <= product.minimum_stock" class="absolute -top-1 -right-1">
                    <span class="flex h-2 w-2">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                    </span>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Pagination (Compact with Numbers) -->
        <div v-if="totalPages > 1" class="mt-4 pt-4 border-t border-gray-200">
          <div class="flex items-center justify-center gap-1">
            <!-- First Button -->
            <button
              @click="currentPage = 1"
              :disabled="currentPage === 1"
              class="px-2.5 py-1.5 text-xs rounded-lg font-medium transition-colors"
              :class="currentPage === 1 
                ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                : 'bg-white border border-gray-300 text-gray-700 hover:bg-emerald-50 hover:border-emerald-500'"
            >
              <i class="fas fa-angle-double-left"></i>
            </button>

            <!-- Previous Button -->
            <button
              @click="currentPage--"
              :disabled="currentPage === 1"
              class="px-2.5 py-1.5 text-xs rounded-lg font-medium transition-colors"
              :class="currentPage === 1 
                ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                : 'bg-white border border-gray-300 text-gray-700 hover:bg-emerald-50 hover:border-emerald-500'"
            >
              <i class="fas fa-angle-left"></i>
            </button>

            <!-- Page Numbers -->
            <button
              v-for="page in visiblePages"
              :key="page"
              @click="currentPage = page"
              class="px-3 py-1.5 text-xs rounded-lg font-medium transition-colors min-w-[32px]"
              :class="currentPage === page 
                ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-md' 
                : 'bg-white border border-gray-300 text-gray-700 hover:bg-emerald-50 hover:border-emerald-500'"
            >
              {{ page }}
            </button>

            <!-- Next Button -->
            <button
              @click="currentPage++"
              :disabled="currentPage === totalPages"
              class="px-2.5 py-1.5 text-xs rounded-lg font-medium transition-colors"
              :class="currentPage === totalPages 
                ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                : 'bg-white border border-gray-300 text-gray-700 hover:bg-emerald-50 hover:border-emerald-500'"
            >
              <i class="fas fa-angle-right"></i>
            </button>

            <!-- Last Button -->
            <button
              @click="currentPage = totalPages"
              :disabled="currentPage === totalPages"
              class="px-2.5 py-1.5 text-xs rounded-lg font-medium transition-colors"
              :class="currentPage === totalPages 
                ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
                : 'bg-white border border-gray-300 text-gray-700 hover:bg-emerald-50 hover:border-emerald-500'"
            >
              <i class="fas fa-angle-double-right"></i>
            </button>
          </div>

          <!-- Page Info Text -->
          <p class="text-center text-xs text-gray-500 mt-2">
            Menampilkan {{ ((currentPage - 1) * perPage) + 1 }} - {{ Math.min(currentPage * perPage, filteredProducts.length) }} dari {{ filteredProducts.length }} produk
          </p>
        </div>
      </div>
    </div>

    <!-- Cart & Checkout -->
    <div class="lg:col-span-1">
      <div class="card sticky top-6">
        <!-- Header -->
        <div class="flex items-center justify-between mb-4 pb-3 border-b">
          <h3 class="text-lg font-bold text-gray-800">
            <i class="fas fa-shopping-cart mr-2 text-emerald-600"></i>
            Keranjang Belanja
          </h3>
          <span class="bg-emerald-100 text-emerald-700 px-2 py-1 rounded text-xs font-semibold">
            {{ cartItems.length }}
          </span>
        </div>

        <!-- Cart Items -->
        <div class="mb-4 max-h-64 overflow-y-auto space-y-2">
          <div v-if="cartItems.length === 0" class="text-center py-8 text-gray-400">
            <i class="fas fa-shopping-basket text-4xl mb-2"></i>
            <p class="text-sm">Keranjang masih kosong</p>
          </div>

          <div v-for="item in cartItems" :key="item.product_id" class="border rounded p-2">
            <div class="flex justify-between items-start mb-2">
              <h4 class="font-semibold text-sm flex-1">{{ item.product_name }}</h4>
              <button @click="removeItem(item.product_id)" class="text-red-500 hover:text-red-700 ml-2">
                <i class="fas fa-times text-xs"></i>
              </button>
            </div>
            <div class="flex items-center gap-2 mb-1">
              <button
                @click="decreaseQuantity(item.product_id)"
                class="w-7 h-7 border rounded hover:bg-gray-100"
              >
                <i class="fas fa-minus text-xs"></i>
              </button>
              <input
                :value="item.quantity"
                @input="updateQuantity(item.product_id, $event.target.value)"
                type="number"
                class="w-12 text-center border rounded py-1 text-sm"
                min="1"
                :max="item.max_stock"
              />
              <button
                @click="increaseQuantity(item.product_id)"
                class="w-7 h-7 border rounded hover:bg-gray-100"
              >
                <i class="fas fa-plus text-xs"></i>
              </button>
              <span class="text-xs text-gray-600 ml-auto">Rp {{ formatNumber(item.price) }}</span>
            </div>
            <div class="text-xs text-gray-600">
              Subtotal: <span class="font-semibold text-emerald-600">Rp {{ formatNumber(item.price * item.quantity) }}</span>
            </div>
          </div>
        </div>

        <!-- Totals -->
        <div class="border-t pt-3 mb-3 space-y-2">
          <div class="flex justify-between text-sm">
            <span class="text-gray-600">Subtotal</span>
            <span class="font-semibold">Rp {{ formatNumber(subtotal) }}</span>
          </div>
          
          <div class="flex justify-between items-center text-sm">
            <span class="text-gray-600">Diskon</span>
            <input
              v-model.number="discount"
              type="number"
              class="w-24 text-right border rounded px-2 py-1 text-sm"
              min="0"
              :max="subtotal"
              placeholder="0"
            />
          </div>
          
          <div class="flex justify-between items-center text-sm">
            <span class="text-gray-600">Pajak</span>
            <input
              v-model.number="tax"
              type="number"
              class="w-24 text-right border rounded px-2 py-1 text-sm"
              min="0"
              placeholder="0"
            />
          </div>
          
          <div class="flex justify-between font-bold text-lg pt-2 border-t">
            <span>Total</span>
            <span class="text-emerald-600">Rp {{ formatNumber(total) }}</span>
          </div>
        </div>

        <!-- Payment Method -->
        <div class="mb-3">
          <label class="block text-sm font-semibold mb-1 text-gray-700">Metode Pembayaran</label>
          <select v-model="paymentMethod" class="input w-full">
            <option value="cash">Cash</option>
            <option value="transfer">Transfer Bank</option>
            <option value="debit">Kartu Debit</option>
            <option value="credit">Kartu Kredit</option>
          </select>
        </div>

        <!-- Payment Amount -->
        <div class="mb-3">
          <label class="block text-sm font-semibold mb-1 text-gray-700">Jumlah Bayar</label>
          <input
            v-model.number="paidAmount"
            type="number"
            class="input w-full"
            placeholder="0"
            :min="total"
          />
        </div>

        <!-- Change Amount -->
        <div v-if="paidAmount >= total && paidAmount > 0" class="mb-3 p-3 bg-emerald-50 border border-emerald-200 rounded">
          <div class="flex justify-between items-center">
            <span class="font-semibold text-emerald-800">Kembalian</span>
            <span class="text-xl font-bold text-emerald-700">Rp {{ formatNumber(paidAmount - total) }}</span>
          </div>
        </div>

        <!-- Customer Name -->
        <div class="mb-4">
          <label class="block text-sm font-semibold mb-1 text-gray-700">
            Nama Pelanggan <span class="text-gray-400 font-normal">(Opsional)</span>
          </label>
          <input 
            v-model="customerName" 
            type="text" 
            class="input w-full" 
            placeholder="Nama pelanggan" 
          />
        </div>

        <!-- Action Buttons -->
        <div class="space-y-2">
          <button
            @click="checkout"
            :disabled="cartItems.length === 0 || paidAmount < total || loading"
            class="w-full py-3 rounded-lg font-semibold text-white transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            :class="cartItems.length === 0 || paidAmount < total || loading
              ? 'bg-gray-400 cursor-not-allowed' 
              : 'bg-emerald-600 hover:bg-emerald-700'"
          >
            <i class="fas fa-check-circle mr-2"></i>
            {{ loading ? 'Memproses...' : 'Bayar' }}
          </button>

          <button
            @click="clearCartItems"
            class="w-full py-2.5 rounded-lg font-semibold bg-red-600 text-white hover:bg-red-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
            :disabled="cartItems.length === 0"
          >
            <i class="fas fa-trash-alt mr-2"></i> 
            Bersihkan Keranjang
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Success Modal -->
  <div v-if="showSuccessModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-[9999]" @click.self="closeModal">
    <div class="bg-white rounded-lg p-8 max-w-md w-full mx-4 relative shadow-2xl">
      <!-- Close Button (X) di pojok kanan atas -->
      <button @click="closeModal" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 text-2xl font-bold w-8 h-8 flex items-center justify-center hover:bg-gray-100 rounded-full transition-colors">
        <i class="fas fa-times"></i>
      </button>
      
      <div class="text-center">
        <div class="text-6xl mb-4 text-green-500">
          <i class="fas fa-check-circle"></i>
        </div>
        <h3 class="text-2xl font-bold mb-2">Transaksi Berhasil!</h3>
        <p class="text-gray-600 mb-4">Invoice: {{ lastTransaction?.invoice_number }}</p>
        <p class="text-3xl font-bold text-primary-600 mb-6">
          Rp {{ formatNumber(lastTransaction?.total || 0) }}
        </p>
        <div class="flex gap-2">
          <button @click="printReceipt" class="flex-1 py-3 rounded-lg font-semibold text-white bg-emerald-600 hover:bg-emerald-700 transition-colors">
            <i class="fas fa-print mr-2"></i> Cetak Struk
          </button>
          <button @click="closeModal" class="flex-1 py-3 rounded-lg font-semibold text-gray-700 bg-white border-2 border-gray-300 hover:bg-gray-50 transition-colors">
            <i class="fas fa-times mr-2"></i> Tutup
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, watch } from 'vue'
import { useProductStore } from '@/stores/product'
import { useCategoryStore } from '@/stores/category'
import { useTransactionStore } from '@/stores/transaction'

const productStore = useProductStore()
const categoryStore = useCategoryStore()
const transactionStore = useTransactionStore()

const products = ref([])
const categories = ref([])
const barcodeInput = ref('')
const searchQuery = ref('')
const selectedCategory = ref('')
const currentPage = ref(1)
const perPage = 9  // 3 baris x 3 kolom
const discount = ref(0)
const tax = ref(0)
const paymentMethod = ref('cash')
const paidAmount = ref(0)
const customerName = ref('')
const loading = ref(false)
const showSuccessModal = ref(false)
const lastTransaction = ref(null)

const cartItems = computed(() => transactionStore.cartItems)
const subtotal = computed(() => transactionStore.cartTotal)
const total = computed(() => subtotal.value - discount.value + tax.value)

// Filtered products based on search and category
const filteredProducts = computed(() => {
  let filtered = [...products.value] // Create copy to avoid mutation

  // Filter by search query (nama produk, SKU, atau barcode)
  if (searchQuery.value && searchQuery.value.trim()) {
    const query = searchQuery.value.toLowerCase().trim()
    filtered = filtered.filter(product => 
      product.name.toLowerCase().includes(query) ||
      product.sku?.toLowerCase().includes(query) ||
      product.barcode?.toLowerCase().includes(query)
    )
  }

  // Filter by category
  if (selectedCategory.value) {
    filtered = filtered.filter(product => 
      product.category_id == selectedCategory.value
    )
  }

  return filtered
})

// Paginated products (9 per page = 3 rows x 3 cols)
const paginatedProducts = computed(() => {
  const start = (currentPage.value - 1) * perPage
  const end = start + perPage
  return filteredProducts.value.slice(start, end)
})

const totalPages = computed(() => {
  return Math.ceil(filteredProducts.value.length / perPage)
})

// Visible page numbers (max 5 pages shown)
const visiblePages = computed(() => {
  const pages = []
  const total = totalPages.value
  const current = currentPage.value
  
  if (total <= 5) {
    // Show all pages if 5 or less
    for (let i = 1; i <= total; i++) {
      pages.push(i)
    }
  } else {
    // Show 5 pages with current page in the middle
    if (current <= 3) {
      // Show first 5 pages
      for (let i = 1; i <= 5; i++) {
        pages.push(i)
      }
    } else if (current >= total - 2) {
      // Show last 5 pages
      for (let i = total - 4; i <= total; i++) {
        pages.push(i)
      }
    } else {
      // Show current page in the middle
      for (let i = current - 2; i <= current + 2; i++) {
        pages.push(i)
      }
    }
  }
  
  return pages
})

// Reset page to 1 when search or category changes
watch([searchQuery, selectedCategory], () => {
  currentPage.value = 1
})

onMounted(async () => {
  await loadCategories()
  await loadProducts()
})

async function loadProducts() {
  loading.value = true
  try {
    await productStore.fetchProducts({ per_page: 500, is_active: 1 })
    products.value = productStore.products || []
    console.log('Products loaded:', products.value.length)
  } catch (error) {
    console.error('Error loading products:', error)
    products.value = []
  } finally {
    loading.value = false
  }
}

async function loadCategories() {
  try {
    await categoryStore.fetchCategories({ per_page: 100 })
    categories.value = categoryStore.categories || []
    console.log('Categories loaded:', categories.value.length)
  } catch (error) {
    console.error('Error loading categories:', error)
    categories.value = []
  }
}

function filterProducts() {
  // Trigger computed property update
  // (computed will auto-update when searchQuery or selectedCategory changes)
}

// Quick add to cart when press Enter on search (for barcode scanner)
async function quickAddBySearch() {
  if (!searchQuery.value.trim()) return

  // Try to find exact match by SKU or barcode first
  const exactMatch = products.value.find(p => 
    p.sku === searchQuery.value || 
    p.barcode === searchQuery.value
  )

  if (exactMatch) {
    addProductToCart(exactMatch)
    searchQuery.value = '' // Clear search after adding
  } else if (filteredProducts.value.length === 1) {
    // If only one result, add it
    addProductToCart(filteredProducts.value[0])
    searchQuery.value = ''
  }
  // Otherwise, just show filtered results
}

function getImageUrl(imagePath) {
  if (!imagePath) return null
  
  // If full URL, return as is
  if (imagePath.startsWith('http')) {
    return imagePath
  }
  
  // If relative path, construct URL
  // Assuming images are stored in storage/app/public/products/
  return `${import.meta.env.VITE_API_URL || 'http://localhost:8000'}/storage/${imagePath}`
}

function handleImageError(event) {
  // Replace broken image with placeholder icon
  event.target.style.display = 'none'
  event.target.parentElement.innerHTML = '<i class="fas fa-box-open text-5xl text-gray-300"></i>'
}

function addProductToCart(product) {
  if (product.stock <= 0) {
    alert('Stok habis')
    return
  }

  const result = transactionStore.addToCart(product)
  if (!result.success) {
    alert(result.message)
  } else {
    // Play warning sound if product is low stock
    if (product.stock <= product.minimum_stock) {
      playLowStockWarning()
    }
  }
}

function updateQuantity(productId, value) {
  const qty = parseInt(value)
  if (!isNaN(qty) && qty > 0) {
    const result = transactionStore.updateCartItem(productId, qty)
    if (!result.success) {
      alert(result.message)
    }
  }
}

function increaseQuantity(productId) {
  const item = cartItems.value.find(i => i.product_id === productId)
  if (item) {
    updateQuantity(productId, item.quantity + 1)
  }
}

function decreaseQuantity(productId) {
  const item = cartItems.value.find(i => i.product_id === productId)
  if (item && item.quantity > 1) {
    updateQuantity(productId, item.quantity - 1)
  }
}

function removeItem(productId) {
  transactionStore.removeFromCart(productId)
}

function clearCartItems() {
  if (confirm('Hapus semua item di keranjang?')) {
    transactionStore.clearCart()
    discount.value = 0
    tax.value = 0
    paidAmount.value = 0
    customerName.value = ''
  }
}

async function checkout() {
  if (cartItems.value.length === 0) {
    alert('Keranjang masih kosong')
    return
  }

  if (paidAmount.value < total.value) {
    alert('Jumlah bayar kurang')
    return
  }

  loading.value = true

  const transactionData = {
    items: cartItems.value,
    discount: discount.value,
    tax: tax.value,
    paid_amount: paidAmount.value,
    payment_method: paymentMethod.value,
    customer_name: customerName.value || null,
  }

  const result = await transactionStore.createTransaction(transactionData)

  if (result.success) {
    lastTransaction.value = result.data
    showSuccessModal.value = true
    
    // Reset form
    discount.value = 0
    tax.value = 0
    paidAmount.value = 0
    customerName.value = ''
    
    // Reload products to update stock
    await loadProducts()
  } else {
    alert('Transaksi gagal: ' + result.message)
  }

  loading.value = false
}

function printReceipt() {
  // Open small popup window at the side (not blocking the modal)
  const printWindow = window.open('', '', 'width=350,height=650,left=100,top=100')
  
  printWindow.document.write(`
    <html>
      <head>
        <title>Struk - ${lastTransaction.value?.invoice_number}</title>
        <style>
          body { font-family: monospace; font-size: 12px; padding: 10px; margin: 0; }
          .center { text-align: center; }
          .line { border-top: 1px dashed #000; margin: 10px 0; }
        </style>
      </head>
      <body>
        <div class="center">
          <h2>TOKO PERTANIAN SINAR JAYA</h2>
          <p>Jl. Pertanian No. 123</p>
          <p>Telp: 0812-3456-7890</p>
        </div>
        <div class="line"></div>
        <p>Invoice: ${lastTransaction.value?.invoice_number}</p>
        <p>Tanggal: ${new Date().toLocaleString('id-ID')}</p>
        <p>Kasir: ${lastTransaction.value?.user?.name}</p>
        ${customerName.value ? `<p>Pelanggan: ${customerName.value}</p>` : ''}
        <div class="line"></div>
        ${lastTransaction.value?.items.map(item => `
          <p>${item.product_name}<br/>
          ${item.quantity} x ${formatNumber(item.price)} = ${formatNumber(item.total)}</p>
        `).join('')}
        <div class="line"></div>
        <p>Subtotal: Rp ${formatNumber(lastTransaction.value?.subtotal)}</p>
        <p>Diskon: Rp ${formatNumber(lastTransaction.value?.discount)}</p>
        <p>Pajak: Rp ${formatNumber(lastTransaction.value?.tax)}</p>
        <p><strong>TOTAL: Rp ${formatNumber(lastTransaction.value?.total)}</strong></p>
        <p>Bayar: Rp ${formatNumber(lastTransaction.value?.paid_amount)}</p>
        <p>Kembali: Rp ${formatNumber(lastTransaction.value?.change_amount)}</p>
        <div class="line"></div>
        <p class="center">Terima Kasih</p>
        <p class="center">Selamat Berbelanja Kembali</p>
        
        <div class="center" style="margin-top: 20px;">
          <button onclick="window.print()" style="padding: 10px 20px; background: #10b981; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px; margin-right: 10px;">
            Cetak / Save PDF
          </button>
          <button onclick="window.close()" style="padding: 10px 20px; background: #ef4444; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">
            Tutup
          </button>
        </div>
      </body>
    </html>
  `)
  printWindow.document.close()
}

function closeModal() {
  showSuccessModal.value = false
  lastTransaction.value = null
}

function playLowStockWarning() {
  // Play a short warning beep when adding low stock product to cart
  const audioContext = new (window.AudioContext || window.webkitAudioContext)()
  const oscillator = audioContext.createOscillator()
  const gainNode = audioContext.createGain()
  
  oscillator.connect(gainNode)
  gainNode.connect(audioContext.destination)
  
  // Warning tone
  oscillator.frequency.setValueAtTime(600, audioContext.currentTime)
  oscillator.type = 'sine'
  
  gainNode.gain.setValueAtTime(0.2, audioContext.currentTime)
  gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.3)
  
  oscillator.start(audioContext.currentTime)
  oscillator.stop(audioContext.currentTime + 0.2)
  
  // Find the product that was just added
  const cartItems = transactionStore.cartItems || []
  const lastItem = cartItems[cartItems.length - 1]
  
  if (lastItem && 'speechSynthesis' in window) {
    // Speak warning after beep with Indonesian voice check
    setTimeout(() => {
      const speakWarning = () => {
        const stockStatus = lastItem.product?.stock === 0 ? 'habis' : `hanya tersisa ${lastItem.product?.stock || 0} unit`
        const utterance = new SpeechSynthesisUtterance(
          `Perhatian! Stok ${lastItem.product_name} ${stockStatus}`
        )
        utterance.lang = 'id-ID'
        utterance.rate = 0.95
        utterance.pitch = 1
        utterance.volume = 0.8
        
        // Get available voices
        const voices = window.speechSynthesis.getVoices()
        
        // Try to find Indonesian voice
        const indonesianVoice = voices.find(voice => 
          voice.lang === 'id-ID' || 
          voice.lang === 'id' ||
          voice.lang.startsWith('id-') ||
          voice.name.toLowerCase().includes('indonesia')
        )
        
        if (indonesianVoice) {
          utterance.voice = indonesianVoice
          window.speechSynthesis.speak(utterance)
        } else {
          // If no Indonesian voice, don't speak (just beep already played)
          console.warn('Indonesian voice not available for low stock warning')
        }
      }
      
      // Check if voices are already loaded
      let voices = window.speechSynthesis.getVoices()
      
      if (voices.length > 0) {
        speakWarning()
      } else {
        // Wait for voices to load
        window.speechSynthesis.onvoiceschanged = () => {
          speakWarning()
          window.speechSynthesis.onvoiceschanged = null
        }
      }
    }, 300)
  }
}

function formatNumber(num) {
  return new Intl.NumberFormat('id-ID').format(num)
}
</script>

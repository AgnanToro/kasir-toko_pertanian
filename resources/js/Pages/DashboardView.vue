<template>
  <div class="space-y-6">
    <!-- Filter Waktu -->
    <div class="bg-white rounded-2xl shadow-sm p-4 sm:p-5">
      <div class="flex flex-col gap-4">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
          <div>
            <h2 class="text-lg font-bold text-gray-900">Dashboard</h2>
            <p class="text-xs text-gray-500">Ringkasan data penjualan</p>
          </div>
          
          <!-- Quick Filter Buttons -->
          <div class="flex gap-2 overflow-x-auto pb-2 sm:pb-0">
            <button
              @click="setTimeFilter('today')"
              :class="[
                'px-4 py-2 rounded-lg text-sm font-medium transition-all whitespace-nowrap',
                timeFilter === 'today' 
                  ? 'bg-emerald-600 text-white shadow-md' 
                  : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
              ]"
            >
              <i class="fas fa-calendar-day mr-1.5"></i>
              Hari Ini
            </button>
            <button
              @click="setTimeFilter('week')"
              :class="[
                'px-4 py-2 rounded-lg text-sm font-medium transition-all whitespace-nowrap',
                timeFilter === 'week' 
                  ? 'bg-emerald-600 text-white shadow-md' 
                  : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
              ]"
            >
              <i class="fas fa-calendar-week mr-1.5"></i>
              Minggu Ini
            </button>
            <button
              @click="setTimeFilter('month')"
              :class="[
                'px-4 py-2 rounded-lg text-sm font-medium transition-all whitespace-nowrap',
                timeFilter === 'month' 
                  ? 'bg-emerald-600 text-white shadow-md' 
                  : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
              ]"
            >
              <i class="fas fa-calendar-alt mr-1.5"></i>
              Bulan Ini
            </button>
            <button
              @click="setTimeFilter('year')"
              :class="[
                'px-4 py-2 rounded-lg text-sm font-medium transition-all whitespace-nowrap',
                timeFilter === 'year' 
                  ? 'bg-emerald-600 text-white shadow-md' 
                  : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
              ]"
            >
              <i class="fas fa-calendar mr-1.5"></i>
              Tahun Ini
            </button>
            <button
              @click="setTimeFilter('custom')"
              :class="[
                'px-4 py-2 rounded-lg text-sm font-medium transition-all whitespace-nowrap',
                timeFilter === 'custom' 
                  ? 'bg-blue-600 text-white shadow-md' 
                  : 'bg-gray-100 text-gray-600 hover:bg-gray-200'
              ]"
            >
              <i class="fas fa-calendar-range mr-1.5"></i>
              Custom
            </button>
          </div>
        </div>

        <!-- Custom Date Range (shown when custom filter is active) -->
        <div v-if="timeFilter === 'custom'" class="flex flex-col sm:flex-row gap-3 p-4 bg-gray-50 rounded-lg border border-gray-200">
          <div class="flex-1">
            <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Mulai</label>
            <input
              v-model="customStartDate"
              type="date"
              class="input w-full"
              :max="customEndDate || today"
            />
          </div>
          <div class="flex-1">
            <label class="block text-xs font-medium text-gray-700 mb-1">Tanggal Akhir</label>
            <input
              v-model="customEndDate"
              type="date"
              class="input w-full"
              :min="customStartDate"
              :max="today"
            />
          </div>
          <div class="flex items-end">
            <button
              @click="applyCustomFilter"
              :disabled="!customStartDate || !customEndDate"
              class="btn btn-primary whitespace-nowrap h-[42px]"
            >
              <i class="fas fa-check mr-2"></i>
              Terapkan
            </button>
          </div>
        </div>

        <!-- Custom Range Display -->
        <div v-if="timeFilter === 'custom' && customStartDate && customEndDate && isCustomApplied" 
             class="flex items-center gap-2 text-sm text-gray-600 bg-blue-50 px-4 py-2 rounded-lg">
          <i class="fas fa-info-circle text-blue-600"></i>
          <span>Menampilkan data dari <strong>{{ formatDisplayDate(customStartDate) }}</strong> sampai <strong>{{ formatDisplayDate(customEndDate) }}</strong></span>
        </div>
      </div>
    </div>

    <!-- Low Stock Alert -->
    <div v-if="lowStockProducts.length > 0" class="bg-gradient-to-r from-red-50 to-orange-50 border-2 border-red-300 rounded-2xl shadow-lg overflow-hidden">
      <div class="p-5 sm:p-6">
        <!-- Alert Header -->
        <div class="flex items-start justify-between mb-4">
          <div class="flex items-start gap-3">
            <button
              @click="playAlertSound"
              class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center animate-pulse hover:bg-red-600 transition-colors cursor-pointer"
              title="Klik untuk putar suara alarm"
            >
              <i class="fas fa-exclamation-triangle text-white text-2xl"></i>
            </button>
            <div>
              <h3 class="text-xl font-bold text-red-700 flex items-center gap-2">
                <i class="fas fa-bell animate-pulse"></i>
                Peringatan Stok Minimum!
              </h3>
              <p class="text-sm text-red-600 mt-1">
                {{ lowStockProducts.length }} produk mencapai atau di bawah stok minimum
              </p>
              <p v-if="isAlarmActive" class="text-xs text-orange-600 font-semibold mt-1 animate-pulse">
                <i class="fas fa-bell-slash"></i> Alarm berulang aktif (setiap 5 detik)
              </p>
            </div>
          </div>
          <div class="flex flex-wrap gap-2">
            <button 
              @click="playAlertSound"
              class="px-4 py-2 bg-orange-600 text-white rounded-lg hover:bg-orange-700 transition-colors text-sm font-semibold"
              title="Putar suara alarm sekali"
            >
              <i class="fas fa-volume-up mr-2"></i>
              Putar Alarm
            </button>
            <button 
              v-if="!isAlarmActive"
              @click="startAlarmLoop"
              class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors text-sm font-semibold"
              title="Aktifkan alarm berulang"
            >
              <i class="fas fa-repeat mr-2"></i>
              Alarm Berulang
            </button>
            <button 
              v-if="isAlarmActive"
              @click="stopAlarmLoop"
              class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition-colors text-sm font-semibold animate-pulse"
              title="Matikan alarm berulang"
            >
              <i class="fas fa-stop mr-2"></i>
              Stop Alarm
            </button>
            <button 
              @click="showLowStockDetail = !showLowStockDetail"
              class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors text-sm font-semibold"
            >
              <i :class="showLowStockDetail ? 'fas fa-chevron-up' : 'fas fa-chevron-down'" class="mr-2"></i>
              {{ showLowStockDetail ? 'Sembunyikan' : 'Lihat Detail' }}
            </button>
          </div>
        </div>

        <!-- Alert Detail (Expandable) -->
        <div v-if="showLowStockDetail" class="space-y-3 mt-4 max-h-96 overflow-y-auto">
          <div 
            v-for="product in lowStockProducts" 
            :key="product.id"
            class="bg-white rounded-xl p-4 border-2 border-red-200 hover:border-red-400 transition-all"
          >
            <div class="flex items-center justify-between">
              <div class="flex items-center gap-4 flex-1">
                <!-- Product Image -->
                <div v-if="product.image" class="w-16 h-16 rounded-lg overflow-hidden flex-shrink-0">
                  <img :src="`/storage/${product.image}`" :alt="product.name" class="w-full h-full object-cover" />
                </div>
                <div v-else class="w-16 h-16 bg-gray-100 rounded-lg flex items-center justify-center flex-shrink-0">
                  <i class="fas fa-box text-2xl text-gray-300"></i>
                </div>

                <!-- Product Info -->
                <div class="flex-1 min-w-0">
                  <h4 class="font-bold text-gray-800 truncate">{{ product.name }}</h4>
                  <p class="text-sm text-gray-600">SKU: {{ product.sku }}</p>
                  <div class="flex items-center gap-4 mt-2">
                    <div class="flex items-center gap-2">
                      <span class="text-xs font-semibold text-gray-600">Stok Sekarang:</span>
                      <span class="px-2 py-1 bg-red-100 text-red-700 rounded-md text-sm font-bold">
                        {{ product.stock }} {{ product.unit }}
                      </span>
                    </div>
                    <div class="flex items-center gap-2">
                      <span class="text-xs font-semibold text-gray-600">Stok Minimum:</span>
                      <span class="px-2 py-1 bg-orange-100 text-orange-700 rounded-md text-sm font-bold">
                        {{ product.minimum_stock }} {{ product.unit }}
                      </span>
                    </div>
                  </div>
                </div>
              </div>

              <!-- Action Button -->
              <RouterLink
                :to="`/products/${product.id}/edit`"
                class="px-4 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors text-sm font-semibold whitespace-nowrap ml-4"
              >
                <i class="fas fa-edit mr-2"></i>
                Edit Stok
              </RouterLink>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
      <!-- Revenue Card -->
      <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-2xl shadow-lg p-5 sm:p-6 text-white transform hover:scale-105 transition-transform duration-200">
        <div class="flex items-start justify-between mb-3">
          <div>
            <p class="text-blue-100 text-xs sm:text-sm font-medium mb-1">Total Pendapatan</p>
            <h3 class="text-xl sm:text-2xl font-bold">
              Rp {{ formatNumber(dashboardData?.summary?.total_revenue || 0) }}
            </h3>
          </div>
          <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
            <i class="fas fa-wallet text-2xl"></i>
          </div>
        </div>
        <div class="flex items-center text-xs sm:text-sm text-blue-100">
          <i class="fas fa-calendar-check mr-1"></i>
          <span>{{ getFilterLabel() }}</span>
        </div>
      </div>

      <!-- Transactions Card -->
      <div class="bg-gradient-to-br from-emerald-500 to-emerald-600 rounded-2xl shadow-lg p-5 sm:p-6 text-white transform hover:scale-105 transition-transform duration-200">
        <div class="flex items-start justify-between mb-3">
          <div>
            <p class="text-emerald-100 text-xs sm:text-sm font-medium mb-1">Total Transaksi</p>
            <h3 class="text-xl sm:text-2xl font-bold">
              {{ dashboardData?.summary?.total_transactions || 0 }}
            </h3>
          </div>
          <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
            <i class="fas fa-receipt text-2xl"></i>
          </div>
        </div>
        <div class="flex items-center text-xs sm:text-sm text-emerald-100">
          <i class="fas fa-shopping-cart mr-1"></i>
          <span>Total penjualan</span>
        </div>
      </div>

      <!-- Products Sold Card -->
      <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-2xl shadow-lg p-5 sm:p-6 text-white transform hover:scale-105 transition-transform duration-200">
        <div class="flex items-start justify-between mb-3">
          <div>
            <p class="text-purple-100 text-xs sm:text-sm font-medium mb-1">Produk Terjual</p>
            <h3 class="text-xl sm:text-2xl font-bold">
              {{ dashboardData?.summary?.total_products_sold || 0 }}
            </h3>
          </div>
          <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
            <i class="fas fa-box-open text-2xl"></i>
          </div>
        </div>
        <div class="flex items-center text-xs sm:text-sm text-purple-100">
          <i class="fas fa-chart-line mr-1"></i>
          <span>Total item</span>
        </div>
      </div>

      <!-- Low Stock Card -->
      <div class="bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl shadow-lg p-5 sm:p-6 text-white transform hover:scale-105 transition-transform duration-200">
        <div class="flex items-start justify-between mb-3">
          <div>
            <p class="text-orange-100 text-xs sm:text-sm font-medium mb-1">Stok Menipis</p>
            <h3 class="text-xl sm:text-2xl font-bold">
              {{ dashboardData?.summary?.low_stock_count || 0 }}
            </h3>
          </div>
          <div class="w-12 h-12 bg-white bg-opacity-20 rounded-xl flex items-center justify-center">
            <i class="fas fa-exclamation-triangle text-2xl"></i>
          </div>
        </div>
        <div class="flex items-center text-xs sm:text-sm text-orange-100">
          <i class="fas fa-info-circle mr-1"></i>
          <span>Perlu restock</span>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 sm:gap-6">
      <!-- Daily Sales Chart -->
      <div class="bg-white rounded-2xl shadow-sm p-5 sm:p-6">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="text-lg font-bold text-gray-900">Penjualan Harian</h3>
            <p class="text-xs text-gray-500">Grafik penjualan 7 hari terakhir</p>
          </div>
          <div class="w-10 h-10 bg-emerald-100 rounded-xl flex items-center justify-center">
            <i class="fas fa-chart-line text-emerald-600"></i>
          </div>
        </div>
        <div class="h-64">
          <canvas ref="dailySalesChart"></canvas>
        </div>
      </div>

      <!-- Payment Method Chart -->
      <div class="bg-white rounded-2xl shadow-sm p-5 sm:p-6">
        <div class="flex items-center justify-between mb-4">
          <div>
            <h3 class="text-lg font-bold text-gray-900">Metode Pembayaran</h3>
            <p class="text-xs text-gray-500">Distribusi metode pembayaran</p>
          </div>
          <div class="w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
            <i class="fas fa-credit-card text-blue-600"></i>
          </div>
        </div>
        <div class="h-64 flex items-center justify-center">
          <canvas ref="paymentMethodChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Top Selling Products -->
    <div class="bg-white rounded-2xl shadow-sm p-5 sm:p-6">
      <div class="flex items-center justify-between mb-5">
        <div>
          <h3 class="text-lg font-bold text-gray-900">Produk Terlaris</h3>
          <p class="text-xs text-gray-500">Top 5 produk dengan penjualan tertinggi</p>
        </div>
        <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
          <i class="fas fa-trophy text-purple-600"></i>
        </div>
      </div>
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead>
            <tr class="border-b border-gray-200">
              <th class="text-left py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Produk</th>
              <th class="text-center py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Terjual</th>
              <th class="text-right py-3 px-4 text-xs font-semibold text-gray-600 uppercase tracking-wider">Pendapatan</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-for="(product, index) in dashboardData?.top_selling_products" :key="product.product_id" class="hover:bg-gray-50 transition-colors">
              <td class="py-4 px-4">
                <div class="flex items-center space-x-3">
                  <div class="flex-shrink-0 w-8 h-8 bg-gradient-to-br from-emerald-400 to-teal-500 rounded-lg flex items-center justify-center text-white font-bold text-sm">
                    {{ index + 1 }}
                  </div>
                  <span class="font-medium text-gray-900">{{ product.product_name }}</span>
                </div>
              </td>
              <td class="py-4 px-4 text-center">
                <span class="inline-flex items-center px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-sm font-semibold">
                  <i class="fas fa-box mr-1.5 text-xs"></i>
                  {{ product.total_sold }}
                </span>
              </td>
              <td class="py-4 px-4 text-right font-semibold text-gray-900">
                Rp {{ formatNumber(product.total_revenue) }}
              </td>
            </tr>
          </tbody>
        </table>
        <div v-if="!dashboardData?.top_selling_products?.length" class="text-center py-12">
          <i class="fas fa-inbox text-5xl text-gray-300 mb-3"></i>
          <p class="text-gray-500">Belum ada data produk terjual</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted, nextTick, watch, computed } from 'vue'
import { RouterLink } from 'vue-router'
import { useDashboardStore } from '@/stores/dashboard'
import { useProductStore } from '@/stores/product'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

const dashboardStore = useDashboardStore()
const productStore = useProductStore()
const dashboardData = ref(null)
const timeFilter = ref('today')

// Custom date range
const customStartDate = ref('')
const customEndDate = ref('')
const isCustomApplied = ref(false)

// Low stock alert
const lowStockProducts = ref([])
const showLowStockDetail = ref(true)
const isAlarmActive = ref(false)
let alarmInterval = null

// Get today's date for max attribute
const today = computed(() => {
  const date = new Date()
  return date.toISOString().split('T')[0]
})

const dailySalesChart = ref(null)
const paymentMethodChart = ref(null)

let dailyChartInstance = null
let paymentChartInstance = null

onMounted(async () => {
  // Load voices for speech synthesis
  if ('speechSynthesis' in window) {
    window.speechSynthesis.getVoices()
    // Some browsers need time to load voices
    window.speechSynthesis.onvoiceschanged = () => {
      window.speechSynthesis.getVoices()
    }
  }
  
  await loadDashboard()
  await loadLowStockProducts()
  
  // Auto play alert sound if there are low stock products
  if (lowStockProducts.value.length > 0) {
    // Play sound after 2 seconds delay to ensure page is loaded and user has interacted
    setTimeout(() => {
      playAlertSound()
      // Start alarm loop automatically after first play
      startAlarmLoop()
    }, 2000)
  }
})

// Cleanup alarm interval when component unmounts
onUnmounted(() => {
  stopAlarmLoop()
})

// Watch for filter changes (but not for custom until applied)
watch(timeFilter, async (newValue) => {
  if (newValue !== 'custom') {
    isCustomApplied.value = false
    await loadDashboard()
  }
})

async function setTimeFilter(filter) {
  timeFilter.value = filter
  
  // If switching away from custom, clear custom dates
  if (filter !== 'custom') {
    customStartDate.value = ''
    customEndDate.value = ''
    isCustomApplied.value = false
  }
}

async function applyCustomFilter() {
  if (!customStartDate.value || !customEndDate.value) {
    alert('Pilih tanggal mulai dan akhir')
    return
  }

  // Validate date range
  if (new Date(customStartDate.value) > new Date(customEndDate.value)) {
    alert('Tanggal mulai tidak boleh lebih besar dari tanggal akhir')
    return
  }

  isCustomApplied.value = true
  await loadDashboard()
}

function getFilterLabel() {
  if (timeFilter.value === 'custom' && isCustomApplied.value) {
    return 'Periode custom'
  }
  
  const labels = {
    today: 'Hari ini',
    week: 'Minggu ini',
    month: 'Bulan ini',
    year: 'Tahun ini',
    custom: 'Pilih periode'
  }
  return labels[timeFilter.value] || 'Hari ini'
}

function formatDisplayDate(dateString) {
  const date = new Date(dateString)
  return date.toLocaleDateString('id-ID', {
    day: 'numeric',
    month: 'long',
    year: 'numeric'
  })
}

async function loadDashboard() {
  const params = {}
  
  if (timeFilter.value === 'custom' && isCustomApplied.value) {
    // Use custom date range
    params.start_date = customStartDate.value
    params.end_date = customEndDate.value
  } else {
    // Use predefined period
    params.period = timeFilter.value
  }
  
  await dashboardStore.fetchDashboard(params)
  dashboardData.value = dashboardStore.dashboardData
  
  await nextTick()
  renderCharts()
}

async function loadLowStockProducts() {
  // Fetch all products
  await productStore.fetchProducts()
  const allProducts = productStore.products || []
  
  // Filter products where stock <= minimum_stock
  lowStockProducts.value = allProducts.filter(product => 
    product.stock <= product.minimum_stock
  ).sort((a, b) => a.stock - b.stock) // Sort by stock ascending (lowest first)
}

function playAlertSound() {
  // Create audio context for better browser support
  const audioContext = new (window.AudioContext || window.webkitAudioContext)()
  
  // Function to create a beep
  const createBeep = (startTime, frequency = 800, duration = 0.15) => {
    const oscillator = audioContext.createOscillator()
    const gainNode = audioContext.createGain()
    
    oscillator.connect(gainNode)
    gainNode.connect(audioContext.destination)
    
    oscillator.frequency.setValueAtTime(frequency, startTime)
    oscillator.type = 'sine'
    
    gainNode.gain.setValueAtTime(0.3, startTime)
    gainNode.gain.exponentialRampToValueAtTime(0.01, startTime + duration)
    
    oscillator.start(startTime)
    oscillator.stop(startTime + duration)
  }
  
  // Create alarm pattern: beep-beep-beep (repeat for ~15 seconds)
  const beepInterval = 0.3 // seconds between beeps
  const beepDuration = 0.15
  const totalDuration = 3 // 3 seconds of beeps before speech
  const numberOfBeeps = Math.floor(totalDuration / beepInterval)
  
  // Play beeps
  for (let i = 0; i < numberOfBeeps; i++) {
    createBeep(audioContext.currentTime + (i * beepInterval), 800, beepDuration)
  }
  
  // Text-to-Speech announcement after beeps
  setTimeout(() => {
    speakLowStockProducts()
  }, totalDuration * 1000)
}

function speakLowStockProducts() {
  // Check if browser supports speech synthesis
  if ('speechSynthesis' in window) {
    // Cancel any ongoing speech
    window.speechSynthesis.cancel()
    
    // Get top 3 low stock products
    const topLowStock = lowStockProducts.value.slice(0, 3)
    
    if (topLowStock.length === 0) return
    
    // Wait for voices to be loaded
    const speakWithVoice = () => {
      // Create speech text
      let speechText = 'Peringatan! '
      
      topLowStock.forEach((product, index) => {
        // Check if stock is 0 (habis) or just low (menipis)
        const stockStatus = product.stock === 0 ? 'habis' : 'menipis'
        
        if (index === 0) {
          speechText += `Stok ${product.name} ${stockStatus}. `
        } else if (index === 1 && topLowStock.length > 1) {
          speechText += `Stok ${product.name} ${stockStatus}. `
        } else if (index === 2 && topLowStock.length > 2) {
          speechText += `Dan stok ${product.name} ${stockStatus}. `
        }
      })
      
      if (lowStockProducts.value.length > 3) {
        speechText += `Dan ${lowStockProducts.value.length - 3} produk lainnya. `
      }
      
      speechText += 'Segera lakukan restok.'
      
      // Create speech utterance
      const utterance = new SpeechSynthesisUtterance(speechText)
      
      // Set voice properties
      utterance.lang = 'id-ID' // Indonesian language
      utterance.rate = 0.9 // Slightly slower for clarity
      utterance.pitch = 1
      utterance.volume = 1
      
      // Get available voices
      const voices = window.speechSynthesis.getVoices()
      
      // Try to find Indonesian voice (online Google voice or local Indonesian voice)
      const indonesianVoice = voices.find(voice => 
        voice.lang === 'id-ID' || 
        voice.lang === 'id' ||
        voice.lang.startsWith('id-') ||
        voice.name.toLowerCase().includes('indonesia')
      )
      
      if (indonesianVoice) {
        utterance.voice = indonesianVoice
        console.log('Using Indonesian voice:', indonesianVoice.name)
      } else {
        // If no Indonesian voice available, just use beep sound without TTS
        console.warn('Indonesian voice not available. Only playing beep sound.')
        // Don't speak if no Indonesian voice found
        return
      }
      
      // Speak
      window.speechSynthesis.speak(utterance)
    }
    
    // Check if voices are already loaded
    let voices = window.speechSynthesis.getVoices()
    
    if (voices.length > 0) {
      // Voices already loaded
      speakWithVoice()
    } else {
      // Wait for voices to load
      window.speechSynthesis.onvoiceschanged = () => {
        speakWithVoice()
        // Clear event listener after first call
        window.speechSynthesis.onvoiceschanged = null
      }
    }
  } else {
    console.warn('Text-to-Speech tidak didukung di browser ini')
  }
}

function startAlarmLoop() {
  // Stop existing alarm if any
  stopAlarmLoop()
  
  isAlarmActive.value = true
  
  // Set interval to play alarm every 5 seconds (for testing)
  // Change to 300000 (5 minutes) or 600000 (10 minutes) for production
  alarmInterval = setInterval(() => {
    // Check if still has low stock products
    if (lowStockProducts.value.length > 0) {
      playAlertSound()
    } else {
      // Auto stop if no more low stock
      stopAlarmLoop()
    }
  }, 30000) // 5 seconds for testing (change to 300000 for 5 minutes)
  
  console.log('Alarm berulang diaktifkan - akan berbunyi setiap 5 detik')
}

function stopAlarmLoop() {
  if (alarmInterval) {
    clearInterval(alarmInterval)
    alarmInterval = null
  }
  isAlarmActive.value = false
  
  // Stop any ongoing speech
  if ('speechSynthesis' in window) {
    window.speechSynthesis.cancel()
  }
  
  console.log('Alarm berulang dimatikan')
}

function renderCharts() {
  if (!dashboardData.value) return

  // Daily Sales Chart
  if (dailySalesChart.value) {
    if (dailyChartInstance) dailyChartInstance.destroy()
    
    const ctx = dailySalesChart.value.getContext('2d')
    const dailySales = dashboardData.value.daily_sales || []
    
    dailyChartInstance = new Chart(ctx, {
      type: 'line',
      data: {
        labels: dailySales.map(d => d.date),
        datasets: [{
          label: 'Penjualan (Rp)',
          data: dailySales.map(d => d.total),
          borderColor: 'rgb(34, 197, 94)',
          backgroundColor: 'rgba(34, 197, 94, 0.1)',
          tension: 0.4
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
          legend: {
            display: true
          }
        }
      }
    })
  }

  // Payment Method Chart
  if (paymentMethodChart.value) {
    if (paymentChartInstance) paymentChartInstance.destroy()
    
    const ctx = paymentMethodChart.value.getContext('2d')
    const paymentData = dashboardData.value.sales_by_payment_method || []
    
    paymentChartInstance = new Chart(ctx, {
      type: 'doughnut',
      data: {
        labels: paymentData.map(p => p.payment_method.toUpperCase()),
        datasets: [{
          data: paymentData.map(p => p.total),
          backgroundColor: [
            'rgb(59, 130, 246)',
            'rgb(34, 197, 94)',
            'rgb(251, 191, 36)',
            'rgb(239, 68, 68)'
          ]
        }]
      },
      options: {
        responsive: true,
        maintainAspectRatio: false
      }
    })
  }
}

function formatNumber(num) {
  return new Intl.NumberFormat('id-ID').format(num)
}
</script>

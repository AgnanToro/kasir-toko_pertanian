<template>
  <div>
    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
      <div class="card bg-gradient-to-br from-blue-500 to-blue-600 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-blue-100 text-sm mb-1">Total Pendapatan</p>
            <h3 class="text-2xl font-bold">
              Rp {{ formatNumber(dashboardData?.summary?.total_revenue || 0) }}
            </h3>
          </div>
          <span class="text-4xl">💰</span>
        </div>
      </div>

      <div class="card bg-gradient-to-br from-green-500 to-green-600 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-green-100 text-sm mb-1">Total Transaksi</p>
            <h3 class="text-2xl font-bold">
              {{ dashboardData?.summary?.total_transactions || 0 }}
            </h3>
          </div>
          <span class="text-4xl">📝</span>
        </div>
      </div>

      <div class="card bg-gradient-to-br from-purple-500 to-purple-600 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-purple-100 text-sm mb-1">Produk Terjual</p>
            <h3 class="text-2xl font-bold">
              {{ dashboardData?.summary?.total_products_sold || 0 }}
            </h3>
          </div>
          <span class="text-4xl">📦</span>
        </div>
      </div>

      <div class="card bg-gradient-to-br from-red-500 to-red-600 text-white">
        <div class="flex items-center justify-between">
          <div>
            <p class="text-red-100 text-sm mb-1">Stok Menipis</p>
            <h3 class="text-2xl font-bold">
              {{ dashboardData?.summary?.low_stock_count || 0 }}
            </h3>
          </div>
          <span class="text-4xl">⚠️</span>
        </div>
      </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
      <!-- Daily Sales Chart -->
      <div class="card">
        <h3 class="text-lg font-semibold mb-4">Penjualan Harian</h3>
        <div class="h-64">
          <canvas ref="dailySalesChart"></canvas>
        </div>
      </div>

      <!-- Payment Method Chart -->
      <div class="card">
        <h3 class="text-lg font-semibold mb-4">Metode Pembayaran</h3>
        <div class="h-64">
          <canvas ref="paymentMethodChart"></canvas>
        </div>
      </div>
    </div>

    <!-- Top Selling Products -->
    <div class="card">
      <h3 class="text-lg font-semibold mb-4">Produk Terlaris</h3>
      <div class="overflow-x-auto">
        <table class="table">
          <thead>
            <tr>
              <th>Produk</th>
              <th>Total Terjual</th>
              <th>Total Pendapatan</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="product in dashboardData?.top_selling_products" :key="product.product_id">
              <td>{{ product.product_name }}</td>
              <td>{{ product.total_sold }}</td>
              <td>Rp {{ formatNumber(product.total_revenue) }}</td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, nextTick } from 'vue'
import { useDashboardStore } from '@/stores/dashboard'
import { Chart, registerables } from 'chart.js'

Chart.register(...registerables)

const dashboardStore = useDashboardStore()
const dashboardData = ref(null)

const dailySalesChart = ref(null)
const paymentMethodChart = ref(null)

let dailyChartInstance = null
let paymentChartInstance = null

onMounted(async () => {
  await loadDashboard()
})

async function loadDashboard() {
  await dashboardStore.fetchDashboard()
  dashboardData.value = dashboardStore.dashboardData
  
  await nextTick()
  renderCharts()
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

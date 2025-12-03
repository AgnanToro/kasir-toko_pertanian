<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Mobile Menu Button -->
    <button
      @click="sidebarOpen = !sidebarOpen"
      class="lg:hidden fixed top-4 left-4 z-50 p-2 bg-white rounded-lg shadow-lg"
    >
      <i class="fas fa-bars text-gray-700 text-xl"></i>
    </button>

    <!-- Sidebar Overlay (Mobile) -->
    <div
      v-if="sidebarOpen"
      @click="sidebarOpen = false"
      class="lg:hidden fixed inset-0 bg-black bg-opacity-50 z-40"
    ></div>

    <!-- Sidebar -->
    <aside
      :class="[
        'fixed inset-y-0 left-0 w-64 bg-white shadow-xl z-50 transform transition-transform duration-300 ease-in-out',
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'
      ]"
    >
      <!-- Logo -->
      <div class="flex items-center justify-between h-16 px-6 bg-gradient-to-r from-emerald-600 to-teal-600">
        <div class="flex items-center space-x-3">
          <div class="w-10 h-10 bg-white rounded-lg flex items-center justify-center shadow-lg">
            <i class="fas fa-store text-emerald-600 text-xl"></i>
          </div>
          <div>
            <h1 class="text-lg font-bold text-white leading-tight">Toko Pertanian</h1>
            <p class="text-xs text-emerald-100">Sinar Jaya</p>
          </div>
        </div>
        <button @click="sidebarOpen = false" class="lg:hidden text-white">
          <i class="fas fa-times text-xl"></i>
        </button>
      </div>
      
      <!-- Navigation -->
      <nav class="mt-6 px-3">
        <RouterLink
          v-for="item in menuItems"
          :key="item.path"
          :to="item.path"
          @click="sidebarOpen = false"
          class="flex items-center px-4 py-3 mb-1 text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 rounded-xl transition-all duration-200 group"
          :class="{ 'bg-gradient-to-r from-emerald-50 to-teal-50 text-emerald-700 shadow-sm': isActive(item.path) }"
        >
          <div class="w-10 h-10 flex items-center justify-center rounded-lg mr-3 transition-all duration-200"
               :class="isActive(item.path) ? 'bg-emerald-600 text-white' : 'bg-gray-100 text-gray-600 group-hover:bg-emerald-100 group-hover:text-emerald-600'">
            <i :class="item.icon" class="text-lg"></i>
          </div>
          <span class="font-semibold text-sm">{{ item.label }}</span>
        </RouterLink>
      </nav>

      <!-- User Info -->
      <div class="absolute bottom-0 left-0 right-0 p-4 border-t border-gray-200 bg-gray-50">
        <div class="flex items-center justify-between">
          <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-gradient-to-br from-emerald-600 to-teal-600 rounded-full flex items-center justify-center">
              <i class="fas fa-user text-white"></i>
            </div>
            <div class="flex-1">
              <p class="text-sm font-semibold text-gray-800">{{ authStore.user?.name }}</p>
              <p class="text-xs text-gray-500">Administrator</p>
            </div>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main Content -->
    <div class="lg:ml-64 min-h-screen">
      <!-- Header -->
      <header class="bg-white shadow-sm h-16 flex items-center justify-between px-4 sm:px-6 sticky top-0 z-30">
        <div class="flex items-center space-x-4">
          <div class="hidden lg:block">
            <h2 class="text-xl font-bold text-gray-900">{{ pageTitle }}</h2>
            <p class="text-xs text-gray-500">{{ pageSubtitle }}</p>
          </div>
          <div class="lg:hidden">
            <h2 class="text-lg font-bold text-gray-900">{{ pageTitle }}</h2>
          </div>
        </div>
        <div class="flex items-center space-x-3">
          <button
            @click="handleLogout"
            class="flex items-center space-x-2 px-4 py-2 bg-red-50 text-red-600 hover:bg-red-100 rounded-lg transition-colors font-semibold text-sm"
          >
            <i class="fas fa-sign-out-alt"></i>
            <span class="hidden sm:inline">Keluar</span>
          </button>
        </div>
      </header>

      <!-- Page Content -->
      <main class="p-4 sm:p-6">
        <RouterView />
      </main>
    </div>
  </div>
</template>

<script setup>
import { ref, computed } from 'vue'
import { RouterLink, RouterView, useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const sidebarOpen = ref(false)

const menuItems = [
  { path: '/', icon: 'fas fa-chart-line', label: 'Dashboard' },
  { path: '/pos', icon: 'fas fa-cash-register', label: 'POS / Kasir' },
  { path: '/products', icon: 'fas fa-box', label: 'Produk' },
  { path: '/categories', icon: 'fas fa-tags', label: 'Kategori' },
  { path: '/transactions', icon: 'fas fa-receipt', label: 'Transaksi' },
]

const pageTitle = computed(() => {
  const titles = {
    '/': 'Dashboard',
    '/pos': 'Point of Sale',
    '/products': 'Manajemen Produk',
    '/categories': 'Kategori Produk',
    '/transactions': 'Riwayat Transaksi'
  }
  return titles[route.path] || 'Kasir Toko Pertanian'
})

const pageSubtitle = computed(() => {
  const subtitles = {
    '/': 'Ringkasan data dan statistik',
    '/pos': 'Sistem kasir dan penjualan',
    '/products': 'Kelola data produk',
    '/categories': 'Kelola kategori produk',
    '/transactions': 'Lihat semua transaksi'
  }
  return subtitles[route.path] || ''
})

function isActive(path) {
  if (path === '/') {
    return route.path === '/'
  }
  return route.path.startsWith(path)
}

async function handleLogout() {
  await authStore.logout()
  router.push('/login')
}
</script>

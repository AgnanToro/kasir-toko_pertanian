<template>
  <div class="min-h-screen bg-gray-50">
    <!-- Sidebar -->
    <aside class="fixed inset-y-0 left-0 w-64 bg-white shadow-lg z-50">
      <div class="flex items-center justify-center h-16 bg-primary-600">
        <h1 class="text-xl font-bold text-white">🌾 Toko Pertanian</h1>
      </div>
      
      <nav class="mt-6">
        <RouterLink
          v-for="item in menuItems"
          :key="item.path"
          :to="item.path"
          class="flex items-center px-6 py-3 text-gray-700 hover:bg-primary-50 hover:text-primary-600 transition-colors"
          :class="{ 'bg-primary-50 text-primary-600 border-r-4 border-primary-600': $route.path === item.path }"
        >
          <span class="text-2xl mr-3">{{ item.icon }}</span>
          <span class="font-medium">{{ item.label }}</span>
        </RouterLink>
      </nav>
    </aside>

    <!-- Main Content -->
    <div class="ml-64">
      <!-- Header -->
      <header class="bg-white shadow-sm h-16 flex items-center justify-between px-6">
        <div>
          <h2 class="text-xl font-semibold text-gray-800">{{ pageTitle }}</h2>
        </div>
        <div class="flex items-center space-x-4">
          <span class="text-sm text-gray-600">{{ authStore.user?.name }}</span>
          <button @click="handleLogout" class="btn btn-danger btn-sm">
            Logout
          </button>
        </div>
      </header>

      <!-- Page Content -->
      <main class="p-6">
        <RouterView />
      </main>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'
import { RouterLink, RouterView, useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()

const menuItems = [
  { path: '/', icon: '📊', label: 'Dashboard' },
  { path: '/pos', icon: '💰', label: 'POS / Kasir' },
  { path: '/products', icon: '📦', label: 'Produk' },
  { path: '/categories', icon: '🏷️', label: 'Kategori' },
  { path: '/transactions', icon: '📝', label: 'Transaksi' },
]

const pageTitle = computed(() => {
  const titles = {
    '/': 'Dashboard',
    '/pos': 'Point of Sale',
    '/products': 'Manajemen Produk',
    '/categories': 'Manajemen Kategori',
    '/transactions': 'Riwayat Transaksi'
  }
  return titles[route.path] || 'Kasir Toko Pertanian'
})

async function handleLogout() {
  await authStore.logout()
  router.push('/login')
}
</script>

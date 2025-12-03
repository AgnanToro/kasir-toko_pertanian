<template>
  <div class="space-y-6">
    <!-- Header Actions -->
    <div class="bg-white rounded-2xl shadow-sm p-5 sm:p-6">
      <div class="flex flex-col lg:flex-row gap-4 justify-between items-start lg:items-center">
        <div class="flex-1 w-full lg:max-w-md">
          <div class="relative">
            <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-gray-400"></i>
            <input
              v-model="searchQuery"
              @input="handleSearch"
              type="text"
              class="w-full pl-11 pr-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition outline-none"
              placeholder="Cari nama produk, SKU, atau barcode..."
            />
          </div>
        </div>
        <div class="flex gap-3 w-full lg:w-auto">
          <select 
            v-model="filterCategory" 
            @change="handleFilter" 
            class="flex-1 lg:flex-none px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition outline-none"
          >
            <option value="">Semua Kategori</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
              {{ cat.name }}
            </option>
          </select>
          <RouterLink 
            to="/products/create" 
            class="inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-xl hover:from-emerald-700 hover:to-teal-700 transition-all shadow-lg shadow-emerald-500/30 whitespace-nowrap"
          >
            <i class="fas fa-plus mr-2"></i>
            Tambah Produk
          </RouterLink>
        </div>
      </div>
    </div>

    <!-- Products Table -->
    <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
      <div class="overflow-x-auto">
        <table class="w-full">
          <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
              <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Gambar</th>
              <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Produk</th>
              <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Kategori</th>
              <th class="text-left py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">SKU</th>
              <th class="text-right py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Harga Modal</th>
              <th class="text-right py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Harga Jual</th>
              <th class="text-center py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Stok</th>
              <th class="text-center py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Status</th>
              <th class="text-center py-4 px-6 text-xs font-semibold text-gray-600 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-100">
            <tr v-if="loading">
              <td colspan="9" class="text-center py-12">
                <i class="fas fa-spinner fa-spin text-3xl text-emerald-600 mb-2"></i>
                <p class="text-gray-500">Memuat data...</p>
              </td>
            </tr>
            <tr v-else-if="products.length === 0">
              <td colspan="9" class="text-center py-12">
                <i class="fas fa-box-open text-5xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 text-lg">Tidak ada produk</p>
              </td>
            </tr>
            <tr v-else v-for="product in products" :key="product.id" class="hover:bg-gray-50 transition-colors">
              <td class="py-4 px-6">
                <div class="w-16 h-16 bg-gradient-to-br from-emerald-100 to-teal-100 rounded-xl flex items-center justify-center shadow-sm">
                  <i class="fas fa-box text-2xl text-emerald-600"></i>
                </div>
              </td>
              <td class="py-4 px-6">
                <div class="font-semibold text-gray-900">{{ product.name }}</div>
                <div class="text-xs text-gray-500 mt-1">
                  <i class="fas fa-barcode mr-1"></i>{{ product.barcode }}
                </div>
              </td>
              <td class="py-4 px-6">
                <span class="inline-flex items-center px-3 py-1 bg-gray-100 text-gray-700 rounded-lg text-sm">
                  <i class="fas fa-folder mr-2"></i>
                  {{ product.category?.name || '-' }}
                </span>
              </td>
              <td class="py-4 px-6">
                <span class="inline-flex items-center px-3 py-1 bg-blue-100 text-blue-700 rounded-lg text-sm font-mono font-semibold">
                  {{ product.sku }}
                </span>
              </td>
              <td class="py-4 px-6 text-right font-semibold text-gray-700">
                Rp {{ formatNumber(product.purchase_price) }}
              </td>
              <td class="py-4 px-6 text-right font-semibold text-emerald-600">
                Rp {{ formatNumber(product.selling_price) }}
              </td>
              <td class="py-4 px-6 text-center">
                <div class="relative inline-flex items-center">
                  <div 
                    class="inline-flex items-center px-3 py-1 rounded-lg text-sm font-semibold"
                    :class="product.is_low_stock 
                      ? 'bg-red-100 text-red-700 animate-pulse' 
                      : 'bg-emerald-100 text-emerald-700'"
                  >
                    <i :class="product.is_low_stock ? 'fas fa-exclamation-triangle' : 'fas fa-check-circle'" class="mr-2"></i>
                    {{ product.stock }} {{ product.unit }}
                  </div>
                  <!-- Low Stock Warning Bell -->
                  <div v-if="product.is_low_stock" class="absolute -top-1 -right-1">
                    <span class="flex h-3 w-3">
                      <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                      <span class="relative inline-flex rounded-full h-3 w-3 bg-red-500"></span>
                    </span>
                  </div>
                </div>
              </td>
              <td class="py-4 px-6 text-center">
                <span
                  class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold"
                  :class="product.is_active 
                    ? 'bg-emerald-100 text-emerald-700' 
                    : 'bg-gray-100 text-gray-700'"
                >
                  {{ product.is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
              </td>
              <td class="py-4 px-6">
                <div class="flex gap-2 justify-center relative z-10">
                  <RouterLink
                    :to="`/products/${product.id}`"
                    class="w-9 h-9 flex items-center justify-center bg-emerald-100 text-emerald-600 hover:bg-emerald-200 rounded-lg transition-colors cursor-pointer"
                    title="Lihat Detail"
                  >
                    <i class="fas fa-eye"></i>
                  </RouterLink>
                  <RouterLink
                    :to="`/products/${product.id}/edit`"
                    class="w-9 h-9 flex items-center justify-center bg-blue-100 text-blue-600 hover:bg-blue-200 rounded-lg transition-colors cursor-pointer"
                    title="Edit"
                  >
                    <i class="fas fa-edit"></i>
                  </RouterLink>
                  <button
                    @click="handleDelete(product.id)"
                    class="w-9 h-9 flex items-center justify-center bg-red-100 text-red-600 hover:bg-red-200 rounded-lg transition-colors cursor-pointer"
                    title="Hapus"
                  >
                    <i class="fas fa-trash-alt"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="mt-6 pt-4 border-t border-gray-200">
        <div class="flex items-center justify-center gap-1">
          <!-- First Button -->
          <button
            @click="goToPage(1)"
            :disabled="pagination.current_page === 1"
            class="px-2.5 py-1.5 text-xs rounded-lg font-medium transition-colors"
            :class="pagination.current_page === 1 
              ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
              : 'bg-white border border-gray-300 text-gray-700 hover:bg-emerald-50 hover:border-emerald-500'"
          >
            <i class="fas fa-angle-double-left"></i>
          </button>

          <!-- Previous Button -->
          <button
            @click="goToPage(pagination.current_page - 1)"
            :disabled="pagination.current_page === 1"
            class="px-2.5 py-1.5 text-xs rounded-lg font-medium transition-colors"
            :class="pagination.current_page === 1 
              ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
              : 'bg-white border border-gray-300 text-gray-700 hover:bg-emerald-50 hover:border-emerald-500'"
          >
            <i class="fas fa-angle-left"></i>
          </button>

          <!-- Page Numbers -->
          <button
            v-for="page in visiblePages"
            :key="page"
            @click="goToPage(page)"
            class="px-3 py-1.5 text-xs rounded-lg font-medium transition-colors min-w-[32px]"
            :class="pagination.current_page === page 
              ? 'bg-gradient-to-r from-emerald-500 to-teal-500 text-white shadow-md' 
              : 'bg-white border border-gray-300 text-gray-700 hover:bg-emerald-50 hover:border-emerald-500'"
          >
            {{ page }}
          </button>

          <!-- Next Button -->
          <button
            @click="goToPage(pagination.current_page + 1)"
            :disabled="pagination.current_page === pagination.last_page"
            class="px-2.5 py-1.5 text-xs rounded-lg font-medium transition-colors"
            :class="pagination.current_page === pagination.last_page 
              ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
              : 'bg-white border border-gray-300 text-gray-700 hover:bg-emerald-50 hover:border-emerald-500'"
          >
            <i class="fas fa-angle-right"></i>
          </button>

          <!-- Last Button -->
          <button
            @click="goToPage(pagination.last_page)"
            :disabled="pagination.current_page === pagination.last_page"
            class="px-2.5 py-1.5 text-xs rounded-lg font-medium transition-colors"
            :class="pagination.current_page === pagination.last_page 
              ? 'bg-gray-100 text-gray-400 cursor-not-allowed' 
              : 'bg-white border border-gray-300 text-gray-700 hover:bg-emerald-50 hover:border-emerald-500'"
          >
            <i class="fas fa-angle-double-right"></i>
          </button>
        </div>

        <!-- Page Info Text -->
        <p class="text-center text-xs text-gray-500 mt-2">
          Menampilkan {{ pagination.from || 0 }} - {{ pagination.to || 0 }} dari {{ pagination.total || 0 }} produk
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { RouterLink } from 'vue-router'
import { useProductStore } from '@/stores/product'
import { useCategoryStore } from '@/stores/category'

const productStore = useProductStore()
const categoryStore = useCategoryStore()

const searchQuery = ref('')
const filterCategory = ref('')

const products = computed(() => productStore.products)
const loading = computed(() => productStore.loading)
const pagination = computed(() => productStore.pagination)
const categories = computed(() => categoryStore.categories)

// Visible page numbers (max 5 pages shown)
const visiblePages = computed(() => {
  const pages = []
  const total = pagination.value.last_page || 1
  const current = pagination.value.current_page || 1
  
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

onMounted(async () => {
  await loadData()
})

async function loadData() {
  await categoryStore.fetchCategories()
  await productStore.fetchProducts({
    page: 1,
    per_page: 10  // 10 produk per halaman
  })
}

async function handleSearch() {
  await productStore.fetchProducts({
    page: 1,  // Reset ke halaman 1 saat search
    per_page: 10,
    search: searchQuery.value,
    category_id: filterCategory.value
  })
}

async function handleFilter() {
  await productStore.fetchProducts({
    page: 1,  // Reset ke halaman 1 saat filter
    per_page: 10,
    search: searchQuery.value,
    category_id: filterCategory.value
  })
}

async function goToPage(page) {
  console.log('🔄 Going to page:', page)
  await productStore.fetchProducts({
    page,
    per_page: 10,
    search: searchQuery.value,
    category_id: filterCategory.value
  })
  console.log('📄 Products after fetch:', products.value.length)
  console.log('📊 Pagination after fetch:', pagination.value)
}

async function handleDelete(id) {
  if (confirm('Yakin ingin menghapus produk ini?')) {
    const result = await productStore.deleteProduct(id)
    if (result.success) {
      alert('Produk berhasil dihapus')
      await loadData()
    } else {
      alert('Gagal menghapus produk: ' + result.message)
    }
  }
}

function formatNumber(num) {
  return new Intl.NumberFormat('id-ID').format(num)
}
</script>

<template>
  <div>
    <!-- Header Actions -->
    <div class="card mb-6">
      <div class="flex flex-col md:flex-row gap-4 justify-between items-start md:items-center">
        <div class="flex-1 w-full md:w-auto">
          <input
            v-model="searchQuery"
            @input="handleSearch"
            type="text"
            class="input"
            placeholder="Cari produk..."
          />
        </div>
        <div class="flex gap-2 w-full md:w-auto">
          <select v-model="filterCategory" @change="handleFilter" class="input">
            <option value="">Semua Kategori</option>
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
              {{ cat.name }}
            </option>
          </select>
          <RouterLink to="/products/create" class="btn btn-primary whitespace-nowrap">
            ➕ Tambah Produk
          </RouterLink>
        </div>
      </div>
    </div>

    <!-- Products Table -->
    <div class="card">
      <div class="overflow-x-auto">
        <table class="table">
          <thead>
            <tr>
              <th>Gambar</th>
              <th>Produk</th>
              <th>Kategori</th>
              <th>SKU</th>
              <th>Harga Modal</th>
              <th>Harga Jual</th>
              <th>Stok</th>
              <th>Status</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="9" class="text-center py-8">Loading...</td>
            </tr>
            <tr v-else-if="products.length === 0">
              <td colspan="9" class="text-center py-8 text-gray-500">Tidak ada produk</td>
            </tr>
            <tr v-else v-for="product in products" :key="product.id">
              <td>
                <div class="w-16 h-16 bg-gray-200 rounded flex items-center justify-center text-2xl">
                  📦
                </div>
              </td>
              <td>
                <div class="font-semibold">{{ product.name }}</div>
                <div class="text-xs text-gray-500">{{ product.barcode }}</div>
              </td>
              <td>{{ product.category?.name }}</td>
              <td>
                <span class="badge badge-info">{{ product.sku }}</span>
              </td>
              <td>Rp {{ formatNumber(product.purchase_price) }}</td>
              <td>Rp {{ formatNumber(product.selling_price) }}</td>
              <td>
                <span
                  :class="product.is_low_stock ? 'text-red-600 font-bold' : 'text-gray-900'"
                >
                  {{ product.stock }} {{ product.unit }}
                </span>
              </td>
              <td>
                <span
                  class="badge"
                  :class="product.is_active ? 'badge-success' : 'badge-danger'"
                >
                  {{ product.is_active ? 'Aktif' : 'Nonaktif' }}
                </span>
              </td>
              <td>
                <div class="flex gap-2">
                  <RouterLink
                    :to="`/products/${product.id}/edit`"
                    class="text-blue-600 hover:text-blue-800"
                  >
                    ✏️
                  </RouterLink>
                  <button
                    @click="handleDelete(product.id)"
                    class="text-red-600 hover:text-red-800"
                  >
                    🗑️
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="flex justify-center items-center gap-2 mt-6">
        <button
          @click="goToPage(pagination.current_page - 1)"
          :disabled="pagination.current_page === 1"
          class="btn btn-outline"
        >
          ← Prev
        </button>
        <span class="text-sm text-gray-600">
          Halaman {{ pagination.current_page }} dari {{ pagination.last_page }}
        </span>
        <button
          @click="goToPage(pagination.current_page + 1)"
          :disabled="pagination.current_page === pagination.last_page"
          class="btn btn-outline"
        >
          Next →
        </button>
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

onMounted(async () => {
  await loadData()
})

async function loadData() {
  await categoryStore.fetchCategories()
  await productStore.fetchProducts()
}

async function handleSearch() {
  await productStore.fetchProducts({
    search: searchQuery.value,
    category_id: filterCategory.value
  })
}

async function handleFilter() {
  await productStore.fetchProducts({
    search: searchQuery.value,
    category_id: filterCategory.value
  })
}

async function goToPage(page) {
  await productStore.fetchProducts({
    page,
    search: searchQuery.value,
    category_id: filterCategory.value
  })
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

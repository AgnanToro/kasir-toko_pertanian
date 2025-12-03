<template>
  <div>
    <div class="card max-w-5xl mx-auto">
      <!-- Header -->
      <div class="border-b-2 border-gray-200 pb-4 mb-6">
        <h2 class="text-3xl font-bold text-gray-800 flex items-center">
          <i class="fas fa-eye text-emerald-600 mr-3"></i>
          Detail Produk
        </h2>
        <p class="text-gray-600 mt-1">Informasi lengkap tentang produk</p>
      </div>

      <div v-if="loading" class="text-center py-12">
        <i class="fas fa-spinner fa-spin text-4xl text-emerald-600 mb-3"></i>
        <p class="text-gray-500">Memuat data produk...</p>
      </div>

      <div v-else-if="product" class="space-y-6">
        <!-- Gambar Produk -->
        <div class="flex justify-center">
          <div v-if="product.image" class="relative">
            <img 
              :src="`/storage/${product.image}`" 
              :alt="product.name"
              class="w-full max-w-md h-80 object-cover rounded-xl shadow-2xl border-4 border-emerald-100"
            />
          </div>
          <div v-else class="w-full max-w-md h-80 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl flex flex-col items-center justify-center shadow-lg">
            <i class="fas fa-image text-8xl text-gray-300 mb-4"></i>
            <p class="text-gray-400 font-semibold">Tidak ada gambar</p>
          </div>
        </div>

        <!-- Info Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Nama Produk -->
          <div class="md:col-span-2 p-5 bg-gradient-to-r from-emerald-50 to-teal-50 rounded-lg border-2 border-emerald-200">
            <label class="block text-xs font-semibold mb-2 text-emerald-600 uppercase">
              <i class="fas fa-box mr-2"></i>
              Nama Produk
            </label>
            <p class="text-2xl font-bold text-gray-800">{{ product.name }}</p>
          </div>

          <!-- Kategori -->
          <div class="p-5 bg-gray-50 rounded-lg border-2 border-gray-200">
            <label class="block text-xs font-semibold mb-2 text-gray-600 uppercase">
              <i class="fas fa-folder mr-2"></i>
              Kategori
            </label>
            <p class="text-lg font-bold text-gray-800">{{ product.category?.name || '-' }}</p>
          </div>

          <!-- Satuan -->
          <div class="p-5 bg-gray-50 rounded-lg border-2 border-gray-200">
            <label class="block text-xs font-semibold mb-2 text-gray-600 uppercase">
              <i class="fas fa-balance-scale mr-2"></i>
              Satuan
            </label>
            <p class="text-lg font-bold text-gray-800">{{ product.unit }}</p>
          </div>

          <!-- SKU -->
          <div class="p-5 bg-gray-50 rounded-lg border-2 border-gray-200">
            <label class="block text-xs font-semibold mb-2 text-gray-600 uppercase">
              <i class="fas fa-barcode mr-2"></i>
              SKU
            </label>
            <p class="text-lg font-mono font-bold text-gray-800">{{ product.sku }}</p>
          </div>

          <!-- Barcode -->
          <div class="p-5 bg-gray-50 rounded-lg border-2 border-gray-200">
            <label class="block text-xs font-semibold mb-2 text-gray-600 uppercase">
              <i class="fas fa-qrcode mr-2"></i>
              Barcode
            </label>
            <p class="text-lg font-mono font-bold text-gray-800">{{ product.barcode || '-' }}</p>
          </div>

          <!-- Harga Modal -->
          <div class="p-5 bg-gradient-to-br from-blue-50 to-blue-100 rounded-lg border-2 border-blue-200">
            <label class="block text-xs font-semibold mb-2 text-blue-600 uppercase">
              <i class="fas fa-money-bill-wave mr-2"></i>
              Harga Modal
            </label>
            <p class="text-2xl font-bold text-blue-700">Rp {{ formatNumber(product.purchase_price) }}</p>
          </div>

          <!-- Harga Jual -->
          <div class="p-5 bg-gradient-to-br from-emerald-50 to-emerald-100 rounded-lg border-2 border-emerald-200">
            <label class="block text-xs font-semibold mb-2 text-emerald-600 uppercase">
              <i class="fas fa-tags mr-2"></i>
              Harga Jual
            </label>
            <p class="text-2xl font-bold text-emerald-700">Rp {{ formatNumber(product.selling_price) }}</p>
          </div>

          <!-- Keuntungan -->
          <div 
            class="p-5 rounded-lg border-2"
            :class="profit >= 0 
              ? 'bg-gradient-to-br from-green-50 to-green-100 border-green-300' 
              : 'bg-gradient-to-br from-red-50 to-red-100 border-red-300'"
          >
            <label class="block text-xs font-semibold mb-2 uppercase"
                   :class="profit >= 0 ? 'text-green-600' : 'text-red-600'">
              <i :class="profit >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'" class="mr-2"></i>
              Keuntungan per Unit
            </label>
            <p class="text-2xl font-bold"
               :class="profit >= 0 ? 'text-green-700' : 'text-red-700'">
              Rp {{ formatNumber(Math.abs(profit)) }}
            </p>
          </div>

          <!-- Stok -->
          <div class="p-5 bg-gradient-to-br from-purple-50 to-purple-100 rounded-lg border-2 border-purple-200">
            <label class="block text-xs font-semibold mb-2 text-purple-600 uppercase">
              <i class="fas fa-boxes mr-2"></i>
              Stok Tersedia
            </label>
            <p class="text-2xl font-bold text-purple-700">{{ product.stock }} {{ product.unit }}</p>
          </div>

          <!-- Stok Minimum -->
          <div class="p-5 bg-gradient-to-br from-orange-50 to-orange-100 rounded-lg border-2 border-orange-200">
            <label class="block text-xs font-semibold mb-2 text-orange-600 uppercase">
              <i class="fas fa-exclamation-triangle mr-2"></i>
              Stok Minimum
            </label>
            <p class="text-2xl font-bold text-orange-700">{{ product.minimum_stock }} {{ product.unit }}</p>
          </div>

          <!-- Status -->
          <div class="p-5 bg-gray-50 rounded-lg border-2 border-gray-200">
            <label class="block text-xs font-semibold mb-2 text-gray-600 uppercase">
              <i class="fas fa-toggle-on mr-2"></i>
              Status
            </label>
            <span 
              class="inline-block px-4 py-2 rounded-full text-sm font-bold"
              :class="product.is_active 
                ? 'bg-emerald-100 text-emerald-700' 
                : 'bg-gray-100 text-gray-700'"
            >
              <i :class="product.is_active ? 'fas fa-check-circle' : 'fas fa-times-circle'" class="mr-1"></i>
              {{ product.is_active ? 'Aktif' : 'Nonaktif' }}
            </span>
          </div>

          <!-- Deskripsi -->
          <div v-if="product.description" class="md:col-span-2 p-5 bg-gray-50 rounded-lg border-2 border-gray-200">
            <label class="block text-xs font-semibold mb-2 text-gray-600 uppercase">
              <i class="fas fa-align-left mr-2"></i>
              Deskripsi
            </label>
            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{{ product.description }}</p>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-4 mt-8 pt-6 border-t-2 border-gray-200">
          <RouterLink
            :to="`/products/${product.id}/edit`"
            class="flex-1 py-3 rounded-lg font-semibold text-white bg-blue-600 hover:bg-blue-700 transition-colors text-center shadow-lg"
          >
            <i class="fas fa-edit mr-2"></i>
            Edit Produk
          </RouterLink>
          <RouterLink
            to="/products"
            class="flex-1 py-3 rounded-lg font-semibold text-gray-700 bg-white border-2 border-gray-300 hover:bg-gray-50 transition-colors text-center"
          >
            <i class="fas fa-arrow-left mr-2"></i>
            Kembali
          </RouterLink>
        </div>
      </div>

      <div v-else class="text-center py-12">
        <i class="fas fa-exclamation-triangle text-5xl text-red-500 mb-4"></i>
        <p class="text-gray-600 text-lg">Produk tidak ditemukan</p>
        <RouterLink 
          to="/products"
          class="inline-block mt-4 px-6 py-2 bg-emerald-600 text-white rounded-lg hover:bg-emerald-700 transition-colors"
        >
          <i class="fas fa-arrow-left mr-2"></i>
          Kembali ke Daftar Produk
        </RouterLink>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import { useProductStore } from '@/stores/product'

const route = useRoute()
const productStore = useProductStore()

const loading = ref(true)
const product = computed(() => productStore.product)
const profit = computed(() => {
  if (!product.value) return 0
  return product.value.selling_price - product.value.purchase_price
})

onMounted(async () => {
  await loadProduct()
})

async function loadProduct() {
  loading.value = true
  await productStore.fetchProduct(route.params.id)
  loading.value = false
}

function formatNumber(num) {
  return new Intl.NumberFormat('id-ID').format(num)
}
</script>

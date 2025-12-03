<template>
  <div>
    <div class="card max-w-5xl mx-auto">
      <!-- Header -->
      <div class="border-b-2 border-gray-200 pb-4 mb-6">
        <h2 class="text-3xl font-bold text-gray-800 flex items-center">
          <i :class="isEdit ? 'fas fa-edit' : 'fas fa-plus-circle'" class="mr-3 text-emerald-600"></i>
          {{ isEdit ? 'Edit Produk' : 'Tambah Produk' }}
        </h2>
        <p class="text-gray-600 mt-1">{{ isEdit ? 'Perbarui informasi produk' : 'Tambahkan produk baru ke inventori' }}</p>
      </div>

      <form @submit.prevent="handleSubmit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Name -->
          <div class="md:col-span-2">
            <label class="block text-sm font-semibold mb-2 text-gray-700">
              <i class="fas fa-box text-emerald-600 mr-2"></i>
              Nama Produk <span class="text-red-500">*</span>
            </label>
            <input 
              v-model="form.name" 
              type="text" 
              class="input w-full" 
              placeholder="Masukkan nama produk"
              required 
            />
          </div>

          <!-- Category -->
          <div>
            <label class="block text-sm font-semibold mb-2 text-gray-700">
              <i class="fas fa-folder text-emerald-600 mr-2"></i>
              Kategori <span class="text-red-500">*</span>
            </label>
            <select v-model="form.category_id" class="input w-full" required>
              <option value="">Pilih Kategori</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                {{ cat.name }}
              </option>
            </select>
          </div>

          <!-- Unit -->
          <div>
            <label class="block text-sm font-semibold mb-2 text-gray-700">
              <i class="fas fa-balance-scale text-emerald-600 mr-2"></i>
              Satuan <span class="text-red-500">*</span>
            </label>
            <select v-model="form.unit" class="input w-full" required>
              <option value="pcs">Pcs</option>
              <option value="kg">Kg</option>
              <option value="liter">Liter</option>
              <option value="karung">Karung</option>
              <option value="pack">Pack</option>
              <option value="botol">Botol</option>
              <option value="roll">Roll</option>
            </select>
          </div>

          <!-- SKU -->
          <div>
            <label class="block text-sm font-semibold mb-2 text-gray-700">
              <i class="fas fa-barcode text-emerald-600 mr-2"></i>
              SKU
            </label>
            <input 
              v-model="form.sku" 
              type="text" 
              class="input w-full bg-gray-50" 
              placeholder="Auto generate jika kosong" 
              readonly 
            />
            <p class="text-xs text-gray-500 mt-1">Otomatis terisi jika dikosongkan</p>
          </div>

          <!-- Barcode -->
          <div>
            <label class="block text-sm font-semibold mb-2 text-gray-700">
              <i class="fas fa-qrcode text-emerald-600 mr-2"></i>
              Barcode
            </label>
            <input 
              v-model="form.barcode" 
              type="text" 
              class="input w-full" 
              placeholder="Masukkan barcode produk"
            />
          </div>

          <!-- Purchase Price -->
          <div>
            <label class="block text-sm font-semibold mb-2 text-gray-700">
              <i class="fas fa-money-bill-wave text-blue-600 mr-2"></i>
              Harga Modal <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">Rp</span>
              <input 
                v-model.number="form.purchase_price" 
                type="number" 
                class="input w-full pl-10" 
                min="0" 
                step="0.01"
                placeholder="0"
                required 
              />
            </div>
          </div>

          <!-- Selling Price -->
          <div>
            <label class="block text-sm font-semibold mb-2 text-gray-700">
              <i class="fas fa-tag text-emerald-600 mr-2"></i>
              Harga Jual <span class="text-red-500">*</span>
            </label>
            <div class="relative">
              <span class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-500 font-semibold">Rp</span>
              <input 
                v-model.number="form.selling_price" 
                type="number" 
                class="input w-full pl-10" 
                min="0" 
                step="0.01"
                placeholder="0"
                required 
              />
            </div>
            <p v-if="form.selling_price > 0 && form.purchase_price > 0" class="text-xs mt-1"
               :class="form.selling_price > form.purchase_price ? 'text-emerald-600' : 'text-red-600'">
              <i :class="form.selling_price > form.purchase_price ? 'fas fa-arrow-up' : 'fas fa-arrow-down'"></i>
              Keuntungan: Rp {{ formatNumber(form.selling_price - form.purchase_price) }}
            </p>
          </div>

          <!-- Stock -->
          <div>
            <label class="block text-sm font-semibold mb-2 text-gray-700">
              <i class="fas fa-boxes text-emerald-600 mr-2"></i>
              Stok <span class="text-red-500">*</span>
            </label>
            <input 
              v-model.number="form.stock" 
              type="number" 
              class="input w-full" 
              min="0" 
              placeholder="0"
              required 
            />
          </div>

          <!-- Minimum Stock -->
          <div>
            <label class="block text-sm font-semibold mb-2 text-gray-700">
              <i class="fas fa-exclamation-triangle text-orange-600 mr-2"></i>
              Stok Minimum <span class="text-red-500">*</span>
            </label>
            <input 
              v-model.number="form.minimum_stock" 
              type="number" 
              class="input w-full" 
              min="0" 
              placeholder="10"
              required 
            />
            <p class="text-xs text-gray-500 mt-1">Peringatan saat stok mencapai batas ini</p>
          </div>

          <!-- Description -->
          <div class="md:col-span-2">
            <label class="block text-sm font-semibold mb-2 text-gray-700">
              <i class="fas fa-align-left text-emerald-600 mr-2"></i>
              Deskripsi
            </label>
            <textarea 
              v-model="form.description" 
              class="input w-full" 
              rows="4"
              placeholder="Tambahkan deskripsi produk (opsional)"
            ></textarea>
          </div>

          <!-- Image -->
          <div class="md:col-span-2">
            <label class="block text-sm font-semibold mb-2 text-gray-700">
              <i class="fas fa-image text-emerald-600 mr-2"></i>
              Gambar Produk
            </label>
            
            <!-- Preview/Existing Image -->
            <div v-if="imagePreview || existingImage" class="mb-4">
              <div class="relative inline-block">
                <img 
                  :src="imagePreview || `/storage/${existingImage}`" 
                  alt="Preview"
                  class="h-56 w-auto object-cover rounded-xl shadow-lg border-4 border-emerald-200"
                />
                <button
                  v-if="imagePreview"
                  @click="removeNewImage"
                  type="button"
                  class="absolute -top-3 -right-3 w-10 h-10 bg-red-500 text-white rounded-full hover:bg-red-600 transition-colors flex items-center justify-center shadow-lg"
                >
                  <i class="fas fa-times text-lg"></i>
                </button>
              </div>
              <p class="text-sm font-semibold mt-3"
                 :class="imagePreview ? 'text-emerald-600' : 'text-blue-600'">
                <i :class="imagePreview ? 'fas fa-check-circle' : 'fas fa-image'" class="mr-2"></i>
                {{ imagePreview ? 'Gambar baru dipilih (belum tersimpan)' : 'Gambar saat ini' }}
              </p>
            </div>

            <div class="border-2 border-dashed border-gray-300 rounded-lg p-4 hover:border-emerald-500 transition-colors">
              <input 
                @change="handleImageChange" 
                type="file" 
                accept="image/*" 
                ref="fileInput"
                class="block w-full text-sm text-gray-600 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 cursor-pointer"
              />
              <p class="text-xs text-gray-500 mt-2">
                <i class="fas fa-info-circle mr-1"></i>
                Max 2MB • Format: JPEG, PNG, JPG, GIF
                <span v-if="existingImage && !imagePreview" class="ml-2 text-orange-600">
                  • Upload gambar baru untuk menggantinya
                </span>
              </p>
            </div>
          </div>

          <!-- Is Active -->
          <div class="md:col-span-2">
            <label class="flex items-center p-4 bg-gray-50 rounded-lg border-2 border-gray-200 hover:border-emerald-500 transition-colors cursor-pointer">
              <input 
                v-model="form.is_active" 
                type="checkbox" 
                class="w-5 h-5 text-emerald-600 rounded focus:ring-emerald-500 mr-3" 
              />
              <div>
                <span class="text-sm font-semibold text-gray-800">Produk Aktif</span>
                <p class="text-xs text-gray-600 mt-0.5">Produk akan ditampilkan di sistem POS</p>
              </div>
            </label>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-4 mt-8 pt-6 border-t-2 border-gray-200">
          <button 
            type="submit" 
            :disabled="loading" 
            class="flex-1 py-3 rounded-lg font-semibold text-white bg-emerald-600 hover:bg-emerald-700 transition-colors disabled:opacity-50 disabled:cursor-not-allowed shadow-lg"
          >
            <i :class="loading ? 'fas fa-spinner fa-spin' : 'fas fa-save'" class="mr-2"></i>
            {{ loading ? 'Menyimpan...' : 'Simpan' }}
          </button>
          <RouterLink 
            to="/products" 
            class="flex-1 py-3 rounded-lg font-semibold text-gray-700 bg-white border-2 border-gray-300 hover:bg-gray-50 transition-colors text-center"
          >
            <i class="fas fa-times mr-2"></i>
            Batal
          </RouterLink>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRouter, useRoute, RouterLink } from 'vue-router'
import { useProductStore } from '@/stores/product'
import { useCategoryStore } from '@/stores/category'

const router = useRouter()
const route = useRoute()
const productStore = useProductStore()
const categoryStore = useCategoryStore()

const isEdit = computed(() => !!route.params.id)
const categories = computed(() => categoryStore.categories)

const form = ref({
  name: '',
  category_id: '',
  sku: '',
  barcode: '',
  description: '',
  purchase_price: 0,
  selling_price: 0,
  stock: 0,
  minimum_stock: 10,
  unit: 'pcs',
  is_active: true,
  image: null
})

const loading = ref(false)
const imagePreview = ref(null)
const existingImage = ref(null)
const fileInput = ref(null)

onMounted(async () => {
  await categoryStore.fetchCategories()
  
  if (isEdit.value) {
    await loadProduct()
  }
})

async function loadProduct() {
  await productStore.fetchProduct(route.params.id)
  const product = productStore.product
  
  if (product) {
    form.value = {
      name: product.name,
      category_id: product.category_id,
      sku: product.sku,
      barcode: product.barcode || '',
      description: product.description || '',
      purchase_price: product.purchase_price,
      selling_price: product.selling_price,
      stock: product.stock,
      minimum_stock: product.minimum_stock,
      unit: product.unit,
      is_active: product.is_active,
      image: null
    }
    // Set existing image for preview
    if (product.image) {
      existingImage.value = product.image
    }
  }
}

function handleImageChange(event) {
  const file = event.target.files[0]
  if (file) {
    // Validate file size (max 2MB)
    if (file.size > 2 * 1024 * 1024) {
      alert('Ukuran file terlalu besar! Maksimal 2MB')
      event.target.value = ''
      return
    }
    
    // Validate file type
    const validTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/gif']
    if (!validTypes.includes(file.type)) {
      alert('Format file tidak valid! Hanya JPEG, PNG, JPG, atau GIF')
      event.target.value = ''
      return
    }
    
    form.value.image = file
    
    // Create preview
    const reader = new FileReader()
    reader.onload = (e) => {
      imagePreview.value = e.target.result
    }
    reader.readAsDataURL(file)
  } else {
    form.value.image = null
    imagePreview.value = null
  }
}

function removeNewImage() {
  imagePreview.value = null
  form.value.image = null
  if (fileInput.value) {
    fileInput.value.value = ''
  }
}

async function handleSubmit() {
  loading.value = true

  let result
  if (isEdit.value) {
    result = await productStore.updateProduct(route.params.id, form.value)
  } else {
    result = await productStore.createProduct(form.value)
  }

  if (result.success) {
    alert(`Produk berhasil ${isEdit.value ? 'diupdate' : 'ditambahkan'}`)
    router.push('/products')
  } else {
    // Better error display
    let errorMsg = result.message
    if (typeof errorMsg === 'object') {
      errorMsg = Object.values(errorMsg).flat().join('\n')
    }
    alert('Gagal menyimpan produk:\n' + errorMsg)
  }

  loading.value = false
}

function formatNumber(num) {
  return new Intl.NumberFormat('id-ID').format(num)
}
</script>

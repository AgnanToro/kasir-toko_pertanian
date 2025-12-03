<template>
  <div>
    <div class="card max-w-4xl mx-auto">
      <h2 class="text-2xl font-bold mb-6">{{ isEdit ? 'Edit Produk' : 'Tambah Produk' }}</h2>

      <form @submit.prevent="handleSubmit">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <!-- Name -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-2">Nama Produk *</label>
            <input v-model="form.name" type="text" class="input" required />
          </div>

          <!-- Category -->
          <div>
            <label class="block text-sm font-medium mb-2">Kategori *</label>
            <select v-model="form.category_id" class="input" required>
              <option value="">Pilih Kategori</option>
              <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                {{ cat.name }}
              </option>
            </select>
          </div>

          <!-- Unit -->
          <div>
            <label class="block text-sm font-medium mb-2">Satuan *</label>
            <select v-model="form.unit" class="input" required>
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
            <label class="block text-sm font-medium mb-2">SKU</label>
            <input v-model="form.sku" type="text" class="input" placeholder="Auto generate jika kosong" />
          </div>

          <!-- Barcode -->
          <div>
            <label class="block text-sm font-medium mb-2">Barcode</label>
            <input v-model="form.barcode" type="text" class="input" />
          </div>

          <!-- Purchase Price -->
          <div>
            <label class="block text-sm font-medium mb-2">Harga Modal *</label>
            <input v-model.number="form.purchase_price" type="number" class="input" min="0" required />
          </div>

          <!-- Selling Price -->
          <div>
            <label class="block text-sm font-medium mb-2">Harga Jual *</label>
            <input v-model.number="form.selling_price" type="number" class="input" min="0" required />
          </div>

          <!-- Stock -->
          <div>
            <label class="block text-sm font-medium mb-2">Stok *</label>
            <input v-model.number="form.stock" type="number" class="input" min="0" required />
          </div>

          <!-- Minimum Stock -->
          <div>
            <label class="block text-sm font-medium mb-2">Stok Minimum *</label>
            <input v-model.number="form.minimum_stock" type="number" class="input" min="0" required />
          </div>

          <!-- Description -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-2">Deskripsi</label>
            <textarea v-model="form.description" class="input" rows="3"></textarea>
          </div>

          <!-- Image -->
          <div class="md:col-span-2">
            <label class="block text-sm font-medium mb-2">Gambar Produk</label>
            <input @change="handleImageChange" type="file" accept="image/*" class="input" />
            <p class="text-xs text-gray-500 mt-1">Max 2MB (JPEG, PNG, JPG, GIF)</p>
          </div>

          <!-- Is Active -->
          <div class="md:col-span-2">
            <label class="flex items-center">
              <input v-model="form.is_active" type="checkbox" class="mr-2" />
              <span class="text-sm font-medium">Produk Aktif</span>
            </label>
          </div>
        </div>

        <!-- Actions -->
        <div class="flex gap-4 mt-6">
          <button type="submit" :disabled="loading" class="btn btn-primary">
            {{ loading ? 'Menyimpan...' : 'Simpan' }}
          </button>
          <RouterLink to="/products" class="btn btn-secondary">
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
  }
}

function handleImageChange(event) {
  const file = event.target.files[0]
  if (file) {
    form.value.image = file
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
    alert('Gagal menyimpan produk: ' + result.message)
  }

  loading.value = false
}
</script>

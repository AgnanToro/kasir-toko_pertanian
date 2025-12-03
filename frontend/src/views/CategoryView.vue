<template>
  <div>
    <div class="card mb-6">
      <button @click="showModal = true" class="btn btn-primary">
        ➕ Tambah Kategori
      </button>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
      <div v-for="category in categories" :key="category.id" class="card">
        <div class="flex items-start justify-between mb-3">
          <div class="text-4xl">{{ category.icon || '📁' }}</div>
          <div class="flex gap-2">
            <button @click="editCategory(category)" class="text-blue-600 hover:text-blue-800">
              ✏️
            </button>
            <button @click="deleteCategory(category.id)" class="text-red-600 hover:text-red-800">
              🗑️
            </button>
          </div>
        </div>
        <h3 class="font-bold text-lg mb-1">{{ category.name }}</h3>
        <p class="text-sm text-gray-600 mb-2">{{ category.description }}</p>
        <div class="flex items-center justify-between text-sm">
          <span class="text-gray-500">{{ category.products_count || 0 }} produk</span>
          <span
            class="badge"
            :class="category.is_active ? 'badge-success' : 'badge-danger'"
          >
            {{ category.is_active ? 'Aktif' : 'Nonaktif' }}
          </span>
        </div>
      </div>
    </div>

    <!-- Modal -->
    <div v-if="showModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 max-w-md w-full mx-4">
        <h3 class="text-xl font-bold mb-4">{{ isEdit ? 'Edit Kategori' : 'Tambah Kategori' }}</h3>

        <form @submit.prevent="handleSubmit">
          <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Nama *</label>
            <input v-model="form.name" type="text" class="input" required />
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Icon (Emoji)</label>
            <input v-model="form.icon" type="text" class="input" placeholder="🌱" />
          </div>

          <div class="mb-4">
            <label class="block text-sm font-medium mb-2">Deskripsi</label>
            <textarea v-model="form.description" class="input" rows="3"></textarea>
          </div>

          <div class="mb-4">
            <label class="flex items-center">
              <input v-model="form.is_active" type="checkbox" class="mr-2" />
              <span class="text-sm font-medium">Aktif</span>
            </label>
          </div>

          <div class="flex gap-2">
            <button type="submit" :disabled="loading" class="btn btn-primary flex-1">
              {{ loading ? 'Menyimpan...' : 'Simpan' }}
            </button>
            <button type="button" @click="closeModal" class="btn btn-secondary flex-1">
              Batal
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useCategoryStore } from '@/stores/category'

const categoryStore = useCategoryStore()

const categories = computed(() => categoryStore.categories)
const loading = ref(false)
const showModal = ref(false)
const isEdit = ref(false)
const editId = ref(null)

const form = ref({
  name: '',
  icon: '',
  description: '',
  is_active: true
})

onMounted(async () => {
  await categoryStore.fetchCategories()
})

function editCategory(category) {
  isEdit.value = true
  editId.value = category.id
  form.value = {
    name: category.name,
    icon: category.icon || '',
    description: category.description || '',
    is_active: category.is_active
  }
  showModal.value = true
}

async function handleSubmit() {
  loading.value = true

  let result
  if (isEdit.value) {
    result = await categoryStore.updateCategory(editId.value, form.value)
  } else {
    result = await categoryStore.createCategory(form.value)
  }

  if (result.success) {
    alert(`Kategori berhasil ${isEdit.value ? 'diupdate' : 'ditambahkan'}`)
    closeModal()
    await categoryStore.fetchCategories()
  } else {
    alert('Gagal menyimpan kategori: ' + result.message)
  }

  loading.value = false
}

async function deleteCategory(id) {
  if (confirm('Yakin ingin menghapus kategori ini?')) {
    const result = await categoryStore.deleteCategory(id)
    if (result.success) {
      alert('Kategori berhasil dihapus')
      await categoryStore.fetchCategories()
    } else {
      alert('Gagal menghapus kategori: ' + result.message)
    }
  }
}

function closeModal() {
  showModal.value = false
  isEdit.value = false
  editId.value = null
  form.value = {
    name: '',
    icon: '',
    description: '',
    is_active: true
  }
}
</script>

<template>
  <div class="space-y-6">
    <!-- Header -->
    <div class="bg-white rounded-2xl shadow-sm p-5 sm:p-6">
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <div>
          <h2 class="text-2xl font-bold text-gray-900">Kategori Produk</h2>
          <p class="text-sm text-gray-500 mt-1">Kelola kategori untuk mengorganisir produk</p>
        </div>
        <button 
          @click="showModal = true" 
          class="w-full sm:w-auto flex items-center justify-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-xl hover:from-emerald-700 hover:to-teal-700 transition-all duration-200 shadow-lg shadow-emerald-500/30"
        >
          <i class="fas fa-plus mr-2"></i>
          Tambah Kategori
        </button>
      </div>
    </div>

    <!-- Categories Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
      <div 
        v-for="category in categories" 
        :key="category.id" 
        class="bg-white rounded-2xl shadow-sm p-5 hover:shadow-md transition-shadow duration-200"
      >
        <div class="flex items-start justify-between mb-4">
          <div class="w-12 h-12 bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl flex items-center justify-center text-white shadow-lg">
            <i class="fas fa-folder text-xl"></i>
          </div>
          <div class="flex gap-2">
            <button 
              @click="editCategory(category)" 
              class="w-9 h-9 flex items-center justify-center bg-blue-100 text-blue-600 hover:bg-blue-200 rounded-lg transition-colors"
              title="Edit"
            >
              <i class="fas fa-edit"></i>
            </button>
            <button 
              @click="deleteCategory(category.id)" 
              class="w-9 h-9 flex items-center justify-center bg-red-100 text-red-600 hover:bg-red-200 rounded-lg transition-colors"
              title="Hapus"
            >
              <i class="fas fa-trash-alt"></i>
            </button>
          </div>
        </div>
        <h3 class="font-bold text-lg text-gray-900 mb-2">{{ category.name }}</h3>
        <p class="text-sm text-gray-600 mb-4 line-clamp-2">
          {{ category.description || 'Tidak ada deskripsi' }}
        </p>
        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
          <div class="flex items-center text-sm text-gray-500">
            <i class="fas fa-box mr-2"></i>
            <span>{{ category.products_count || 0 }} produk</span>
          </div>
          <span
            class="px-3 py-1 text-xs font-semibold rounded-full"
            :class="category.is_active 
              ? 'bg-emerald-100 text-emerald-700' 
              : 'bg-gray-100 text-gray-700'"
          >
            {{ category.is_active ? 'Aktif' : 'Nonaktif' }}
          </span>
        </div>
      </div>
    </div>

    <!-- Empty State -->
    <div v-if="!categories.length" class="bg-white rounded-2xl shadow-sm p-12 text-center">
      <i class="fas fa-folder-open text-6xl text-gray-300 mb-4"></i>
      <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum ada kategori</h3>
      <p class="text-gray-500 mb-6">Mulai dengan menambahkan kategori pertama Anda</p>
      <button 
        @click="showModal = true" 
        class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-xl hover:from-emerald-700 hover:to-teal-700 transition-all"
      >
        <i class="fas fa-plus mr-2"></i>
        Tambah Kategori
      </button>
    </div>

    <!-- Modal -->
    <div 
      v-if="showModal" 
      class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4"
      @click.self="closeModal"
    >
      <div class="bg-white rounded-2xl p-6 max-w-md w-full mx-4 shadow-2xl transform transition-all">
        <div class="flex items-center justify-between mb-6">
          <h3 class="text-2xl font-bold text-gray-900">
            <i :class="isEdit ? 'fas fa-edit' : 'fas fa-plus-circle'" class="mr-2 text-emerald-600"></i>
            {{ isEdit ? 'Edit Kategori' : 'Tambah Kategori' }}
          </h3>
          <button 
            @click="closeModal" 
            class="w-8 h-8 flex items-center justify-center bg-gray-100 hover:bg-gray-200 rounded-lg transition-colors"
          >
            <i class="fas fa-times text-gray-600"></i>
          </button>
        </div>

        <form @submit.prevent="handleSubmit" class="space-y-5">
          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-tag mr-2 text-gray-400"></i>Nama Kategori *
            </label>
            <input 
              v-model="form.name" 
              type="text" 
              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition outline-none" 
              placeholder="Contoh: Pupuk, Pestisida, dll"
              required 
            />
          </div>

          <div>
            <label class="block text-sm font-semibold text-gray-700 mb-2">
              <i class="fas fa-align-left mr-2 text-gray-400"></i>Deskripsi
            </label>
            <textarea 
              v-model="form.description" 
              class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition outline-none resize-none" 
              rows="3"
              placeholder="Deskripsi singkat tentang kategori"
            ></textarea>
          </div>

          <div class="flex items-center p-4 bg-gray-50 rounded-xl">
            <input 
              v-model="form.is_active" 
              type="checkbox" 
              id="is_active"
              class="w-5 h-5 text-emerald-600 border-gray-300 rounded focus:ring-emerald-500 focus:ring-2"
            />
            <label for="is_active" class="ml-3 text-sm font-semibold text-gray-700 cursor-pointer">
              <i class="fas fa-toggle-on mr-2 text-emerald-600"></i>
              Kategori Aktif
            </label>
          </div>

          <div class="flex gap-3 pt-2">
            <button 
              type="submit" 
              :disabled="loading" 
              class="flex-1 flex items-center justify-center px-6 py-3 bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold rounded-xl hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition-all disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <i :class="loading ? 'fas fa-spinner fa-spin' : 'fas fa-save'" class="mr-2"></i>
              {{ loading ? 'Menyimpan...' : 'Simpan' }}
            </button>
            <button 
              type="button" 
              @click="closeModal" 
              class="flex-1 flex items-center justify-center px-6 py-3 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors"
            >
              <i class="fas fa-times mr-2"></i>
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
    description: '',
    is_active: true
  }
}
</script>

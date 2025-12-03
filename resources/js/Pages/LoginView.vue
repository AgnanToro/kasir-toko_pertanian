<template>
  <div class="min-h-screen bg-gradient-to-br from-emerald-500 via-teal-600 to-cyan-600 flex items-center justify-center px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
      <!-- Header -->
      <div class="text-center">
        <div class="mx-auto h-16 w-16 sm:h-20 sm:w-20 bg-white rounded-2xl shadow-lg flex items-center justify-center mb-4">
          <i class="fas fa-store text-3xl sm:text-4xl text-emerald-600"></i>
        </div>
        <h1 class="text-3xl sm:text-4xl font-bold text-white mb-2">Toko Pertanian</h1>
        <p class="text-emerald-50 text-sm sm:text-base">Sistem Kasir Sinar Jaya</p>
      </div>

      <!-- Login Card -->
      <div class="bg-white rounded-2xl shadow-2xl overflow-hidden">
        <div class="px-6 sm:px-8 py-8">
          <div class="mb-6">
            <h2 class="text-2xl font-bold text-gray-900">Masuk ke Akun</h2>
            <p class="text-gray-600 text-sm mt-1">Silakan login untuk melanjutkan</p>
          </div>

          <form @submit.prevent="handleLogin" class="space-y-5">
            <!-- Email Input -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-envelope mr-2 text-gray-400"></i>Email
              </label>
              <input
                v-model="form.email"
                type="email"
                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition duration-200 outline-none"
                placeholder="Masukkan email"
                required
              />
            </div>

            <!-- Password Input -->
            <div>
              <label class="block text-sm font-semibold text-gray-700 mb-2">
                <i class="fas fa-lock mr-2 text-gray-400"></i>Password
              </label>
              <div class="relative">
                <input
                  v-model="form.password"
                  :type="showPassword ? 'text' : 'password'"
                  class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition duration-200 outline-none pr-12"
                  placeholder="Masukkan password"
                  required
                />
                <button
                  type="button"
                  @click="showPassword = !showPassword"
                  class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition"
                >
                  <i :class="showPassword ? 'fas fa-eye-slash' : 'fas fa-eye'"></i>
                </button>
              </div>
            </div>

            <!-- Error Message -->
            <div v-if="error" class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl text-sm flex items-start">
              <i class="fas fa-exclamation-circle mr-2 mt-0.5"></i>
              <span>{{ error }}</span>
            </div>

            <!-- Submit Button -->
            <button
              type="submit"
              :disabled="loading"
              class="w-full bg-gradient-to-r from-emerald-600 to-teal-600 text-white font-semibold py-3.5 px-4 rounded-xl hover:from-emerald-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition duration-200 disabled:opacity-50 disabled:cursor-not-allowed shadow-lg shadow-emerald-500/30"
            >
              <span v-if="loading" class="flex items-center justify-center">
                <i class="fas fa-spinner fa-spin mr-2"></i>
                Memproses...
              </span>
              <span v-else class="flex items-center justify-center">
                <i class="fas fa-sign-in-alt mr-2"></i>
                Masuk
              </span>
            </button>
          </form>
        </div>
      </div>

      <!-- Footer -->
      <div class="text-center">
        <p class="text-white text-xs sm:text-sm opacity-90">
          <i class="fas fa-copyright mr-1"></i>
          {{ new Date().getFullYear() }} Sinar Jaya. All rights reserved.
        </p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const form = ref({
  email: '',
  password: ''
})

const loading = ref(false)
const error = ref('')
const showPassword = ref(false)

async function handleLogin() {
  loading.value = true
  error.value = ''

  const result = await authStore.login(form.value.email, form.value.password)

  if (result.success) {
    router.push('/')
  } else {
    error.value = result.message
  }

  loading.value = false
}
</script>

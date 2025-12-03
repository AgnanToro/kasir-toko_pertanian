<template>
  <div>
    <!-- Header -->
    <div class="mb-6">
      <h2 class="text-2xl font-bold text-gray-800 flex items-center">
        <i class="fas fa-receipt mr-3 text-emerald-600"></i>
        Daftar Transaksi
      </h2>
      <p class="text-gray-600 mt-1">Kelola dan pantau semua transaksi penjualan</p>
    </div>

    <!-- Filters -->
    <div class="card mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="relative">
          <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
          <input
            v-model="searchQuery"
            @input="handleSearch"
            type="text"
            class="input pl-10"
            placeholder="Cari invoice atau pelanggan..."
          />
        </div>
        <div class="relative">
          <i class="fas fa-calendar-alt absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
          <input
            v-model="startDate"
            @change="handleFilter"
            type="date"
            class="input pl-10"
          />
        </div>
        <div class="relative">
          <i class="fas fa-calendar-check absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
          <input
            v-model="endDate"
            @change="handleFilter"
            type="date"
            class="input pl-10"
          />
        </div>
        <div class="relative">
          <i class="fas fa-wallet absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
          <select v-model="paymentMethod" @change="handleFilter" class="input pl-10">
            <option value="">Semua Pembayaran</option>
            <option value="cash">Cash</option>
            <option value="transfer">Transfer</option>
            <option value="debit">Debit</option>
            <option value="credit">Credit</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Transactions Table -->
    <div class="card">
      <div class="overflow-x-auto">
        <table class="min-w-full">
          <thead>
            <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Invoice</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Tanggal</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Kasir</th>
              <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wider">Pelanggan</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Total</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Keuntungan</th>
              <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase tracking-wider">Pembayaran</th>
              <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase tracking-wider">Aksi</th>
            </tr>
          </thead>
          <tbody class="bg-white divide-y divide-gray-200">
            <tr v-if="loading">
              <td colspan="8" class="px-4 py-12 text-center">
                <i class="fas fa-spinner fa-spin text-3xl text-gray-400 mb-2"></i>
                <p class="text-gray-500">Memuat data...</p>
              </td>
            </tr>
            <tr v-else-if="transactions.length === 0">
              <td colspan="8" class="px-4 py-12 text-center">
                <i class="fas fa-inbox text-5xl text-gray-300 mb-3"></i>
                <p class="text-gray-500 font-medium">Tidak ada transaksi</p>
                <p class="text-sm text-gray-400">Transaksi akan muncul di sini</p>
              </td>
            </tr>
            <tr v-else v-for="transaction in transactions" :key="transaction.id" class="hover:bg-gray-50 transition-colors">
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="font-mono text-xs font-semibold text-gray-800">
                  {{ transaction.invoice_number }}
                </div>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="text-xs text-gray-700">
                  <i class="fas fa-calendar text-gray-400 mr-1"></i>
                  {{ formatDate(transaction.transaction_date) }}
                </div>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="text-xs text-gray-700">
                  <i class="fas fa-user text-gray-400 mr-1"></i>
                  {{ transaction.user?.name }}
                </div>
              </td>
              <td class="px-4 py-3 whitespace-nowrap">
                <div class="text-xs text-gray-700">
                  <i class="fas fa-user-tag text-gray-400 mr-1"></i>
                  {{ transaction.customer_name || '-' }}
                </div>
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-right">
                <span class="font-bold text-sm text-gray-900">Rp {{ formatNumber(transaction.total) }}</span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-right">
                <div class="flex items-center justify-end">
                  <span 
                    class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold"
                    :class="transaction.net_profit >= 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'"
                  >
                    <i :class="transaction.net_profit >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'" class="mr-1"></i>
                    Rp {{ formatNumber(Math.abs(transaction.net_profit || 0)) }}
                  </span>
                </div>
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-center">
                <span 
                  class="inline-block px-3 py-1 rounded-full text-xs font-bold"
                  :class="{
                    'bg-emerald-100 text-emerald-700': transaction.payment_method === 'cash',
                    'bg-blue-100 text-blue-700': transaction.payment_method === 'transfer',
                    'bg-purple-100 text-purple-700': transaction.payment_method === 'debit',
                    'bg-orange-100 text-orange-700': transaction.payment_method === 'credit'
                  }"
                >
                  {{ transaction.payment_method.toUpperCase() }}
                </span>
              </td>
              <td class="px-4 py-3 whitespace-nowrap text-right">
                <div class="flex gap-2 justify-end relative z-10">
                  <RouterLink
                    :to="`/transactions/${transaction.id}`"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 text-xs font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors shadow-sm cursor-pointer"
                  >
                    <i class="fas fa-eye"></i>
                    Detail
                  </RouterLink>
                  <button
                    @click="handleDelete(transaction.id)"
                    class="inline-flex items-center px-2.5 py-1.5 text-xs font-semibold text-white bg-red-600 hover:bg-red-700 rounded-lg transition-colors shadow-sm cursor-pointer"
                  >
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="flex justify-center items-center gap-2 mt-6 pt-6 border-t">
        <button
          @click="goToPage(pagination.current_page - 1)"
          :disabled="pagination.current_page === 1"
          class="px-4 py-2 rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          :class="pagination.current_page === 1 
            ? 'bg-gray-100 text-gray-400' 
            : 'bg-white border border-gray-300 text-gray-700 hover:bg-emerald-50 hover:border-emerald-500'"
        >
          <i class="fas fa-chevron-left mr-1"></i> Prev
        </button>
        <span class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-50 rounded-lg">
          Halaman {{ pagination.current_page }} dari {{ pagination.last_page }}
        </span>
        <button
          @click="goToPage(pagination.current_page + 1)"
          :disabled="pagination.current_page === pagination.last_page"
          class="px-4 py-2 rounded-lg font-medium transition-colors disabled:opacity-50 disabled:cursor-not-allowed"
          :class="pagination.current_page === pagination.last_page 
            ? 'bg-gray-100 text-gray-400' 
            : 'bg-white border border-gray-300 text-gray-700 hover:bg-emerald-50 hover:border-emerald-500'"
        >
          Next <i class="fas fa-chevron-right ml-1"></i>
        </button>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { RouterLink } from 'vue-router'
import { useTransactionStore } from '@/stores/transaction'

const transactionStore = useTransactionStore()

const searchQuery = ref('')
const startDate = ref('')
const endDate = ref('')
const paymentMethod = ref('')

const transactions = computed(() => transactionStore.transactions)
const loading = computed(() => transactionStore.loading)
const pagination = computed(() => transactionStore.pagination)

onMounted(async () => {
  await loadTransactions()
})

async function loadTransactions() {
  await transactionStore.fetchTransactions({
    search: searchQuery.value,
    start_date: startDate.value,
    end_date: endDate.value,
    payment_method: paymentMethod.value
  })
}

async function handleSearch() {
  await loadTransactions()
}

async function handleFilter() {
  await loadTransactions()
}

async function goToPage(page) {
  await transactionStore.fetchTransactions({
    page,
    search: searchQuery.value,
    start_date: startDate.value,
    end_date: endDate.value,
    payment_method: paymentMethod.value
  })
}

async function handleDelete(id) {
  if (confirm('Yakin ingin menghapus transaksi ini? Stok akan dikembalikan.')) {
    const result = await transactionStore.deleteTransaction(id)
    if (result.success) {
      alert('Transaksi berhasil dihapus')
      await loadTransactions()
    } else {
      alert('Gagal menghapus transaksi: ' + result.message)
    }
  }
}

function formatNumber(num) {
  return new Intl.NumberFormat('id-ID').format(num)
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>

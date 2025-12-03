<template>
  <div>
    <!-- Filters -->
    <div class="card mb-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <input
          v-model="searchQuery"
          @input="handleSearch"
          type="text"
          class="input"
          placeholder="Cari invoice..."
        />
        <input
          v-model="startDate"
          @change="handleFilter"
          type="date"
          class="input"
        />
        <input
          v-model="endDate"
          @change="handleFilter"
          type="date"
          class="input"
        />
        <select v-model="paymentMethod" @change="handleFilter" class="input">
          <option value="">Semua Pembayaran</option>
          <option value="cash">Cash</option>
          <option value="transfer">Transfer</option>
          <option value="debit">Debit</option>
          <option value="credit">Credit</option>
        </select>
      </div>
    </div>

    <!-- Transactions Table -->
    <div class="card">
      <div class="overflow-x-auto">
        <table class="table">
          <thead>
            <tr>
              <th>Invoice</th>
              <th>Tanggal</th>
              <th>Kasir</th>
              <th>Pelanggan</th>
              <th>Total</th>
              <th>Pembayaran</th>
              <th>Aksi</th>
            </tr>
          </thead>
          <tbody>
            <tr v-if="loading">
              <td colspan="7" class="text-center py-8">Loading...</td>
            </tr>
            <tr v-else-if="transactions.length === 0">
              <td colspan="7" class="text-center py-8 text-gray-500">Tidak ada transaksi</td>
            </tr>
            <tr v-else v-for="transaction in transactions" :key="transaction.id">
              <td>
                <span class="font-semibold">{{ transaction.invoice_number }}</span>
              </td>
              <td>{{ formatDate(transaction.transaction_date) }}</td>
              <td>{{ transaction.user?.name }}</td>
              <td>{{ transaction.customer_name || '-' }}</td>
              <td class="font-semibold">Rp {{ formatNumber(transaction.total) }}</td>
              <td>
                <span class="badge badge-info">
                  {{ transaction.payment_method.toUpperCase() }}
                </span>
              </td>
              <td>
                <div class="flex gap-2">
                  <RouterLink
                    :to="`/transactions/${transaction.id}`"
                    class="text-blue-600 hover:text-blue-800"
                  >
                    👁️ Detail
                  </RouterLink>
                  <button
                    @click="handleDelete(transaction.id)"
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

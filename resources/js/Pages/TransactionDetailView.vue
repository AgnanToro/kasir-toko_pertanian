<template>
  <div>
    <div class="card max-w-5xl mx-auto">
      <div v-if="loading" class="text-center py-12">
        <i class="fas fa-spinner fa-spin text-4xl text-gray-400 mb-3"></i>
        <p class="text-gray-500">Memuat detail transaksi...</p>
      </div>

      <div v-else-if="transaction">
        <!-- Header -->
        <div class="border-b-2 border-gray-200 pb-6 mb-6">
          <div class="flex justify-between items-start">
            <div>
              <h2 class="text-3xl font-bold text-gray-800 mb-2 flex items-center">
                <i class="fas fa-file-invoice text-emerald-600 mr-3"></i>
                Detail Transaksi
              </h2>
              <p class="font-mono text-lg font-semibold text-gray-600">{{ transaction.invoice_number }}</p>
            </div>
            <div class="text-right">
              <div class="text-sm text-gray-600 mb-2">
                <i class="fas fa-calendar-alt mr-1"></i>
                {{ formatDate(transaction.transaction_date) }}
              </div>
              <span 
                class="inline-block px-4 py-2 rounded-lg text-sm font-bold"
                :class="{
                  'bg-emerald-100 text-emerald-700': transaction.payment_method === 'cash',
                  'bg-blue-100 text-blue-700': transaction.payment_method === 'transfer',
                  'bg-purple-100 text-purple-700': transaction.payment_method === 'debit',
                  'bg-orange-100 text-orange-700': transaction.payment_method === 'credit'
                }"
              >
                <i class="fas fa-wallet mr-1"></i>
                {{ transaction.payment_method.toUpperCase() }}
              </span>
            </div>
          </div>
        </div>

        <!-- Info Grid -->
        <div class="grid grid-cols-2 gap-6 mb-6">
          <div class="bg-gradient-to-br from-blue-50 to-blue-100 p-5 rounded-lg border border-blue-200">
            <p class="text-sm font-semibold text-blue-700 mb-2 flex items-center">
              <i class="fas fa-user-tie mr-2"></i>
              Kasir
            </p>
            <p class="text-lg font-bold text-gray-800">{{ transaction.user?.name }}</p>
          </div>
          <div class="bg-gradient-to-br from-purple-50 to-purple-100 p-5 rounded-lg border border-purple-200">
            <p class="text-sm font-semibold text-purple-700 mb-2 flex items-center">
              <i class="fas fa-user-tag mr-2"></i>
              Pelanggan
            </p>
            <p class="text-lg font-bold text-gray-800">{{ transaction.customer_name || '-' }}</p>
          </div>
        </div>

        <!-- Items -->
        <div class="mb-6">
          <h3 class="font-bold text-xl mb-4 flex items-center text-gray-800">
            <i class="fas fa-shopping-cart text-emerald-600 mr-2"></i>
            Item Pembelian
          </h3>
          <div class="overflow-x-auto border rounded-lg">
            <table class="min-w-full">
              <thead>
                <tr class="bg-gradient-to-r from-gray-50 to-gray-100 border-b-2 border-gray-200">
                  <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">Produk</th>
                  <th class="px-4 py-3 text-left text-xs font-semibold text-gray-700 uppercase">SKU</th>
                  <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Harga</th>
                  <th class="px-4 py-3 text-center text-xs font-semibold text-gray-700 uppercase">Qty</th>
                  <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Diskon</th>
                  <th class="px-4 py-3 text-right text-xs font-semibold text-gray-700 uppercase">Total</th>
                </tr>
              </thead>
              <tbody class="bg-white divide-y divide-gray-200">
                <tr v-for="item in transaction.items" :key="item.id" class="hover:bg-gray-50 transition-colors">
                  <td class="px-4 py-3 text-sm font-semibold text-gray-800">{{ item.product_name }}</td>
                  <td class="px-4 py-3 text-xs font-mono text-gray-600">{{ item.product_sku }}</td>
                  <td class="px-4 py-3 text-sm text-right text-gray-700">Rp {{ formatNumber(item.price) }}</td>
                  <td class="px-4 py-3 text-sm text-center font-semibold text-gray-800">{{ item.quantity }}</td>
                  <td class="px-4 py-3 text-sm text-right text-red-600">Rp {{ formatNumber(item.discount) }}</td>
                  <td class="px-4 py-3 text-sm text-right font-bold text-emerald-600">Rp {{ formatNumber(item.total) }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Totals -->
        <div class="bg-gradient-to-br from-gray-50 to-gray-100 border-2 border-gray-200 rounded-lg p-6">
          <div class="max-w-md ml-auto space-y-3">
            <div class="flex justify-between items-center py-2 border-b border-gray-300">
              <span class="text-gray-700 font-medium">Subtotal</span>
              <span class="font-bold text-gray-800">Rp {{ formatNumber(transaction.subtotal) }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-300">
              <span class="text-red-600 font-medium">Diskon</span>
              <span class="font-bold text-red-600">- Rp {{ formatNumber(transaction.discount) }}</span>
            </div>
            <div class="flex justify-between items-center py-2 border-b border-gray-300">
              <span class="text-gray-700 font-medium">Pajak</span>
              <span class="font-bold text-gray-800">Rp {{ formatNumber(transaction.tax) }}</span>
            </div>
            <div class="flex justify-between items-center py-3 border-t-2 border-gray-400">
              <span class="text-xl font-bold text-gray-800">Total</span>
              <span class="text-2xl font-bold text-emerald-600">Rp {{ formatNumber(transaction.total) }}</span>
            </div>
            <div class="flex justify-between items-center py-2 bg-blue-50 px-3 rounded-lg">
              <span class="text-blue-700 font-medium">Dibayar</span>
              <span class="font-bold text-blue-700">Rp {{ formatNumber(transaction.paid_amount) }}</span>
            </div>
            <div class="flex justify-between items-center py-2 bg-purple-50 px-3 rounded-lg">
              <span class="text-purple-700 font-medium">Kembalian</span>
              <span class="font-bold text-purple-700">Rp {{ formatNumber(transaction.change_amount) }}</span>
            </div>
            <div 
              class="flex justify-between items-center py-3 px-4 rounded-lg border-2 mt-3"
              :class="transaction.net_profit >= 0 ? 'bg-emerald-50 border-emerald-300' : 'bg-red-50 border-red-300'"
            >
              <span class="font-bold flex items-center" :class="transaction.net_profit >= 0 ? 'text-emerald-800' : 'text-red-800'">
                <i class="fas fa-chart-line mr-2"></i>
                Keuntungan Bersih
              </span>
              <span class="text-xl font-bold" :class="transaction.net_profit >= 0 ? 'text-emerald-700' : 'text-red-700'">
                <i :class="transaction.net_profit >= 0 ? 'fas fa-arrow-up' : 'fas fa-arrow-down'" class="mr-1"></i>
                Rp {{ formatNumber(Math.abs(transaction.net_profit || 0)) }}
              </span>
            </div>
          </div>
        </div>

        <!-- Notes -->
        <div v-if="transaction.notes" class="mt-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 rounded-lg">
          <p class="text-sm font-bold mb-2 text-yellow-800 flex items-center">
            <i class="fas fa-sticky-note mr-2"></i>
            Catatan
          </p>
          <p class="text-sm text-yellow-900">{{ transaction.notes }}</p>
        </div>

        <!-- Actions -->
        <div class="flex gap-4 mt-8 pt-6 border-t-2 border-gray-200">
          <button @click="printReceipt" class="flex-1 py-3 rounded-lg font-semibold text-white bg-emerald-600 hover:bg-emerald-700 transition-colors shadow-lg">
            <i class="fas fa-print mr-2"></i> Cetak Struk
          </button>
          <RouterLink to="/transactions" class="flex-1 py-3 rounded-lg font-semibold text-gray-700 bg-white border-2 border-gray-300 hover:bg-gray-50 transition-colors text-center">
            <i class="fas fa-arrow-left mr-2"></i> Kembali
          </RouterLink>
        </div>
      </div>

      <div v-else class="text-center py-12">
        <i class="fas fa-exclamation-circle text-5xl text-gray-300 mb-3"></i>
        <p class="text-gray-500 font-medium">Transaksi tidak ditemukan</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted, computed } from 'vue'
import { useRoute, RouterLink } from 'vue-router'
import { useTransactionStore } from '@/stores/transaction'

const route = useRoute()
const transactionStore = useTransactionStore()

const transaction = computed(() => transactionStore.transaction)
const loading = computed(() => transactionStore.loading)

onMounted(async () => {
  await transactionStore.fetchTransaction(route.params.id)
})

function printReceipt() {
  if (!transaction.value) return

  const printWindow = window.open('', '', 'width=300,height=600')
  printWindow.document.write(`
    <html>
      <head>
        <title>Struk - ${transaction.value.invoice_number}</title>
        <style>
          body { font-family: monospace; font-size: 12px; padding: 10px; }
          .center { text-align: center; }
          .line { border-top: 1px dashed #000; margin: 10px 0; }
        </style>
      </head>
      <body>
        <div class="center">
          <h2>TOKO PERTANIAN SINAR JAYA</h2>
          <p>Jl. Pertanian No. 123</p>
          <p>Telp: 0812-3456-7890</p>
        </div>
        <div class="line"></div>
        <p>Invoice: ${transaction.value.invoice_number}</p>
        <p>Tanggal: ${formatDate(transaction.value.transaction_date)}</p>
        <p>Kasir: ${transaction.value.user?.name}</p>
        ${transaction.value.customer_name ? `<p>Pelanggan: ${transaction.value.customer_name}</p>` : ''}
        <div class="line"></div>
        ${transaction.value.items.map(item => `
          <p>${item.product_name}<br/>
          ${item.quantity} x ${formatNumber(item.price)} = ${formatNumber(item.total)}</p>
        `).join('')}
        <div class="line"></div>
        <p>Subtotal: Rp ${formatNumber(transaction.value.subtotal)}</p>
        <p>Diskon: Rp ${formatNumber(transaction.value.discount)}</p>
        <p>Pajak: Rp ${formatNumber(transaction.value.tax)}</p>
        <p><strong>TOTAL: Rp ${formatNumber(transaction.value.total)}</strong></p>
        <p>Bayar: Rp ${formatNumber(transaction.value.paid_amount)}</p>
        <p>Kembali: Rp ${formatNumber(transaction.value.change_amount)}</p>
        <div class="line"></div>
        <p class="center">Terima Kasih</p>
        <p class="center">Selamat Berbelanja Kembali</p>
      </body>
    </html>
  `)
  printWindow.document.close()
  printWindow.print()
}

function formatNumber(num) {
  return new Intl.NumberFormat('id-ID').format(num)
}

function formatDate(date) {
  return new Date(date).toLocaleDateString('id-ID', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
  })
}
</script>
